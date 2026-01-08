<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\Repair;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Imports\DonationImport;
use Maatwebsite\Excel\Facades\Excel;

class DonationController extends Controller
{
    /**
     * Menampilkan daftar donasi dengan fitur Filter & Sorting A-Z
     */
    public function index(Request $request)
    {
        // 1. Data untuk Dropdown Filter (Dinamis dari tabel donations)
        $list_rs = Donation::distinct()->whereNotNull('nama_rs')->orderBy('nama_rs', 'asc')->pluck('nama_rs');
        $list_donatur = Donation::distinct()->whereNotNull('donatur')->orderBy('donatur', 'asc')->pluck('donatur');
        $list_alkes_donasi = Donation::distinct()->whereNotNull('nama_alkes')->orderBy('nama_alkes', 'asc')->pluck('nama_alkes');

        // 2. Data untuk Modal Input (Diambil dari database perbaikan alat)
        $list_alkes = Repair::distinct()->whereNotNull('nama_alkes')->orderBy('nama_alkes', 'asc')->pluck('nama_alkes');

        // 3. Query Utama
        $query = Donation::query();

        // 4. Logika Filter (Rekap)
        $query->when($request->filter_rs, function ($q, $rs) {
            return $q->where('nama_rs', $rs);
        })
        ->when($request->filter_donatur, function ($q, $donatur) {
            return $q->where('donatur', $donatur);
        })
        ->when($request->filter_alkes, function ($q, $alkes) {
            return $q->where('nama_alkes', $alkes);
        });

        // 5. Eksekusi Data: Urutkan Nama Alat A-Z dan Paginasi
        $donations = $query->orderBy('nama_alkes', 'asc')
                           ->paginate(15)
                           ->withQueryString();

        return view('donations.index', compact(
            'donations', 
            'list_rs', 
            'list_donatur', 
            'list_alkes_donasi', 
            'list_alkes'
        ));
    }

    /**
     * Menyimpan data donasi baru
     */
    public function store(Request $request)
    {
        // Gunakan 'only' untuk mencegah error "Unknown column /donations"
        $data = $request->only([
            'input_by', 
            'nama_alkes', 
            'nama_rs', 
            'merek', 
            'tipe_model', 
            'jumlah_total_donasi', 
            'donatur', 
            'tanggal_terima_donatur', 
            'jumlah', 
            'status', 
            'keterangan_lain',
            'sisa_stok' // Nilai ini didapat dari kalkulasi JS di view
        ]);

        // Proses Upload File
        if ($request->hasFile('file_donasi')) {
            $file = $request->file('file_donasi');
            if ($file->isValid()) {
                $data['file_donasi'] = $file->store('donations', 'public');
            }
        }

        Donation::create($data);

        return redirect()->back()->with('success', 'Data Donasi & Stok berhasil ditambahkan!');
    }

    /**
     * Memperbarui data donasi
     */
    public function update(Request $request, $id)
    {
        $donation = Donation::findOrFail($id);

        $data = $request->only([
            'input_by', 'nama_alkes', 'nama_rs', 'merek', 'tipe_model', 
            'jumlah_total_donasi', 'donatur', 'tanggal_terima_donatur', 
            'jumlah', 'status', 'keterangan_lain', 'sisa_stok'
        ]);

        if ($request->hasFile('file_donasi')) {
            $file = $request->file('file_donasi');
            if ($file->isValid()) {
                // Hapus file fisik lama jika ada
                if ($donation->file_donasi && Storage::disk('public')->exists($donation->file_donasi)) {
                    Storage::disk('public')->delete($donation->file_donasi);
                }
                $data['file_donasi'] = $file->store('donations', 'public');
            }
        }

        $donation->update($data);

        return redirect()->back()->with('success', 'Perubahan data donasi berhasil disimpan!');
    }

    /**
     * Menghapus data donasi beserta filenya
     */
    public function destroy($id)
    {
        $donation = Donation::findOrFail($id);
        
        // Hapus file dari folder storage
        if ($donation->file_donasi && Storage::disk('public')->exists($donation->file_donasi)) {
            Storage::disk('public')->delete($donation->file_donasi);
        }
        
        $donation->delete();

        return redirect()->back()->with('success', 'Data donasi telah dihapus.');
    }

    public function import(Request $request) 
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv'
        ]);

        Excel::import(new DonationImport, $request->file('file_excel'));

        return redirect()->back()->with('success', 'Data Donasi Berhasil Diimport!');
    }
}