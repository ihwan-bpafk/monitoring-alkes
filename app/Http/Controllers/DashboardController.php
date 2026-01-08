<?php

namespace App\Http\Controllers;

use App\Models\Repair;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Donation;
use App\Exports\AlkesSummaryExport;
use Maatwebsite\Excel\Facades\Excel;

class DashboardController extends Controller
{
    /**
     * Menampilkan Dashboard Utama dengan Filter RS dan Kategori
     */
    public function index(Request $request)
    {
        // 1. Ambil list unik untuk dropdown filter
        $list_rs = Repair::whereNotNull('nama_rs')
            ->distinct()
            ->orderBy('nama_rs', 'asc')
            ->pluck('nama_rs');

        $list_kategori = Repair::whereNotNull('kategori')
            ->distinct()
            ->orderBy('kategori', 'asc')
            ->pluck('kategori');

        // 2. Tangkap filter dari URL
        $selected_rs = $request->query('nama_rs');
        $selected_kategori = $request->query('kategori');
        $donationQuery = Donation::query();

        if ($selected_rs) {
            $donationQuery->where('nama_rs', $selected_rs);
        }

        $donations = $donationQuery->select('nama_alkes', DB::raw('SUM(jumlah) as total_donasi'))
        ->groupBy('nama_alkes')
        ->get()
        ->pluck('total_donasi', 'nama_alkes');

        // 3. Query Dasar (Base Query)
        $query = Repair::query();

        // 4. Terapkan filter jika ada
        if ($selected_rs) {
            $query->where('nama_rs', $selected_rs);
        }

        if ($selected_kategori) {
            $query->where('kategori', $selected_kategori);
        }

        // 5. Eksekusi Data untuk Dashboard (berdasarkan filter yang aktif)
        $totalData = $query->count();

        // --- A. DATA KONDISI AKHIR ALKES ---
        $statusData = (clone $query)
        ->select('status_perbaikan', DB::raw('count(*) as total'))
        ->where('status_perbaikan', '!=', '-') // Menghapus/mengecualikan yang bernilai '-'
        ->groupBy('status_perbaikan')
        ->get();

        // --- B. DATA RESPON PENYEDIA ---
        $responData = (clone $query)
            // 1. Tetap gunakan filter vendor yang sudah terbukti jalan
            ->whereNotNull('nama_penyedia')
            // 2. Logika Eksklusi: Buang jika salah satu bernilai 'Bisa Dipakai'
            // Secara matematis: NOT (A OR B) itu sama dengan (NOT A AND NOT B)
            ->where('grade_kerusakan', '!=', 'Bisa Dipakai')
            ->where('status_perbaikan', '!=', 'Bisa Dipakai')

            // 3. Ambil data untuk Chart
            ->select('respon_penyedia', DB::raw('count(*) as total'))
            ->groupBy('respon_penyedia')
            ->get();

        $totalWithVendor = $responData->sum('total');

        // --- C. DATA KONDISI AWAL ALKES ---
        $gradeData = (clone $query)
            ->select('grade_kerusakan', DB::raw('count(*) as total'))
            ->groupBy('grade_kerusakan')
            ->get();

        // 6. Kirim data ke view

        
        // Query Ringkasan Alat dengan Status Baru
        // $alkesSummary = (clone $query)
        //     ->select('nama_alkes', 
        //         DB::raw('count(*) as jumlah'),
        //         // Menghitung jumlah 'Bisa Dipakai'
        //         DB::raw("SUM(CASE WHEN status_perbaikan = 'Bisa Dipakai' THEN 1 ELSE 0 END) as bisa_dipakai"),
        //         // Menghitung jumlah 'Dalam Proses Perbaikan'
        //         DB::raw("SUM(CASE WHEN status_perbaikan = 'Dalam Proses Perbaikan' THEN 1 ELSE 0 END) as proses"),
        //         // Menghitung jumlah 'Harus di Ganti (BAP)'
        //         DB::raw("SUM(CASE WHEN status_perbaikan = 'Harus di Ganti (BAP)' THEN 1 ELSE 0 END) as ganti")
        //     )
        //     ->groupBy('nama_alkes')
        //     ->orderBy('jumlah', 'desc')
        //     ->get();

        $alkesSummary = (clone $query)
        ->select('nama_alkes', 
            DB::raw('count(*) as jumlah'),
            DB::raw("SUM(CASE WHEN status_perbaikan = 'Bisa Dipakai' THEN 1 ELSE 0 END) as bisa_dipakai"),
            DB::raw("SUM(CASE WHEN status_perbaikan = 'Dalam Proses Perbaikan' THEN 1 ELSE 0 END) as proses"),
            DB::raw("SUM(CASE WHEN status_perbaikan = 'Harus di Ganti (BAP)' THEN 1 ELSE 0 END) as ganti")
        )
        ->groupBy('nama_alkes')
        ->orderBy('jumlah', 'desc')
        ->get()
        ->map(function($item) use ($donations) {
            // Gabungkan data donasi ke dalam collection alkesSummary
            $item->total_donasi = $donations[$item->nama_alkes] ?? 0;
            
            // Rumus: Kebutuhan = BAP - Donasi (Jika hasil negatif, jadikan 0)
            $kebutuhan = $item->ganti - $item->total_donasi;
            $item->kebutuhan = $kebutuhan > 0 ? $kebutuhan : 0;
            
            return $item;
        });

        return view('dashboard.index', compact(
            'list_rs', 'selected_rs', 'list_kategori', 'selected_kategori',
            'totalData', 'totalWithVendor', 'statusData', 'responData', 'gradeData',
            'alkesSummary' // Kirim variabel ini ke view
        ));
    }

    public function exportExcel(Request $request)
    {
        // Gunakan logic query yang sama dengan index (pastikan filter RS/Kategori terbawa)
        $query = \App\Models\Repair::query();
        
        if ($request->nama_rs) {
            $query->where('nama_rs', $request->nama_rs);
        }
        if ($request->kategori) {
            $query->where('kategori', $request->kategori);
        }

        $donations = \App\Models\Donation::when($request->nama_rs, fn($q) => $q->where('nama_rs', $request->nama_rs))
            ->select('nama_alkes', \DB::raw('SUM(jumlah) as total_donasi'))
            ->groupBy('nama_alkes')->get()->pluck('total_donasi', 'nama_alkes');

        $alkesSummary = $query->select('nama_alkes', 
                \DB::raw('count(*) as jumlah'),
                \DB::raw("SUM(CASE WHEN status_perbaikan = 'Bisa Dipakai' THEN 1 ELSE 0 END) as bisa_dipakai"),
                \DB::raw("SUM(CASE WHEN status_perbaikan = 'Dalam Proses Perbaikan' THEN 1 ELSE 0 END) as proses"),
                \DB::raw("SUM(CASE WHEN status_perbaikan = 'Harus di Ganti (BAP)' THEN 1 ELSE 0 END) as ganti")
            )
            ->groupBy('nama_alkes')->get()
            ->map(function($item) use ($donations) {
                $item->total_donasi = $donations[$item->nama_alkes] ?? 0;
                $kebutuhan = $item->ganti - $item->total_donasi;
                $item->kebutuhan = $kebutuhan > 0 ? $kebutuhan : 0;
                return $item;
            });

        $nama_file = 'Rekap_Alkes_' . ($request->nama_rs ?? 'Semua_RS') . '_' . date('Y-m-d') . '.xlsx';
        
        return Excel::download(new AlkesSummaryExport($alkesSummary), $nama_file);
    }
}