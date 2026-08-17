<?php

namespace App\Services;

use App\Models\Donation;
use App\Models\DonationLog;
use App\Models\Repair;
use Illuminate\Support\Facades\DB;

class DonationService
{
    /**
     * Mengambil daftar opsi filter untuk dropdown
     */
    public function getFilterOptions(): array
    {
        return [
            'list_pemberi'      => Donation::distinct()->orderBy('pemberi_donasi')->pluck('pemberi_donasi'),
            'list_alkes_donasi' => Donation::distinct()->orderBy('nama_alkes')->pluck('nama_alkes'),
            'list_penerima'     => Donation::distinct()->orderBy('diterima_oleh')->pluck('diterima_oleh'),
            'list_alkes_master' => Repair::distinct()->orderBy('nama_alkes', 'asc')->pluck('nama_alkes'),
        ];
    }

    /**
     * Mengambil daftar donasi berdasarkan filter
     */
    public function getFilteredDonations(array $filters, bool $paginate = true)
    {
        $query = Donation::with('distributions');

        $query->when($filters['filter_pemberi'] ?? null, function ($q, $pemberi) {
            return $q->where('pemberi_donasi', $pemberi);
        });

        $query->when($filters['filter_alkes'] ?? null, function ($q, $alkes) {
            return $q->where('nama_alkes', $alkes);
        });

        $query->when($filters['filter_petugas'] ?? null, function ($q, $petugas) {
            return $q->where('diterima_oleh', $petugas);
        });

        $query->when($filters['filter_stok'] ?? null, function ($q, $v) {
            if ($v == 'tersedia') {
                return $q->where('sisa_stok', '>', 0);
            } elseif ($v == 'habis') {
                return $q->where('sisa_stok', '<=', 0);
            }
        });

        $query->orderBy('nama_alkes', 'asc');

        return $paginate ? $query->paginate(10)->withQueryString() : $query->get();
    }

    /**
     * Menyimpan data donasi baru ke database dengan sistem transaksi
     */
    public function createDonation(array $data, string $userName)
    {
        return DB::transaction(function () use ($data, $userName) {
            $statusAwal = $data['status_akhir'] ?? '-';

            $donation = Donation::create([
                'pemberi_donasi' => $data['pemberi_donasi'],
                'nama_alkes'     => $data['nama_alkes'],
                'merek'          => $data['merek'] ?? null,
                'jumlah_donasi'  => $data['jumlah_donasi'],
                'diterima_oleh'  => $data['diterima_oleh'] ?? null,
                'sisa_stok'      => $data['jumlah_donasi'],
                'status_akhir'   => $statusAwal,
            ]);

            // Catat Log Pertama
            DonationLog::create([
                'donation_id'   => $donation->id,
                'status'        => $statusAwal,
                'diupdate_oleh' => $userName,
                'catatan'       => 'Penerimaan awal data donasi.',
            ]);

            return $donation;
        });
    }

    /**
     * Memperbarui status dan jumlah stok donasi
     */
    public function updateDonationStatus(int $id, array $data, string $userName)
    {
        $donation = Donation::findOrFail($id);
        $sudahDistribusi = $donation->jumlah_donasi - $donation->sisa_stok;

        return DB::transaction(function () use ($data, $donation, $sudahDistribusi, $userName) {
            $statusBaru = $data['status_akhir'] ?? '-';
            $jumlahBaru = $data['jumlah_donasi'];
            
            // Hitung ulang sisa stok berdasarkan input baru
            $sisaStokBaru = $jumlahBaru - $sudahDistribusi;

            $donation->update([
                'jumlah_donasi' => $jumlahBaru,
                'sisa_stok'     => $sisaStokBaru,
                'status_akhir'  => $statusBaru
            ]);

            DonationLog::create([
                'donation_id'   => $donation->id,
                'status'        => 'Update Data: ' . $statusBaru,
                'diupdate_oleh' => $userName,
                'catatan'       => $data['catatan'] ?? "Update jumlah menjadi {$jumlahBaru} unit dan status menjadi {$statusBaru}.",
            ]);

            return $donation;
        });
    }

    /**
     * Menghapus donasi beserta file dan relasinya (Log, Distribusi)
     */
    public function deleteDonation(int $id)
    {
        $donation = Donation::findOrFail($id);

        return DB::transaction(function () use ($donation) {
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
    }
}
