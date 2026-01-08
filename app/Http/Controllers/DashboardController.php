<?php

namespace App\Http\Controllers;

use App\Models\Repair;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Donation;
use App\Exports\AlkesSummaryExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Distribution;

class DashboardController extends Controller
{
    /**
     * Menampilkan Dashboard Utama dengan Filter RS dan Kategori
     */
    public function index(Request $request)
    {
        // 1. Dropdown Filter
        $list_rs = Repair::whereNotNull('nama_rs')->distinct()->orderBy('nama_rs', 'asc')->pluck('nama_rs');
        $list_kategori = Repair::whereNotNull('kategori')->distinct()->orderBy('kategori', 'asc')->pluck('kategori');

        // 2. Tangkap filter
        $selected_rs = $request->query('nama_rs');
        $selected_kategori = $request->query('kategori');

        // --- LOGIKA BARU: Ambil Donasi yang sudah berstatus 'Diterima' di RS ---
        $distQuery = Distribution::query()
            ->join('donations', 'distributions.donation_id', '=', 'donations.id')
            ->where('distributions.status', 'Diterima'); // Hanya yang sudah diterima RS

        if ($selected_rs) {
            $distQuery->where('distributions.nama_rs', $selected_rs);
        }

        $donationsDist = $distQuery->select('donations.nama_alkes', DB::raw('SUM(distributions.jumlah_distribusi) as total_masuk'))
            ->groupBy('donations.nama_alkes')
            ->get()
            ->pluck('total_masuk', 'nama_alkes');

        // 3. Base Query untuk Repairs
        $query = Repair::query();
        if ($selected_rs) { $query->where('nama_rs', $selected_rs); }
        if ($selected_kategori) { $query->where('kategori', $selected_kategori); }

        $totalData = $query->count();

        // --- A. DATA CHART ---
        $statusData = (clone $query)->select('status_perbaikan', DB::raw('count(*) as total'))
            ->where('status_perbaikan', '!=', '-')->groupBy('status_perbaikan')->get();

        $responData = (clone $query)->whereNotNull('nama_penyedia')
            ->where('grade_kerusakan', '!=', 'Bisa Dipakai')->where('status_perbaikan', '!=', 'Bisa Dipakai')
            ->select('respon_penyedia', DB::raw('count(*) as total'))->groupBy('respon_penyedia')->get();

        $totalWithVendor = $responData->sum('total');

        $gradeData = (clone $query)->select('grade_kerusakan', DB::raw('count(*) as total'))
            ->groupBy('grade_kerusakan')->get();

        // --- B. RINGKASAN INVENTARIS (SINKRON KE DISTRIBUSI) ---
        $alkesSummary = (clone $query)
            ->select('nama_alkes', 
                DB::raw('count(*) as jumlah'),
                DB::raw("SUM(CASE WHEN status_perbaikan = 'Bisa Dipakai' THEN 1 ELSE 0 END) as bisa_dipakai"),
                DB::raw("SUM(CASE WHEN status_perbaikan = 'Dalam Proses Perbaikan' THEN 1 ELSE 0 END) as proses"),
                DB::raw("SUM(CASE WHEN status_perbaikan = 'Harus di Ganti (BAP)' THEN 1 ELSE 0 END) as ganti")
            )
            ->groupBy('nama_alkes')->orderBy('jumlah', 'desc')->get()
            ->map(function($item) use ($donationsDist) {
                // Ambil jumlah donasi yang SUDAH DITERIMA oleh RS ini
                $item->total_donasi = $donationsDist[$item->nama_alkes] ?? 0;
                
                // Kebutuhan = Jumlah yang harus diganti (BAP) - Donasi yang sudah sampai
                $kebutuhan = $item->ganti - $item->total_donasi;
                $item->kebutuhan = $kebutuhan > 0 ? $kebutuhan : 0;
                
                return $item;
            });

        return view('dashboard.index', compact(
            'list_rs', 'selected_rs', 'list_kategori', 'selected_kategori',
            'totalData', 'totalWithVendor', 'statusData', 'responData', 'gradeData',
            'alkesSummary'
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