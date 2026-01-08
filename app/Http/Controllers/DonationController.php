<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use Illuminate\Http\Request;

class DonationController extends Controller
{
    public function index(Request $request)
    {
        $query = Donation::query();

        // Fitur Pencarian Sederhana
        if ($request->search) {
            $query->where('pemberi_donasi', 'like', '%' . $request->search . '%')
                  ->orWhere('nama_alkes', 'like', '%' . $request->search . '%');
        }

        // Urutkan berdasarkan Nama Alat A-Z
        $donations = $query->orderBy('nama_alkes', 'asc')->paginate(10)->withQueryString();

        return view('donations.index', compact('donations'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'pemberi_donasi' => 'required|string',
            'nama_alkes'     => 'required|string',
            'merek'          => 'nullable|string',
            'jumlah_donasi'  => 'required|integer|min:1',
            'diterima_oleh'  => 'required|string',
            'tanggal_masuk'  => 'required|date',
        ]);

        // Logika: Sisa stok awal = Jumlah donasi
        $data['sisa_stok'] = $request->jumlah_donasi;

        Donation::create($data);

        return redirect()->back()->with('success', 'Data Donasi berhasil dicatat!');
    }

    public function destroy($id)
    {
        Donation::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Data Donasi telah dihapus.');
    }
}