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
        Schema::create('ruangans', function (Blueprint $table) {
        $table->id('id_ruangan'); // Primary Key
        $table->string('kode_ruang')->unique(); // Misal: R-301
        $table->string('nama_ruangan'); // Misal: Lab Komputer
        $table->integer('kapasitas')->nullable(); // Misal: 40
        $table->string('status')->default('Tersedia'); // Tersedia/Penuh
        $table->timestamps(); // Created_at & Updated_at
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ruangans');
    }
};
