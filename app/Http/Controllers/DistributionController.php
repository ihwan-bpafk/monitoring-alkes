<?php

namespace App\Http\Controllers;

use App\Models\Distribution;
use App\Models\Donation;
use App\Models\DonationLog;
use App\Models\Repair;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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
            'status' => 'required', // Tambahkan validasi status
        ]);

        $donation = Donation::findOrFail($request->donation_id);

        if ($donation->sisa_stok < $request->jumlah_distribusi) {
            return redirect()->back()->with('error', 'Gagal! Stok tidak mencukupi.');
        }

        DB::transaction(function () use ($request, $donation) {
            // Upload file BA (seperti kode sebelumnya)
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
                'status' => $request->status, // Mengambil dari input
                'petugas_pengirim' => auth()->user()->name,
                'file_ba' => $fileName,
                'keterangan' => $request->keterangan,
            ]);

            $donation->decrement('sisa_stok', $request->jumlah_distribusi);

            // Update Log Tracking
            DonationLog::create([
                'donation_id' => $donation->id,
                'status' => 'Distribusi: ' . $request->status,
                'diupdate_oleh' => auth()->user()->name,
                'catatan' => "Kirim {$request->jumlah_distribusi} unit ke {$request->nama_rs} (Status: {$request->status})",
            ]);
        });

        return redirect()->back()->with('success', 'Data distribusi berhasil disimpan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'jumlah_distribusi' => 'required|integer|min:1',
            'status' => 'required',
        ]);

        $dist = Distribution::findOrFail($id);
        $donation = Donation::findOrFail($dist->donation_id);

        $selisih = $dist->jumlah_distribusi - $request->jumlah_distribusi;

        if (($donation->sisa_stok + $selisih) < 0) {
            return redirect()->back()->with('error', 'Gagal! Stok di gudang tidak mencukupi.');
        }

        DB::transaction(function () use ($request, $dist, $donation, $selisih) {
            // Update data distribusi termasuk STATUS
            $dist->update([
                'nama_rs' => $request->nama_rs,
                'jumlah_distribusi' => $request->jumlah_distribusi,
                'tanggal_distribusi' => $request->tanggal_distribusi,
                'status' => $request->status, // Update Status Baru
                'keterangan' => $request->keterangan,
            ]);

            $donation->increment('sisa_stok', $selisih);

            DonationLog::create([
                'donation_id' => $donation->id,
                'status' => 'Update Distribusi: ' . $request->status,
                'diupdate_oleh' => auth()->user()->name,
                'catatan' => "Revisi distribusi ke {$request->nama_rs}. Status menjadi: {$request->status}",
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