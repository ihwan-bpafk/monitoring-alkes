<?php

namespace App\Http\Controllers;

use App\Models\Repair;
use App\Models\RepairHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RepairController extends Controller
{
    public function index()
    {
        // Load data dengan history terbaru di atas (Eager Loading)
        $repairs = Repair::with(['histories' => function($q) {
            $q->latest();
        }])->latest()->get();

        return view('repairs.index', compact('repairs'));
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
        
        // Data dasar yang diupdate
        $repair->status_perbaikan = $request->status_perbaikan;
        $repair->rtl = $request->rtl;
        $repair->komponen = $request->Komponen; // Sesuai name="Komponen" di view
        $repair->kondisi_kontrak = $request->kondisi_kontrak;
        $repair->tindakan_penyedia = $request->tindakan_penyedia;

        // 1. Update/Ganti File BAP
        if ($request->hasFile('file_bap')) {
            if ($repair->file_bap) {
                Storage::disk('public')->delete($repair->file_bap);
            }
            $repair->file_bap = $request->file('file_bap')->store('bap', 'public');
        }

        // 2. Tambah Foto Baru ke dalam Array Foto Lama (Multiple)
        if ($request->hasFile('foto_kondisi')) {
            $currentPhotos = $repair->foto_kondisi ?? []; 
            foreach ($request->file('foto_kondisi') as $file) {
                $currentPhotos[] = $file->store('repairs', 'public');
            }
            $repair->foto_kondisi = $currentPhotos;
        }

        $repair->save();

        // 3. Catat History Perubahan
        $repair->histories()->create([
            'status_perbaikan' => $request->status_perbaikan,
            'keterangan_perubahan' => $request->keterangan_log,
            'user_nama' => $request->petugas,
        ]);

        return redirect()->back()->with('success', 'Progress berhasil diperbarui!');
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
}