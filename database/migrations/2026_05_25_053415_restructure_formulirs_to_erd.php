<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // 1. Buat tabel calon_siswa
        Schema::create('calon_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nama_lengkap');
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->string('nik', 20);
            $table->string('agama');
            $table->string('warga_negara');
            $table->unsignedTinyInteger('anak_ke')->nullable();
            $table->unsignedTinyInteger('jumlah_saudara')->nullable();
            $table->text('alamat_lengkap');
            $table->timestamps();
        });

        // 2. Tabel ayah
        Schema::create('ayah', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('nik', 20);
            $table->string('pekerjaan');
            $table->string('agama');
            $table->string('pendidikan');
            $table->string('penghasilan');
            $table->string('no_telp');
            $table->text('alamat');
            $table->timestamps();
        });

        // 3. Tabel ibu
        Schema::create('ibu', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('nik', 20);
            $table->string('pekerjaan');
            $table->string('agama');
            $table->string('pendidikan');
            $table->string('penghasilan');
            $table->string('no_telp');
            $table->text('alamat');
            $table->timestamps();
        });

        // 4. Tabel wali
        Schema::create('wali', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('nik', 20);
            $table->string('pekerjaan');
            $table->string('agama');
            $table->string('pendidikan');
            $table->string('penghasilan');
            $table->string('no_telp');
            $table->text('alamat');
            $table->timestamps();
        });

        // 5. Tambahkan kolom foreign key sementara ke tabel formulirs
        Schema::table('formulirs', function (Blueprint $table) {
            $table->foreignId('id_calon_siswa')->nullable()->after('id');
            $table->foreignId('id_ayah')->nullable()->after('id_calon_siswa');
            $table->foreignId('id_ibu')->nullable()->after('id_ayah');
            $table->foreignId('id_wali')->nullable()->after('id_ibu');
        });

        // 6. Migrasi data dari formulirs lama ke tabel baru
        $formulirs = DB::table('formulirs')->get();
        foreach ($formulirs as $formulir) {
            // Insert calon_siswa
            $calonSiswaId = DB::table('calon_siswa')->insertGetId([
                'user_id' => $formulir->user_id,
                'nama_lengkap' => $formulir->nama_lengkap,
                'tempat_lahir' => $formulir->tempat_lahir,
                'tanggal_lahir' => $formulir->tanggal_lahir,
                'nik' => $formulir->nik,
                'agama' => $formulir->agama,
                'warga_negara' => $formulir->warga_negara,
                'anak_ke' => $formulir->anak_ke,
                'jumlah_saudara' => $formulir->jumlah_saudara,
                'alamat_lengkap' => $formulir->alamat_lengkap,
                'created_at' => $formulir->created_at,
                'updated_at' => $formulir->updated_at,
            ]);

            // Insert ayah & ibu jika tipe_wali = orang_tua
            $ayahId = null;
            $ibuId = null;
            $waliId = null;
            if ($formulir->tipe_wali === 'orang_tua') {
                $ayahId = DB::table('ayah')->insertGetId([
                    'nama' => $formulir->nama_ayah,
                    'nik' => $formulir->no_ktp_ayah,
                    'pekerjaan' => $formulir->pekerjaan_ayah,
                    'agama' => $formulir->agama_ayah,
                    'pendidikan' => $formulir->pendidikan_ayah,
                    'penghasilan' => $formulir->penghasilan_ayah,
                    'no_telp' => $formulir->no_telp_ayah,
                    'alamat' => $formulir->alamat_ayah,
                    'created_at' => $formulir->created_at,
                    'updated_at' => $formulir->updated_at,
                ]);
                $ibuId = DB::table('ibu')->insertGetId([
                    'nama' => $formulir->nama_ibu,
                    'nik' => $formulir->no_ktp_ibu,
                    'pekerjaan' => $formulir->pekerjaan_ibu,
                    'agama' => $formulir->agama_ibu,
                    'pendidikan' => $formulir->pendidikan_ibu,
                    'penghasilan' => $formulir->penghasilan_ibu,
                    'no_telp' => $formulir->no_telp_ibu,
                    'alamat' => $formulir->alamat_ibu,
                    'created_at' => $formulir->created_at,
                    'updated_at' => $formulir->updated_at,
                ]);
            } else {
                $waliId = DB::table('wali')->insertGetId([
                    'nama' => $formulir->nama_wali,
                    'nik' => $formulir->no_ktp_wali,
                    'pekerjaan' => $formulir->pekerjaan_wali,
                    'agama' => $formulir->agama_wali,
                    'pendidikan' => $formulir->pendidikan_wali,
                    'penghasilan' => $formulir->penghasilan_wali,
                    'no_telp' => $formulir->no_telp_wali,
                    'alamat' => $formulir->alamat_wali,
                    'created_at' => $formulir->created_at,
                    'updated_at' => $formulir->updated_at,
                ]);
            }

            // Update formulir dengan foreign key
            DB::table('formulirs')->where('id', $formulir->id)->update([
                'id_calon_siswa' => $calonSiswaId,
                'id_ayah' => $ayahId,
                'id_ibu' => $ibuId,
                'id_wali' => $waliId,
            ]);
        }

        // 7. Hapus kolom yang sudah dipindahkan dari tabel formulirs
        Schema::table('formulirs', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn([
                'user_id',
                'nama_lengkap',
                'tempat_lahir',
                'tanggal_lahir',
                'nik',
                'agama',
                'warga_negara',
                'anak_ke',
                'jumlah_saudara',
                'alamat_lengkap',
                'nama_ayah',
                'pekerjaan_ayah',
                'agama_ayah',
                'pendidikan_ayah',
                'no_ktp_ayah',
                'penghasilan_ayah',
                'no_telp_ayah',
                'alamat_ayah',
                'nama_ibu',
                'pekerjaan_ibu',
                'agama_ibu',
                'pendidikan_ibu',
                'no_ktp_ibu',
                'penghasilan_ibu',
                'no_telp_ibu',
                'alamat_ibu',
                'nama_wali',
                'pekerjaan_wali',
                'agama_wali',
                'pendidikan_wali',
                'no_ktp_wali',
                'penghasilan_wali',
                'no_telp_wali',
                'alamat_wali',
            ]);
        });

        // 8. Jadikan foreign key NOT NULL dan tambahkan constraint
        Schema::table('formulirs', function (Blueprint $table) {
            $table->foreignId('id_calon_siswa')->nullable(false)->change();
            $table->foreign('id_calon_siswa')->references('id')->on('calon_siswa')->onDelete('cascade');
            $table->foreign('id_ayah')->references('id')->on('ayah')->onDelete('cascade');
            $table->foreign('id_ibu')->references('id')->on('ibu')->onDelete('cascade');
            $table->foreign('id_wali')->references('id')->on('wali')->onDelete('cascade');
        });
    }

    public function down()
    {
        // Rollback: kembalikan struktur seperti semula (opsional, cukup hapus tabel baru)
        Schema::table('formulirs', function (Blueprint $table) {
            $table->dropForeign(['id_calon_siswa']);
            $table->dropForeign(['id_ayah']);
            $table->dropForeign(['id_ibu']);
            $table->dropForeign(['id_wali']);
            $table->dropColumn(['id_calon_siswa', 'id_ayah', 'id_ibu', 'id_wali']);
        });
        Schema::dropIfExists('calon_siswa');
        Schema::dropIfExists('ayah');
        Schema::dropIfExists('ibu');
        Schema::dropIfExists('wali');
    }
};