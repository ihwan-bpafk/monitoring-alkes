<?php

namespace App\Exports;

use App\Models\Distribution;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;

class DistributionsExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function query()
    {
        // Ambil data distribusi beserta relasi donasinya
        $query = Distribution::with('donation');

        // Logic filter yang sinkron dengan halaman index Ahmad
        $query->when($this->filters['filter_rs'] ?? null, fn($q, $v) => $q->where('nama_rs', $v));
        $query->when($this->filters['filter_alkes'] ?? null, fn($q, $v) => $q->where('donation_id', $v));
        $query->when($this->filters['filter_status'] ?? null, fn($q, $v) => $q->where('status', $v));

        return $query->latest();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nama Alat Kesehatan',
            'Pemberi Donasi',
            'Rumah Sakit Tujuan',
            'Jumlah Unit',
            'Tanggal Distribusi',
            'Status',
            'Petugas Pengirim',
            'Keterangan'
        ];
    }

    public function map($dist): array
    {
        return [
            $dist->id,
            $dist->donation->nama_alkes ?? '-',
            $dist->donation->pemberi_donasi ?? '-',
            $dist->nama_rs,
            $dist->jumlah_distribusi . ' Unit',
            \Carbon\Carbon::parse($dist->tanggal_distribusi)->format('d-m-Y'),
            $dist->status,
            $dist->petugas_pengirim,
            $dist->keterangan
        ];
    }
}