<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ruangan extends Model
{
    use HasFactory;

    protected $table = 'ruangan';

    protected $fillable = [
        'kode_ruangan',
        'nama_ruangan',
        'lokasi_lantai',
        'penanggung_jawab',
    ];

    /**
     * Relasi alkes penempatan asli di ruangan ini.
     */
    public function alkesAsli(): HasMany
    {
        return $this->hasMany(Alkes::class, 'ruangan_id');
    }

    /**
     * Relasi alkes penempatan asli di ruangan ini (alias alkes).
     */
    public function alkes(): HasMany
    {
        return $this->hasMany(Alkes::class, 'ruangan_id');
    }

    /**
     * Relasi alkes yang secara fisik berada di ruangan ini saat ini.
     */
    public function alkesLokasi(): HasMany
    {
        return $this->hasMany(Alkes::class, 'lokasi_ruangan_id');
    }
}
