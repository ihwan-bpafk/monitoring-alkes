<?php

namespace App\Services;

use App\Models\Repair;
use Illuminate\Support\Facades\Storage;

class RepairService
{
    /**
     * Mengambil opsi filter untuk dropdown (dinamis dari database)
     */
    public function getFilterOptions(): array
    {
        return [
            'list_rs'       => \App\Models\Fasyankes::orderBy('nama_fasyankes', 'asc')->pluck('lokasi', 'nama_fasyankes'),
            'list_alkes'    => Repair::whereNotNull('nama_alkes')->distinct()->orderBy('nama_alkes', 'asc')->pluck('nama_alkes'),
            'list_alkes_master' => \App\Models\Alkes::orderBy('nama_alkes', 'asc')->pluck('nama_alkes'),
            'list_kategori' => Repair::whereNotNull('kategori')->distinct()->orderBy('kategori', 'asc')->pluck('kategori'),
            'list_grade'    => Repair::whereNotNull('grade_kerusakan')->distinct()->orderBy('grade_kerusakan', 'asc')->pluck('grade_kerusakan'),
            'list_status'   => Repair::whereNotNull('status_perbaikan')->distinct()->orderBy('status_perbaikan', 'asc')->pluck('status_perbaikan'),
            'list_respon'   => Repair::whereNotNull('respon_penyedia')->distinct()->orderBy('respon_penyedia', 'asc')->pluck('respon_penyedia'),
        ];
    }

    /**
     * Mengambil data perbaikan dengan filter, pencarian, dan opsi pagination
     */
    public function getFilteredRepairs(array $filters, bool $paginate = true, string $orderBy = 'nama_alkes', string $direction = 'asc', ?int $limit = null)
    {
        $query = Repair::query();

        $query->when($filters['search'] ?? null, function ($q, $search) {
            return $q->where(function($sub) use ($search) {
                $sub->where('nama_rs', 'like', '%' . $search . '%')
                    ->orWhere('nama_alkes', 'like', '%' . $search . '%')
                    ->orWhere('sn', 'like', '%' . $search . '%')
                    ->orWhere('lokasi', 'like', '%' . $search . '%');
            });
        })
        ->when($filters['nama_rs'] ?? null, fn($q, $rs) => $q->where('nama_rs', 'like', '%' . $rs . '%'))
        ->when($filters['nama_alkes'] ?? null, fn($q, $alkes) => $q->where('nama_alkes', 'like', '%' . $alkes . '%'))
        ->when($filters['kategori'] ?? null, fn($q, $kat) => $q->where('kategori', $kat))
        ->when($filters['grade_kerusakan'] ?? null, fn($q, $grade) => $q->where('grade_kerusakan', $grade))
        ->when($filters['status_perbaikan'] ?? null, fn($q, $status) => $q->where('status_perbaikan', $status))
        ->when($filters['respon_penyedia'] ?? null, fn($q, $respon) => $q->where('respon_penyedia', $respon));

        if ($orderBy === 'latest') {
            $query->latest();
        } else {
            $query->orderBy($orderBy, $direction);
        }

        if ($limit && !$paginate) {
            $query->limit($limit);
        }

        return $paginate ? $query->paginate(10)->withQueryString() : $query->get();
    }

    /**
     * Mengambil data perbaikan tunggal beserta riwayat historisnya
     */
    public function getRepairWithHistory(int $id)
    {
        return Repair::with(['histories' => fn($q) => $q->latest()])->findOrFail($id);
    }

    /**
     * Membuat data perbaikan baru beserta file upload dan riwayat log
     */
    public function createRepair(array $data, $fileBap = null, ?array $fotoKondisi = [])
    {
        // 1. Handle File BAP
        if ($fileBap && $fileBap->isValid()) {
            $data['file_bap'] = $fileBap->store('bap', 'public');
        }

        // 2. Handle Foto Kondisi (Multiple)
        $paths = [];
        if (!empty($fotoKondisi)) {
            foreach ($fotoKondisi as $file) {
                if ($file->isValid()) {
                    $paths[] = $file->store('repairs', 'public');
                }
            }
            if (!empty($paths)) {
                $data['foto_kondisi'] = $paths;
            }
        }

        // 3. Simpan Data Utama
        $repair = Repair::create($data);

        // 4. Catat ke History
        $repair->histories()->create([
            'status_perbaikan' => $data['status_perbaikan'] ?? 'Laporan Diterima',
            'keterangan_perubahan' => 'Laporan awal berhasil dibuat.',
            'user_nama' => $data['input_by'] ?? 'Sistem'
        ]);

        return $repair;
    }

    /**
     * Memperbarui data perbaikan, file terkait, serta mencatat log perubahan
     */
    public function updateRepair(int $id, array $data, $fileBap = null, ?array $fotoKondisi = [], ?string $petugas = null, ?string $keteranganLog = null)
    {
        $repair = Repair::findOrFail($id);
        
        // Simpan data lama untuk perbandingan log
        $komponenLama = $repair->komponen;
        $statusLama = $repair->status_perbaikan;

        // Update Field Text (Gunakan fill jika model sudah di-set guarded/fillable)
        // Kita menggunakan array assignment manual untuk memastikan kompatibilitas persis seperti aslinya
        $repair->nama_rs = $data['nama_rs'] ?? $repair->nama_rs;
        $repair->lokasi = $data['lokasi'] ?? $repair->lokasi;
        $repair->nama_alkes = $data['nama_alkes'] ?? $repair->nama_alkes;
        $repair->merek = $data['merek'] ?? $repair->merek;
        $repair->tipe_model = $data['tipe_model'] ?? $repair->tipe_model;
        $repair->serial_number = $data['serial_number'] ?? $repair->serial_number;
        
        $repair->input_by = $data['input_by'] ?? $repair->input_by;
        $repair->kategori = $data['kategori'] ?? $repair->kategori;
        $repair->kondisi_kontrak = $data['kondisi_kontrak'] ?? $repair->kondisi_kontrak;
        $repair->grade_kerusakan = $data['grade_kerusakan'] ?? $repair->grade_kerusakan;
        $repair->status_perbaikan = $data['status_perbaikan'] ?? $repair->status_perbaikan;
        
        $repair->nama_penyedia = $data['nama_penyedia'] ?? $repair->nama_penyedia;
        $repair->komponen = $data['komponen'] ?? $repair->komponen;
        $repair->respon_penyedia = $data['respon_penyedia'] ?? $repair->respon_penyedia;
        $repair->tindakan_penyedia = $data['tindakan_penyedia'] ?? $repair->tindakan_penyedia;
        $repair->rtl = $data['rtl'] ?? $repair->rtl;
        $repair->keterangan_lain = $data['keterangan_lain'] ?? $repair->keterangan_lain;

        // Handle File BAP
        if ($fileBap && $fileBap->isValid()) {
            if ($repair->file_bap) { 
                Storage::disk('public')->delete($repair->file_bap); 
            }
            $repair->file_bap = $fileBap->store('bap', 'public');
        }

        // Handle Foto Kondisi
        if (!empty($fotoKondisi)) {
            $currentPhotos = is_array($repair->foto_kondisi) ? $repair->foto_kondisi : []; 
            foreach ($fotoKondisi as $file) {
                if ($file->isValid()) {
                    $currentPhotos[] = $file->store('repairs', 'public');
                }
            }
            $repair->foto_kondisi = $currentPhotos;
        }

        $repair->save();

        // Buat Pesan History yang Informatif
        $pesanHistory = $keteranganLog ?? 'Pembaruan data unit dan progres.';

        if ($komponenLama != ($data['komponen'] ?? null)) {
            $pesanHistory .= " (Komponen: " . ($data['komponen'] ?? '-') . ")";
        }
        
        if ($statusLama != ($data['status_perbaikan'] ?? null)) {
            $pesanHistory .= " [Status berubah menjadi: " . ($data['status_perbaikan'] ?? '-') . "]";
        }

        $repair->histories()->create([
            'status_perbaikan' => $data['status_perbaikan'] ?? $repair->status_perbaikan,
            'keterangan_perubahan' => $pesanHistory,
            'user_nama' => $petugas ?? 'Sistem',
        ]);

        return $repair;
    }

    /**
     * Menghapus data perbaikan beserta file fisiknya
     */
    public function deleteRepair(int $id)
    {
        $repair = Repair::findOrFail($id);

        if ($repair->foto_kondisi && is_array($repair->foto_kondisi)) {
            foreach ($repair->foto_kondisi as $foto) {
                Storage::disk('public')->delete($foto);
            }
        }

        if ($repair->file_bap) {
            Storage::disk('public')->delete($repair->file_bap);
        }

        return $repair->delete();
    }
}
