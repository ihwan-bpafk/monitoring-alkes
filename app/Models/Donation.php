<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    protected $guarded = [];

    // Relasi untuk melihat riwayat tracking
    public function logs()
    {
        return $this->hasMany(DonationLog::class)->latest();
    }
    // Tambahkan di dalam class Donation
    public function distributions()
    {
        return $this->hasMany(Distribution::class);
    }
}