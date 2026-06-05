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
        // 1. Tambah kolom user_id ke formulirs
        Schema::table('formulirs', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->onDelete('cascade');
        });

        // 2. Salin data user_id dari calon_siswa ke formulirs
        $formulirs = DB::table('formulirs')->get();
        foreach ($formulirs as $f) {
            $calon = DB::table('calon_siswa')->where('id', $f->id_calon_siswa)->first();
            if ($calon && isset($calon->user_id)) {
                DB::table('formulirs')->where('id', $f->id)->update(['user_id' => $calon->user_id]);
            }
        }

        // 3. Hapus kolom user_id dari calon_siswa
        Schema::table('calon_siswa', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Kembalikan kolom user_id ke calon_siswa
        Schema::table('calon_siswa', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
        });

        // 2. Salin balik data user_id dari formulirs ke calon_siswa
        $formulirs = DB::table('formulirs')->get();
        foreach ($formulirs as $f) {
            if ($f->user_id) {
                DB::table('calon_siswa')->where('id', $f->id_calon_siswa)->update(['user_id' => $f->user_id]);
            }
        }

        // 3. Hapus kolom user_id dari formulirs
        Schema::table('formulirs', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
