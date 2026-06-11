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
        Schema::create('formulirs', function (Blueprint $table) {
            $table->id();
            $table->string('no_pendaftaran')->unique()->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('id_gelombang')->nullable()->constrained('gelombangs')->onDelete('set null');
            $table->foreignId('id_calon_siswa')->constrained('calon_siswa')->onDelete('cascade');
            $table->foreignId('id_ayah')->nullable()->constrained('ayah')->onDelete('cascade');
            $table->foreignId('id_ibu')->nullable()->constrained('ibu')->onDelete('cascade');
            $table->foreignId('id_wali')->nullable()->constrained('wali')->onDelete('cascade');
            $table->enum('tipe_wali', ['orang_tua', 'wali']);
            $table->enum('status', ['menunggu', 'diterima', 'ditolak'])->default('menunggu');
            $table->boolean('is_bukan_pindahan')->default(false);
            $table->string('asal_sekolah')->nullable();
            $table->string('no_ijazah')->nullable();
            $table->string('tahun_ijazah', 4)->nullable();
            $table->string('diterima_kelas')->nullable();
            $table->string('pindah_dari')->nullable();
            $table->string('no_pindah')->nullable();
            $table->date('tanggal_pindah')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formulirs');
    }
};
