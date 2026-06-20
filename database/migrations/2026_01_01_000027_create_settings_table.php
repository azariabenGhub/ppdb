<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Insert default settings
        DB::table('settings')->insert([
            [
                'key' => 'beranda_alur',
                'value' => json_encode([
                    [
                        'step' => 1,
                        'title' => 'Cek berkas persyaratan',
                        'description' => 'Calon peserta didik mempersiapkan berkas-berkas yang dibutuhkan.',
                        'date' => ''
                    ],
                    [
                        'step' => 2,
                        'title' => 'Pembayaran Formulir',
                        'description' => "Melakukan pembayaran formulir ke:\nDKI Syariah: norek\nA.N Ziyadatul Ihsan",
                        'date' => ''
                    ],
                    [
                        'step' => 3,
                        'title' => 'Daftar Online',
                        'description' => 'Calon peserta didik mengisi biodata pada formulir pendaftaran dan tunggu verifikasi dari panitia.',
                        'date' => ''
                    ],
                    [
                        'step' => 4,
                        'title' => 'Cek Jadwal Tes',
                        'description' => 'Setelah formulir diverifikasi, periksa jadwal tes akademik dan quran. Tes dilakukan secara offline di sekolah.',
                        'date' => ''
                    ],
                    [
                        'step' => 5,
                        'title' => 'Pengumuman Tes',
                        'description' => 'Calon peserta didik memeriksa detail nilai dan hasil tes pada laman pengumuman.',
                        'date' => ''
                    ],
                    [
                        'step' => 6,
                        'title' => 'Daftar Ulang',
                        'description' => 'Setelah dinyatakan lulus, unggah berkas-berkas persyaratan pada laman daftar ulang.',
                        'date' => ''
                    ],
                    [
                        'step' => 7,
                        'title' => 'Pembayaran Daftar Ulang',
                        'description' => 'Lihat detail biaya daftar ulang dan lakukan pembayaran pada nomor rekening yang tertera.',
                        'date' => ''
                    ]
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'beranda_kontak',
                'value' => "Ririn Asmarwati, S.Pd.I (0878 8751 8892)\nHayatun Nufus, S.Pd. I (0878 7707 0284)\nMamluatul Mukarromah (0822 1073 3866)",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'beranda_alamat',
                'value' => "Jl. Sadar No. 33 Rt.001/014 Jatinegara, Cipinang\nMuara, Kota Jakarta Timur, D.K.I. Jakarta",
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
