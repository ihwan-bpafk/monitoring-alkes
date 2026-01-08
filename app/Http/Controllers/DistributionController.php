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
    public function index(Request $request)
    {
        // 1. Ambil daftar unik dari database
        $list_rs = Distribution::distinct()->orderBy('nama_rs')->pluck('nama_rs');
        
        // MENGAMBIL STATUS LANGSUNG DARI DB
        $list_status = Distribution::distinct()
            ->whereNotNull('status')
            ->orderBy('status')
            ->pluck('status');
        
        // Ambil nama alkes unik dari relasi
        $list_alkes = Donation::whereHas('distributions')
            ->distinct()
            ->orderBy('nama_alkes')
            ->pluck('nama_alkes');

        // 2. Query Utama dengan Filter (Tetap Sama)
        $query = Distribution::with('donation');

        $query->when($request->filter_rs, function ($q, $rs) {
            return $q->where('nama_rs', $rs);
        });

        $query->when($request->filter_status, function ($q, $st) {
            return $q->where('status', $st);
        });

        $query->when($request->filter_alkes, function ($q, $alkes) {
            return $q->whereHas('donation', function ($sub) use ($alkes) {
                $sub->where('nama_alkes', $alkes);
            });
        });

        // 3. Eksekusi
        $distributions = $query->latest()->paginate(10)->withQueryString();
        $donations_available = Donation::where('sisa_stok', '>', 0)->get();

        return view('distributions.index', compact(
            'distributions', 
            'donations_available', 
            'list_rs', 
            'list_status', 
            'list_alkes'
        ));
    }

    /**
     * Menyimpan alokasi distribusi baru dan mengurangi sisa stok donasi.
     */
    public function store(Request $request)
    {
        // Validasi Input
        if (auth()->user()->role !== 1 || auth()->user()->role !== 2) {
            return redirect()->back()->with('error', 'tidak memiliki akses!');
        }
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
        if (auth()->user()->role !== 1 || auth()->user()->role !== 2) {
            return redirect()->back()->with('error', 'tidak memiliki akses!');
        }
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
        if (auth()->user()->role !== 1 || auth()->user()->role !== 2) {
            return redirect()->back()->with('error', 'tidak memiliki akses!');
        }
        $distribution = Distribution::findOrFail($id);
        $distribution->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Status distribusi berhasil diperbarui.');
    }
}