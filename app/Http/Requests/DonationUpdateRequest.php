<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\Donation;
use Illuminate\Support\Facades\Auth;

class DonationUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check() && in_array(Auth::user()->role, [1, 2]);
    }

    protected function failedAuthorization()
    {
        throw new HttpResponseException(
            redirect()->back()->with('error', 'Tidak memiliki akses!')
        );
    }

    public function rules(): array
    {
        // Mendapatkan ID dari route (bisa berupa {id} atau {donation})
        $donationId = $this->route('id') ?? $this->route('donation');
        $donation = Donation::findOrFail($donationId);
        
        // Logika bisnis: Jumlah baru tidak boleh lebih kecil dari yang sudah keluar (distribusi)
        $sudahDistribusi = $donation->jumlah_donasi - $donation->sisa_stok;

        return [
            'jumlah_donasi' => ['required', 'integer', 'min:' . $sudahDistribusi],
            'status_akhir'  => ['nullable', 'string'],
            'catatan'       => ['nullable', 'string'],
            'bencana_id'    => ['nullable', 'exists:bencanas,id'],
        ];
    }

    public function messages(): array
    {
        $donationId = $this->route('id') ?? $this->route('donation');
        $donation = Donation::find($donationId);
        $sudahDistribusi = $donation ? ($donation->jumlah_donasi - $donation->sisa_stok) : 0;

        return [
            'jumlah_donasi.min' => 'Gagal! Alat sudah terdistribusi ' . $sudahDistribusi . ' unit, jumlah total tidak boleh kurang dari itu.',
        ];
    }
}
