<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeleksiTes extends Model
{
    protected $table = 'seleksi_tes';
    protected $primaryKey = 'id_seleksi_tes';
    protected $fillable = [
        'id_pendaftar',
        'id_penjadwal',
        'id_penilaian',
        'jadwal_tes',
        'kelulusan_tes'
    ];

    public function pendaftar()
    {
        return $this->belongsTo(User::class, 'id_pendaftar');
    }
    public function penjadwal()
    {
        return $this->belongsTo(User::class, 'id_penjadwal');
    }
    public function penilaian()
    {
        return $this->belongsTo(Penilaian::class, 'id_penilaian');
    }
}
