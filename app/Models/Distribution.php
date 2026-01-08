<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Distribution extends Model
{
    protected $guarded = [];

    public function distribution()
    {
        return $this->belongsTo(Distribution::class);
    }
    public function donation()
    {
        // Pastikan nama modelnya benar (Donation)
        // Dan pastikan foreign key di tabel distributions bernama donation_id
        return $this->belongsTo(Donation::class);
    }
}