<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FasyankesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Bersihkan tabel Fasyankes
        \App\Models\Fasyankes::truncate();

        $sumateraFasyankes = [
            'RSUD Datu Beru',
            'RSUD dr. Fauziah Bireuen',
            'RSUD dr. Zubir Mahmud Idi',
            'RSUD H. Sahudin Kutacane',
            'RSUD Langsa',
            'RSUD Muda Sedia Aceh Tamiang',
            'RSUD Muyang Kute',
            'RSUD Sultan Abdul Aziz Syah',
            'RSUD Tanjung Pura'
        ];

        foreach ($sumateraFasyankes as $fasyankes) {
            \App\Models\Fasyankes::create([
                'nama_fasyankes' => $fasyankes,
                'bencana_id' => 1,
                'jenis' => 'RSUD'
            ]);
        }

        $nttFasyankes = [
            'RSUD Ruteng',
            'RSUD Mbay',
            'RSUD TC Hillers Maumere',
            'RSUD Ende',
            'RSUD Bajawa',
            'RSUD Larantuka',
            'RSUD Borong',
            'RSUD Komodo',
            'RSUD Lewoleba',
        ];

        foreach ($nttFasyankes as $fasyankes) {
            \App\Models\Fasyankes::create([
                'nama_fasyankes' => $fasyankes,
                'bencana_id' => 2,
                'jenis' => 'RSUD'
            ]);
        }
    }
}
