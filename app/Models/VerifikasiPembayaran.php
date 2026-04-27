<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VerifikasiPembayaran extends Model
{
    protected $table = 'verifikasi_pembayarans';
    protected $primaryKey = 'id_verifikasi_pembayaran';
    protected $fillable = ['id_bukti_pembayaran', 'id_verifikator', 'hasil_verifikasi', 'catatan'];

    public function buktiPembayaran() {
        return $this->belongsTo(BuktiPembayaran::class, 'id_bukti_pembayaran');
    }
    public function kwitansi() {
        return $this->hasOne(Kwitansi::class, 'id_verifikasi_pembayaran');
    }
}
