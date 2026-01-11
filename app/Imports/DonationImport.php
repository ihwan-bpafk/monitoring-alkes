<?php

namespace App\Imports;

use App\Models\Donation;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DonationImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Donation([
            'pemberi_donasi' => $row['pemberi_donasi'],
            'nama_alkes'     => $row['nama_alkes'],
            'merek'          => $row['merek'],
            'jumlah_donasi'  => $row['jumlah_donasi'],
            'diterima_oleh'  => $row['diterima_oleh'],
            'sisa_stok'      => $row['sisa_stok'],
            'status_akhir'   => $row['status_akhir'] ?? '-',
        ]);
    }
}