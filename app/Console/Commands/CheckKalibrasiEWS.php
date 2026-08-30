<?php

namespace App\Console\Commands;

use App\Mail\EwsKalibrasiMail;
use App\Models\Alkes;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class CheckKalibrasiEWS extends Command
{
    protected $signature = 'ews:check-kalibrasi';
    protected $description = 'Cek batas masa kalibrasi alkes untuk H-30 dan H-7';

    public function handle()
    {
        $h30 = now()->addDays(30)->toDateString();
        $h7 = now()->addDays(7)->toDateString();

        $alkesPeringatan = Alkes::whereIn('tanggal_kalibrasi_berikutnya', [$h30, $h7])->get();

        if ($alkesPeringatan->isEmpty()) {
            $this->info('Tidak ada alkes yang mendekati batas kalibrasi hari ini.');
            return;
        }

        foreach ($alkesPeringatan as $alkes) {
            $sisaHari = Carbon::parse($alkes->tanggal_kalibrasi_berikutnya)->diffInDays(now());
            
            // Format Hari (7 atau 30)
            $labelHari = $sisaHari <= 8 ? '7' : '30';

            // Buat notifikasi di Dashboard
            Notification::create([
                'alkes_id' => $alkes->id,
                'ruangan_asal_id' => $alkes->ruangan_id,
                'judul' => "Peringatan Kalibrasi H-{$labelHari} ({$alkes->nama_barang})",
                'pesan' => "Alat {$alkes->nama_barang} (SN: {$alkes->nomor_seri}) masa kalibrasinya akan habis pada " . Carbon::parse($alkes->tanggal_kalibrasi_berikutnya)->format('d M Y') . ". Harap segera jadwalkan kalibrasi.",
                'tipe' => 'peringatan_kalibrasi',
            ]);

            // Coba kirim email
            try {
                // Di sini Anda bisa mengubah email tujuan ke email Kepala Elektromedis
                Mail::to('kepala.elektromedis@rsjko.local')->send(new EwsKalibrasiMail($alkes, $labelHari));
                $this->info("Email EWS berhasil dikirim untuk alat: {$alkes->nama_barang}");
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("EWS Mail Error (SN: {$alkes->nomor_seri}): " . $e->getMessage());
                $this->error("Gagal mengirim email untuk alat: {$alkes->nama_barang}. Error: " . $e->getMessage());
            }
        }

        $this->info('Pengecekan EWS Kalibrasi Selesai.');
    }
}
