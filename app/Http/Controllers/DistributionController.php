<?php

namespace App\Http\Controllers;

use App\Http\Requests\DistributionFilterRequest;
use App\Http\Requests\DistributionStoreRequest;
use App\Http\Requests\DistributionUpdateRequest;
use App\Services\DistributionService;
use App\Exports\DistributionsExport;
use Exception;
use Illuminate\Support\Facades\Auth;

class DistributionController extends Controller
{
    protected DistributionService $distributionService;

    public function __construct(DistributionService $distributionService)
    {
        $this->distributionService = $distributionService;
    }

    public function index(DistributionFilterRequest $request)
    {
        $filters = $request->validated();

        $dropdowns = $this->distributionService->getFilterOptions();
        $distributions = $this->distributionService->getFilteredDistributions($filters, true);

        return view('distributions.index', array_merge(compact('distributions'), $dropdowns));
    }

    public function store(DistributionStoreRequest $request)
    {
        $data = $request->validated();
        $file = $request->file('file_ba');
        $userName = Auth::user()->name;

        try {
            $this->distributionService->createDistribution($data, $file, $userName);
            return redirect()->back()->with('success', 'Data distribusi berhasil disimpan.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function update(DistributionUpdateRequest $request, string $id)
    {
        $data = $request->validated();
        $userName = Auth::user()->name;

        try {
            $this->distributionService->updateDistribution($id, $data, $userName);
            return redirect()->back()->with('success', 'Data distribusi berhasil diperbarui.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        if (!Auth::check() || !in_array(Auth::user()->role, [1, 2])) {
            return redirect()->back()->with('error', 'tidak memiliki akses!');
        }

        $userName = Auth::user()->name;
        $this->distributionService->deleteDistribution($id, $userName);

        return redirect()->back()->with('success', 'Distribusi dibatalkan dan stok telah dikembalikan ke gudang.');
    }

    public function exportExcel(DistributionFilterRequest $request) 
    {
        $filters = $request->validated();
        $fileName = 'Laporan_Distribusi_Alkes_' . now()->format('Ymd_His') . '.xlsx';

        return (new DistributionsExport($filters))->download($fileName);
    }
}