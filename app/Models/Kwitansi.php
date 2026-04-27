<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kwitansi extends Model
{
    protected $table = 'kwitansis';
    protected $primaryKey = 'id_kwitansi';
    protected $fillable = ['id_verifikasi_pembayaran', 'id_pendaftar', 'kwitansi'];
}
