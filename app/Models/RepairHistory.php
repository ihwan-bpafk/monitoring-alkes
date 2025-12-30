<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RepairHistory extends Model
{
    protected $fillable = ['repair_id', 'status_perbaikan', 'keterangan_perubahan', 'user_nama'];

    public function repair()
    {
        return $this->belongsTo(Repair::class);
    }
}