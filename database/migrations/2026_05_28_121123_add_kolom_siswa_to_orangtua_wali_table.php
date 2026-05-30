<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Tambah kolom siswa ke daftar_ulang_orang_tua
        Schema::table('daftar_ulang_orang_tua', function (Blueprint $table) {
            if (!Schema::hasColumn('daftar_ulang_orang_tua', 'nama_lengkap')) {
                $table->string('nama_lengkap')->nullable();
            }
            if (!Schema::hasColumn('daftar_ulang_orang_tua', 'tempat_lahir')) {
                $table->string('tempat_lahir')->nullable();
            }
            if (!Schema::hasColumn('daftar_ulang_orang_tua', 'tanggal_lahir')) {
                $table->date('tanggal_lahir')->nullable();
            }
            if (!Schema::hasColumn('daftar_ulang_orang_tua', 'jenis_kelamin')) {
                $table->string('jenis_kelamin', 10)->nullable();
            }
            if (!Schema::hasColumn('daftar_ulang_orang_tua', 'asal_sekolah')) {
                $table->string('asal_sekolah')->nullable();
            }
        });

        // Tambah kolom siswa ke daftar_ulang_wali
        Schema::table('daftar_ulang_wali', function (Blueprint $table) {
            if (!Schema::hasColumn('daftar_ulang_wali', 'nama_lengkap')) {
                $table->string('nama_lengkap')->nullable();
            }
            if (!Schema::hasColumn('daftar_ulang_wali', 'tempat_lahir')) {
                $table->string('tempat_lahir')->nullable();
            }
            if (!Schema::hasColumn('daftar_ulang_wali', 'tanggal_lahir')) {
                $table->date('tanggal_lahir')->nullable();
            }
            if (!Schema::hasColumn('daftar_ulang_wali', 'jenis_kelamin')) {
                $table->string('jenis_kelamin', 10)->nullable();
            }
            if (!Schema::hasColumn('daftar_ulang_wali', 'asal_sekolah')) {
                $table->string('asal_tk')->nullable();
            }
        });

        // Hapus bagian dropColumn untuk daftar_ulang (tidak diperlukan)
        // Jika Anda yakin kolom tersebut ada, gunakan conditional check.
    }

    public function down()
    {
        Schema::table('daftar_ulang_orang_tua', function (Blueprint $table) {
            $table->dropColumn([
                'nama_lengkap', 'tempat_lahir', 'tanggal_lahir', 'asal_sekolah'
            ]);
        });
        Schema::table('daftar_ulang_wali', function (Blueprint $table) {
            $table->dropColumn([
                'nama_lengkap', 'tempat_lahir', 'tanggal_lahir', 'asal_sekolah'
            ]);
        });
    }
};