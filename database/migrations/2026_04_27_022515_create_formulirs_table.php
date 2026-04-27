<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('formulirs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Relasi ke user yang login

            // Bagian 1: Biodata Siswa
            $table->string('nama_lengkap');
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->string('nik', 20);
            $table->string('agama');
            $table->string('warga_negara');
            $table->unsignedTinyInteger('anak_ke')->nullable();
            $table->unsignedTinyInteger('jumlah_saudara')->nullable();
            $table->text('alamat_lengkap');

            // Bagian 2: Orang Tua / Wali
            $table->enum('tipe_wali', ['orang_tua', 'wali']);
            // Data Ayah
            $table->string('nama_ayah')->nullable();
            $table->string('pekerjaan_ayah')->nullable();
            $table->string('agama_ayah')->nullable();
            $table->string('pendidikan_ayah')->nullable();
            $table->string('no_ktp_ayah', 20)->nullable();
            $table->string('penghasilan_ayah')->nullable();
            $table->string('no_telp_ayah')->nullable();
            $table->text('alamat_ayah')->nullable();
            // Data Ibu
            $table->string('nama_ibu')->nullable();
            $table->string('pekerjaan_ibu')->nullable();
            $table->string('agama_ibu')->nullable();
            $table->string('pendidikan_ibu')->nullable();
            $table->string('no_ktp_ibu', 20)->nullable();
            $table->string('penghasilan_ibu')->nullable();
            $table->string('no_telp_ibu')->nullable();
            $table->text('alamat_ibu')->nullable();
            // Data Wali
            $table->string('nama_wali')->nullable();
            $table->string('pekerjaan_wali')->nullable();
            $table->string('agama_wali')->nullable();
            $table->string('pendidikan_wali')->nullable();
            $table->string('no_ktp_wali', 20)->nullable();
            $table->string('penghasilan_wali')->nullable();
            $table->string('no_telp_wali')->nullable();
            $table->text('alamat_wali')->nullable();

            // Bagian 3: Data Akademik
            $table->boolean('is_bukan_pindahan')->default(false);
            $table->string('asal_sekolah')->nullable();
            $table->string('no_ijazah')->nullable();
            $table->string('tahun_ijazah', 4)->nullable();
            $table->string('diterima_kelas')->nullable();
            $table->string('pindah_dari')->nullable();
            $table->string('no_pindah')->nullable();
            $table->date('tanggal_pindah')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formulirs');
    }
};
