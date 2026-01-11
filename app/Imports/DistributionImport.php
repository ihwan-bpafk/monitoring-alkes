<?php

namespace App\Imports;

use App\Models\Distribution;
use App\Models\Donation;
use App\Models\DonationLog;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DistributionImport implements ToModel, WithHeadingRow
{
    
    public function model(array $row)
    {
        return DB::transaction(function () use ($row) {
            // 1. Cari data donasi berdasarkan ID di Excel
            $donation = Donation::find($row['donation_id']);

            if (!$donation) {
                return null; // Lewati jika ID donasi tidak ditemukan
            }

            // 2. Validasi stok (Jangan sampai minus)
            if ($donation->sisa_stok < $row['jumlah_distribusi']) {
                return null; // Lewati jika stok tidak cukup
            }

            // 3. Simpan data Distribusi
            $dist = Distribution::create([
                'donation_id'       => $row['donation_id'],
                'nama_rs'           => $row['nama_rs'],
                'jumlah_distribusi' => $row['jumlah_distribusi'],
                'tanggal_distribusi'=> now(), // Default hari ini jika tidak ada di Excel
                'status'            => $row['status'] ?? 'Dikirim',
                'petugas_pengirim'  => 'System (Excel Import)',
                'keterangan'        => 'Import data massal via Excel',
            ]);

            // 4. POTONG STOK OTOMATIS
            $donation->decrement('sisa_stok', $row['jumlah_distribusi']);

            // 5. UPDATE STATUS AKHIR DONASI
            $donation->update([
                'status_akhir' => 'Didistribusikan ke ' . $row['nama_rs']
            ]);

            // 6. CATAT LOG TRACKING
            DonationLog::create([
                'donation_id'   => $donation->id,
                'status'        => 'Distribusi (Import)',
                'diupdate_oleh' => 'System',
                'catatan'       => "Distribusi massal: {$row['jumlah_distribusi']} unit ke {$row['nama_rs']}.",
            ]);

            return $dist;
        });
    }
}