<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use Illuminate\Http\Request;

class DonationController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil daftar unik untuk isi filter dropdown
        $list_donatur = Donation::distinct()->orderBy('pemberi_donasi')->pluck('pemberi_donasi');
        $list_alkes_donasi = Donation::distinct()->orderBy('nama_alkes')->pluck('nama_alkes');
        $list_petugas = Donation::distinct()->orderBy('diterima_oleh')->pluck('diterima_oleh');

        // 2. Query utama dengan filter
        $query = Donation::query();

        $query->when($request->filter_donatur, function ($q, $donatur) {
            return $q->where('pemberi_donasi', $donatur);
        });

        $query->when($request->filter_alkes, function ($q, $alkes) {
            return $q->where('nama_alkes', $alkes);
        });

        $query->when($request->filter_petugas, function ($q, $petugas) {
            return $q->where('diterima_oleh', $petugas);
        });

        // 3. Paginate dengan tetap membawa parameter filter di URL
        $donations = $query->orderBy('nama_alkes', 'asc')
                        ->paginate(10)
                        ->withQueryString();

        return view('donations.index', compact(
            'donations', 
            'list_donatur', 
            'list_alkes_donasi', 
            'list_petugas'
        ));
    }

    public function store(Request $request)
    {
        if (auth()->user()->role !== 1 || auth()->user()->role !== 2) {
            return redirect()->back()->with('error', 'tidak memiliki akses!');
        }
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
        if (auth()->user()->role !== 1 || auth()->user()->role !== 2) {
            return redirect()->back()->with('error', 'tidak memiliki akses!');
        }
        Donation::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Data Donasi telah dihapus.');
    }
}