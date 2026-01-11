<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonationLog extends Model
{
    use HasFactory;

    // Kolom yang boleh diisi
    protected $fillable = [
        'donation_id',
        'status',
        'diupdate_oleh',
        'catatan'
    ];

    // Relasi balik ke Donation
    public function donation()
    {
        return $this->belongsTo(Donation::class);
    }
}