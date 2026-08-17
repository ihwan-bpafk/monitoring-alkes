<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BencanaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('bencanas')->insert([
            'nama_bencana' => 'Bencana NTT',
            'lokasi' => 'Nusa Tenggara Timur',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
