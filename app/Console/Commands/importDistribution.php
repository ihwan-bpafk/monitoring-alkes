<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Donation;
use App\Models\Distribution;
use App\Models\DonationLog;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\DistributionImport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ImportDistribution extends Command
{
    // Perintah: php artisan import:distribution donasi.xlsx
    protected $signature = 'import:distribution {file}';
    protected $description = 'Import distribusi langsung (Trust Excel Data)';

    public function handle()
    {
        $file = $this->argument('file');
        $path = base_path($file);

        if (!File::exists($path)) {
            $this->error("File '$file' tidak ditemukan di root project!");
            return;
        }

        $this->info("🚀 Memulai import data distribusi massal...");

        // 1. Ambil data dari Excel
        $rows = Excel::toCollection(new DistributionImport, $path)->first();
        $total = count($rows);
        $countSuccess = 0;

        // 2. Gunakan Transaction agar data konsisten
        DB::transaction(function () use ($rows, &$countSuccess) {
            foreach ($rows as $row) {
                $donation = Donation::find($row['donation_id']);

                if ($donation) {
                    // Simpan Distribusi
                    Distribution::create([
                        'donation_id' => $row['donation_id'],
                        'nama_rs' => $row['nama_rs'],
                        'jumlah_distribusi' => $row['jumlah_distribusi'],
                        'tanggal_distribusi' => now(),
                        'status' => $row['status'] ?? 'Dikirim',
                        'petugas_pengirim' => 'System (Import Massal)',
                    ]);

                    // Potong Stok Otomatis
                    $donation->decrement('sisa_stok', $row['jumlah_distribusi']);

                    // Update Status Terakhir di Master Donasi
                    $donation->update([
                        'status_akhir' => 'Didistribusikan ke ' . $row['nama_rs']
                    ]);

                    // Catat Log Riwayat (Audit Trail)
                    DonationLog::create([
                        'donation_id' => $donation->id,
                        'status' => 'Distribusi Massal',
                        'diupdate_oleh' => 'System',
                        'catatan' => "Import massal: {$row['jumlah_distribusi']} unit dikirim ke {$row['nama_rs']}.",
                    ]);

                    $countSuccess++;
                }
            }
        });

        $this->info("✅ Berhasil! $countSuccess dari $total baris data distribusi telah diproses.");
        $this->line("Stok gudang telah diperbarui dan riwayat telah dicatat otomatis.");
    }
}