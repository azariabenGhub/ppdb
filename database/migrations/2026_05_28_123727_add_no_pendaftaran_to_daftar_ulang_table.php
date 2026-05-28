<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('daftar_ulang', function (Blueprint $table) {
            if (!Schema::hasColumn('daftar_ulang', 'no_pendaftaran')) {
                $table->string('no_pendaftaran')->nullable()->after('user_id');
            }
        });
    }

    public function down()
    {
        Schema::table('daftar_ulang', function (Blueprint $table) {
            $table->dropColumn('no_pendaftaran');
        });
    }
};