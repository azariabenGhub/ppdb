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
        Schema::create('penilaian', function (Blueprint $table) {
            $table->id('id_nilai');
            $table->foreignId('id_penilai')->constrained('users')->onDelete('cascade');
            $table->foreignId('id_pendaftar')->constrained('users')->onDelete('cascade'); // siswa yang dinilai
            $table->string('kemampuan_membaca');
            $table->string('kemampuan_menulis');
            $table->string('kemampuan_berhitung');
            $table->string('baca_alquran');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penilaian');
    }
};
