<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('daftar_ulang_orang_tua', function (Blueprint $table) {
            $table->id();
            // Data Ayah
            $table->string('nama_ayah');
            $table->string('pendidikan_ayah')->nullable();
            $table->string('pekerjaan_ayah')->nullable();
            $table->text('alamat_ktp')->nullable();      // alamat KTP ayah
            $table->string('no_hp', 20)->nullable();     // nomor HP ayah (kontak utama)

            // Data Ibu (tanpa no_telp, alamat, penghasilan)
            $table->string('nama_ibu');
            $table->string('pendidikan_ibu')->nullable();
            $table->string('pekerjaan_ibu')->nullable();

            // Field tambahan untuk daftar ulang
            $table->text('alamat_domisili')->nullable(); // alamat tinggal sekarang
            $table->string('narahubung', 20)->nullable(); // kontak darurat selain wali

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('daftar_ulang_orang_tua');
    }
};