<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DonationController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil data unik untuk filter
        $list_rs = Donation::distinct()->orderBy('nama_rs')->pluck('nama_rs');
        $list_donatur = Donation::distinct()->orderBy('donatur')->pluck('donatur');
        $list_alkes_donasi = Donation::distinct()->orderBy('nama_alkes')->pluck('nama_alkes');

        // Ambil data alkes dari tabel repairs untuk modal input (seperti sebelumnya)
        $list_alkes = \App\Models\Repair::distinct()->orderBy('nama_alkes')->pluck('nama_alkes');

        // 2. Query dengan Filter
        $query = Donation::query();

        if ($request->filter_rs) {
            $query->where('nama_rs', $request->filter_rs);
        }
        if ($request->filter_donatur) {
            $query->where('donatur', $request->filter_donatur);
        }
        if ($request->filter_alkes) {
            $query->where('nama_alkes', $request->filter_alkes);
        }

        $donations = $query->latest()->paginate(15)->withQueryString();

        return view('donations.index', compact(
            'donations', 'list_rs', 'list_donatur', 'list_alkes_donasi', 'list_alkes'
        ));
    }

    // Jangan lupa update store & update untuk menerima 'tanggal_diterima'
    public function store(Request $request) {
        $data = $request->only(['input_by', 'nama_rs', 'nama_alkes', 'merek', 'tipe_model', 'jumlah', 'donatur', 'keterangan_lain', 'tanggal_diterima']);
        if ($request->hasFile('file_donasi')) {
            $data['file_donasi'] = $request->file('file_donasi')->store('donations', 'public');
        }
        Donation::create($data);
        return redirect()->back()->with('success', 'Data berhasil disimpan');
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