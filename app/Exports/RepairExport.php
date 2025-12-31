<?php

namespace App\Exports;

use App\Models\Repair;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RepairExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{

    // Tambahkan properti di dalam class
    protected $filters;

    // Konstruktor untuk menerima filter
    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Repair::query();

        if (!empty($this->filters['nama_rs'])) {
            $query->where('nama_rs', 'like', '%' . $this->filters['nama_rs'] . '%');
        }
        if (!empty($this->filters['nama_alkes'])) {
            $query->where('nama_alkes', 'like', '%' . $this->filters['nama_alkes'] . '%');
        }
        if (!empty($this->filters['status_perbaikan'])) {
            $query->where('status_perbaikan', $this->filters['status_perbaikan']);
        }
        if (!empty($this->filters['grade_kerusakan'])) {
            $query->where('grade_kerusakan', $this->filters['grade_kerusakan']);
        }
        // TAMBAHKAN LOGIKA INI
        if (!empty($this->filters['respon_penyedia'])) {
            $query->where('respon_penyedia', $this->filters['respon_penyedia']);
        }

        return $query->latest()->get();
    }

    // Mendefinisikan Judul Kolom (Header)
    public function headings(): array
    {
        return [
            ['LAPORAN MONITORING PERBAIKAN ALAT KESEHATAN'], // Judul Besar
            ['BPAFK MEDAN - Update: ' . now()->format('d/m/Y')],
            [],
            [
                'No', 'Tanggal Input', 'Rumah Sakit', 'Lokasi', 'Nama Alat', 'Kategori', 
                'SN', 'Merek', 'Model', 'Penyedia', 'Kontrak', 
                'Grade Kerusakan', 'Respon Penyedia', 'Tindakan Penyedia', 'Status Akhir', 'Komponen Rusak', 'RTL', 'Keterangan'
            ]
        ];
    }

    // Memetakan data dari Database ke Kolom Excel
    public function map($repair): array
    {
        static $no = 1;
        return [
            $no++,
            $repair->tanggal_input,
            $repair->nama_rs,
            $repair->lokasi,
            $repair->nama_alkes,
            $repair->kategori,
            $repair->serial_number,
            $repair->merek,
            $repair->tipe_model,
            $repair->nama_penyedia,
            $repair->kondisi_kontrak,
            $repair->grade_kerusakan,
            $repair->respon_penyedia,
            $repair->tindakan_penyedia,
            $repair->status_perbaikan,
            $repair->komponen,
            $repair->rtl,
            $repair->keterangan_lain,
        ];
    }

    // Memberikan Warna & Styling
    public function styles(Worksheet $sheet)
    {
        // Styling Judul Besar
        $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(14);
        
        // Styling Header Tabel (Baris ke-4) - Menjangkau Kolom A sampai R
        $sheet->getStyle('A4:R4')->getFont()->setBold(true);
        $sheet->getStyle('A4:R4')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('E2EFDA');

        // Tambahkan Auto Filter agar user Excel bisa menyaring data sendiri
        $sheet->setAutoFilter('A4:R4');

        // Tambahkan Border ke seluruh data
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle('A4:R' . $highestRow)->applyFromArray($styleArray);
    }
}