<?php

namespace App\Http\Controllers;

use App\Http\Requests\RepairFilterRequest;
use App\Http\Requests\RepairRequest;
use App\Services\RepairService;
use App\Exports\RepairExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;

class RepairController extends Controller
{
    protected RepairService $repairService;

    public function __construct(RepairService $repairService)
    {
        $this->repairService = $repairService;
    }

    public function index(RepairFilterRequest $request)
    {
        $filters = $request->validated();
        
        $dropdowns = $this->repairService->getFilterOptions();
        $repairs = $this->repairService->getFilteredRepairs($filters, true, 'nama_alkes', 'asc');

        return view('repairs.index', array_merge(compact('repairs'), $dropdowns));
    }

    public function show(string $id)
    {
        $repair = $this->repairService->getRepairWithHistory($id);

        return response()->json($repair);
    }

    public function store(RepairRequest $request)
    {
        // Validasi dan auth role sudah dilakukan di RepairRequest

        $data = $request->validated();
        $fileBap = $request->file('file_bap');
        $fotoKondisi = $request->file('foto_kondisi');

        $this->repairService->createRepair($data, $fileBap, $fotoKondisi);

        return redirect()->back()->with('success', 'Laporan berhasil disimpan!');
    }

    public function updateStatus(RepairRequest $request, string $id)
    {
        // Validasi dan auth role sudah dilakukan di RepairRequest

        $data = $request->validated();
        $fileBap = $request->file('file_bap');
        $fotoKondisi = $request->file('foto_kondisi');
        $petugas = $request->input('petugas');
        $keteranganLog = $request->input('keterangan_log');

        $this->repairService->updateRepair($id, $data, $fileBap, $fotoKondisi, $petugas, $keteranganLog);

        return redirect()->back()->with('success', 'Seluruh data unit dan progres perbaikan berhasil diperbarui!');
    }

    public function destroy(string $id)
    {
        // Otorisasi penghapusan langsung di sini (bisa dipindah ke policy/request jika diinginkan)
        if (Auth::user()->role !== 1) {
            return redirect()->back()->with('error', 'tidak memiliki akses!');
        }

        $this->repairService->deleteRepair($id);

        return redirect()->back()->with('success', 'Data laporan dan file terkait berhasil dihapus.');
    }

    public function exportExcel(RepairFilterRequest $request)
    {
        // Parameter export diambil langsung dari validasi filter
        $filters = [
            'nama_rs'          => $request->input('nama_rs'),
            'nama_alkes'       => $request->input('nama_alkes'),
            'status_perbaikan' => $request->input('status_perbaikan'),
            'grade_kerusakan'  => $request->input('grade_kerusakan'),
            'respon_penyedia'  => $request->input('respon_penyedia'),
        ];

        $fileName = 'Laporan_BPAFK_' . date('Ymd_His') . '.xlsx';
        return Excel::download(new RepairExport($filters), $fileName);
    }

    public function reportPage()
    {
        $dropdowns = $this->repairService->getFilterOptions();
        // Menggunakan latest() -> ini diterjemahkan ke getFilteredRepairs tanpa search/filter
        $repairs = $this->repairService->getFilteredRepairs([], false, 'latest');

        return view('repairs.report', array_merge(compact('repairs'), $dropdowns));
    }

    public function previewExport(RepairFilterRequest $request)
    {
        $filters = $request->validated();
        
        $repairs = $this->repairService->getFilteredRepairs($filters, false, 'latest');

        return view('repairs._report_rows', compact('repairs'))->render();
    }
}