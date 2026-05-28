<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('daftar_ulang_wali', function (Blueprint $table) {
            $table->id();
            $table->string('nama_wali');
            $table->string('pendidikan_wali')->nullable();
            $table->string('pekerjaan_wali')->nullable();
            $table->text('alamat_ktp')->nullable();
            $table->string('no_hp', 20)->nullable();

            // Field tambahan
            $table->text('alamat_domisili')->nullable();
            $table->string('narahubung', 20)->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('daftar_ulang_wali');
    }
};