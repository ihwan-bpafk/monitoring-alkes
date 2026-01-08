<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\Distribution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DistributionController extends Controller
{
    /**
     * Menampilkan daftar distribusi dan alat yang tersedia untuk dialokasikan.
     */
    public function index()
    {
        // 1. Ambil semua riwayat distribusi, urutkan dari yang terbaru (Eager Loading relasi donation)
        $distributions = Distribution::with('donation')
            ->latest()
            ->paginate(10);

        // 2. Ambil daftar donasi yang masih memiliki stok (untuk dropdown di Modal)
        $donations_available = Donation::where('sisa_stok', '>', 0)
            ->orderBy('nama_alkes', 'asc')
            ->get();

        return view('distributions.index', compact('distributions', 'donations_available'));
    }

    /**
     * Menyimpan alokasi distribusi baru dan mengurangi sisa stok donasi.
     */
    public function store(Request $request)
    {
        // Validasi Input
        $request->validate([
            'donation_id'        => 'required|exists:donations,id',
            'nama_rs'            => 'required|string|max:255',
            'jumlah_distribusi'  => 'required|integer|min:1',
            'tanggal_distribusi' => 'required|date',
            'petugas_pengirim'   => 'required|string',
        ]);

        // Ambil data donasi asal
        $donation = Donation::findOrFail($request->donation_id);

        // Cek apakah stok mencukupi
        if ($donation->sisa_stok < $request->jumlah_distribusi) {
            return redirect()->back()
                ->with('error', "Gagal! Stok {$donation->nama_alkes} tidak mencukupi. (Sisa: {$donation->sisa_stok})");
        }

        // Gunakan Transaction agar jika salah satu gagal, semua dibatalkan
        DB::transaction(function () use ($request, $donation) {
            // 1. Buat record distribusi
            Distribution::create([
                'donation_id'        => $request->donation_id,
                'nama_rs'            => $request->nama_rs,
                'jumlah_distribusi'  => $request->jumlah_distribusi,
                'tanggal_distribusi' => $request->tanggal_distribusi,
                'petugas_pengirim'   => $request->petugas_pengirim,
                'status'             => 'Dikirim', // Status default
                'keterangan'         => $request->keterangan,
            ]);

            // 2. Kurangi sisa stok di tabel donations
            $donation->decrement('sisa_stok', $request->jumlah_distribusi);
        });

        return redirect()->back()->with('success', 'Distribusi berhasil diproses dan stok telah berkurang.');
    }

    /**
     * Menghapus riwayat distribusi dan mengembalikan stok ke tabel donasi (Restok).
     */
    public function destroy($id)
    {
        $distribution = Distribution::findOrFail($id);
        $donation     = Donation::findOrFail($distribution->donation_id);

        DB::transaction(function () use ($distribution, $donation) {
            // 1. Kembalikan stok ke tabel donasi
            $donation->increment('sisa_stok', $distribution->jumlah_distribusi);

            // 2. Hapus data distribusi
            $distribution->delete();
        });

        return redirect()->back()->with('success', 'Data distribusi dihapus dan stok telah dikembalikan ke gudang.');
    }

    /**
     * Update Status Distribusi (Contoh: dari 'Dikirim' menjadi 'Diterima')
     */
    public function updateStatus(Request $request, $id)
    {
        $distribution = Distribution::findOrFail($id);
        $distribution->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Status distribusi berhasil diperbarui.');
    }
}