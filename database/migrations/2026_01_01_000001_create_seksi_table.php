<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seksi', function (Blueprint $table) {
            $table->id();
            $table->string('kode_seksi')->unique();
            $table->string('nama_seksi');
            $table->string('penanggung_jawab')->nullable();
            $table->string('kontak')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seksi');
    }
};
