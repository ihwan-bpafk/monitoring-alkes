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
    protected $search;

    public function __construct($search = null)
    {
        $this->search = $search;
    }

    public function collection()
    {
        $query = Repair::query();

        if ($this->search) {
            $query->where('nama_rs', 'like', "%{$this->search}%")
                ->orWhere('nama_alkes', 'like', "%{$this->search}%")
                ->orWhere('serial_number', 'like', "%{$this->search}%")
                ->orWhere('lokasi', 'like', "%{$this->search}%");
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
        // Bold Header (Baris ke-4)
        $sheet->getStyle('A4:O4')->getFont()->setBold(true);
        $sheet->getStyle('A1:A2')->getFont()->setSize(14)->setBold(true);

        // Tambahkan Auto Filter pada Header
        $sheet->setAutoFilter('A4:O4');

        // Mewarnai Baris Berdasarkan Status
        $rows = $sheet->getHighestRow();
        for ($i = 5; $i <= $rows; $i++) {
            $status = $sheet->getCell('K' . $i)->getValue();
            
            if ($status == 'Selesai Diperbaiki') {
                $sheet->getStyle('A'.$i.':O'.$i)->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('C6EFCE'); // Hijau Muda
            } elseif ($status == 'Harus Diganti') {
                $sheet->getStyle('A'.$i.':O'.$i)->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFC7CE'); // Merah Muda
            } elseif ($status == 'Dalam Proses') {
                $sheet->getStyle('A'.$i.':O'.$i)->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFEB9C'); // Kuning
            }
        }
    }
}