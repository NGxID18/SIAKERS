<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogPemeliharaan extends Model
{
    use HasFactory;

    protected $table = 'log_pemeliharaan';

    protected $fillable = [
        'alkes_id',
        'jenis_tindakan',
        'tanggal_mulai',
        'tanggal_selesai',
        'pelaksana_vendor',
        'deskripsi_kerusakan',
        'tindakan_perbaikan',
        'biaya',
        'status_hasil',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'biaya' => 'decimal:2',
    ];

    public function alkes(): BelongsTo
    {
        return $this->belongsTo(Alkes::class, 'alkes_id');
    }
}
