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
        if (auth()->user()->role !== 1 && auth()->user()->role !== 2) {
            return redirect()->back()->with('error', 'tidak memiliki akses!');
        }
        $request->validate([
            'nama_alkes' => 'required',
            'jumlah_donasi' => 'required|integer',
        ]);

        DB::transaction(function () use ($request) {
            // 1. Simpan Data Donasi Utama
            $donation = Donation::create([
                'nama_alkes'    => $request->nama_alkes,
                'merek'         => $request->merek,
                'jumlah_donasi' => $request->jumlah_donasi,
                'diterima_oleh' => $request->diterima_oleh,
                'sisa_stok'     => $request->jumlah_donasi, // Awalnya sisa stok = jumlah donasi
                'status_akhir'  => 'Masuk Gudang BPAFK',
            ]);

            // 2. Catat riwayat pertama (Tracking)
            DonationLog::create([
                'donation_id'   => $donation->id,
                'status'        => 'Masuk Gudang BPAFK',
                'diupdate_oleh' => auth()->user()->name,
                'catatan'       => 'Penerimaan awal alat kesehatan.',
            ]);
        });

        return redirect()->back()->with('success', 'Data Donasi berhasil diinput dengan tracking.');
    }

    // Fungsi khusus untuk Update Status Saja (Tracking Lokasi)
    public function updateStatus(Request $request, $id)
    {
        if (auth()->user()->role !== 1 && auth()->user()->role !== 2) {
            return redirect()->back()->with('error', 'tidak memiliki akses!');
        }
        $donation = Donation::findOrFail($id);

        DB::transaction(function () use ($request, $donation) {
            // Update status di tabel utama
            $donation->update(['status_akhir' => $request->status_baru]);

            // Tambah baris baru di tabel history
            DonationLog::create([
                'donation_id'   => $donation->id,
                'status'        => $request->status_baru,
                'diupdate_oleh' => auth()->user()->name,
                'catatan'       => $request->catatan_update,
            ]);
        });

        return redirect()->back()->with('success', 'Status posisi alat berhasil diperbarui.');
    }

    public function destroy($id)
    {
        if (auth()->user()->role !== 1 && auth()->user()->role !== 2) {
            return redirect()->back()->with('error', 'tidak memiliki akses!');
        }
        Donation::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Data Donasi telah dihapus.');
    }
}