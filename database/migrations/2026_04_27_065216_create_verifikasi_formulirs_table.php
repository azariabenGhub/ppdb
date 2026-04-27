<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('verifikasi_formulirs', function (Blueprint $table) {
            $table->id('id_verform');
            $table->foreignId('id_verifikator')->constrained('users')->onDelete('cascade');
            $table->foreignId('id_formulir')->constrained('formulirs')->onDelete('cascade');
            $table->enum('hasil_verifikasi', ['diterima', 'ditolak']);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verifikasi_formulirs');
    }
};