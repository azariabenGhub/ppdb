<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wali extends Model
{
    use HasFactory;

    protected $table = 'wali';

    protected $fillable = ['nama', 'nik', 'pekerjaan', 'agama', 'pendidikan', 'penghasilan', 'no_telp', 'alamat'];

    public function formulir()
    {
        return $this->hasOne(Formulir::class, 'id_wali');
    }
}