<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolSetting extends Model
{
    protected $fillable = ['nama_sekolah', 'logo_path', 'alamat', 'informasi'];

    public static function current(): self
    {
        return static::query()->firstOrCreate(['id' => 1], ['nama_sekolah' => 'SMKN 43 Jakarta']);
    }
}
