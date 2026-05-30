<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('calon_siswa', function (Blueprint $table) {
            $table->boolean('punya_nisn')->default(false)->after('alamat_lengkap');
            $table->string('nisn', 20)->nullable()->after('punya_nisn');
        });
    }

    public function down()
    {
        Schema::table('calon_siswa', function (Blueprint $table) {
            $table->dropColumn(['punya_nisn', 'nisn']);
        });
    }
};