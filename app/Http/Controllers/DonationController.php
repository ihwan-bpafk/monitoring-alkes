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
        $data = $request->validate([
            'input_by' => 'required',
            'nama_alkes' => 'required',
            'nama_rs' => 'required',
            'merek' => 'nullable',
            'tipe_model' => 'nullable',
            'jumlah' => 'required|integer',
            'donatur' => 'required',
            'keterangan_lain' => 'nullable',
            'file_donasi' => 'nullable|file|mimes:pdf,jpg,png|max:5120'
        ]);

        if ($request->hasFile('file_donasi')) {
            $data['file_donasi'] = $request->file('file_donasi')->store('donations', 'public');
        }

        Donation::create($data);

        return redirect()->back()->with('success', 'Data Donasi berhasil disimpan!');
    }

    public function update(Request $request, Donation $donation)
    {
        $data = $request->all();

        if ($request->hasFile('file_donasi')) {
            // Hapus file lama jika ada
            if ($donation->file_donasi) {
                Storage::disk('public')->delete($donation->file_donasi);
            }
            $data['file_donasi'] = $request->file('file_donasi')->store('donations', 'public');
        }

        $donation->update($data);

        return redirect()->back()->with('success', 'Data Donasi berhasil diperbarui!');
    }

    public function destroy(Donation $donation)
    {
        if ($donation->file_donasi) {
            Storage::disk('public')->delete($donation->file_donasi);
        }
        $donation->delete();
        return redirect()->back()->with('success', 'Data Donasi berhasil dihapus!');
    }
}