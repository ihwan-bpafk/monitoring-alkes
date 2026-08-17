<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserNTTSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hospitals = [
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

        foreach ($hospitals as $rs) {
            // Hapus kata 'RSUD ' dan spasi, lalu jadikan huruf kecil untuk password
            $shortPassword = str_replace(' ', '', strtolower(str_replace('RSUD ', '', $rs)));
            
            \App\Models\User::updateOrCreate(
                ['username' => $rs],
                [
                    'name' => $rs,
                    'password' => \Illuminate\Support\Facades\Hash::make($shortPassword),
                    'role' => 2,
                    'bencana_id' => 2,
                ]
            );
        }
    }
}
