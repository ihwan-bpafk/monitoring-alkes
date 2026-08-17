<?php

namespace App\Services;

use App\Models\Distribution;
use App\Models\Donation;
use App\Models\DonationLog;
use App\Models\Repair;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Http\UploadedFile;

class DistributionService
{
    /**
     * Mengambil daftar opsi filter untuk dropdown
     */
    public function getFilterOptions(): array
    {
        return [
            'list_rs_master'     => \App\Models\Fasyankes::orderBy('nama_fasyankes')->pluck('nama_fasyankes'),
            'list_alkes_dist'    => Donation::whereHas('distributions')->distinct()->orderBy('nama_alkes')->pluck('nama_alkes'),
            'list_pemberi'       => Donation::whereHas('distributions')->distinct()->orderBy('pemberi_donasi')->pluck('pemberi_donasi'),
            'list_status'        => Distribution::whereHas('donation')->distinct()->pluck('status'),
            'availableDonations' => Donation::where('sisa_stok', '>', 0)->get(),
        ];
    }

    /**
     * Mengambil daftar distribusi berdasarkan filter
     */
    public function getFilteredDistributions(array $filters, bool $paginate = true)
    {
        $query = Distribution::with('donation')->whereHas('donation');

        $query->when($filters['filter_rs'] ?? null, fn($q, $v) => $q->where('nama_rs', $v));

        $query->when($filters['filter_alkes'] ?? null, function($q, $v) {
            $q->whereHas('donation', function($query) use ($v) {
                $query->where('nama_alkes', $v);
            });
        });

        $query->when($filters['filter_pemberi'] ?? null, function($q, $v) {
            $q->whereHas('donation', function($query) use ($v) {
                $query->where('pemberi_donasi', $v);
            });
        });

        $query->when($filters['filter_status'] ?? null, fn($q, $v) => $q->where('status', $v));

        return $paginate ? $query->latest()->paginate(10)->withQueryString() : $query->latest()->get();
    }

    /**
     * Membuat data distribusi baru dengan pengecekan stok dan transaksi DB
     */
    public function createDistribution(array $data, ?UploadedFile $file, string $userName)
    {
        $donation = Donation::findOrFail($data['donation_id']);

        if ($donation->sisa_stok < $data['jumlah_distribusi']) {
            throw new Exception('Gagal! Stok tidak mencukupi.');
        }

        return DB::transaction(function () use ($data, $file, $donation, $userName) {
            $fileName = null;
            if ($file) {
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/berita_acara'), $fileName);
            }

            $distribution = Distribution::create([
                'donation_id'        => $data['donation_id'],
                'nama_rs'            => $data['nama_rs'],
                'jumlah_distribusi'  => $data['jumlah_distribusi'],
                'tanggal_distribusi' => $data['tanggal_distribusi'] ?? null,
                'status'             => $data['status'],
                'petugas_pengirim'   => $userName,
                'file_ba'            => $fileName,
                'keterangan'         => $data['keterangan'] ?? null,
            ]);

            $donation->decrement('sisa_stok', $data['jumlah_distribusi']);

            DonationLog::create([
                'donation_id'   => $donation->id,
                'status'        => 'Distribusi: ' . $data['status'],
                'diupdate_oleh' => $userName,
                'catatan'       => "Kirim {$data['jumlah_distribusi']} unit ke {$data['nama_rs']} (Status: {$data['status']})",
            ]);

            return $distribution;
        });
    }

    /**
     * Memperbarui data distribusi dan mengembalikan/mengurangi stok berdasarkan selisih
     */
    public function updateDistribution(int $id, array $data, string $userName)
    {
        $dist = Distribution::findOrFail($id);
        $donation = Donation::findOrFail($dist->donation_id);

        $selisih = $dist->jumlah_distribusi - $data['jumlah_distribusi'];

        if (($donation->sisa_stok + $selisih) < 0) {
            throw new Exception('Gagal! Stok di gudang tidak mencukupi.');
        }

        return DB::transaction(function () use ($data, $dist, $donation, $selisih, $userName) {
            $dist->update([
                'nama_rs'            => $data['nama_rs'],
                'jumlah_distribusi'  => $data['jumlah_distribusi'],
                'tanggal_distribusi' => $data['tanggal_distribusi'] ?? $dist->tanggal_distribusi,
                'status'             => $data['status'],
                'keterangan'         => $data['keterangan'] ?? $dist->keterangan,
            ]);

            $donation->increment('sisa_stok', $selisih);

            DonationLog::create([
                'donation_id'   => $donation->id,
                'status'        => 'Update Distribusi: ' . $data['status'],
                'diupdate_oleh' => $userName,
                'catatan'       => "Revisi distribusi ke {$data['nama_rs']}. Status menjadi: {$data['status']}",
            ]);

            return $dist;
        });
    }

    /**
     * Menghapus (membatalkan) distribusi dan mengembalikan stok
     */
    public function deleteDistribution(int $id, string $userName)
    {
        $dist = Distribution::findOrFail($id);
        $donation = Donation::findOrFail($dist->donation_id);

        return DB::transaction(function () use ($dist, $donation, $userName) {
            // 1. Kembalikan stok ke gudang
            $donation->increment('sisa_stok', $dist->jumlah_distribusi);

            // 2. Hapus file fisik jika ada
            if ($dist->file_ba && file_exists(public_path('uploads/berita_acara/' . $dist->file_ba))) {
                unlink(public_path('uploads/berita_acara/' . $dist->file_ba));
            }

            // 3. Catat log pembatalan
            DonationLog::create([
                'donation_id'   => $donation->id,
                'status'        => 'Batal Distribusi',
                'diupdate_oleh' => $userName,
                'catatan'       => "Distribusi ke {$dist->nama_rs} sebanyak {$dist->jumlah_distribusi} unit dibatalkan. Stok dikembalikan.",
            ]);

            // 4. Hapus data distribusi
            $dist->delete();
        });
    }
}
