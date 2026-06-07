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
       Schema::create('presensi', function (Blueprint $table) {
    $table->id('id_presensi'); // Primary Key

    // Foreign Key ke tabel keluarga (Harus ada/Not Null)
    $table->foreignId('id_keluarga')
          ->constrained('keluarga', 'id_keluarga')
          ->onDelete('cascade');

    // Foreign Key ke anggota_keluarga (Dibuat NULLABLE agar AI bisa update nanti)
    $table->unsignedBigInteger('id_anggota_keluarga')->nullable();
    $table->foreign('id_anggota_keluarga')
          ->references('id_anggota_keluarga')
          ->on('anggota_keluarga')
          ->onDelete('cascade');

    $table->string('keterangan_sholat');
    $table->dateTime('waktu_absen');
    $table->string('face_id')->nullable();
    $table->string('status')->nullable(); // Untuk menyimpan nama file foto hasil scan
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presensi');
    }
};
