<?php

namespace App\Models;

use App\Models\Scopes\BencanaScope;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Donation extends Model
{
    protected static function booted()
    {
        static::addGlobalScope(new BencanaScope);
    }

    public function bencana()
    {
        return $this->belongsTo(Bencana::class);
    }
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
