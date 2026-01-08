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
        Schema::create('distributions', function (Blueprint $table) {
            $table->id();
            // Relasi ke tabel donations
            $table->foreignId('donation_id')->constrained('donations')->onDelete('cascade');
            
            $table->string('nama_rs'); // Alokasi distribusi
            $table->integer('jumlah_distribusi');
            $table->string('status')->default('Dikirim'); // Dikirim, Diterima
            $table->date('tanggal_distribusi')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distributions');
    }
};
