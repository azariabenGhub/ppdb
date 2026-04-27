<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Formulir extends Model
{
    use HasFactory;

    protected $table = 'formulirs';

    protected $fillable = [
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
        'tipe_wali',
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
        'is_bukan_pindahan',
        'asal_sekolah',
        'no_ijazah',
        'tahun_ijazah',
        'diterima_kelas',
        'pindah_dari',
        'no_pindah',
        'tanggal_pindah'
    ];

    // Relasi ke user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
