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
        Schema::create('anggota_keluarga', function (Blueprint $table) {
            // Primary Key
            $table->id('id_anggota_keluarga'); 

            // Foreign Key
            $table->foreignId('id_keluarga')->constrained('keluarga', 'id_keluarga')->onDelete('cascade');
            
            $table->string('nama_anggota', 100);
            $table->string('hubungan', 50);
            $table->string('face_id')->nullable(); 
            $table->string('status_wajah', 20)->nullable()->default(null);
            
            $table->timestamps();
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('anggota_keluarga');
    }
};
