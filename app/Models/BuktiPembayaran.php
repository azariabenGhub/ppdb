<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuktiPembayaran extends Model
{
    protected $table = 'bukti_pembayarans';
    protected $primaryKey = 'id_bukti_pembayaran';
    protected $fillable = ['id_pendaftar', 'bukti_pembayaran', 'jenis_pembayaran', 'status'];

    public function pendaftar() {
        return $this->belongsTo(User::class, 'id_pendaftar');
    }
    public function verifikasi() {
        return $this->hasOne(VerifikasiPembayaran::class, 'id_bukti_pembayaran');
    }
    public function kwitansi() {
        return $this->hasOneThrough(Kwitansi::class, VerifikasiPembayaran::class, 'id_bukti_pembayaran', 'id_verifikasi_pembayaran');
    }
}
