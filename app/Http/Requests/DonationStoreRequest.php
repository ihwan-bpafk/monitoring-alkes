<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class DonationStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Berdasarkan Controller asli, hanya role 1 dan 2 yang bisa store
        return Auth::check() && in_array(Auth::user()->role, [1, 2]);
    }

    protected function failedAuthorization()
    {
        throw new HttpResponseException(
            redirect()->back()->with('error', 'tidak memiliki akses!')
        );
    }

    public function rules(): array
    {
        return [
            'pemberi_donasi' => ['required', 'string'],
            'nama_alkes'     => ['required', 'string'],
            'merek'          => ['nullable', 'string'],
            'jumlah_donasi'  => ['required', 'integer', 'min:1'],
            'diterima_oleh'  => ['nullable', 'string'],
            'status_akhir'   => ['nullable', 'string'],
        ];
    }
}
