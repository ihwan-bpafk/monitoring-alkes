<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RepairFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search'           => ['nullable', 'string'],
            'nama_rs'          => ['nullable', 'string'],
            'nama_alkes'       => ['nullable', 'string'],
            'kategori'         => ['nullable', 'string'],
            'status_perbaikan' => ['nullable', 'string'],
            'grade_kerusakan'  => ['nullable', 'string'],
            'respon_penyedia'  => ['nullable', 'string'],
        ];
    }
}
