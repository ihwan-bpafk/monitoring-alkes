<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class RepairRequest extends FormRequest
{
    /**
     * Tentukan apakah user memiliki akses ke request ini.
     * Sesuai controller asli: HANYA role 1 yang boleh create/update
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->role === 1;
    }

    /**
     * Custom response ketika authorization gagal (menyamai controller asli)
     */
    protected function failedAuthorization()
    {
        throw new HttpResponseException(
            redirect()->back()->with('error', 'tidak memiliki akses!')
        );
    }

    /**
     * Aturan validasi untuk proses input dan update data perbaikan.
     */
    public function rules(): array
    {
        return [
            'input_by'          => ['nullable', 'string'],
            'tanggal_input'     => ['nullable', 'date'],
            'nama_rs'           => ['nullable', 'string'],
            'lokasi'            => ['nullable', 'string'],
            'nama_alkes'        => ['nullable', 'string'],
            'serial_number'     => ['nullable', 'string'],
            'nama_penyedia'     => ['nullable', 'string'],
            'kategori'          => ['nullable', 'string'],
            'merek'             => ['nullable', 'string'],
            'tipe_model'        => ['nullable', 'string'],
            'grade_kerusakan'   => ['nullable', 'string'],
            'kondisi_kontrak'   => ['nullable', 'string'],
            'status_perbaikan'  => ['nullable', 'string'],
            'komponen'          => ['nullable', 'string'],
            'respon_penyedia'   => ['nullable', 'string'],
            'tindakan_penyedia' => ['nullable', 'string'],
            'keterangan_lain'   => ['nullable', 'string'],
            'rtl'               => ['nullable', 'string'],
            
            // Tambahan untuk update
            'petugas'           => ['nullable', 'string'],
            'keterangan_log'    => ['nullable', 'string'],

            'file_bap'          => ['nullable', 'file'],
            'foto_kondisi'      => ['nullable', 'array'],
            'foto_kondisi.*'    => ['nullable', 'image'],
        ];
    }
}
