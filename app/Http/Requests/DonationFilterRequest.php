<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DonationFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'filter_pemberi' => ['nullable', 'string'],
            'filter_alkes'   => ['nullable', 'string'],
            'filter_petugas' => ['nullable', 'string'],
            'filter_stok'    => ['nullable', 'string', 'in:tersedia,habis'],
        ];
    }
}
