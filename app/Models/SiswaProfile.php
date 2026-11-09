<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiswaProfile extends Model
{
    protected $fillable = [
        'user_id',
        'nisn',
        'nis',
        'nama_lengkap',
        'jurusan',
        'kelas',
        'tanggal_lahir',
        'no_hp',
        'alamat',
        'foto_profil',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
