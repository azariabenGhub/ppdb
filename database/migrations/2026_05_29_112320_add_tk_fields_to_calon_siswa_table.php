<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('calon_siswa', function (Blueprint $table) {
            $table->boolean('pernah_tk')->default(false)->after('tanggal_lahir');
            $table->string('asal_tk')->nullable()->after('pernah_tk');
        });
    }

    public function down()
    {
        Schema::table('calon_siswa', function (Blueprint $table) {
            $table->dropColumn(['pernah_tk', 'asal_tk']);
        });
    }
};