<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DashboardFilterRequest extends FormRequest
{
    /**
     * Tentukan apakah user memiliki akses ke request ini.
     */
    public function authorize(): bool
    {
        return true; // Asumsikan semua user terautentikasi dapat mengakses dashboard
    }

    /**
     * Aturan validasi untuk filter dashboard.
     */
    public function rules(): array
    {
        return [
            'nama_rs' => ['nullable', 'string'],
            'kategori' => ['nullable', 'string'],
        ];
    }
}
