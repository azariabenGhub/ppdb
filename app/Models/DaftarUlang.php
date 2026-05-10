<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DaftarUlang extends Model
{
    use HasFactory;

    protected $table = 'daftar_ulang';
    protected $fillable = [
        'user_id', 'akte_kelahiran', 'ijazah_tk', 'ktp_orang_tua', 'kartu_keluarga',
        'nisn_file', 'surat_pernyataan', 'surat_pakta_integritas', 'status', 'catatan'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
