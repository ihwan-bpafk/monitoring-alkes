<?php

namespace App\Exports;

use App\Models\Donation;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;

class DonationsExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Donation::query();

        // Samakan logic filter dengan yang ada di Controller Ahmad
        $query->when($this->filters['filter_pemberi'] ?? null, fn($q, $v) => $q->where('pemberi_donasi', $v));
        $query->when($this->filters['filter_alkes'] ?? null, fn($q, $v) => $q->where('nama_alkes', $v));
        $query->when($this->filters['filter_petugas'] ?? null, fn($q, $v) => $q->where('diterima_oleh', $v));
        
        if (($this->filters['filter_stok'] ?? null) == 'tersedia') {
            $query->where('sisa_stok', '>', 0);
        } elseif (($this->filters['filter_stok'] ?? null) == 'habis') {
            $query->where('sisa_stok', 0);
        }

        return $query->latest();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Pemberi Donasi',
            'Nama Alkes',
            'Merek',
            'Jumlah Donasi',
            'Sisa Stok',
            'Diterima Oleh',
            'Status Akhir',
            'Tanggal Masuk'
        ];
    }

    public function map($donation): array
    {
        return [
            $donation->id,
            $donation->pemberi_donasi,
            $donation->nama_alkes,
            $donation->merek,
            $donation->jumlah_donasi,
            $donation->sisa_stok,
            $donation->diterima_oleh,
            $donation->status_akhir,
            $donation->created_at->format('d-m-Y')
        ];
    }
}