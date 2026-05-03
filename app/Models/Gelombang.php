<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gelombang extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_gelombang',
        'tahun',
        'periode_mulai',
        'periode_selesai',
        'kuota',
        'biaya_formulir',
        'biaya_daftar_ulang',
        'status'
    ];

    protected $casts = [
        'periode_mulai' => 'datetime',
        'periode_selesai' => 'datetime',
        'tahun' => 'integer',
    ];

    protected $appends = ['sisa_kuota'];

    // Hitung sisa kuota (pendaftar yang sudah terdaftar di gelombang ini)
    public function getSisaKuotaAttribute()
    {
        $terdaftar = Formulir::where('id_gelombang', $this->id)->count();
        return $this->kuota - $terdaftar;
    }

    public function formulirs()
    {
        return $this->hasMany(Formulir::class, 'id_gelombang');
    }

    public function isAktif()
    {
        $now = now();
        return $this->status === 'aktif' && $now >= $this->periode_mulai && $now <= $this->periode_selesai;
    }
}
