<?php

namespace App\Http\Controllers;

use App\Http\Requests\DonationFilterRequest;
use App\Http\Requests\DonationStoreRequest;
use App\Http\Requests\DonationUpdateRequest;
use App\Services\DonationService;
use App\Exports\DonationsExport;
use Illuminate\Support\Facades\Auth;

class DonationController extends Controller
{
    protected DonationService $donationService;

    public function __construct(DonationService $donationService)
    {
        $this->donationService = $donationService;
    }

    public function index(DonationFilterRequest $request)
    {
        $filters = $request->validated();

        $dropdowns = $this->donationService->getFilterOptions();
        $donations = $this->donationService->getFilteredDonations($filters, true);

        return view('donations.index', array_merge(compact('donations'), $dropdowns));
    }

    public function store(DonationStoreRequest $request)
    {
        $data = $request->validated();
        $userName = Auth::user()->name;

        $this->donationService->createDonation($data, $userName);

        return redirect()->back()->with('success', 'Data Donasi berhasil disimpan!');
    }

    public function updateStatus(DonationUpdateRequest $request, string $id)
    {
        $data = $request->validated();
        $userName = Auth::user()->name;

        $this->donationService->updateDonationStatus($id, $data, $userName);

        return redirect()->back()->with('success', 'Data donasi dan stok berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        if (!Auth::check() || !in_array(Auth::user()->role, [1, 2])) {
            return redirect()->back()->with('error', 'tidak memiliki akses!');
        }

        $this->donationService->deleteDonation($id);

        return redirect()->route('donations.index')->with('success', 'Master Donasi dan seluruh riwayat distribusinya berhasil dihapus.');
    }

    public function exportExcel(DonationFilterRequest $request) 
    {
        $filters = $request->validated();
        $fileName = 'Laporan_Donasi_BPAFK_' . now()->format('Ymd_His') . '.xlsx';

        return (new DonationsExport($filters))->download($fileName);
    }
}