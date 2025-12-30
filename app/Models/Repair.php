<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Repair extends Model
{
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