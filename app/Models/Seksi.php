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

    /**
     * Relasi alkes milik seksi (seksi_pemilik_id).
     */
    public function alkes(): HasMany
    {
        return $this->hasMany(Alkes::class, 'seksi_pemilik_id');
    }

    /**
     * Relasi alkes yang berada di lokasi seksi ini.
     */
    public function alkesLokasi(): HasMany
    {
        return $this->hasMany(Alkes::class, 'lokasi_seksi_id');
    }
}
