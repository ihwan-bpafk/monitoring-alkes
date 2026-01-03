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
        // 1. Ambil data unik untuk semua dropdown filter (Dinamis dari DB)
        $list_rs = Repair::whereNotNull('nama_rs')->distinct()->orderBy('nama_rs', 'asc')->pluck('nama_rs');
        $list_alkes = Repair::whereNotNull('nama_alkes')->distinct()->orderBy('nama_alkes', 'asc')->pluck('nama_alkes');
        $list_kategori = Repair::whereNotNull('kategori')->distinct()->orderBy('kategori', 'asc')->pluck('kategori');
        
        // Tambahan filter baru
        $list_grade = Repair::whereNotNull('grade_kerusakan')->distinct()->orderBy('grade_kerusakan', 'asc')->pluck('grade_kerusakan');
        $list_status = Repair::whereNotNull('status_perbaikan')->distinct()->orderBy('status_perbaikan', 'asc')->pluck('status_perbaikan');
        $list_respon = Repair::whereNotNull('respon_penyedia')->distinct()->orderBy('respon_penyedia', 'asc')->pluck('respon_penyedia');

        // 2. Query Utama
        $query = Repair::query();

        // 3. Gabungkan Pencarian General (Input Text)
        $query->when($request->search, function ($q) use ($request) {
            return $q->where(function($sub) use ($request) {
                $sub->where('nama_rs', 'like', '%' . $request->search . '%')
                    ->orWhere('nama_alkes', 'like', '%' . $request->search . '%')
                    ->orWhere('sn', 'like', '%' . $request->search . '%')
                    ->orWhere('lokasi', 'like', '%' . $request->search . '%');
            });
        })
        // 4. Filter Spesifik dari Dropdown
        ->when($request->nama_rs, fn($q, $rs) => $q->where('nama_rs', $rs))
        ->when($request->nama_alkes, fn($q, $alkes) => $q->where('nama_alkes', $alkes))
        ->when($request->kategori, fn($q, $kat) => $q->where('kategori', $kat))
        ->when($request->grade_kerusakan, fn($q, $grade) => $q->where('grade_kerusakan', $grade))
        ->when($request->status_perbaikan, fn($q, $status) => $q->where('status_perbaikan', $status))
        ->when($request->respon_penyedia, fn($q, $respon) => $q->where('respon_penyedia', $respon));

        // 5. Eksekusi Query dengan Pagination dan simpan query string filter
        $repairs = $query->latest()->paginate(15)->withQueryString();

        // 6. Kirim semua variabel list ke View
        return view('repairs.index', compact(
            'repairs', 
            'list_rs', 
            'list_alkes', 
            'list_kategori', 
            'list_grade', 
            'list_status', 
            'list_respon'
        ));
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
        
        // Simpan data lama untuk perbandingan di history
        $komponenLama = $repair->komponen;
        $statusLama = $repair->status_perbaikan;

        // 1. Update Semua Data (Identitas Unit + Status + Progres)
        $repair->nama_rs = $request->nama_rs;
        $repair->lokasi = $request->lokasi;
        $repair->nama_alkes = $request->nama_alkes;
        $repair->merek = $request->merek;
        $repair->tipe_model = $request->tipe_model;
        $repair->serial_number = $request->serial_number;
        
        $repair->input_by = $request->input_by;
        $repair->kategori = $request->kategori;
        $repair->kondisi_kontrak = $request->kondisi_kontrak;
        $repair->grade_kerusakan = $request->grade_kerusakan;
        $repair->status_perbaikan = $request->status_perbaikan;
        
        $repair->nama_penyedia = $request->nama_penyedia;
        $repair->komponen = $request->komponen;
        $repair->respon_penyedia = $request->respon_penyedia;
        $repair->tindakan_penyedia = $request->tindakan_penyedia;
        $repair->rtl = $request->rtl;
        $repair->keterangan_lain = $request->keterangan_lain;

        // 2. Logika File BAP (Replace/Ganti file lama jika ada upload baru)
        if ($request->hasFile('file_bap')) {
            if ($repair->file_bap) { 
                \Storage::disk('public')->delete($repair->file_bap); 
            }
            $repair->file_bap = $request->file('file_bap')->store('bap', 'public');
        }

        // 3. Logika Foto Kondisi (Append/Tambah foto baru ke daftar lama)
        if ($request->hasFile('foto_kondisi')) {
            $currentPhotos = is_array($repair->foto_kondisi) ? $repair->foto_kondisi : []; 
            foreach ($request->file('foto_kondisi') as $file) {
                $currentPhotos[] = $file->store('repairs', 'public');
            }
            $repair->foto_kondisi = $currentPhotos;
        }

        $repair->save();

        // 4. Buat Pesan History yang Informatif
        // Menggunakan 'keterangan_log' dari textarea modal sebagai pesan utama
        $pesanHistory = $request->keterangan_log ?? 'Pembaruan data unit dan progres.';

        if ($komponenLama != $request->komponen) {
            $pesanHistory .= " (Komponen: " . ($request->komponen ?? '-') . ")";
        }
        
        if ($statusLama != $request->status_perbaikan) {
            $pesanHistory .= " [Status berubah menjadi: " . $request->status_perbaikan . "]";
        }

        $repair->histories()->create([
            'status_perbaikan' => $request->status_perbaikan,
            'keterangan_perubahan' => $pesanHistory,
            'user_nama' => $request->petugas,
        ]);

        return redirect()->back()->with('success', 'Seluruh data unit dan progres perbaikan berhasil diperbarui!');
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
        // Ambil semua data unik untuk dropdown filter
        $list_rs = Repair::whereNotNull('nama_rs')->distinct()->orderBy('nama_rs', 'asc')->pluck('nama_rs');
        $list_alkes = Repair::whereNotNull('nama_alkes')->distinct()->orderBy('nama_alkes', 'asc')->pluck('nama_alkes');
        $list_kategori = Repair::whereNotNull('kategori')->distinct()->orderBy('kategori', 'asc')->pluck('kategori');
        $list_grade = Repair::whereNotNull('grade_kerusakan')->distinct()->orderBy('grade_kerusakan', 'asc')->pluck('grade_kerusakan');
        $list_status = Repair::whereNotNull('status_perbaikan')->distinct()->orderBy('status_perbaikan', 'asc')->pluck('status_perbaikan');
        $list_respon = Repair::whereNotNull('respon_penyedia')->distinct()->orderBy('respon_penyedia', 'asc')->pluck('respon_penyedia');
        $repairs = Repair::latest()->get();

        return view('repairs.report', compact(
            'list_rs', 'list_kategori', 'list_alkes', 'list_grade', 'list_status', 'list_respon', 'repairs'
        ));
    }

    // Method untuk filter AJAX
    public function previewExport(Request $request)
    {
        $query = Repair::query();

        // Gunakan when() agar kode lebih bersih
        $query->when($request->nama_rs, function ($q) use ($request) {
            return $q->where('nama_rs', 'like', '%' . $request->nama_rs . '%');
        })
        ->when($request->nama_alkes, function ($q) use ($request) {
            return $q->where('nama_alkes', 'like', '%' . $request->nama_alkes . '%');
        })
        ->when($request->kategori, function ($q) use ($request) {
            return $q->where('kategori', $request->kategori);
        })
        ->when($request->status_perbaikan, function ($q) use ($request) {
            return $q->where('status_perbaikan', $request->status_perbaikan);
        })
        ->when($request->grade_kerusakan, function ($q) use ($request) {
            return $q->where('grade_kerusakan', $request->grade_kerusakan);
        })
        ->when($request->respon_penyedia, function ($q) use ($request) {
            return $q->where('respon_penyedia', $request->respon_penyedia);
        });

        $repairs = $query->latest()->get();

        return view('repairs._report_rows', compact('repairs'))->render();
    }
}