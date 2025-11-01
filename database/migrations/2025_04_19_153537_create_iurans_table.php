<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('iurans', function (Blueprint $table) {
            $table->id('id_bayar');
            $table->foreignId('user_id')->constrained()->on('users')->onDelete('cascade'); // Relasi ke tabel users
            $table->string('jenis_iuran');
            $table->date('tgl_bayar')->nullable();
            $table->unsignedInteger('total_bayar');
            $table->enum('status', ['Belum Bayar', 'Sudah Bayar'])->default('Belum Bayar');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iurans');
    }
};