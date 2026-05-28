<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Formulir;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password', 'role', 'google_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => 'string'
        ];
    }

    public function calonSiswa()
    {
        return $this->hasOne(CalonSiswa::class);
    }

    // Relasi ke Formulir melalui CalonSiswa
    public function formulir()
    {
        return $this->hasOneThrough(
            Formulir::class,
            CalonSiswa::class,
            'user_id',        // foreign key di calon_siswa
            'id_calon_siswa', // foreign key di formulirs
            'id',             // local key di users
            'id'              // local key di calon_siswa
        );
    }

    public function buktiPembayaran()
    {
        return $this->hasMany(BuktiPembayaran::class, 'id_pendaftar');
    }

    public function seleksiTes()
    {
        return $this->hasOne(SeleksiTes::class, 'id_pendaftar');
    }

    public function daftarUlang()
    {
        return $this->hasOne(DaftarUlang::class, 'user_id');
    }
}
