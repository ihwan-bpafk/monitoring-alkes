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
        $data = $request->all();

        if ($request->hasFile('file_donasi')) {
            // Simpan file ke folder 'donations' di disk 'public'
            $data['file_donasi'] = $request->file('file_donasi')->store('donations', 'public');
        }

        \App\Models\Donation::create($data);

        return redirect()->back()->with('success', 'Data Donasi berhasil disimpan!');
    }

    public function update(Request $request, $id)
    {
        $donation = \App\Models\Donation::findOrFail($id);
        $data = $request->all();

        if ($request->hasFile('file_donasi')) {
            // Hapus file lama jika ada
            if ($donation->file_donasi) {
                \Storage::disk('public')->delete($donation->file_donasi);
            }
            $data['file_donasi'] = $request->file('file_donasi')->store('donations', 'public');
        }

        $donation->update($data);

        return redirect()->back()->with('success', 'Data Donasi diperbarui!');
    }

    public function destroy($id)
    {
        $donation = \App\Models\Donation::findOrFail($id);
        
        // Hapus file fisik
        if ($donation->file_donasi) {
            \Storage::disk('public')->delete($donation->file_donasi);
        }
        
        $donation->delete();

        return redirect()->back()->with('success', 'Data Donasi berhasil dihapus!');
    }
}