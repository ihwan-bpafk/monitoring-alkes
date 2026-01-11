<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Imports\DonationImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\File;

class ImportDonation extends Command
{
    // Perintah: php artisan import:donation nama_file.xlsx
    protected $signature = 'import:donation {file}';

    protected $description = 'Import data donasi dari root directory (sejajar .env)';

    public function handle()
    {
        // PERBAIKAN: Gunakan string 'file' (pakai tanda kutip), bukan variabel $file
        $file = $this->argument('file'); 
        
        // 1. Definisikan path ke root folder menggunakan base_path
        $path = base_path($file);

        // 2. Cek apakah file ada di root folder
        if (!\Illuminate\Support\Facades\File::exists($path)) {
            $this->error("File tidak ditemukan!");
            $this->line("Pastikan file '$file' berada di folder utama sejajar dengan .env");
            return;
        }

        $this->info("Memulai import data dari $file...");

        try {
            // 3. Jalankan import menggunakan variabel $path yang sudah benar
            \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\DonationImport, $path);
            
            $this->info("Berhasil! Data dari $file telah masuk ke database.");
        } catch (\Exception $e) {
            $this->error("Terjadi kesalahan: " . $e->getMessage());
        }
    }
}