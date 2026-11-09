<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sertifikat extends Model
{
    protected $table = 'sertifikat';

    protected $fillable = [
        'pkl_siswa_id',
        'nomor_sertifikat',
        'tanggal_terbit',
        'file_path',
        'qr_code_path',
        'verifikasi_url',
        'is_valid',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_terbit' => 'date',
            'is_valid' => 'boolean',
        ];
    }

    public function pklSiswa(): BelongsTo
    {
        return $this->belongsTo(PKLSiswa::class, 'pkl_siswa_id');
    }
}
