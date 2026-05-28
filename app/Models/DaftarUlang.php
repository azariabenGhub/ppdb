<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DaftarUlang extends Model
{
    use HasFactory;

    protected $table = 'daftar_ulang';
    protected $fillable = [
        'user_id',
        'no_pendaftaran',
        'akte_kelahiran',
        'ijazah_tk',
        'ktp_orang_tua',
        'kartu_keluarga',
        'nisn_file',
        'surat_pernyataan',
        'surat_pakta_integritas',
        'status',
        'catatan'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orangTua()
    {
        return $this->belongsTo(DaftarUlangOrangTua::class, 'id_orang_tua');
    }

    public function wali()
    {
        return $this->belongsTo(DaftarUlangWali::class, 'id_wali');
    }
}
