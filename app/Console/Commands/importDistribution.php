<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Imports\DistributionImport;
use Maatwebsite\Excel\Facades\Excel;

class ImportDistribution extends Command
{
    // Nama perintah yang akan diketik di terminal
    protected $signature = 'import:distributions {file}';

    protected $description = 'Import data donasi dari file Excel melalui terminal';

    public function handle()
    {
        $file = $this->argument('file');

        // Cek apakah file ada
        if (!file_exists($file)) {
            $this->error("File tidak ditemukan di path: $file");
            return;
        }

        $this->info("Sedang memproses import dari: $file ...");

        try {
            Excel::import(new DistributionImport, $file);
            $this->info("Berhasil! Data donasi telah masuk ke database.");
        } catch (\Exception $e) {
            $this->error("Gagal import: " . $e->getMessage());
        }
    }
}