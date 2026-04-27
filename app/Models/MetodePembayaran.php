<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetodePembayaran extends Model
{
    protected $fillable = ['nama_bank', 'nomor_rekening', 'atas_nama', 'gambar_qris', 'keterangan'];
}
