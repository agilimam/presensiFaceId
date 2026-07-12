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
        Schema::create('jadwal_sholat', function (Blueprint $table) {
            $table->id('id_jadwal');
            $table->string('nama_sholat');
            $table->time('jam_mulai');
            $table->time('batas_tepat_waktu');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_sholat');
    }
};
