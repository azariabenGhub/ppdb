<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalonSiswa extends Model
{
    use HasFactory;

    protected $table = 'calon_siswa';

    protected $fillable = [
        'nama_lengkap',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'pernah_tk',
        'asal_tk',
        'nik',
        'agama',
        'warga_negara',
        'anak_ke',
        'jumlah_saudara',
        'alamat_lengkap',
        'punya_nisn',
        'nisn',
    ];

    public function user()
    {
        return $this->hasOneThrough(
            User::class,
            Formulir::class,
            'id_calon_siswa',  // foreign key on formulirs table
            'id',              // foreign key on users table (User.id)
            'id',              // local key on calon_siswa table
            'user_id'          // local key on formulirs table
        );
    }

    public function formulir()
    {
        return $this->hasOne(Formulir::class, 'id_calon_siswa');
    }
}