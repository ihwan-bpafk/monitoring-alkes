<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Repair;

class DonationController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil daftar unik untuk isi filter dropdown (Pivot/Distinct)
        // Diambil dari data yang sudah ada di tabel donations
        $list_pemberi = Donation::distinct()->orderBy('pemberi_donasi')->pluck('pemberi_donasi');
        $list_alkes_donasi = Donation::distinct()->orderBy('nama_alkes')->pluck('nama_alkes');
        $list_penerima = Donation::distinct()->orderBy('diterima_oleh')->pluck('diterima_oleh');

        // Ambil dari master repair untuk modal tambah donasi
        $list_alkes_master = Repair::distinct()
            ->orderBy('nama_alkes', 'asc')
            ->pluck('nama_alkes');

        // 2. Query utama dengan sistem Filter dinamis
        $query = Donation::with('distributions');

        // Filter berdasarkan Pemberi Donasi (Sudah disinkronkan dengan View)
        $query->when($request->filter_pemberi, function ($q, $pemberi) {
            return $q->where('pemberi_donasi', $pemberi);
        });

        // Filter berdasarkan Nama Alat
        $query->when($request->filter_alkes, function ($q, $alkes) {
            return $q->where('nama_alkes', $alkes);
        });

        // Filter berdasarkan Petugas Penerima
        $query->when($request->filter_petugas, function ($q, $petugas) {
            return $q->where('diterima_oleh', $petugas);
        });

        // Filter berdasarkan Status Stok (Tersedia / Habis)
        $query->when($request->filter_stok, function ($q, $v) {
            if ($v == 'tersedia') {
                return $q->where('sisa_stok', '>', 0);
            } elseif ($v == 'habis') {
                return $q->where('sisa_stok', '<=', 0);
            }
        });

        // 3. Eksekusi Query dengan Pagination dan menjaga parameter URL
        $donations = $query->orderBy('nama_alkes', 'asc')
                            ->paginate(10)
                            ->withQueryString(); // Menjaga filter tetap aktif saat pindah halaman

        // 4. Return ke View dengan data yang dibutuhkan
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
        if (auth()->user()->role !== 1 && auth()->user()->role !== 2) {
            return redirect()->back()->with('error', 'tidak memiliki akses!');
        }
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
        if (auth()->user()->role !== 1 && auth()->user()->role !== 2) {
            return redirect()->back()->with('error', 'Tidak memiliki akses!');
        }

        $donation = Donation::findOrFail($id);

        // Validasi: Jumlah baru tidak boleh lebih kecil dari yang sudah keluar (distribusi)
        $sudah_distribusi = $donation->jumlah_donasi - $donation->sisa_stok;
        
        $request->validate([
            'jumlah_donasi' => 'required|integer|min:' . $sudah_distribusi,
        ], [
            'jumlah_donasi.min' => 'Gagal! Alat sudah terdistribusi ' . $sudah_distribusi . ' unit, jumlah total tidak boleh kurang dari itu.'
        ]);

        \DB::transaction(function () use ($request, $donation, $sudah_distribusi) {
            $status_baru = $request->status_akhir ?: '-';
            $jumlah_baru = $request->jumlah_donasi;

            // Hitung ulang sisa stok berdasarkan input baru
            $sisa_stok_baru = $jumlah_baru - $sudah_distribusi;

            $donation->update([
                'jumlah_donasi' => $jumlah_baru,
                'sisa_stok'     => $sisa_stok_baru,
                'status_akhir'  => $status_baru
            ]);

            \App\Models\DonationLog::create([
                'donation_id'   => $donation->id,
                'status'        => 'Update Data: ' . $status_baru,
                'diupdate_oleh' => auth()->user()->name,
                'catatan'       => $request->catatan ?? "Update jumlah menjadi {$jumlah_baru} unit dan status menjadi {$status_baru}.",
            ]);
        });

        return redirect()->back()->with('success', 'Data donasi dan stok berhasil diperbarui.');
    }

    public function destroy($id)
    {
        if (auth()->user()->role !== 1 && auth()->user()->role !== 2) {
            return redirect()->back()->with('error', 'tidak memiliki akses!');
        }
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