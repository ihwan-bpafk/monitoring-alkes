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
        Schema::create('fasyankes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bencana_id')->constrained('bencanas')->onDelete('cascade');
            $table->string('nama_fasyankes');
            $table->string('lokasi')->nullable();
            $table->string('jenis')->default('RSUD');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fasyankes');
    }
};
