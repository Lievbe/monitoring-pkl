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
        'logo_sekolah_path',
        'logo_perusahaan_path',
        'nama_sekolah',
        'nama_perusahaan',
        'nama_pembimbing',
        'logos_snapshot_at',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_terbit' => 'date',
            'is_valid' => 'boolean',
            'logos_snapshot_at' => 'datetime',
        ];
    }

    public function pklSiswa(): BelongsTo
    {
        return $this->belongsTo(PKLSiswa::class, 'pkl_siswa_id');
    }
}
