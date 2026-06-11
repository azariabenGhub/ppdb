<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daftar_ulang_orang_tua', function (Blueprint $table) {
            $table->id();
            // Data Ayah
            $table->string('nama_ayah');
            $table->string('pendidikan_ayah')->nullable();
            $table->string('pekerjaan_ayah')->nullable();
            $table->text('alamat_ktp')->nullable();
            $table->string('no_hp', 20)->nullable();

            // Data Ibu
            $table->string('nama_ibu');
            $table->string('pendidikan_ibu')->nullable();
            $table->string('pekerjaan_ibu')->nullable();

            // Field tambahan
            $table->text('alamat_domisili')->nullable();
            $table->string('narahubung', 20)->nullable();
            $table->boolean('is_bukan_pindahan')->default(false);

            // Data Siswa
            $table->string('nama_lengkap')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('jenis_kelamin', 10)->nullable();
            $table->string('asal_tk')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daftar_ulang_orang_tua');
    }
};
