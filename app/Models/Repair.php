<?php

namespace App\Models;

use App\Models\Scopes\BencanaScope;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Repair extends Model
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

    // WAJIB: Mengubah string JSON di DB menjadi array PHP otomatis
    protected $casts = [
        'foto_kondisi' => 'array',
    ];

    public function histories()
    {
        return $this->hasMany(RepairHistory::class)->orderBy('created_at', 'desc');
    }
}
