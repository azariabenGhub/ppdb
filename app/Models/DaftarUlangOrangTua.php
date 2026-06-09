<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DaftarUlangOrangTua extends Model
{
    use HasFactory;

    protected $table = 'daftar_ulang_orang_tua';

    protected $fillable = [
        'nama_lengkap', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'asal_tk',
        'alamat_domisili', 'is_bukan_pindahan', 'nama_ayah', 'pendidikan_ayah', 'pekerjaan_ayah', 'alamat_ktp', 'no_hp',
        'nama_ibu', 'pendidikan_ibu', 'pekerjaan_ibu', 'narahubung'
    ];
}