<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Imports\RepairImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\File;

class ImportExcel extends Command
{
    // Ini adalah nama perintah yang akan Anda ketik di terminal
    protected $signature = 'import:alkes {file}';

    protected $description = 'Import data perbaikan alkes dari file Excel';

    public function handle()
    {
        $file = $this->argument('file');

        // Cek apakah file ada
        if (!\Illuminate\Support\Facades\File::exists($file)) {
            $this->error("File tidak ditemukan di path: $file");
            return;
        }

        $this->info("Memulai proses import...");

        try {
            \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\RepairImport, $file);
            
            // GUNAKAN info() BUKAN success()
            $this->info("BERHASIL: Data dari $file telah dimasukkan ke database."); 
            
        } catch (\Exception $e) {
            $this->error("Terjadi kesalahan: " . $e->getMessage());
        }
    }
}