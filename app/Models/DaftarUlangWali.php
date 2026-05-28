<?php

namespace App\Models;

use App\Models\DaftarUlangForm;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DaftarUlangWali extends Model
{
    use HasFactory;

    protected $table = 'daftar_ulang_wali';

    protected $fillable = [
        'nama_lengkap', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'asal_sekolah',
        'alamat_domisili', 'is_bukan_pindahan',
        'nama_wali', 'pendidikan_wali', 'pekerjaan_wali', 'alamat_ktp', 'no_hp', 'narahubung'
    ];
}