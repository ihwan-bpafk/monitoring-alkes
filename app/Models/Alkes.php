<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Alkes extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $fillable = ['nama_alkes'];
}
