<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alkes', function (Blueprint $table) {
            $table->id();
            $table->string('kode_inventaris')->unique();
            $table->string('nomor_seri')->nullable()->index();
            $table->foreignId('nomenklatur_id')->constrained('nomenklatur')->onDelete('cascade');
            $table->string('merk')->nullable();
            $table->string('tipe')->nullable();
            $table->foreignId('seksi_id')->constrained('seksi')->onDelete('cascade');
            $table->foreignId('ruangan_id')->nullable()->constrained('ruangan')->onDelete('set null');
            $table->string('status')->default('tersedia')->index();
            $table->string('kondisi')->default('baik')->index();
            $table->date('tanggal_pengadaan')->nullable();
            $table->decimal('nilai_aset', 15, 2)->default(0);
            $table->date('tanggal_kalibrasi_terakhir')->nullable();
            $table->date('tanggal_kalibrasi_berikutnya')->nullable();
            $table->string('foto_alat')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            // High-Performance Composite Index for Fast Multi-Filter Queries
            $table->index(['seksi_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alkes');
    }
};
