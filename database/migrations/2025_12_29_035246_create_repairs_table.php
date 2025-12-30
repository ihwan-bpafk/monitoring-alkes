<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        // Tabel Utama
        Schema::create('repairs', function (Blueprint $table) {
            $table->id();
            $table->string('input_by')->nullable();
            $table->date('tanggal_input')->nullable();
            $table->string('nama_rs')->nullable();
            $table->string('lokasi')->nullable();
            
            // Identitas Alat
            $table->string('nama_alkes')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('kategori')->nullable();
            $table->string('merek')->nullable();
            $table->string('tipe_model')->nullable();
            $table->string('nama_penyedia')->nullable();
            $table->string('grade_kerusakan')->nullable();
            $table->string('kondisi_kontrak')->nullable();
            
            // Status & Progress
            $table->string('status_perbaikan')->nullable();
            $table->string('komponen')->nullable();
            $table->text('respon_penyedia')->nullable();
            $table->text('tindakan_penyedia')->nullable();
            $table->text('rtl')->nullable();
            
            // File & Dokumentasi
            $table->string('file_bap')->nullable();
            $table->text('foto_kondisi')->nullable();
            
            $table->text('keterangan_lain')->nullable();
            $table->timestamps();
        });

        // Tabel History
        Schema::create('repair_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('repair_id')->constrained('repairs')->onDelete('cascade');
            $table->string('status_perbaikan');
            $table->text('keterangan_perubahan');
            $table->string('user_nama')->nullable();
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('repair_histories');
        Schema::dropIfExists('repairs');
    }
};