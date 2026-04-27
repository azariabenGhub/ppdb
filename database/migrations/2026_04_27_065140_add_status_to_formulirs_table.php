<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('formulirs', function (Blueprint $table) {
            $table->enum('status', ['menunggu', 'diterima', 'ditolak'])
                  ->default('menunggu')
                  ->after('alamat_lengkap');
        });
    }

    public function down(): void
    {
        Schema::table('formulirs', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
