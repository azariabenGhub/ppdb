<?php
namespace App\Models;

use App\Models\Formulir;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerifikasiFormulir extends Model
{
    use HasFactory;

    protected $table = 'verifikasi_formulirs';
    protected $primaryKey = 'id_verform';

    protected $fillable = [
        'id_verifikator',
        'id_formulir',
        'hasil_verifikasi',
        'catatan',
    ];

    public function formulir()
    {
        return $this->belongsTo(Formulir::class, 'id_formulir');
    }

    public function verifikator()
    {
        return $this->belongsTo(User::class, 'id_verifikator');
    }
}