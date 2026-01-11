<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Distribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'donation_id', 'nama_rs', 'jumlah_distribusi', 
        'tanggal_distribusi', 'status', 'petugas_pengirim', 'keterangan'
    ];

    // Relasi: Setiap distribusi merujuk pada satu data donasi
    public function donation()
    {
        return $this->belongsTo(Donation::class);
    }
}