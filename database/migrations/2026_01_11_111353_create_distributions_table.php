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
            // Relasi ke tabel donations, jika data donasi dihapus maka distribusi ikut terhapus
            $table->foreignId('donation_id')->constrained('donations')->onDelete('cascade');
            
            $table->string('nama_rs'); // Nama RSUD / Puskesmas tujuan
            $table->integer('jumlah_distribusi'); // Jumlah unit yang dikirim
            $table->date('tanggal_distribusi');
            
            // Status distribusi: 'Dikirim' atau 'Diterima RS'
            $table->string('status')->default('Dikirim');
            
            $table->string('petugas_pengirim'); // Akan diambil dari Auth::user()->name
            $table->text('keterangan')->nullable(); // Catatan tambahan (opsional)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('distributions');
    }
};
