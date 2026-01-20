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
        // 1. Data untuk Dropdown Filter
        $list_rs = Repair::whereNotNull('nama_rs')->distinct()->orderBy('nama_rs', 'asc')->pluck('nama_rs');
        $list_kategori = Repair::whereNotNull('kategori')->distinct()->orderBy('kategori', 'asc')->pluck('kategori');

        $selected_rs = $request->query('nama_rs');
        $selected_kategori = $request->query('kategori');

        // --- 2. LOGIKA DISTRIBUSI (SUM DENGAN STATUS BERBEDA) ---
        $distQuery = Distribution::query()
            ->join('donations', 'distributions.donation_id', '=', 'donations.id');

        if ($selected_rs) {
            $distQuery->where('distributions.nama_rs', $selected_rs);
        }

        $donationsDist = $distQuery->select(
                'donations.nama_alkes',
                // Total Gabungan: Alokasi + Dikirim + Diterima
                DB::raw("SUM(distributions.jumlah_distribusi) as total_pemenuhan_gabungan"),
                DB::raw("SUM(CASE WHEN distributions.status = 'Alokasi' THEN distributions.jumlah_distribusi ELSE 0 END) as total_alokasi")
            )
            ->groupBy('donations.nama_alkes')
            ->get()
            ->keyBy('nama_alkes');

        // --- 3. BASE QUERY REPAIRS ---
        $query = Repair::query();
        if ($selected_rs) { $query->where('nama_rs', $selected_rs); }
        if ($selected_kategori) { $query->where('kategori', $selected_kategori); }

        $totalData = $query->count();

        // (Data Chart tetap sama seperti sebelumnya...)
        $statusData = (clone $query)->select('status_perbaikan', DB::raw('count(*) as total'))->where('status_perbaikan', '!=', '-')->groupBy('status_perbaikan')->get();
        $responData = (clone $query)->whereNotNull('respon_penyedia')->where('grade_kerusakan', '!=', 'Bisa Dipakai')->select('respon_penyedia', DB::raw('count(*) as total'))->groupBy('respon_penyedia')->get();
        $totalWithVendor = $responData->sum('total');
        $gradeData = (clone $query)->select('grade_kerusakan', DB::raw('count(*) as total'))->groupBy('grade_kerusakan')->get();

        // --- 4. RINGKASAN INVENTARIS ---
        $alkesSummary = (clone $query)
            ->select('nama_alkes', 
                DB::raw('count(*) as jumlah_repair'),
                DB::raw("SUM(CASE WHEN status_perbaikan = 'Bisa Dipakai' THEN 1 ELSE 0 END) as bisa_dipakai"),
                DB::raw("SUM(CASE WHEN status_perbaikan = 'Dalam Proses Perbaikan' THEN 1 ELSE 0 END) as proses"),
                DB::raw("SUM(CASE WHEN status_perbaikan = 'Harus di Ganti (BAP)' THEN 1 ELSE 0 END) as ganti")
            )
            ->groupBy('nama_alkes')
            ->orderBy('nama_alkes', 'asc')
            ->get()
            ->map(function($item) use ($donationsDist) {
                $distData = $donationsDist[$item->nama_alkes] ?? null;
                
                $item->total_pemenuhan = $distData ? $distData->total_pemenuhan_gabungan : 0;
                $item->total_alokasi = $distData ? $distData->total_alokasi : 0;
                
                // Total Unit = Aset Lama + Alokasi Baru
                $item->jumlah = $item->jumlah_repair + $item->total_alokasi;
                
                // RUMUS BARU: Kebutuhan = BAP - (Alokasi + Distribusi)
                $kebutuhan = $item->ganti - $item->total_pemenuhan;
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
        // 1. Ambil Parameter Filter
        $selected_rs = $request->query('nama_rs');
        $selected_kategori = $request->query('kategori');

        // 2. Query Dasar untuk Repair (Ringkasan Kondisi Alat)
        $query = \App\Models\Repair::query();
        
        if ($selected_rs) {
            $query->where('nama_rs', $selected_rs);
        }
        if ($selected_kategori) {
            $query->where('kategori', $selected_kategori);
        }

        // 3. LOGIKA SINKRONISASI: Ambil Donasi yang sudah berstatus 'Diterima RS'
        // Kita samakan persis dengan logic di function index()
        $distQuery = \App\Models\Distribution::query()
            ->join('donations', 'distributions.donation_id', '=', 'donations.id')
            ->where('distributions.status', 'Diterima RS');

        if ($selected_rs) {
            $distQuery->where('distributions.nama_rs', $selected_rs);
        }

        $donationsDist = $distQuery->select('donations.nama_alkes', \DB::raw('SUM(distributions.jumlah_distribusi) as total_masuk'))
            ->groupBy('donations.nama_alkes')
            ->get()
            ->pluck('total_masuk', 'nama_alkes');

        // 4. Eksekusi Ringkasan Inventaris
        $alkesSummary = $query->select('nama_alkes', 
                \DB::raw('count(*) as jumlah'),
                \DB::raw("SUM(CASE WHEN status_perbaikan = 'Bisa Dipakai' THEN 1 ELSE 0 END) as bisa_dipakai"),
                \DB::raw("SUM(CASE WHEN status_perbaikan = 'Dalam Proses Perbaikan' THEN 1 ELSE 0 END) as proses"),
                \DB::raw("SUM(CASE WHEN status_perbaikan = 'Harus di Ganti (BAP)' THEN 1 ELSE 0 END) as ganti")
            )
            ->groupBy('nama_alkes')
            ->orderBy('jumlah', 'desc')
            ->get()
            ->map(function($item) use ($donationsDist) {
                // Gunakan data dari distributionsDist (yang berstatus Diterima RS)
                $item->total_donasi = $donationsDist[$item->nama_alkes] ?? 0;
                
                // Rumus Kebutuhan
                $kebutuhan = $item->ganti - $item->total_donasi;
                $item->kebutuhan = $kebutuhan > 0 ? $kebutuhan : 0;
                
                return $item;
            });

        // 5. Proses Download
        $nama_file = 'Rekap_Alkes_' . ($selected_rs ?? 'Semua_RS') . '_' . date('Y-m-d') . '.xlsx';
        
        return Excel::download(new AlkesSummaryExport($alkesSummary), $nama_file);
    }
}