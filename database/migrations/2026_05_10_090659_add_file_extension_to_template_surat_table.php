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
        Schema::table('template_surat', function (Blueprint $table) {
            $table->string('file_extension')->nullable()->after('file_path');
            $table->string('mime_type')->nullable()->after('file_extension');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('template_surat', function (Blueprint $table) {
            $table->dropColumn(['file_extension', 'mime_type']);
        });
    }
};
