<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('daftar_ulang', function (Blueprint $table) {
            // Cek apakah kolom sudah ada, jika belum tambahkan
            if (!Schema::hasColumn('daftar_ulang', 'id_orang_tua')) {
                $table->unsignedBigInteger('id_orang_tua')->nullable()->after('user_id');
                $table->foreign('id_orang_tua')
                      ->references('id')->on('daftar_ulang_orang_tua')
                      ->onDelete('set null');
            }

            if (!Schema::hasColumn('daftar_ulang', 'id_wali')) {
                $table->unsignedBigInteger('id_wali')->nullable()->after('id_orang_tua');
                $table->foreign('id_wali')
                      ->references('id')->on('daftar_ulang_wali')
                      ->onDelete('set null');
            }
        });
    }

    public function down()
    {
        Schema::table('daftar_ulang', function (Blueprint $table) {
            // Hapus foreign key dan kolom
            if (Schema::hasColumn('daftar_ulang', 'id_orang_tua')) {
                $table->dropForeign(['id_orang_tua']);
                $table->dropColumn('id_orang_tua');
            }
            if (Schema::hasColumn('daftar_ulang', 'id_wali')) {
                $table->dropForeign(['id_wali']);
                $table->dropColumn('id_wali');
            }
        });
    }
};