<?php

namespace App\Imports;

use App\Models\Donation;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Row;

class DonationImport implements OnEachRow, WithHeadingRow
{
    public function onRow(Row $row)
    {
        $data = $row->toArray();

        // Logika Kalkulasi Stok: Sisa = Total - Distribusi

        // Simpan data ke tabel Donations
        Donation::create([
            // Menggunakan penamaan dari Gambar 2
            'id'             => $data['id'],
            'pemberi_donasi' => $data['pemberi_donasi'] ?? '-', 
            'nama_alkes'     => $data['nama_alkes'] ?? '-',
            'merek'          => $data['merek'] ?? '-',
            'jumlah_donasi'  => $data['jumlah_donasi'] ?? 0,
            'diterima_oleh'  => $data['diterima_oleh']?? 'System',
            
            // Logika Sinkronisasi: Stok awal yang tersedia sama dengan jumlah yang masuk
            'sisa_stok'      => $data['jumlah_donasi']?? 0,
            
            // Field tambahan dari migrasi sebelumnya
            'tanggal_masuk'  => $data['tanggal_masuk'] ?? now()->format('Y-m-d'),
            'keterangan'     => $data['keterangan'] ?? '-',
        ]);
    }
}