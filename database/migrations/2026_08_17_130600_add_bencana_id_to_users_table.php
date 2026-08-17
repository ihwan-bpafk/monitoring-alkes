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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('bencana_id')->nullable()->constrained('bencanas')->nullOnDelete();
        });

        // Set existing non-admin users to Sumatera (id = 1)
        \Illuminate\Support\Facades\DB::table('users')->where('role', '!=', 1)->update(['bencana_id' => 1]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
