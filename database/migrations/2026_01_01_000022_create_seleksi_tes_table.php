<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('seleksi_tes', function (Blueprint $table) {
            $table->id('id_seleksi_tes');
            $table->foreignId('id_pendaftar')->constrained('users')->onDelete('cascade');
            $table->foreignId('id_penjadwal')->constrained('users')->onDelete('cascade');
            $table->foreignId('id_penilaian')->nullable()->constrained('penilaian', 'id_nilai')->onDelete('set null');
            $table->dateTime('jadwal_tes');
            $table->enum('kelulusan_tes', ['lulus', 'tidak_lulus'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seleksi_tes');
    }
};
