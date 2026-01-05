<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DonationController extends Controller
{
    public function index()
    {
        $donations = Donation::latest()->paginate(10);
        return view('donations.index', compact('donations'));
    }

    public function store(Request $request)
    {
        // 1. Ambil hanya kolom yang benar-benar ada di tabel donations
        $data = $request->only([
            'input_by', 
            'nama_rs', 
            'nama_alkes', 
            'merek', 
            'tipe_model', 
            'jumlah', 
            'donatur', 
            'keterangan_lain'
        ]);

        // 2. Proses File secara terpisah
        if ($request->hasFile('file_donasi')) {
            $file = $request->file('file_donasi');
            if ($file->isValid()) {
                // Pastikan hasilnya disimpan ke KEY 'file_donasi'
                $data['file_donasi'] = $file->store('donations', 'public');
            }
        }

        // 3. Simpan
        Donation::create($data);

        return redirect()->back()->with('success', 'Data Donasi berhasil disimpan!');
    }

    public function update(Request $request, $id)
    {
        $donation = Donation::findOrFail($id);
        
        // Ambil hanya kolom database
        $data = $request->only([
            'input_by', 'nama_rs', 'nama_alkes', 'merek', 
            'tipe_model', 'jumlah', 'donatur', 'keterangan_lain'
        ]);

        if ($request->hasFile('file_donasi')) {
            $file = $request->file('file_donasi');
            if ($file->isValid()) {
                // Hapus file lama jika ada
                if ($donation->file_donasi && Storage::disk('public')->exists($donation->file_donasi)) {
                    Storage::disk('public')->delete($donation->file_donasi);
                }
                $data['file_donasi'] = $file->store('donations', 'public');
            }
        }

        $donation->update($data);

        return redirect()->back()->with('success', 'Data Donasi diperbarui!');
    }

    public function destroy($id)
    {
        $donation = Donation::findOrFail($id);
        
        if ($donation->file_donasi && Storage::disk('public')->exists($donation->file_donasi)) {
            Storage::disk('public')->delete($donation->file_donasi);
        }
        
        $donation->delete();

        return redirect()->back()->with('success', 'Data Donasi berhasil dihapus!');
    }
}