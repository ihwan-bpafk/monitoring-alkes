<?php

namespace App\Imports;

use App\Models\Repair;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Row;

class RepairImport implements OnEachRow, WithHeadingRow
{
    public function onRow(Row $row)
    {
        $data = $row->toArray();

        // 1. Simpan data ke tabel Repairs
        $repair = Repair::create([
            'input_by'        => 'System (Import Excel)' ?? '-',
            'tanggal_input'   => now()->format('Y-m-d') ?? '-',
            'nama_rs'         => $data['nama_rs'] ?? '-',
            'lokasi'          => $data['lokasi'] ?? '-',
            'nama_alkes'      => $data['nama_alkes'] ?? '-',
            'serial_number'   => $data['serial_number'] ?? '-',
            'kategori'        => $data['kategori'] ?? '-',
            'merek'           => $data['merek'] ?? '-',
            'tipe_model'      => $data['tipe_model'] ?? '-',
            'nama_penyedia'   => $data['nama_penyedia'] ?? '-',
            'grade_kerusakan' => $data['grade_kerusakan'] ?? '-',
            'status_perbaikan'=> $data['status_perbaikan'] ?? '-',
            'komponen'        => $data['komponen'] ?? '-',
            'kondisi_kontrak' => $data['kondisi_kontrak'] ?? '-',
            'respon_penyedia' => $data['respon_penyedia'] ?? '-',
            'tindakan_penyedia' => $data['tindakan_penyedia'] ?? '-',
            'rtl' => $data['rtl'] ?? '-',
            'keterangan_lain' => $data['keterangan_lain'] ?? '-',
        ]);

        // 2. Lagsung buat History Awal untuk alat ini
        $repair->histories()->create([
            'status_perbaikan'     => $repair->status_perbaikan ?? '-',
            'keterangan_perubahan' => $data['keterangan_lain'] ?? '-',
            'user_nama'            => 'System (Import)' ?? '-',
        ]);
    }
}