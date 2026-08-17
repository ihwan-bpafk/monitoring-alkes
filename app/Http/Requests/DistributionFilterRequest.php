<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DistributionFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'filter_rs'      => ['nullable', 'string'],
            'filter_alkes'   => ['nullable', 'string'],
            'filter_pemberi' => ['nullable', 'string'],
            'filter_status'  => ['nullable', 'string'],
        ];
    }
}
