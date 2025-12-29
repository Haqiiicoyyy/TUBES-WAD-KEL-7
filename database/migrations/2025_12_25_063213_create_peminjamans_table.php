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
        Schema::create('peminjamans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_peminjam');
            $table->string('ruangan'); // Menyimpan nama ruangan
            $table->date('tanggal');
            $table->string('jam_mulai'); // Bisa string misal "08:00"
            $table->string('jam_selesai');
            $table->text('keperluan');
            $table->string('status')->default('menunggu'); // menunggu, disetujui, ditolak
            $table->string('no_hp');
            $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjamans');
    }
};