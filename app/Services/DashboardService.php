<?php

namespace App\Services;

use App\Models\Distribution;
use App\Models\Repair;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * Mengambil opsi filter untuk dropdown (Rumah Sakit & Kategori)
     */
    public function getFilterOptions(): array
    {
        return [
            'list_rs' => Repair::whereNotNull('nama_rs')->distinct()->orderBy('nama_rs', 'asc')->pluck('nama_rs'),
            'list_kategori' => Repair::whereNotNull('kategori')->distinct()->orderBy('kategori', 'asc')->pluck('kategori'),
        ];
    }

    /**
     * Mendapatkan data statistik dan chart untuk Dashboard
     */
    public function getChartData(?string $selectedRs, ?string $selectedKategori): array
    {
        $query = Repair::query();
        
        if ($selectedRs) {
            $query->where('nama_rs', $selectedRs);
        }
        if ($selectedKategori) {
            $query->where('kategori', $selectedKategori);
        }

        $totalData = $query->count();
        
        $statusData = (clone $query)->select('status_perbaikan', DB::raw('count(*) as total'))
            ->where('status_perbaikan', '!=', '-')
            ->groupBy('status_perbaikan')
            ->get();
            
        $responData = (clone $query)->whereNotNull('respon_penyedia')
            ->where('grade_kerusakan', '!=', 'Bisa Dipakai')
            ->select('respon_penyedia', DB::raw('count(*) as total'))
            ->groupBy('respon_penyedia')
            ->get();
            
        $totalWithVendor = $responData->sum('total');
        
        $gradeData = (clone $query)->select('grade_kerusakan', DB::raw('count(*) as total'))
            ->groupBy('grade_kerusakan')
            ->get();

        return compact('totalData', 'totalWithVendor', 'statusData', 'responData', 'gradeData');
    }

    /**
     * Mengambil ringkasan inventaris Alkes dan perhitungan kebutuhannya
     */
    public function getAlkesSummary(?string $selectedRs, ?string $selectedKategori)
    {
        // 1. Data Distribusi
        $distQuery = Distribution::query()
            ->join('donations', 'distributions.donation_id', '=', 'donations.id');

        if ($selectedRs) {
            $distQuery->where('distributions.nama_rs', $selectedRs);
        }

        $donationsDist = $distQuery->select(
                'donations.nama_alkes',
                DB::raw("SUM(CASE WHEN distributions.status = 'Alokasi' THEN distributions.jumlah_distribusi ELSE 0 END) as total_alokasi"),
                DB::raw("SUM(CASE WHEN distributions.status != 'Alokasi' THEN distributions.jumlah_distribusi ELSE 0 END) as total_distribusi")
            )
            ->groupBy('donations.nama_alkes')
            ->get()
            ->keyBy('nama_alkes');

        // 2. Query Utama Repair
        $query = Repair::query();
        
        if ($selectedRs) {
            $query->where('nama_rs', $selectedRs);
        }
        if ($selectedKategori) {
            $query->where('kategori', $selectedKategori);
        }

        return (clone $query)->select(
                'nama_alkes',
                DB::raw('count(*) as jumlah_repair'),
                DB::raw("SUM(CASE WHEN status_perbaikan = 'Bisa Dipakai' THEN 1 ELSE 0 END) as bisa_dipakai"),
                DB::raw("SUM(CASE WHEN status_perbaikan = 'Dalam Proses Perbaikan' THEN 1 ELSE 0 END) as proses"),
                DB::raw("SUM(CASE WHEN status_perbaikan = 'Harus di Ganti (BAP)' THEN 1 ELSE 0 END) as ganti")
            )
            ->groupBy('nama_alkes')
            ->orderBy('nama_alkes', 'asc') // Sortir abjad
            ->get()
            ->map(function ($item) use ($donationsDist) {
                $distData = $donationsDist[$item->nama_alkes] ?? null;

                $item->total_alokasi = $distData ? $distData->total_alokasi : 0;
                $item->total_distribusi = $distData ? $distData->total_distribusi : 0;
                
                // Alias properti untuk kompatibilitas class Export Excel (AlkesSummaryExport)
                $item->alokasi = $item->total_alokasi;
                $item->distribusi = $item->total_distribusi;

                $item->jumlah = $item->jumlah_repair;

                // Rumus Kebutuhan: BAP - (Alokasi + Distribusi)
                $total_masuk = $item->total_alokasi + $item->total_distribusi;
                $kebutuhan = $item->ganti - $total_masuk;
                $item->kebutuhan = $kebutuhan > 0 ? $kebutuhan : 0;

                return $item;
            });
    }
}
