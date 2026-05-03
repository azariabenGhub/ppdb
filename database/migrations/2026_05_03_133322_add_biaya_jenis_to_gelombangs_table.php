<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('gelombangs', function (Blueprint $table) {
            // Hapus kolom biaya_pendaftaran jika ada (opsional)
            if (Schema::hasColumn('gelombangs', 'biaya_pendaftaran')) {
                $table->dropColumn('biaya_pendaftaran');
            }
            $table->decimal('biaya_formulir', 12, 2)->default(0)->after('kuota');
            $table->decimal('biaya_daftar_ulang', 12, 2)->default(0)->after('biaya_formulir');
        });
    }

    public function down()
    {
        Schema::table('gelombangs', function (Blueprint $table) {
            $table->dropColumn(['biaya_formulir', 'biaya_daftar_ulang']);
            $table->decimal('biaya_pendaftaran', 12, 2)->default(0)->after('kuota');
        });
    }
};