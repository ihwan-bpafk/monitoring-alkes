<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bencana extends Model
{
    use HasFactory;

    // Mendefinisikan nama tabel secara eksplisit
    protected $table = 'bencanas';

    // Kolom yang diizinkan untuk diisi (Mass Assignment)
    protected $fillable = [
        'nama_bencana',
        'lokasi',
        'is_active',
    ];

    // Relasi: Satu Bencana memiliki banyak laporan perbaikan alat
    public function repairs()
    {
        return $this->hasMany(Repair::class);
    }

    // Relasi: Satu Bencana memiliki banyak donasi
    public function donations()
    {
        return $this->hasMany(Donation::class);
    }
}
