<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            // Menambah kolom sesuai urutan di gambar
            $table->integer('jumlah_total_donasi')->default(0)->after('merek');
            $table->date('tanggal_terima_donatur')->nullable()->after('donatur'); // Kolom 'diterima' di gambar
            $table->string('status')->nullable()->after('jumlah'); // Kolom 'Status'
            $table->integer('sisa_stok')->default(0)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropColumn(['jumlah_total_donasi', 'tanggal_terima_donatur', 'status', 'sisa_stok']);
        });
    }
};