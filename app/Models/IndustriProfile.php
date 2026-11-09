<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IndustriProfile extends Model
{
    protected $fillable = [
        'user_id',
        'nama_perusahaan',
        'alamat',
        'nama_pembimbing',
        'jabatan',
        'no_hp',
        'email_perusahaan',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
