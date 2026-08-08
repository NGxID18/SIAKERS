<?php

namespace Database\Seeders;

use App\Enums\KondisiAlkes;
use App\Enums\StatusAlkes;
use App\Models\Alkes;
use App\Models\Nomenklatur;
use App\Models\Ruangan;
use Illuminate\Database\Seeder;

class SiakerSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Master Ruangan RSJKO Engku Haji Daud (26 Ruangan CSV 2026 + Elektromedis Admin)
        $ruanganData = [
            ['nama' => 'Elektromedis', 'kode' => 'R-ELEKTROMEDIS'],
            ['nama' => 'CSSD', 'kode' => 'R-CSSD'],
            ['nama' => 'Laboratorium', 'kode' => 'R-LAB'],
            ['nama' => 'Radiologi', 'kode' => 'R-RAD'],
            ['nama' => 'UTD', 'kode' => 'R-UTD'],
            ['nama' => 'MCU', 'kode' => 'R-MCU'],
            ['nama' => 'G. Penunjang', 'kode' => 'R-PENUNJANG'],
            ['nama' => 'IGD', 'kode' => 'R-IGD'],
            ['nama' => 'IGD JIWA', 'kode' => 'R-IGD-JIWA'],
            ['nama' => 'OK', 'kode' => 'R-OK'],
            ['nama' => 'Poli Anak', 'kode' => 'R-POLI-ANAK'],
            ['nama' => 'Poli Bedah', 'kode' => 'R-POLI-BEDAH'],
            ['nama' => 'Poli gigi', 'kode' => 'R-POLI-GIGI'],
            ['nama' => 'Poli Jantung', 'kode' => 'R-POLI-JANTUNG'],
            ['nama' => 'Poli Kebidanan', 'kode' => 'R-POLI-KEBIDANAN'],
            ['nama' => 'Poli Mata', 'kode' => 'R-POLI-MATA'],
            ['nama' => 'Poli Penyakit Dalam', 'kode' => 'R-POLI-PDALAM'],
            ['nama' => 'THT', 'kode' => 'R-THT'],
            ['nama' => 'Irna Anak', 'kode' => 'R-IRNA-ANAK'],
            ['nama' => 'Irna Bedah', 'kode' => 'R-IRNA-BEDAH'],
            ['nama' => 'Irna Penyakit Dalam', 'kode' => 'R-IRNA-PDALAM'],
            ['nama' => 'Irna Perinatology', 'kode' => 'R-IRNA-PERI'],
            ['nama' => 'VK', 'kode' => 'R-VK'],
            ['nama' => 'ICU', 'kode' => 'R-ICU'],
            ['nama' => 'Fisioterapi', 'kode' => 'R-FISIO'],
            ['nama' => 'Hemodialisa', 'kode' => 'R-HEMO'],
        ];

        $ruanganModels = [];
        foreach ($ruanganData as $r) {
            $ruanganModels[strtolower($r['nama'])] = Ruangan::updateOrCreate(
                ['nama_ruangan' => $r['nama']],
                ['kode_ruangan' => $r['kode']]
            );
        }

        // 2. Nomenklatur Standard Kemenkes
        $defaultNom = Nomenklatur::updateOrCreate(
            ['kode_nomenklatur' => 'NOM-STD-01'],
            ['nama_alat' => 'Peralatan Medis RSJKO EHD', 'kategori' => 'General Medical']
        );

        // 3. Pembacaan Dinamis Data Dari 'Data Alkes 2026.csv'
        $csvFile = base_path('Data Alkes 2026.csv');

        if (!file_exists($csvFile)) {
            if (isset($this->command)) {
                $this->command->error("File CSV Data Alkes 2026.csv tidak ditemukan di: {$csvFile}");
            }
            return;
        }

        $handle = fopen($csvFile, 'r');
        if ($handle === false) {
            return;
        }

        // Skip header: No,Nama Barang,Merk,Tipe,Seri Number,Tahun,Jumlah,Ruang,Keterangan
        fgetcsv($handle);

        $counter = 1;

        while (($data = fgetcsv($handle)) !== false) {
            if (count($data) < 9) {
                continue;
            }

            $rawNo = trim($data[0]);
            $namaBarang = trim($data[1]);
            $merk = trim($data[2]);
            $tipe = trim($data[3]);
            $nomorSeri = trim($data[4]);
            $tahunPengadaan = trim($data[5]);
            $rawJumlah = trim($data[6]);
            $ruangNama = trim($data[7]);
            $keterangan = trim($data[8]);

            if (empty($namaBarang)) {
                continue;
            }

            // Map Ruangan
            $ruangKey = strtolower($ruangNama);
            if (isset($ruanganModels[$ruangKey])) {
                $rId = $ruanganModels[$ruangKey]->id;
            } else {
                $rId = $ruanganModels['cssd']->id;
            }

            // Map Jumlah
            $jumlah = (is_numeric($rawJumlah) && (int)$rawJumlah > 0) ? (int)$rawJumlah : 1;

            // Map Kondisi & Status berdasarkan Teks Keterangan
            $ketUpper = strtoupper($keterangan);
            if (str_contains($ketUpper, 'RUSAK') || str_contains($ketUpper, 'TIDAK BISA') || str_contains($ketUpper, 'ERROR') || str_contains($ketUpper, 'TIDAK DAPAT')) {
                $kondisi = KondisiAlkes::RUSAK_BERAT->value;
                $status = StatusAlkes::DALAM_PERBAIKAN->value;
            } elseif (str_contains($ketUpper, 'KURANG BAIK') || str_contains($ketUpper, 'BATERAI TIDAK BAIK')) {
                $kondisi = KondisiAlkes::RUSAK_RINGAN->value;
                $status = StatusAlkes::DALAM_PERBAIKAN->value;
            } else {
                $kondisi = KondisiAlkes::BAIK->value;
                $status = StatusAlkes::TERSEDIA->value;
            }

            // Extract Cara Perolehan if explicitly noted in CSV
            $caraPerolehan = null;
            if (str_contains($ketUpper, 'HIBAH')) {
                $caraPerolehan = 'Hibah';
            } elseif (str_contains($ketUpper, 'BELI')) {
                $caraPerolehan = 'Beli sendiri';
            }

            $invCode = sprintf('INV/ALKES/EHD/%04d', $counter);

            Alkes::updateOrCreate(
                ['kode_inventaris' => $invCode],
                [
                    'nama_barang' => $namaBarang,
                    'nomenklatur_id' => $defaultNom->id,
                    'merk' => ($merk !== '' && $merk !== '-') ? $merk : null,
                    'tipe' => ($tipe !== '' && $tipe !== '-') ? $tipe : null,
                    'nomor_seri' => ($nomorSeri !== '' && $nomorSeri !== '-') ? $nomorSeri : null,
                    'tahun_pengadaan' => ($tahunPengadaan !== '' && $tahunPengadaan !== '-') ? $tahunPengadaan : null,
                    'jumlah' => $jumlah,
                    'cara_perolehan' => $caraPerolehan,
                    'nilai_perolehan' => 0,
                    'ruangan_id' => $rId,
                    'lokasi_ruangan_id' => $rId,
                    'lokasi_saat_ini_note' => null,
                    'status' => $status,
                    'kondisi' => $kondisi,
                    'aspak_status' => 'TERDATA',
                    'kib_status' => false,
                    'keterangan' => $keterangan !== '' ? $keterangan : null,
                ]
            );

            $counter++;
        }

        fclose($handle);
    }
}
