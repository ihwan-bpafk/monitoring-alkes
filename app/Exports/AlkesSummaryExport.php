<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AlkesSummaryExport implements FromCollection, WithHeadings, WithMapping
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'Nama Alat Kesehatan',
            'Total Unit',
            'Bisa Dipakai',
            'Dalam Proses',
            'Harus Ganti (BAP)',
            'Total Donasi',
            'Kebutuhan'
        ];
    }

    public function map($item): array
    {
        return [
            $item->nama_alkes,
            $item->jumlah,
            $item->bisa_dipakai,
            $item->proses,
            $item->ganti,
            $item->total_donasi,
            $item->kebutuhan > 0 ? $item->kebutuhan . ' Unit' : 'Terpenuhi',
        ];
    }
}