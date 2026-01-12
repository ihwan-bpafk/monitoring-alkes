<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DonationController extends Controller
{
    public function index(Request $request)
    {
        $list_alkes_master = \App\Models\Repair::distinct()
            ->orderBy('nama_alkes', 'asc')
            ->pluck('nama_alkes');
        // 1. Ambil daftar unik untuk isi filter dropdown
        $list_pemberi = Donation::distinct()->orderBy('pemberi_donasi')->pluck('pemberi_donasi');
        $list_alkes_donasi = Donation::distinct()->orderBy('nama_alkes')->pluck('nama_alkes');
        $list_penerima = Donation::distinct()->orderBy('diterima_oleh')->pluck('diterima_oleh');

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
                        
        $query->when($request->filter_stok, function ($q, $v) {
            if ($v == 'tersedia') {
                return $q->where('sisa_stok', '>', 0);
            } elseif ($v == 'habis') {
                return $q->where('sisa_stok', '<=', 0);
            }
        });
        $donations = $query->orderBy('nama_alkes', 'asc')
                        ->paginate(10)
                        ->withQueryString();

        return view('donations.index', compact(
            'donations', 
            'list_pemberi', 
            'list_alkes_donasi', 
            'list_penerima',
            'list_alkes_master'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pemberi_donasi' => 'required|string',
            'nama_alkes'     => 'required|string',
            'jumlah_donasi'  => 'required|integer|min:1',
        ]);

        \DB::transaction(function () use ($request) {
            // Logika: Jika status_akhir kosong, maka diisi '-'
            $status_awal = $request->status_akhir ?: '-';

            $donation = Donation::create([
                'pemberi_donasi' => $request->pemberi_donasi,
                'nama_alkes'     => $request->nama_alkes,
                'merek'          => $request->merek,
                'jumlah_donasi'  => $request->jumlah_donasi,
                'diterima_oleh'  => $request->diterima_oleh,
                'sisa_stok'      => $request->jumlah_donasi,
                'status_akhir'   => $status_awal,
            ]);

            // Catat Log Pertama
            \App\Models\DonationLog::create([
                'donation_id'   => $donation->id,
                'status'        => $status_awal,
                'diupdate_oleh' => auth()->user()->name,
                'catatan'       => 'Penerimaan awal data donasi.',
            ]);
        });

        return redirect()->back()->with('success', 'Data Donasi berhasil disimpan!');
    }

    // Fungsi khusus untuk Update Status Saja (Tracking Lokasi)
    public function updateStatus(Request $request, $id)
    {
        $donation = Donation::findOrFail($id);

        \DB::transaction(function () use ($request, $donation) {
            // Gunakan logika yang sama: input atau '-'
            $status_baru = $request->status_akhir ?: '-';

            $donation->update([
                'status_akhir' => $status_baru
            ]);

            \App\Models\DonationLog::create([
                'donation_id'   => $donation->id,
                'status'        => $status_baru,
                'diupdate_oleh' => auth()->user()->name,
                'catatan'       => $request->catatan ?? 'Perubahan status/posisi alat.',
            ]);
        });

        return redirect()->back()->with('success', 'Status akhir berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $donation = Donation::findOrFail($id);

        // Gunakan Transaction agar jika satu gagal, semua batal (aman)
        DB::transaction(function () use ($donation) {
            // 1. Hapus semua file Berita Acara di distribusi terkait
            foreach ($donation->distributions as $dist) {
                if ($dist->file_ba && file_exists(public_path('uploads/berita_acara/' . $dist->file_ba))) {
                    unlink(public_path('uploads/berita_acara/' . $dist->file_ba));
                }
            }

            // 2. Hapus data distribusi terkait
            $donation->distributions()->delete();

            // 3. Hapus semua log riwayat terkait
            $donation->logs()->delete();

            // 4. Hapus data donasi utama
            $donation->delete();
        });

        return redirect()->route('donations.index')->with('success', 'Master Donasi dan seluruh riwayat distribusinya berhasil dihapus.');
    }

    public function exportExcel(Request $request) 
    {
        $filters = $request->only(['filter_pemberi', 'filter_alkes', 'filter_petugas', 'filter_stok']);
        $fileName = 'Laporan_Donasi_BPAFK_' . now()->format('Ymd_His') . '.xlsx';

        return (new \App\Exports\DonationsExport($filters))->download($fileName);
    }
}