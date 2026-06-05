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

    public function formulir()
    {
        return $this->hasOne(Formulir::class, 'user_id');
    }

    public function calonSiswa()
    {
        return $this->hasOneThrough(
            CalonSiswa::class,
            Formulir::class,
            'user_id',          // foreign key on formulirs table
            'id',               // foreign key on calon_siswa table (CalonSiswa.id)
            'id',               // local key on users table
            'id_calon_siswa'    // local key on formulirs table
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
