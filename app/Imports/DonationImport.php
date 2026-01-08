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
            'input_by'               => 'System (Import Excel)',
            'nama_alkes'             => $data['nama_alkes'] ?? '-',
            'nama_rs'                => $data['nama_rs'] ?? '-',
            'merek'                  => $data['merek'] ?? '-',
            'donatur'                => $data['donatur'] ?? '-',
            'tanggal_terima_donatur' => $data['tanggal_terima_donatur'] ?? now()->format('Y-m-d'),
            'jumlah_total_donasi'    => $data['jumlah_total_donasi'],
            'jumlah'                 => $data['jumlah'],
            'status'                 => $data['status'] ?? '-',
            'sisa_stok'              => $data['sisa_stok'] ?? '0',
            'keterangan_lain'        => $data['keterangan_lain'] ?? '-',
        ]);
    }
}