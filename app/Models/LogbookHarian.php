<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogbookHarian extends Model
{
    protected $table = 'logbook_harian';

    protected $fillable = [
        'pkl_siswa_id',
        'tanggal',
        'kegiatan',
        'keterangan',
        'foto_bukti',
        'status_kehadiran',
        'jam_masuk',
        'jam_keluar',
        'verifikasi_status',
        'catatan_guru',
        'verified_by',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'verified_at' => 'datetime',
        ];
    }

    public function pklSiswa(): BelongsTo
    {
        return $this->belongsTo(PKLSiswa::class, 'pkl_siswa_id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
