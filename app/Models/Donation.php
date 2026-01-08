<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    protected $guarded = []; // Mengizinkan semua kolom

    // Opsional: Jika ingin otomatis sisa_stok = jumlah_donasi saat create
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->sisa_stok = $model->jumlah_donasi;
        });
    }
}