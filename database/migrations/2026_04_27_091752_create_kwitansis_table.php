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
        Schema::create('kwitansis', function (Blueprint $table) {
            $table->id('id_kwitansi');
            $table->foreignId('id_verifikasi_pembayaran')->constrained('verifikasi_pembayarans', 'id_verifikasi_pembayaran')->onDelete('cascade');
            $table->foreignId('id_pendaftar')->constrained('users')->onDelete('cascade');
            $table->string('kwitansi'); // path file kwitansi (PDF/gambar)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kwitansis');
    }
};
