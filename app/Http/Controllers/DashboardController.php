<?php

namespace App\Http\Controllers;

use App\Http\Requests\DashboardFilterRequest;
use App\Services\DashboardService;
use App\Exports\AlkesSummaryExport;
use Maatwebsite\Excel\Facades\Excel;

class DashboardController extends Controller
{
    protected DashboardService $dashboardService;

    /**
     * Inject Dependency DashboardService
     */
    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Menampilkan Dashboard Utama dengan Filter RS dan Kategori
     */
    public function index(DashboardFilterRequest $request)
    {
        $selected_rs = $request->input('nama_rs');
        $selected_kategori = $request->input('kategori');

        $filters = $this->dashboardService->getFilterOptions();
        $chartData = $this->dashboardService->getChartData($selected_rs, $selected_kategori);
        $alkesSummary = $this->dashboardService->getAlkesSummary($selected_rs, $selected_kategori);

        return view('dashboard.index', array_merge([
            'selected_rs' => $selected_rs,
            'selected_kategori' => $selected_kategori,
            'list_rs' => $filters['list_rs'],
            'list_kategori' => $filters['list_kategori'],
            'alkesSummary' => $alkesSummary
        ], $chartData));
    }

    /**
     * Export data ke Excel
     */
    public function exportExcel(DashboardFilterRequest $request)
    {
        $selected_rs = $request->input('nama_rs');
        $selected_kategori = $request->input('kategori');

        // Manfaatkan method yg sama di service untuk mencegah duplikasi (DRY)
        $alkesSummary = $this->dashboardService->getAlkesSummary($selected_rs, $selected_kategori, false);
        
        $nama_file = 'Rekap_Alkes_' . ($selected_rs ?? 'Semua_RS') . '_' . date('Y-m-d') . '.xlsx';

        return Excel::download(new AlkesSummaryExport($alkesSummary), $nama_file);
    }
}
