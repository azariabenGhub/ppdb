<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('daftar_ulang_orang_tua', function (Blueprint $table) {
            if (!Schema::hasColumn('daftar_ulang_orang_tua', 'is_bukan_pindahan')) {
                $table->boolean('is_bukan_pindahan')->default(false)->after('alamat_domisili');
            }
        });

        Schema::table('daftar_ulang_wali', function (Blueprint $table) {
            if (!Schema::hasColumn('daftar_ulang_wali', 'is_bukan_pindahan')) {
                $table->boolean('is_bukan_pindahan')->default(false)->after('alamat_domisili');
            }
        });
    }

    public function down()
    {
        Schema::table('daftar_ulang_orang_tua', function (Blueprint $table) {
            if (Schema::hasColumn('daftar_ulang_orang_tua', 'is_bukan_pindahan')) {
                $table->dropColumn('is_bukan_pindahan');
            }
        });

        Schema::table('daftar_ulang_wali', function (Blueprint $table) {
            if (Schema::hasColumn('daftar_ulang_wali', 'is_bukan_pindahan')) {
                $table->dropColumn('is_bukan_pindahan');
            }
        });
    }
};
