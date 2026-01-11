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
        // 1. Data untuk Filter & Dropdown (Dinamis dari DB)
        $list_rs_master = \App\Models\Repair::whereNotNull('nama_rs')->distinct()->orderBy('nama_rs')->pluck('nama_rs');
        $list_alkes_dist = Donation::whereHas('distributions')->distinct()->orderBy('nama_alkes')->pluck('nama_alkes', 'id');
        $list_status = Distribution::distinct()->pluck('status');

        // 2. Query Utama dengan Filter
        $query = Distribution::with('donation');

        $query->when($request->filter_rs, fn($q, $v) => $q->where('nama_rs', $v));
        $query->when($request->filter_alkes, fn($q, $v) => $q->where('donation_id', $v));
        $query->when($request->filter_status, fn($q, $v) => $q->where('status', $v));

        $distributions = $query->latest()->paginate(10)->withQueryString();
        
        // Untuk Modal Tambah
        $availableDonations = Donation::where('sisa_stok', '>', 0)->get();

        return view('distributions.index', compact(
            'distributions', 'availableDonations', 'list_rs_master', 'list_alkes_dist', 'list_status'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'donation_id' => 'required',
            'nama_rs' => 'required',
            'jumlah_distribusi' => 'required|integer|min:1',
            'file_ba' => 'nullable|mimes:pdf,jpg,png,jpeg|max:2048', // Validasi file
        ]);

        $donation = Donation::findOrFail($request->donation_id);

        if ($donation->sisa_stok < $request->jumlah_distribusi) {
            return redirect()->back()->with('error', 'Stok tidak mencukupi!');
        }

        DB::transaction(function () use ($request, $donation) {
            // Handle Upload File
            $fileName = null;
            if ($request->hasFile('file_ba')) {
                $fileName = time() . '_' . $request->file('file_ba')->getClientOriginalName();
                $request->file('file_ba')->move(public_path('uploads/berita_acara'), $fileName);
            }

            Distribution::create([
                'donation_id' => $request->donation_id,
                'nama_rs' => $request->nama_rs,
                'jumlah_distribusi' => $request->jumlah_distribusi,
                'tanggal_distribusi' => $request->tanggal_distribusi,
                'status' => 'Dikirim',
                'petugas_pengirim' => auth()->user()->name,
                'file_ba' => $fileName,
                'keterangan' => $request->keterangan,
            ]);

            $donation->decrement('sisa_stok', $request->jumlah_distribusi);
            
            // Log Tracking
            \App\Models\DonationLog::create([
                'donation_id' => $donation->id,
                'status' => 'Distribusi',
                'diupdate_oleh' => auth()->user()->name,
                'catatan' => "Kirim {$request->jumlah_distribusi} unit ke {$request->nama_rs}",
            ]);
        });

        return redirect()->back()->with('success', 'Distribusi berhasil diproses!');
    }

    // --- FUNGSI UPDATE ---
    public function update(Request $request, $id)
    {
        $request->validate([
            'jumlah_distribusi' => 'required|integer|min:1',
            'nama_rs' => 'required',
            'file_ba' => 'nullable|mimes:pdf,jpg,png,jpeg|max:2048',
        ]);

        $dist = Distribution::findOrFail($id);
        $donation = Donation::findOrFail($dist->donation_id);

        // Hitung selisih stok
        // Jika jumlah baru 10, jumlah lama 5, maka stok gudang harus dikurangi lagi 5
        // Jika jumlah baru 3, jumlah lama 5, maka stok gudang harus ditambah lagi 2
        $selisih = $dist->jumlah_distribusi - $request->jumlah_distribusi;

        // Validasi jika stok gudang tidak cukup saat jumlah distribusi ditambah
        if (($donation->sisa_stok + $selisih) < 0) {
            return redirect()->back()->with('error', 'Gagal! Stok di gudang tidak mencukupi untuk perubahan ini.');
        }

        DB::transaction(function () use ($request, $dist, $donation, $selisih) {
            // Handle File BA baru jika ada
            if ($request->hasFile('file_ba')) {
                if ($dist->file_ba && file_exists(public_path('uploads/berita_acara/' . $dist->file_ba))) {
                    unlink(public_path('uploads/berita_acara/' . $dist->file_ba));
                }
                $fileName = time() . '_' . $request->file('file_ba')->getClientOriginalName();
                $request->file('file_ba')->move(public_path('uploads/berita_acara'), $fileName);
                $dist->file_ba = $fileName;
            }

            // Update data distribusi
            $dist->update([
                'nama_rs' => $request->nama_rs,
                'jumlah_distribusi' => $request->jumlah_distribusi,
                'tanggal_distribusi' => $request->tanggal_distribusi,
                'keterangan' => $request->keterangan,
            ]);

            // Sinkronkan Stok Donasi (Gunakan increment dengan nilai selisih)
            $donation->increment('sisa_stok', $selisih);

            // Tambah Log Perubahan
            DonationLog::create([
                'donation_id' => $donation->id,
                'status' => 'Update Distribusi',
                'diupdate_oleh' => auth()->user()->name,
                'catatan' => "Revisi distribusi ke {$request->nama_rs}. Jumlah disesuaikan.",
            ]);
        });

        return redirect()->back()->with('success', 'Data distribusi berhasil diperbarui.');
    }

    // --- FUNGSI HAPUS ---
    public function destroy($id)
    {
        $dist = Distribution::findOrFail($id);
        $donation = Donation::findOrFail($dist->donation_id);

        DB::transaction(function () use ($dist, $donation) {
            // 1. Kembalikan stok ke gudang
            $donation->increment('sisa_stok', $dist->jumlah_distribusi);

            // 2. Hapus file fisik jika ada
            if ($dist->file_ba && file_exists(public_path('uploads/berita_acara/' . $dist->file_ba))) {
                unlink(public_path('uploads/berita_acara/' . $dist->file_ba));
            }

            // 3. Catat log pembatalan
            DonationLog::create([
                'donation_id' => $donation->id,
                'status' => 'Batal Distribusi',
                'diupdate_oleh' => auth()->user()->name,
                'catatan' => "Distribusi ke {$dist->nama_rs} sebanyak {$dist->jumlah_distribusi} unit dibatalkan. Stok dikembalikan.",
            ]);

            // 4. Hapus data distribusi
            $dist->delete();
        });

        return redirect()->back()->with('success', 'Distribusi dibatalkan dan stok telah dikembalikan ke gudang.');
    }
}