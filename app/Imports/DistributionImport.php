<?php

namespace App\Imports;

use App\Models\Distribution;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Row;

class DistributionImport implements OnEachRow, WithHeadingRow
{
    public function onRow(Row $row)
    {
        $data = $row->toArray();

        // Logika Kalkulasi Stok: Sisa = Total - Distribusi

        // Simpan data ke tabel Donations
        Distribution::create([
            'donation_id'             => $data['donation_id'],
            'nama_rs' => $data['nama_rs'], 
            'jumlah_distribusi'     => $data['jumlah_distribusi'] ?? '-',
            'status'          => $data['status'] ?? '-',
            'tanggal_distribusi'  => $data['tanggal_distribusi'] ?? now()->format('Y-m-d'),
            'petugas_pengirim'  => $data['petugas_pengirim'] ?? "petugas",
        ]);
    }
}