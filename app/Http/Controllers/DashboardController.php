<?php

namespace App\Http\Controllers;

use App\Models\Repair;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            ->whereNotNull('nama_penyedia')
            ->whereNotIn('nama_penyedia', ['-', '', 'Tidak Ada'])
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
        $alkesSummary = (clone $query)
            ->select('nama_alkes', 
                DB::raw('count(*) as jumlah'),
                // Menghitung jumlah 'Bisa Dipakai'
                DB::raw("SUM(CASE WHEN status_perbaikan = 'Bisa Dipakai' THEN 1 ELSE 0 END) as bisa_dipakai"),
                // Menghitung jumlah 'Dalam Proses Perbaikan'
                DB::raw("SUM(CASE WHEN status_perbaikan = 'Dalam Proses Perbaikan' THEN 1 ELSE 0 END) as proses"),
                // Menghitung jumlah 'Harus di Ganti (BAP)'
                DB::raw("SUM(CASE WHEN status_perbaikan = 'Harus di Ganti (BAP)' THEN 1 ELSE 0 END) as ganti")
            )
            ->groupBy('nama_alkes')
            ->orderBy('jumlah', 'desc')
            ->get();

        return view('dashboard.index', compact(
            'list_rs', 'selected_rs', 'list_kategori', 'selected_kategori',
            'totalData', 'totalWithVendor', 'statusData', 'responData', 'gradeData',
            'alkesSummary' // Kirim variabel ini ke view
        ));
    }
}