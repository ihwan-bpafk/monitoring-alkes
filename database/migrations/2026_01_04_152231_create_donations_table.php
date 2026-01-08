<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Menghapus jika tabel sudah ada agar fresh
        Schema::dropIfExists('donations');

        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            // Data sesuai permintaan menu Donasi
            $table->string('pemberi_donasi'); // Donor
            $table->string('nama_alkes');     // Nama Alat
            $table->string('merek')->nullable();
            $table->integer('jumlah_donasi'); // Total stok masuk awal
            $table->string('diterima_oleh');  // Petugas penerima di kantor

            // Kolom Tambahan untuk Sinkronisasi Distribusi
            $table->integer('sisa_stok');     // Akan berkurang otomatis saat ada distribusi
            $table->date('tanggal_masuk');    // Tanggal barang sampai di kantor
            $table->text('keterangan')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};