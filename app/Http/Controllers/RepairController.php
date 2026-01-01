<?php

namespace App\Http\Controllers;

use App\Models\Repair;
use App\Models\RepairHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Exports\RepairExport;
use Maatwebsite\Excel\Facades\Excel;

class RepairController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user(); // Ambil data user yang login
        $search = $request->query('search');

        $query = Repair::with(['histories' => function($q) {
            $q->latest();
        }]);

        // --- LOGIKA FILTER AKSES BERDASARKAN NAMA USER ---
        // Jika nama user BUKAN 'Administrator' DAN BUKAN 'Farmalkes'
        if (!in_array($user->name, ['Administrator', 'Farmalkes'])) {
            // Kunci data agar hanya menampilkan RS yang namanya sama dengan nama akun user
            $query->where('nama_rs', $user->name);
        }

        // Logika Pencarian (Existing)
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_rs', 'like', "%{$search}%")
                ->orWhere('nama_alkes', 'like', "%{$search}%")
                ->orWhere('serial_number', 'like', "%{$search}%")
                ->orWhere('lokasi', 'like', "%{$search}%")
                ->orWhere('nama_penyedia', 'like', "%{$search}%");
            });
        }

        // Pagination 20 data per halaman + keep query search
        $repairs = $query->latest()->paginate(20)->withQueryString();

        return view('repairs.index', compact('repairs'));
    }

    public function show($id)
    {
        // Mengambil data perbaikan beserta history-nya berdasarkan ID
        $repair = Repair::with(['histories' => function($q) {
            $q->latest();
        }])->findOrFail($id);

        // Jika Anda ingin menampilkan data dalam format JSON (untuk kebutuhan AJAX detail)
        return response()->json($repair);
        
        // ATAU jika Anda ingin menampilkan di halaman terpisah (tapi kita pakai modal, jadi ini opsional)
        // return view('repairs.show', compact('repair'));
    }

    public function store(Request $request)
    {
        $data = $request->all();

        if ($request->hasFile('file_bap')) {
            $data['file_bap'] = $request->file('file_bap')->store('bap', 'public');
        }

        if ($request->hasFile('foto_kondisi')) {
            $paths = [];
            foreach ($request->file('foto_kondisi') as $file) {
                $paths[] = $file->store('repairs', 'public');
            }
            $data['foto_kondisi'] = $paths; 
        }

        $repair = Repair::create($data);

        $repair->histories()->create([
            'status_perbaikan' => $request->status_perbaikan ?? 'Laporan Diterima',
            'keterangan_perubahan' => 'Laporan awal berhasil dibuat.',
            'user_nama' => $request->input_by
        ]);

        return redirect()->back()->with('success', 'Laporan berhasil disimpan!');
    }

    public function updateStatus(Request $request, $id)
    {
        $repair = Repair::findOrFail($id);
        
        // Simpan data lama untuk perbandingan di history (opsional)
        $komponenLama = $repair->komponen;

        // 1. Update Data Dasar
        $repair->status_perbaikan = $request->status_perbaikan;
        $repair->rtl = $request->rtl;
        $repair->komponen = $request->komponen; // Mengambil dari input value tadi
        $repair->kondisi_kontrak = $request->kondisi_kontrak;
        $repair->kategori = $request->kategori;
        $repair->respon_penyedia = $request->respon_penyedia;
        $repair->tindakan_penyedia = $request->tindakan_penyedia;

        // 2. Logika File BAP & Foto (Tetap sama seperti sebelumnya)
        if ($request->hasFile('file_bap')) {
            if ($repair->file_bap) { Storage::disk('public')->delete($repair->file_bap); }
            $repair->file_bap = $request->file('file_bap')->store('bap', 'public');
        }

        if ($request->hasFile('foto_kondisi')) {
            $currentPhotos = is_array($repair->foto_kondisi) ? $repair->foto_kondisi : []; 
            foreach ($request->file('foto_kondisi') as $file) {
                $currentPhotos[] = $file->store('repairs', 'public');
            }
            $repair->foto_kondisi = $currentPhotos;
        }

        $repair->save();

        // 3. Catat History
        // Keterangan history kita buat lebih lengkap agar informatif
        $pesanHistory = $request->keterangan_log;
        if ($komponenLama != $request->komponen) {
            $pesanHistory .= " (Update Komponen: " . $request->komponen . ")";
        }

        $repair->histories()->create([
            'status_perbaikan' => $request->status_perbaikan,
            'keterangan_perubahan' => $pesanHistory,
            'user_nama' => $request->petugas,
        ]);

        return redirect()->back()->with('success', 'Progress perbaikan berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $repair = Repair::findOrFail($id);

        // 1. Hapus semua foto dari storage (karena array, harus di-loop)
        if ($repair->foto_kondisi && is_array($repair->foto_kondisi)) {
            foreach ($repair->foto_kondisi as $foto) {
                Storage::disk('public')->delete($foto);
            }
        }

        // 2. Hapus file BAST dari storage
        if ($repair->file_bap) {
            Storage::disk('public')->delete($repair->file_bap);
        }

        $repair->delete();

        return redirect()->back()->with('success', 'Data laporan dan file terkait berhasil dihapus.');
    }

    public function exportExcel(Request $request)
    {
        $filters = [
            'nama_rs'          => $request->nama_rs,
            'nama_alkes'       => $request->nama_alkes,
            'status_perbaikan' => $request->status_perbaikan,
            'grade_kerusakan'  => $request->grade_kerusakan,
            'respon_penyedia'  => $request->respon_penyedia,
        ];

        $fileName = 'Laporan_BPAFK_' . date('Ymd_His') . '.xlsx';
        return Excel::download(new RepairExport($filters), $fileName);
    }

    // Method untuk memuat halaman pertama kali
    public function reportPage()
    {
        // Ambil SEMUA data tanpa limit
        $repairs = Repair::latest()->get(); 
        return view('repairs.report', compact('repairs'));
    }

    // Method untuk filter AJAX
    public function previewExport(Request $request)
    {
        $query = Repair::query();

        if ($request->nama_rs) {
            $query->where('nama_rs', 'like', '%' . $request->nama_rs . '%');
        }
        if ($request->nama_alkes) {
            $query->where('nama_alkes', 'like', '%' . $request->nama_alkes . '%');
        }
        if ($request->kategori) {
            $query->where('kategori', $request->kategori);
        }
        if ($request->status_perbaikan) {
            $query->where('status_perbaikan', $request->status_perbaikan);
        }
        if ($request->grade_kerusakan) {
            $query->where('grade_kerusakan', $request->grade_kerusakan);
        }
        if ($request->respon_penyedia) {
            $query->where('respon_penyedia', $request->respon_penyedia);
        }

        // Ambil SEMUA data hasil filter (hapus take(5))
        $repairs = $query->latest()->get();

        // Render view baris tabel dan kirim sebagai response teks/html
        return view('repairs._report_rows', compact('repairs'))->render();
    }
}