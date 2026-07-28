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
        'nomor_seri',
        'nomenklatur_id',
        'merk',
        'tipe',
        'seksi_pemilik_id',
        'lokasi_seksi_id',
        'ruangan_id',
        'status',
        'kondisi',
        'tanggal_pengadaan',
        'nilai_aset',
        'tanggal_kalibrasi_terakhir',
        'tanggal_kalibrasi_berikutnya',
        'foto_alat',
        'catatan',
    ];

    protected $casts = [
        'status' => StatusAlkes::class,
        'kondisi' => KondisiAlkes::class,
        'tanggal_pengadaan' => 'date',
        'tanggal_kalibrasi_terakhir' => 'date',
        'tanggal_kalibrasi_berikutnya' => 'date',
        'nilai_aset' => 'decimal:2',
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

    /**
     * Seksi Pemilik Permanen Aset (Tetap / Permanen).
     */
    public function seksiPemilik(): BelongsTo
    {
        return $this->belongsTo(Seksi::class, 'seksi_pemilik_id');
    }

    /**
     * Alias backward-compatibility seksi -> seksiPemilik.
     */
    public function seksi(): BelongsTo
    {
        return $this->belongsTo(Seksi::class, 'seksi_pemilik_id');
    }

    /**
     * Lokasi Fisik Keberadaan Seksi Saat Ini (Dinamis).
     */
    public function lokasiSeksi(): BelongsTo
    {
        return $this->belongsTo(Seksi::class, 'lokasi_seksi_id');
    }

    public function ruangan(): BelongsTo
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id');
    }

    public function mutasi(): HasMany
    {
        return $this->hasMany(MutasiAlkes::class, 'alkes_id')->latest();
    }

    public function logPemeliharaan(): HasMany
    {
        return $this->hasMany(LogPemeliharaan::class, 'alkes_id')->latest();
    }

    /**
     * Mengecek apakah unit alkes sedang dipindahkan/dipinjamkan di luar seksi pemiliknya.
     */
    public function getIsDipindahkanAttribute(): bool
    {
        return $this->seksi_pemilik_id !== $this->lokasi_seksi_id;
    }
}
