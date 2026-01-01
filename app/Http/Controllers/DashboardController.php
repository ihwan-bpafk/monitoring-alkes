<?php

namespace App\Http\Controllers;

use App\Models\Repair;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Menampilkan Dashboard Utama
     */
    public function index(Request $request)
    {
        // 1. Ambil list semua RS untuk dropdown filter di view
        $list_rs = Repair::select('nama_rs')
            ->whereNotNull('nama_rs')
            ->distinct()
            ->orderBy('nama_rs', 'asc')
            ->pluck('nama_rs');

        // 2. Tangkap filter RS dari URL (jika ada)
        $selected_rs = $request->query('nama_rs');

        // 3. Query Dasar
        $query = Repair::query();

        // 4. Terapkan filter jika user memilih RS tertentu
        if ($selected_rs) {
            $query->where('nama_rs', $selected_rs);
        }

        // 5. Total data keseluruhan (setelah filter RS)
        $totalData = $query->count();

        // --- A. DATA KONDISI AKHIR ALKES (Sebelumnya Status Perbaikan) ---
        // Mengelompokkan berdasarkan: Selesai diperbaiki, Dalam proses, Harus diganti (BAP), -
        $statusData = (clone $query)
            ->select('status_perbaikan', DB::raw('count(*) as total'))
            ->groupBy('status_perbaikan')
            ->get();

        // --- B. DATA RESPON PENYEDIA (Hanya untuk data yang memiliki Vendor) ---
        // Menghitung "Datang" vs "Belum Datang" hanya pada alat yang ada nama penyedianya
        $responData = (clone $query)
            ->whereNotNull('nama_penyedia')
            ->whereNotIn('nama_penyedia', ['-', '', 'Tidak Ada'])
            ->select('respon_penyedia', DB::raw('count(*) as total'))
            ->groupBy('respon_penyedia')
            ->get();

        // Menghitung total khusus alat yang ada vendornya untuk persentase chart respon
        $totalWithVendor = $responData->sum('total');

        // --- C. DATA KONDISI AWAL ALKES (Sebelumnya Grade Kerusakan) ---
        // Mengelompokkan berdasarkan: Bisa dipakai, Rusak ringan, Rusak berat
        $gradeData = (clone $query)
            ->select('grade_kerusakan', DB::raw('count(*) as total'))
            ->groupBy('grade_kerusakan')
            ->get();

        // 6. Kirim semua data ke view dashboard.index
        return view('dashboard.index', compact(
            'list_rs', 
            'selected_rs', 
            'totalData', 
            'totalWithVendor',
            'statusData', 
            'responData', 
            'gradeData'
        ));
    }
}