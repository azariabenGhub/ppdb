<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ibu extends Model
{
    use HasFactory;

    protected $table = 'ibu';

    protected $fillable = ['nama', 'nik', 'pekerjaan', 'agama', 'pendidikan', 'penghasilan', 'no_telp', 'alamat'];

    public function formulir()
    {
        return $this->hasOne(Formulir::class, 'id_ibu');
    }
}