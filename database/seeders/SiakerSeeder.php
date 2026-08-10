<?php

namespace Database\Seeders;

use App\Enums\KondisiAlkes;
use App\Enums\StatusAlkes;
use App\Models\Alkes;
use App\Models\Nomenklatur;
use App\Models\Ruangan;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SiakerSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Master Ruangan RSJKO Engku Haji Daud (26 Ruangan Asli dari CSV Data Alkes 2026)
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
            $ruanganModels[strtolower(trim($r['nama']))] = Ruangan::updateOrCreate(
                ['nama_ruangan' => $r['nama']],
                ['kode_ruangan' => $r['kode']]
            );
        }

        // 2. Nomenklatur Standard Kemenkes
        $defaultNom = Nomenklatur::updateOrCreate(
            ['kode_nomenklatur' => 'NOM-STD-01'],
            ['nama_alat' => 'Peralatan Medis RSJKO EHD', 'kategori' => 'General Medical']
        );

        // 3. Import Data 100% Asli dari 'Data Alkes 2026.csv'
        $csvFile = base_path('Data Alkes 2026.csv');

        if (!file_exists($csvFile)) {
            return;
        }

        $handle = fopen($csvFile, 'r');
        if ($handle === false) {
            return;
        }

        // Skip CSV Header: No,Nama Barang,Merk,Tipe,Seri Number,Tahun,Jumlah,Ruang,Keterangan
        fgetcsv($handle);

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

            // Ruangan Mapping (Original from CSV)
            $ruangKey = strtolower(trim($ruangNama));
            if (isset($ruanganModels[$ruangKey])) {
                $rId = $ruanganModels[$ruangKey]->id;
            } else {
                $rId = $ruanganModels['cssd']->id;
            }

            // Quantity
            $jumlah = (is_numeric($rawJumlah) && (int)$rawJumlah > 0) ? (int)$rawJumlah : 1;

            // Kondisi & Status Mapping
            $ketUpper = strtoupper($keterangan);
            if (str_contains($ketUpper, '1 BAIK, 1 RUSAK')) {
                $kondisi = KondisiAlkes::RUSAK_RINGAN->value;
                $status = StatusAlkes::DALAM_PERBAIKAN->value;
            } elseif (str_contains($ketUpper, 'RUSAK') || str_contains($ketUpper, 'TIDAK BISA') || str_contains($ketUpper, 'ERROR')) {
                $kondisi = KondisiAlkes::RUSAK_BERAT->value;
                $status = StatusAlkes::DALAM_PERBAIKAN->value;
            } else {
                $kondisi = KondisiAlkes::BAIK->value;
                $status = StatusAlkes::TERSEDIA->value;
            }

            // Kalibrasi Status Mapping (Berdasarkan Keterangan CSV Asli)
            $tglKalibrasiTerakhir = null;
            $tglKalibrasiBerikutnya = null;
            if (str_contains($ketUpper, 'SUDAH DIKALIBRASI')) {
                $tglKalibrasiTerakhir = '2025-08-10';
                $tglKalibrasiBerikutnya = '2026-08-10';
            }

            // Simpan Data Asli tanpa Modifikasi Kode Inventaris Palsu
            $invCode = 'ALT-' . str_pad($rawNo, 4, '0', STR_PAD_LEFT);

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
                    'cara_perolehan' => null,
                    'nilai_perolehan' => 0,
                    'ruangan_id' => $rId,
                    'lokasi_ruangan_id' => $rId,
                    'lokasi_saat_ini_note' => null,
                    'status' => $status,
                    'kondisi' => $kondisi,
                    'aspak_status' => 'TERDATA',
                    'kib_status' => false,
                    'tanggal_kalibrasi_terakhir' => $tglKalibrasiTerakhir,
                    'tanggal_kalibrasi_berikutnya' => $tglKalibrasiBerikutnya,
                    'keterangan' => $keterangan !== '' ? $keterangan : null,
                ]
            );
        }

        fclose($handle);
    }
}
