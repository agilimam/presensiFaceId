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
        Schema::create('keluarga', function (Blueprint $table) {
            $table->id('id_keluarga'); //primary key
            $table->foreignId('id_user')->constrained('users', 'id_user')->onDelete('cascade'); //foreign key ke tabel users
            $table->string('nama_keluarga', 100);
            $table->string('nik', 16 )->unique();
            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keluarga');
    }
};
