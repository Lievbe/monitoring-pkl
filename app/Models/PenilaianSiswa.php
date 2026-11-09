<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenilaianSiswa extends Model
{
    protected $table = 'penilaian_siswa';

    protected $fillable = [
        'pkl_siswa_id',
        'guru_id',
        'hard_skill',
        'soft_skill',
        'nilai_akhir',
        'predikat',
        'catatan',
        'tanggal_penilaian',
    ];

    protected function casts(): array
    {
        return [
            'hard_skill' => 'integer',
            'soft_skill' => 'integer',
            'nilai_akhir' => 'integer',
            'tanggal_penilaian' => 'date',
        ];
    }

    public function pklSiswa(): BelongsTo
    {
        return $this->belongsTo(PKLSiswa::class, 'pkl_siswa_id');
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guru_id');
    }
}
