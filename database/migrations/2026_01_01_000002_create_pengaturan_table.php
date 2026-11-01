<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaturan', function (Blueprint $table) {
            $table->id();
            $table->string('kunci', 50)->unique();
            $table->string('nilai', 255);
            $table->string('keterangan', 255)->nullable();
            $table->timestamps();
        });

        // Insert nilai default target BEP harian dari env atau default 400.000
        $defaultBep = env('EWS_BEP_HARIAN', 400000);
        DB::table('pengaturan')->insert([
            'kunci'      => 'bep_harian',
            'nilai'      => (string) $defaultBep,
            'keterangan' => 'Target Break Even Point (BEP) Omzet Harian (Rp)',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaturan');
    }
};
