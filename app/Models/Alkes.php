<?php

namespace App\Models;

use App\Enums\KondisiAlkes;
use App\Enums\StatusAlkes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Alkes extends Model
{
    use HasFactory;

    protected $table = 'alkes';

    protected $fillable = [
        'kode_inventaris',
        'nama_barang',
        'nomenklatur_id',
        'merk',
        'tipe',
        'nomor_seri',
        'tahun_pengadaan',
        'jumlah',
        'cara_perolehan',
        'nilai_perolehan',
        'ruangan_id',
        'lokasi_ruangan_id',
        'lokasi_saat_ini_note',
        'status',
        'kondisi',
        'aspak_status',
        'kib_status',
        'tanggal_kalibrasi_terakhir',
        'tanggal_kalibrasi_berikutnya',
        'foto_alat',
        'keterangan',
    ];

    protected $casts = [
        'status' => StatusAlkes::class,
        'kondisi' => KondisiAlkes::class,
        'jumlah' => 'integer',
        'nilai_perolehan' => 'decimal:2',
        'kib_status' => 'boolean',
        'tanggal_kalibrasi_terakhir' => 'date',
        'tanggal_kalibrasi_berikutnya' => 'date',
    ];

    public function getStatusEnumAttribute(): StatusAlkes
    {
        if ($this->status instanceof StatusAlkes) {
            return $this->status;
        }
        return StatusAlkes::tryFrom($this->status) ?? StatusAlkes::TERSEDIA;
    }

    public function getKondisiEnumAttribute(): KondisiAlkes
    {
        if ($this->kondisi instanceof KondisiAlkes) {
            return $this->kondisi;
        }
        return KondisiAlkes::tryFrom($this->kondisi) ?? KondisiAlkes::BAIK;
    }

    public function nomenklatur(): BelongsTo
    {
        return $this->belongsTo(Nomenklatur::class, 'nomenklatur_id');
    }

    public function ruangan(): BelongsTo
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id');
    }

    public function lokasiRuangan(): BelongsTo
    {
        return $this->belongsTo(Ruangan::class, 'lokasi_ruangan_id');
    }

    public function mutasi(): HasMany
    {
        return $this->hasMany(MutasiAlkes::class, 'alkes_id')->latest();
    }

    public function logPemeliharaan(): HasMany
    {
        return $this->hasMany(LogPemeliharaan::class, 'alkes_id')->latest();
    }

    public function getIsDipindahkanAttribute(): bool
    {
        return $this->ruangan_id !== $this->lokasi_ruangan_id;
    }
}
