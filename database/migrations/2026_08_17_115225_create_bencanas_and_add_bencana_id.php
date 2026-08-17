<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1. Buat tabel master bencanas
        Schema::create('bencanas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_bencana');
            $table->string('lokasi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Insert data default (Bencana Sumatera) agar data lama tidak yatim piatu
        $bencanaSumateraId = DB::table('bencanas')->insertGetId([
            'nama_bencana' => 'Bencana Sumatera',
            'lokasi' => 'Sumatera',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Tambahkan kolom bencana_id ke tabel repairs dan donations
        // Tabel repairs mencatat data seperti nama_alkes, serial_number, dan status_perbaikan.
        Schema::table('repairs', function (Blueprint $table) {
            $table->foreignId('bencana_id')->nullable()->constrained('bencanas')->onDelete('cascade');
        });

        // Tabel donations mencatat data seperti nama_alkes, pemberi_donasi, dan jumlah_donasi.
        Schema::table('donations', function (Blueprint $table) {
            $table->foreignId('bencana_id')->nullable()->constrained('bencanas')->onDelete('cascade');
        });

        // 4. Patching: Update semua data existing ke Bencana Sumatera
        DB::table('repairs')->update(['bencana_id' => $bencanaSumateraId]);
        DB::table('donations')->update(['bencana_id' => $bencanaSumateraId]);
    }

    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropForeign(['bencana_id']);
            $table->dropColumn('bencana_id');
        });

        Schema::table('repairs', function (Blueprint $table) {
            $table->dropForeign(['bencana_id']);
            $table->dropColumn('bencana_id');
        });

        Schema::dropIfExists('bencanas');
    }
};
