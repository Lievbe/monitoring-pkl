<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PKLSiswa extends Model
{
    protected $table = 'pkl_siswa';

    protected $fillable = [
        'siswa_id',
        'guru_id',
        'industri_id',
        'guru_nama',
        'guru_nip',
        'nama_perusahaan',
        'nama_pembimbing',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
        ];
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(User::class, 'siswa_id');
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    public function industri(): BelongsTo
    {
        return $this->belongsTo(User::class, 'industri_id');
    }

    public function logbooks(): HasMany
    {
        return $this->hasMany(LogbookHarian::class, 'pkl_siswa_id');
    }

    public function penilaian(): HasOne
    {
        return $this->hasOne(PenilaianSiswa::class, 'pkl_siswa_id');
    }

    public function sertifikat(): HasOne
    {
        return $this->hasOne(Sertifikat::class, 'pkl_siswa_id');
    }
}
