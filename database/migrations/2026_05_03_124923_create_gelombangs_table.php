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
        Schema::create('gelombangs', function (Blueprint $table) {
            $table->id();
            $table->integer('nomor_gelombang');
            $table->year('tahun');
            $table->dateTime('periode_mulai');
            $table->dateTime('periode_selesai');
            $table->integer('kuota')->default(0);
            $table->decimal('biaya_pendaftaran', 12, 2)->default(0);
            $table->enum('status', ['aktif', 'nonaktif'])->default('nonaktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gelombangs');
    }
};
