<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penilaian extends Model
{
    protected $table = 'penilaian';
    protected $primaryKey = 'id_nilai';
    protected $fillable = [
        'id_penilai',
        'id_pendaftar',
        'kemampuan_membaca',
        'kemampuan_menulis',
        'kemampuan_berhitung',
        'baca_alquran',
        'catatan'
    ];

    public function penilai()
    {
        return $this->belongsTo(User::class, 'id_penilai');
    }
    public function pendaftar()
    {
        return $this->belongsTo(User::class, 'id_pendaftar');
    }
    public function seleksiTes()
    {
        return $this->hasOne(SeleksiTes::class, 'id_penilaian');
    }
}
