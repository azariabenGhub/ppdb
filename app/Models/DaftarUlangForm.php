<?php

namespace App\Models;

use App\Models\DaftarUlangOrangTua;
use App\Models\DaftarUlangWali;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DaftarUlangForm extends Model
{
    use HasFactory;

    protected $table = 'daftar_ulang_forms';

    protected $fillable = [
        'user_id', 'tipe_wali',
        'nama_lengkap', 'tempat_lahir', 'tanggal_lahir', 'nik', 'agama', 'warga_negara',
        'anak_ke', 'jumlah_saudara', 'alamat_lengkap',
        'is_bukan_pindahan', 'asal_sekolah', 'no_ijazah', 'tahun_ijazah',
        'diterima_kelas', 'pindah_dari', 'no_pindah', 'tanggal_pindah',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orangTua()
    {
        return $this->hasOne(DaftarUlangOrangTua::class, 'daftar_ulang_form_id');
    }

    public function wali()
    {
        return $this->hasOne(DaftarUlangWali::class, 'daftar_ulang_form_id');
    }

    // Helper untuk mendapatkan data wali/ortu dinamis
    public function getDataWali()
    {
        if ($this->tipe_wali === 'orang_tua') {
            return $this->orangTua;
        }
        return $this->wali;
    }
}