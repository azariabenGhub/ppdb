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
        Schema::create('daftar_ulang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('no_pendaftaran')->nullable();
            $table->foreignId('id_orang_tua')->nullable()->constrained('daftar_ulang_orang_tua')->onDelete('set null');
            $table->foreignId('id_wali')->nullable()->constrained('daftar_ulang_wali')->onDelete('set null');
            $table->string('akte_kelahiran');
            $table->string('ijazah_tk')->nullable();
            $table->string('ktp_orang_tua');
            $table->string('kartu_keluarga');
            $table->string('nisn_file')->nullable();
            $table->string('surat_pernyataan');
            $table->string('surat_pakta_integritas');
            $table->enum('status', ['menunggu', 'diterima', 'ditolak'])->default('menunggu');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daftar_ulang');
    }
};
