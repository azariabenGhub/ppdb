<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalonSiswa extends Model
{
    use HasFactory;

    protected $table = 'calon_siswa';

    protected $fillable = [
        'user_id', 'nama_lengkap', 'tempat_lahir', 'tanggal_lahir', 'nik',
        'agama', 'warga_negara', 'anak_ke', 'jumlah_saudara', 'alamat_lengkap'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function formulir()
    {
        return $this->hasOne(Formulir::class, 'id_calon_siswa');
    }
}