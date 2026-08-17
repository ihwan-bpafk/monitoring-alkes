<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class DistributionStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
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
            'donation_id'        => ['required'],
            'nama_rs'            => ['required', 'string'],
            'jumlah_distribusi'  => ['required', 'integer', 'min:1'],
            'status'             => ['required', 'string'],
            'tanggal_distribusi' => ['nullable', 'date'],
            'keterangan'         => ['nullable', 'string'],
        ];
    }
}
