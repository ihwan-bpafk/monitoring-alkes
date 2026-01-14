<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // \App\Models\User::create([
        //     'name' => 'Administrator',
        //     'username' => 'admin',
        //     'password' => bcrypt('adminbpafk007'),
        // ]);
        // \App\Models\User::create([
        //     'name' => 'keslan',
        //     'username' => 'admin keslan',
        //     'password' => bcrypt('keslanadmin'),
        //     'role' => 2,
        // ]);
        // \App\Models\User::create([
        //     'name' => 'puskris',
        //     'username' => 'admin puskris',
        //     'password' => bcrypt('puskrisadmin'),
        //     'role' => 2,
        // ]);
        // \App\Models\User::create([
        //     'name' => 'Farmalkes',
        //     'username' => 'farmalkes',
        //     'password' => bcrypt('adminfarmalkes'),
        // ]);
        // \App\Models\User::create([
        //     'name' => 'RSUD Langsa',
        //     'username' => 'RSUD Langsa',
        //     'password' => bcrypt('RSUDLangsa'),
        // ]);
        // \App\Models\User::create([
        //     'name' => 'RSUP Adam Malik',
        //     'username' => 'RSUP Adam Malik',
        //     'password' => bcrypt('RSUPAdamMalik'),
        // ]);
        // \App\Models\User::create([
        //     'name' => 'RSUD Muda Sedia Aceh Tamiang',
        //     'username' => 'RSUD Muda Sedia Aceh Tamiang',
        //     'password' => bcrypt('RSUDMuda'),
        //     'role' => 3,
        // ]);
        // \App\Models\User::create([
        //     'name' => 'RSUD Sultan Abdul Azis Syah',
        //     'username' => 'RSUD Sultan Abdul Azis Syah',
        //     'password' => bcrypt('RSUDSultan'),
        //     'role' => 3,
        // ]);
        // \App\Models\User::create([
        //     'name' => 'RSUD dr. Zubir Mahmud Idi',
        //     'username' => 'RSUD dr. Zubir Mahmud Idi',
        //     'password' => bcrypt('RSUDZubir'),
        //     'role' => 3,
        // ]);
        // \App\Models\User::create([
        //     'name' => 'guset',
        //     'username' => 'guest',
        //     'password' => bcrypt('guest'),
        //     'role' => 3,
        // ]);
        \App\Models\User::create([
            'name' => 'passkas',
            'username' => 'Passkas',
            'password' => bcrypt('Passkas2026'),
            'role' => 1,
        ]);
    }
}
