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
        Schema::create('verifikasi_pembayarans', function (Blueprint $table) {
            $table->id('id_verifikasi_pembayaran');
            $table->foreignId('id_bukti_pembayaran')->constrained('bukti_pembayarans', 'id_bukti_pembayaran')->onDelete('cascade');
            $table->foreignId('id_verifikator')->constrained('users')->onDelete('cascade');
            $table->enum('hasil_verifikasi', ['diterima', 'ditolak']);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verifikasi_pembayarans');
    }
};
