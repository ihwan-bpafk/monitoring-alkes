<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class Fasyankes extends Model
{
    use HasFactory;

    protected $fillable = [
        'bencana_id',
        'nama_fasyankes',
        'lokasi',
        'jenis',
    ];

    /**
     * Scope Global: Filter otomatis berdasarkan sesi Bencana aktif
     */
    protected static function booted()
    {
        static::addGlobalScope('bencana', function (Builder $builder) {
            $bencanaId = session('active_bencana_id');
            // Jika ada sesi bencana yang aktif, lakukan filter
            if ($bencanaId) {
                $builder->where('bencana_id', $bencanaId);
            }
        });
    }

    public function bencana()
    {
        return $this->belongsTo(Bencana::class);
    }
}
