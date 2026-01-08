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
        //     'name' => 'Farmalkes',
        //     'username' => 'farmalkes',
        //     'password' => bcrypt('adminfarmalkes'),
        // ]);
        // \App\Models\User::create([
        //     'name' => 'RSUD Langsa',
        //     'username' => 'RSUD Langsa',
        //     'password' => bcrypt('RSUDLangsa'),
        // ]);
        \App\Models\User::create([
            'name' => 'RSUP Adam Malik',
            'username' => 'RSUP Adam Malik',
            'password' => bcrypt('RSUPAdamMalik'),
        ]);
        // \App\Models\User::create([
        //     'name' => 'RSUD Muda Sedia Aceh Tamiang',
        //     'username' => 'RSUD Muda Sedia Aceh Tamiang',
        //     'password' => bcrypt('RSUDMuda'),
        // ]);
        // \App\Models\User::create([
        //     'name' => 'RSUD Sultan Abdul Azis Syah',
        //     'username' => 'RSUD Sultan Abdul Azis Syah',
        //     'password' => bcrypt('RSUDSultan'),
        // ]);
        // \App\Models\User::create([
        //     'name' => 'RSUD dr. Zubir Mahmud Idi',
        //     'username' => 'RSUD dr. Zubir Mahmud Idi',
        //     'password' => bcrypt('RSUDZubir'),
        // ]);
    }
}
