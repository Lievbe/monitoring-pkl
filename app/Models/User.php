<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'avatar_path'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function siswaProfile(): HasOne
    {
        return $this->hasOne(SiswaProfile::class);
    }

    public function guruProfile(): HasOne
    {
        return $this->hasOne(GuruProfile::class);
    }

    public function industriProfile(): HasOne
    {
        return $this->hasOne(IndustriProfile::class);
    }

    public function placementsAsStudent(): HasMany
    {
        return $this->hasMany(PKLSiswa::class, 'siswa_id');
    }

    public function placementsAsTeacher(): HasMany
    {
        return $this->hasMany(PKLSiswa::class, 'guru_id');
    }

    public function placementsAsIndustry(): HasMany
    {
        return $this->hasMany(PKLSiswa::class, 'industri_id');
    }

    public function penilaianAsTeacher(): HasMany
    {
        return $this->hasMany(PenilaianSiswa::class, 'guru_id');
    }

    public function verifiedLogbooks(): HasMany
    {
        return $this->hasMany(LogbookHarian::class, 'verified_by');
    }

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
        ];
    }
}
