<?php

namespace App\Models;

use App\Models\Ayah;
use App\Models\CalonSiswa;
use App\Models\Gelombang;
use App\Models\Ibu;
use App\Models\VerifikasiFormulir;
use App\Models\Wali;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Formulir extends Model
{
    use HasFactory;

    protected $table = 'formulirs';

    protected $fillable = [
        'user_id',
        'id_calon_siswa',
        'id_ayah',
        'id_ibu',
        'id_wali',
        'no_pendaftaran',
        'id_gelombang',
        'tipe_wali',
        'is_bukan_pindahan',
        'asal_sekolah',
        'no_ijazah',
        'tahun_ijazah',
        'diterima_kelas',
        'pindah_dari',
        'no_pindah',
        'tanggal_pindah',
        'status'
    ];

    protected $appends = [
        'nama_lengkap',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'pernah_tk',
        'asal_tk',
        'nik',
        'agama',
        'warga_negara',
        'anak_ke',
        'jumlah_saudara',
        'alamat_lengkap',
        'nama_ayah',
        'pekerjaan_ayah',
        'agama_ayah',
        'pendidikan_ayah',
        'no_ktp_ayah',
        'penghasilan_ayah',
        'no_telp_ayah',
        'alamat_ayah',
        'nama_ibu',
        'pekerjaan_ibu',
        'agama_ibu',
        'pendidikan_ibu',
        'no_ktp_ibu',
        'penghasilan_ibu',
        'no_telp_ibu',
        'alamat_ibu',
        'nama_wali',
        'pekerjaan_wali',
        'agama_wali',
        'pendidikan_wali',
        'no_ktp_wali',
        'penghasilan_wali',
        'no_telp_wali',
        'alamat_wali',
        'punya_nisn',
        'nisn',
    ];

    // Relasi
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function calonSiswa()
    {
        return $this->belongsTo(CalonSiswa::class, 'id_calon_siswa');
    }

    public function ayah()
    {
        return $this->belongsTo(Ayah::class, 'id_ayah');
    }

    public function ibu()
    {
        return $this->belongsTo(Ibu::class, 'id_ibu');
    }

    public function wali()
    {
        return $this->belongsTo(Wali::class, 'id_wali');
    }

    public function gelombang()
    {
        return $this->belongsTo(Gelombang::class, 'id_gelombang');
    }

    public function verifikasi()
    {
        return $this->hasOne(VerifikasiFormulir::class, 'id_formulir');
    }

    // Accessor untuk atribut yang dulu langsung di formulir
    public function getNamaLengkapAttribute()
    {
        return $this->calonSiswa ? $this->calonSiswa->nama_lengkap : null;
    }

    public function getTempatLahirAttribute()
    {
        return $this->calonSiswa ? $this->calonSiswa->tempat_lahir : null;
    }

    public function getTanggalLahirAttribute()
    {
        return $this->calonSiswa ? $this->calonSiswa->tanggal_lahir : null;
    }

    public function getNikAttribute()
    {
        return $this->calonSiswa ? $this->calonSiswa->nik : null;
    }

    public function getAgamaAttribute()
    {
        return $this->calonSiswa ? $this->calonSiswa->agama : null;
    }

    public function getWargaNegaraAttribute()
    {
        return $this->calonSiswa ? $this->calonSiswa->warga_negara : null;
    }

    public function getAnakKeAttribute()
    {
        return $this->calonSiswa ? $this->calonSiswa->anak_ke : null;
    }

    public function getJumlahSaudaraAttribute()
    {
        return $this->calonSiswa ? $this->calonSiswa->jumlah_saudara : null;
    }

    public function getAlamatLengkapAttribute()
    {
        return $this->calonSiswa ? $this->calonSiswa->alamat_lengkap : null;
    }

    // Data ayah
    public function getNamaAyahAttribute()
    {
        return $this->ayah ? $this->ayah->nama : null;
    }

    public function getPekerjaanAyahAttribute()
    {
        return $this->ayah ? $this->ayah->pekerjaan : null;
    }

    public function getAgamaAyahAttribute()
    {
        return $this->ayah ? $this->ayah->agama : null;
    }

    public function getPendidikanAyahAttribute()
    {
        return $this->ayah ? $this->ayah->pendidikan : null;
    }

    public function getNoKtpAyahAttribute()
    {
        return $this->ayah ? $this->ayah->nik : null;
    }

    public function getPenghasilanAyahAttribute()
    {
        return $this->ayah ? $this->ayah->penghasilan : null;
    }

    public function getNoTelpAyahAttribute()
    {
        return $this->ayah ? $this->ayah->no_telp : null;
    }

    public function getAlamatAyahAttribute()
    {
        return $this->ayah ? $this->ayah->alamat : null;
    }

    // Data ibu
    public function getNamaIbuAttribute()
    {
        return $this->ibu ? $this->ibu->nama : null;
    }

    public function getPekerjaanIbuAttribute()
    {
        return $this->ibu ? $this->ibu->pekerjaan : null;
    }

    public function getAgamaIbuAttribute()
    {
        return $this->ibu ? $this->ibu->agama : null;
    }

    public function getPendidikanIbuAttribute()
    {
        return $this->ibu ? $this->ibu->pendidikan : null;
    }

    public function getNoKtpIbuAttribute()
    {
        return $this->ibu ? $this->ibu->nik : null;
    }

    public function getPenghasilanIbuAttribute()
    {
        return $this->ibu ? $this->ibu->penghasilan : null;
    }

    public function getNoTelpIbuAttribute()
    {
        return $this->ibu ? $this->ibu->no_telp : null;
    }

    public function getAlamatIbuAttribute()
    {
        return $this->ibu ? $this->ibu->alamat : null;
    }

    // Data wali
    public function getNamaWaliAttribute()
    {
        return $this->wali ? $this->wali->nama : null;
    }

    public function getPekerjaanWaliAttribute()
    {
        return $this->wali ? $this->wali->pekerjaan : null;
    }

    public function getAgamaWaliAttribute()
    {
        return $this->wali ? $this->wali->agama : null;
    }

    public function getPendidikanWaliAttribute()
    {
        return $this->wali ? $this->wali->pendidikan : null;
    }

    public function getNoKtpWaliAttribute()
    {
        return $this->wali ? $this->wali->nik : null;
    }

    public function getPenghasilanWaliAttribute()
    {
        return $this->wali ? $this->wali->penghasilan : null;
    }

    public function getNoTelpWaliAttribute()
    {
        return $this->wali ? $this->wali->no_telp : null;
    }

    public function getAlamatWaliAttribute()
    {
        return $this->wali ? $this->wali->alamat : null;
    }

    public function getPunyaNisnAttribute()
    {
        return $this->calonSiswa ? $this->calonSiswa->punya_nisn : false;
    }

    public function getNisnAttribute()
    {
        return $this->calonSiswa ? $this->calonSiswa->nisn : null;
    }

    public function getPernahTkAttribute()
    {
        return $this->calonSiswa ? $this->calonSiswa->pernah_tk : false;
    }

    public function getAsalTkAttribute()
    {
        return $this->calonSiswa ? $this->calonSiswa->asal_tk : null;
    }

    public function getJenisKelaminAttribute()
    {
        return $this->calonSiswa ? $this->calonSiswa->jenis_kelamin : null;
    }
}