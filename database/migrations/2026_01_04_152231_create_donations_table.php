<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->string('input_by'); // Nama petugas penginput
            $table->string('nama_alkes'); // Nama alat kesehatan
            $table->string('nama_rs'); // Asal/Tujuan Rumah Sakit
            $table->string('merek')->nullable();
            $table->string('tipe_model')->nullable();
            $table->integer('jumlah')->default(1); // Jumlah alat
            $table->string('donatur');
            $table->string('file_donasi')->nullable();
            $table->text('keterangan_lain')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};