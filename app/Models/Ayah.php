<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ayah extends Model
{
    use HasFactory;

    protected $table = 'ayah';

    protected $fillable = ['nama', 'nik', 'pekerjaan', 'agama', 'pendidikan', 'penghasilan', 'no_telp', 'alamat'];

    public function formulir()
    {
        return $this->hasOne(Formulir::class, 'id_ayah');
    }
}