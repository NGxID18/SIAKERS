<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Seksi extends Model
{
    use HasFactory;

    protected $table = 'seksi';

    protected $fillable = [
        'kode_seksi',
        'nama_seksi',
        'penanggung_jawab',
        'kontak',
        'keterangan',
    ];

    public function ruangan(): HasMany
    {
        return $this->hasMany(Ruangan::class, 'seksi_id');
    }

    public function alkes(): HasMany
    {
        return $this->hasMany(Alkes::class, 'seksi_id');
    }
}
