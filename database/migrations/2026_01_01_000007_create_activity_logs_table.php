<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('user_role')->default('Seksi Operasional');
            $table->string('seksi_name')->default('Seksi Penunjang Medis');
            $table->string('action'); // Tambah Alkes, Edit Alkes, Hapus Alkes, Mutasi Alkes, Lapor Perbaikan, Login
            $table->text('description');
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
