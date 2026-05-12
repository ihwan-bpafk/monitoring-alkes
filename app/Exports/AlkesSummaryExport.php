<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AlkesSummaryExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping
{
    protected $data;

    public function __construct($data)
    {
        // Data ini sudah di-sort berdasarkan abjad dari Controller
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data;
    }

    /**
     * Header Excel sesuai dengan kolom Dashboard
     */
    public function headings(): array
    {
        return [
            'Nama Alat Kesehatan',
            'Total Unit (Aset)',
            'Bisa Dipakai',
            'Dalam Proses',
            'Harus Ganti (BAP)',
            'Alokasi',
            'Distribusi',
            'Kebutuhan',
        ];
    }

    /**
     * Mapping data per baris
     */
    public function map($item): array
    {
        return [
            $item->nama_alkes,
            $item->jumlah,
            $item->bisa_dipakai,
            $item->proses,
            $item->ganti,
            $item->alokasi,
            $item->distribusi,
            $item->kebutuhan > 0 ? $item->kebutuhan.' Unit' : 'Terpenuhi',
        ];
    }
}
