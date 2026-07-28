<?php

namespace Database\Seeders;

use App\Enums\KondisiAlkes;
use App\Enums\StatusAlkes;
use App\Models\Alkes;
use App\Models\LogPemeliharaan;
use App\Models\MutasiAlkes;
use App\Models\Nomenklatur;
use App\Models\Ruangan;
use App\Models\Seksi;
use Illuminate\Database\Seeder;

class SiakerSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Seksi (6 Seksi Operasional RS)
        $seksisData = [
            [
                'kode_seksi' => 'SEK-PENUNJANG',
                'nama_seksi' => 'Seksi Penunjang Medis',
                'penanggung_jawab' => 'dr. H. Ahmad Fauzi, Sp.PK',
                'kontak' => '0812-3456-7890',
                'keterangan' => 'Mengelola peralatan laboratorium, radiologi, farmasi, dan rekam medis.',
            ],
            [
                'kode_seksi' => 'SEK-PELAYANAN',
                'nama_seksi' => 'Seksi Pelayanan Medis',
                'penanggung_jawab' => 'dr. Siti Rahma, Sp.B',
                'kontak' => '0813-9876-5432',
                'keterangan' => 'Mengelola peralatan poliklinik rawat jalan, IGD, dan kamar bedah.',
            ],
            [
                'kode_seksi' => 'SEK-KEPERAWATAN',
                'nama_seksi' => 'Seksi Keperawatan & Rawat Inap',
                'penanggung_jawab' => 'Ns. Budi Santoso, S.Kep',
                'kontak' => '0857-1122-3344',
                'keterangan' => 'Mengelola alat medis di ruang perawatan umum, VIP, dan bangsal.',
            ],
            [
                'kode_seksi' => 'SEK-ICU',
                'nama_seksi' => 'Seksi Intensive Care Unit (ICU/ICCU/NICU)',
                'penanggung_jawab' => 'dr. Bambang Irawan, Sp.An-KIC',
                'kontak' => '0818-7788-9900',
                'keterangan' => 'Mengelola peralatan perawatan intensif dan penunjang hidup (life support).',
            ],
            [
                'kode_seksi' => 'SEK-REHAB',
                'nama_seksi' => 'Seksi Rehabilitasi Medis & Fisioterapi',
                'penanggung_jawab' => 'dr. Rina Astuti, Sp.KFR',
                'kontak' => '0821-4455-6677',
                'keterangan' => 'Mengelola alat terapi fisik, pemulihan fungsi tubuh, dan biomekanik.',
            ],
            [
                'kode_seksi' => 'SEK-GUDANG',
                'nama_seksi' => 'Gudang Pusat Alkes & ATEM',
                'penanggung_jawab' => 'Ir. Hendra Gunawan (ATEM)',
                'kontak' => '0811-2233-4455',
                'keterangan' => 'Penyimpanan cadangan alat kesehatan buffer RS dan workshop teknis pemeliharaan.',
            ],
        ];

        $seksiModels = [];
        foreach ($seksisData as $sData) {
            $seksiModels[$sData['kode_seksi']] = Seksi::firstOrCreate(
                ['kode_seksi' => $sData['kode_seksi']],
                $sData
            );
        }

        // 2. Seed Ruangan per Seksi
        $ruanganData = [
            'SEK-PENUNJANG' => [
                ['kode_ruangan' => 'R-LAB-01', 'nama_ruangan' => 'Laboratorium Patologi Klinik', 'lokasi_lantai' => 'Lantai 1 Gedung B'],
                ['kode_ruangan' => 'R-RAD-01', 'nama_ruangan' => 'Ruang Radiologi & CT Scan', 'lokasi_lantai' => 'Lantai 1 Gedung A'],
                ['kode_ruangan' => 'R-FAR-01', 'nama_ruangan' => 'Depo Farmasi Central', 'lokasi_lantai' => 'Lantai 1 Gedung Utama'],
            ],
            'SEK-PELAYANAN' => [
                ['kode_ruangan' => 'R-IGD-01', 'nama_ruangan' => 'Resusitasi & Triase IGD', 'lokasi_lantai' => 'Lantai 1 Gedung Utama'],
                ['kode_ruangan' => 'R-OK-01', 'nama_ruangan' => 'Kamar Bedah Central OK-1', 'lokasi_lantai' => 'Lantai 2 Gedung Utama'],
                ['kode_ruangan' => 'R-POLI-01', 'nama_ruangan' => 'Poliklinik Jantung & Spesialis', 'lokasi_lantai' => 'Lantai 2 Gedung B'],
            ],
            'SEK-KEPERAWATAN' => [
                ['kode_ruangan' => 'R-RI-VIP', 'nama_ruangan' => 'Rawat Inap VIP Pavilion', 'lokasi_lantai' => 'Lantai 3 Gedung B'],
                ['kode_ruangan' => 'R-RI-CLASS1', 'nama_ruangan' => 'Rawat Inap Kelas 1 Anggrek', 'lokasi_lantai' => 'Lantai 3 Gedung A'],
                ['kode_ruangan' => 'R-PERI-01', 'nama_ruangan' => 'Ruang Perinatologi', 'lokasi_lantai' => 'Lantai 2 Gedung A'],
            ],
            'SEK-ICU' => [
                ['kode_ruangan' => 'R-ICU-MAIN', 'nama_ruangan' => 'Ruang Utama ICU Bed 1-6', 'lokasi_lantai' => 'Lantai 3 Gedung Utama'],
                ['kode_ruangan' => 'R-ICCU-01', 'nama_ruangan' => 'Ruang Intensive Cardiac Care Unit', 'lokasi_lantai' => 'Lantai 3 Gedung Utama'],
                ['kode_ruangan' => 'R-NICU-01', 'nama_ruangan' => 'Ruang Neonatal ICU (NICU)', 'lokasi_lantai' => 'Lantai 3 Gedung A'],
            ],
            'SEK-REHAB' => [
                ['kode_ruangan' => 'R-FISIO-01', 'nama_ruangan' => 'Ruang Elektroterapi & Diatermi', 'lokasi_lantai' => 'Lantai 1 Gedung C'],
                ['kode_ruangan' => 'R-FISIO-02', 'nama_ruangan' => 'Gymnasium Rehabilitasi & Mekanoterapi', 'lokasi_lantai' => 'Lantai 1 Gedung C'],
                ['kode_ruangan' => 'R-HIDRO-01', 'nama_ruangan' => 'Ruang Hidroterapi', 'lokasi_lantai' => 'Basemen Gedung C'],
            ],
            'SEK-GUDANG' => [
                ['kode_ruangan' => 'R-GUDANG-01', 'nama_ruangan' => 'Gudang Utama Inventaris Buffer', 'lokasi_lantai' => 'Basemen Gedung B'],
                ['kode_ruangan' => 'R-ATEM-WS', 'nama_ruangan' => 'Workshop Kalibrasi & Service ATEM', 'lokasi_lantai' => 'Basemen Gedung B'],
            ],
        ];

        $ruanganModels = [];
        foreach ($ruanganData as $kodeSeksi => $rList) {
            $seksiObj = $seksiModels[$kodeSeksi];
            foreach ($rList as $rItem) {
                $ruanganModels[$rItem['kode_ruangan']] = Ruangan::firstOrCreate(
                    ['kode_ruangan' => $rItem['kode_ruangan']],
                    [
                        'seksi_id' => $seksiObj->id,
                        'nama_ruangan' => $rItem['nama_ruangan'],
                        'lokasi_lantai' => $rItem['lokasi_lantai'],
                    ]
                );
            }
        }

        // 3. Seed Master Nomenklatur Alkes
        $nomenklaturData = [
            ['kode' => 'NOM-VENT-01', 'nama' => 'Ventilator Intensive Care Unit', 'kat' => 'Life Support', 'desk' => 'Alat ventilator mekanis pasien kritis.'],
            ['kode' => 'NOM-DEF-01', 'nama' => 'Defibrillator Biphasic dengan Monitor', 'kat' => 'Emergency', 'desk' => 'Alat resusitasi jantung pasca henti jantung.'],
            ['kode' => 'NOM-EKG-01', 'nama' => 'Elektrokardiograf (EKG 12-Lead)', 'kat' => 'Diagnostic', 'desk' => 'Perekam sinyal gelombang kelistrikan jantung.'],
            ['kode' => 'NOM-USG-01', 'nama' => 'USG Color Doppler 4D Imaging', 'kat' => 'Radiology', 'desk' => 'Pencitraan ultrasonografi organ internal dan kehamilan.'],
            ['kode' => 'NOM-SYP-01', 'nama' => 'Syringe Infusion Pump Digital', 'kat' => 'Drug Delivery', 'desk' => 'Pompa injeksi obat cair presisi tinggi.'],
            ['kode' => 'NOM-INFP-01', 'nama' => 'Volumetric Infusion Pump', 'kat' => 'Drug Delivery', 'desk' => 'Pompa tetes cairan infus konstan.'],
            ['kode' => 'NOM-MON-01', 'nama' => 'Bedside Patient Monitor 5-Para', 'kat' => 'Monitoring', 'desk' => 'Pemantau tanda vital pasien real-time.'],
            ['kode' => 'NOM-SUC-01', 'nama' => 'Medical Suction Pump Portable', 'kat' => 'Airway Support', 'desk' => 'Alat penyedot cairan dan lender saluran napas.'],
            ['kode' => 'NOM-CT-01', 'nama' => 'CT-Scan 128 Slice Scanner', 'kat' => 'Radiology', 'desk' => 'Alat pemindai tomografi komputer organ tubuh.'],
            ['kode' => 'NOM-INC-01', 'nama' => 'Infant Incubator Transport', 'kat' => 'Pediatric', 'desk' => 'Inkubator penghangat bayi prematur.'],
            ['kode' => 'NOM-SWD-01', 'nama' => 'Shortwave Diathermy (SWD) Unit', 'kat' => 'Rehabilitation', 'desk' => 'Alat terapi gelombang pendek penghangat jaringan dalam.'],
            ['kode' => 'NOM-TENS-01', 'nama' => 'TENS & Electrotherapy Combo Unit', 'kat' => 'Rehabilitation', 'desk' => 'Alat stimulasi saraf transkutan pereda nyeri otot.'],
            ['kode' => 'NOM-CAL-01', 'nama' => 'Defibrillator & Safety Analyzer ATEM', 'kat' => 'Calibration', 'desk' => 'Alat penguji kalibrasi dan keselamatan listrik medis.'],
            ['kode' => 'NOM-ANA-01', 'nama' => 'Mesin Anestesi dengan Vaporizer', 'kat' => 'Surgery', 'desk' => 'Alat pemberian gas pembius ruang bedah.'],
            ['kode' => 'NOM-AUTO-01', 'nama' => 'Autoclave Sterilizer Steam 150L', 'kat' => 'Sterilization', 'desk' => 'Alat sterilisasi instrumen medis uap panas.'],
        ];

        $nomModels = [];
        foreach ($nomenklaturData as $nData) {
            $nomModels[$nData['kode']] = Nomenklatur::firstOrCreate(
                ['kode_nomenklatur' => $nData['kode']],
                [
                    'nama_alat' => $nData['nama'],
                    'kategori' => $nData['kat'],
                    'deskripsi' => $nData['desk'],
                ]
            );
        }

        // 4. Seed Minimal 20 Alkes PER SEKSI (Total = 6 x 20 = 120 Unit Alkes)
        $brandList = ['Draeger', 'Siemens', 'GE Healthcare', 'Philips', 'Terumo', 'Mindray', 'Nihon Kohden', 'Zoll', 'Olympus', 'BTL Medical', 'Fukuda Denshi', 'Stryker'];
        $statusValues = [StatusAlkes::TERSEDIA->value, StatusAlkes::SEDANG_DIGUNAKAN->value, StatusAlkes::DALAM_PERBAIKAN->value];
        $kondisiValues = [KondisiAlkes::BAIK->value, KondisiAlkes::BAIK->value, KondisiAlkes::RUSAK_RINGAN->value];

        $globalCounter = 1;

        foreach ($seksisData as $sIndex => $sData) {
            $kodeSeksi = $sData['kode_seksi'];
            $seksiObj = $seksiModels[$kodeSeksi];
            $rList = array_values(array_filter($ruanganModels, fn($r) => $r->seksi_id == $seksiObj->id));

            for ($i = 1; $i <= 20; $i++) {
                $invCode = sprintf('INV/ALKES/2024/%03d', $globalCounter);
                $snCode = 'SN-' . strtoupper(substr($kodeSeksi, 4, 3)) . '-' . (100000 + $globalCounter);
                $nomKey = array_keys($nomModels)[($globalCounter - 1) % count($nomModels)];
                $nomObj = $nomModels[$nomKey];
                $ruangObj = $rList[($i - 1) % count($rList)] ?? reset($rList);
                $brand = $brandList[($globalCounter - 1) % count($brandList)];
                $status = $statusValues[($i - 1) % count($statusValues)];
                $kondisi = ($status == StatusAlkes::DALAM_PERBAIKAN->value) ? KondisiAlkes::RUSAK_RINGAN->value : KondisiAlkes::BAIK->value;
                $assetVal = rand(15, 450) * 1000000;

                Alkes::firstOrCreate(
                    ['kode_inventaris' => $invCode],
                    [
                        'nomor_seri' => $snCode,
                        'nomenklatur_id' => $nomObj->id,
                        'merk' => $brand,
                        'tipe' => 'Series-' . rand(10, 99) . ' Pro',
                        'seksi_id' => $seksiObj->id,
                        'ruangan_id' => $ruangObj->id,
                        'status' => $status,
                        'kondisi' => $kondisi,
                        'tanggal_pengadaan' => date('Y-m-d', strtotime("-" . rand(6, 36) . " months")),
                        'nilai_aset' => $assetVal,
                        'tanggal_kalibrasi_terakhir' => '2025-01-15',
                        'tanggal_kalibrasi_berikutnya' => '2026-01-15',
                        'catatan' => "Unit aset terdaftar di {$seksiObj->nama_seksi} ({$ruangObj->nama_ruangan}). Berfungsi normal.",
                    ]
                );

                $globalCounter++;
            }
        }

        // 5. Seed sampel Mutasi Alkes & Log Pemeliharaan
        $sampleAlkes = Alkes::first();
        if ($sampleAlkes) {
            MutasiAlkes::firstOrCreate(
                ['alkes_id' => $sampleAlkes->id, 'tanggal_mutasi' => '2025-01-10 08:00:00'],
                [
                    'seksi_asal_id' => $seksiModels['SEK-GUDANG']->id,
                    'seksi_tujuan_id' => $seksiModels['SEK-PENUNJANG']->id,
                    'ruangan_asal_id' => $ruanganModels['R-GUDANG-01']->id ?? null,
                    'ruangan_tujuan_id' => $ruanganModels['R-LAB-01']->id ?? null,
                    'pemohon' => 'dr. H. Ahmad Fauzi, Sp.PK',
                    'penanggung_jawab' => 'Ir. Hendra Gunawan (ATEM)',
                    'alasan_mutasi' => 'Penambahan unit cadangan untuk seksi penunjang medis.',
                    'status_persetujuan' => 'Disetujui',
                ]
            );

            LogPemeliharaan::firstOrCreate(
                ['alkes_id' => $sampleAlkes->id, 'tanggal_mulai' => '2025-02-01'],
                [
                    'tanggal_selesai' => '2025-02-02',
                    'pelaksana_vendor' => 'Teknisi ATEM RS',
                    'deskripsi_kerusakan' => 'Pemeriksaan rutin & kalibrasi berkala.',
                    'tindakan_perbaikan' => 'Pembersihan elemen internal & uji fungsi.',
                    'biaya' => 0.00,
                    'status_hasil' => 'Selesai',
                ]
            );
        }
    }
}
