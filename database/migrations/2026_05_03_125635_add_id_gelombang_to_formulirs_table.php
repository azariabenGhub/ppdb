<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('formulirs', function (Blueprint $table) {
            $table->foreignId('id_gelombang')->nullable()->after('user_id')->constrained('gelombangs')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('formulirs', function (Blueprint $table) {
            $table->dropForeign(['id_gelombang']);
            $table->dropColumn('id_gelombang');
        });
    }
};
