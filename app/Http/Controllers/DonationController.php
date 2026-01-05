<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DonationController extends Controller
{
    public function index()
    {
        // Menampilkan data terbaru dengan paginasi
        $donations = Donation::latest()->paginate(10);
        return view('donations.index', compact('donations'));
    }

    public function store(Request $request)
    {
        // 1. Ambil data kecuali token dan file agar tidak masuk ke query SQL
        $data = $request->except(['_token', 'file_donasi']);

        // 2. Proses Upload File
        if ($request->hasFile('file_donasi')) {
            $file = $request->file('file_donasi');
            if ($file->isValid()) {
                // Simpan dan masukkan path ke key 'file_donasi'
                $data['file_donasi'] = $file->store('donations', 'public');
            }
        }

        // 3. Simpan ke Database
        Donation::create($data);

        return redirect()->back()->with('success', 'Data Donasi berhasil disimpan!');
    }

    public function update(Request $request, $id)
    {
        $donation = Donation::findOrFail($id);
        
        // 1. Ambil data kecuali yang tidak perlu
        $data = $request->except(['_token', '_method', 'file_donasi']);

        // 2. Proses Update File
        if ($request->hasFile('file_donasi')) {
            $file = $request->file('file_donasi');
            if ($file->isValid()) {
                // Hapus file lama jika ada di storage
                if ($donation->file_donasi && Storage::disk('public')->exists($donation->file_donasi)) {
                    Storage::disk('public')->delete($donation->file_donasi);
                }
                // Simpan file baru
                $data['file_donasi'] = $file->store('donations', 'public');
            }
        }

        // 3. Update Database
        $donation->update($data);

        return redirect()->back()->with('success', 'Data Donasi diperbarui!');
    }

    public function destroy($id)
    {
        $donation = Donation::findOrFail($id);
        
        // 1. Hapus file fisik dari storage agar tidak memenuhi server
        if ($donation->file_donasi && Storage::disk('public')->exists($donation->file_donasi)) {
            Storage::disk('public')->delete($donation->file_donasi);
        }
        
        // 2. Hapus data dari database
        $donation->delete();

        return redirect()->back()->with('success', 'Data Donasi berhasil dihapus!');
    }
}