<?php

namespace Database\Seeders;

use App\Enums\KondisiAlkes;
use App\Enums\StatusAlkes;
use App\Models\ActivityLog;
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
        // 1. Seed 6 Seksi Operasional RS
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
            $seksiModels[$sData['kode_seksi']] = Seksi::updateOrCreate(
                ['kode_seksi' => $sData['kode_seksi']],
                $sData
            );
        }

        $seksiListArray = array_values($seksiModels);

        // 2. Seed 24 Ruangan Spesifik (4 Ruangan per Seksi)
        $ruanganData = [
            'SEK-PENUNJANG' => [
                ['kode_ruangan' => 'R-LAB-01', 'nama_ruangan' => 'Laboratorium Patologi Klinik', 'lokasi_lantai' => 'Lantai 1 Gedung B'],
                ['kode_ruangan' => 'R-RAD-01', 'nama_ruangan' => 'Ruang Radiologi & CT Scan', 'lokasi_lantai' => 'Lantai 1 Gedung A'],
                ['kode_ruangan' => 'R-FAR-01', 'nama_ruangan' => 'Depo Farmasi Central', 'lokasi_lantai' => 'Lantai 1 Gedung Utama'],
                ['kode_ruangan' => 'R-MKN-01', 'nama_ruangan' => 'Ruang Patologi Anatomi & Mikrobiologi', 'lokasi_lantai' => 'Lantai 2 Gedung B'],
            ],
            'SEK-PELAYANAN' => [
                ['kode_ruangan' => 'R-IGD-01', 'nama_ruangan' => 'Resusitasi & Triase IGD', 'lokasi_lantai' => 'Lantai 1 Gedung Utama'],
                ['kode_ruangan' => 'R-OK-01', 'nama_ruangan' => 'Kamar Bedah Central OK-1', 'lokasi_lantai' => 'Lantai 2 Gedung Utama'],
                ['kode_ruangan' => 'R-POLI-01', 'nama_ruangan' => 'Poliklinik Jantung & Spesialis', 'lokasi_lantai' => 'Lantai 2 Gedung B'],
                ['kode_ruangan' => 'R-POLI-02', 'nama_ruangan' => 'Poliklinik Mata & THT', 'lokasi_lantai' => 'Lantai 2 Gedung B'],
            ],
            'SEK-KEPERAWATAN' => [
                ['kode_ruangan' => 'R-RI-VIP', 'nama_ruangan' => 'Rawat Inap VIP Pavilion', 'lokasi_lantai' => 'Lantai 3 Gedung B'],
                ['kode_ruangan' => 'R-RI-CLASS1', 'nama_ruangan' => 'Rawat Inap Kelas 1 Anggrek', 'lokasi_lantai' => 'Lantai 3 Gedung A'],
                ['kode_ruangan' => 'R-PERI-01', 'nama_ruangan' => 'Ruang Perinatologi', 'lokasi_lantai' => 'Lantai 2 Gedung A'],
                ['kode_ruangan' => 'R-VK-01', 'nama_ruangan' => 'Ruang Bersalin (VK / Delivery Room)', 'lokasi_lantai' => 'Lantai 2 Gedung A'],
            ],
            'SEK-ICU' => [
                ['kode_ruangan' => 'R-ICU-MAIN', 'nama_ruangan' => 'Ruang Utama ICU Bed 1-6', 'lokasi_lantai' => 'Lantai 3 Gedung Utama'],
                ['kode_ruangan' => 'R-ICCU-01', 'nama_ruangan' => 'Ruang Intensive Cardiac Care Unit', 'lokasi_lantai' => 'Lantai 3 Gedung Utama'],
                ['kode_ruangan' => 'R-NICU-01', 'nama_ruangan' => 'Ruang Neonatal ICU (NICU)', 'lokasi_lantai' => 'Lantai 3 Gedung A'],
                ['kode_ruangan' => 'R-PICU-01', 'nama_ruangan' => 'Ruang Pediatric ICU (PICU)', 'lokasi_lantai' => 'Lantai 3 Gedung A'],
            ],
            'SEK-REHAB' => [
                ['kode_ruangan' => 'R-FISIO-01', 'nama_ruangan' => 'Ruang Elektroterapi & Diatermi', 'lokasi_lantai' => 'Lantai 1 Gedung C'],
                ['kode_ruangan' => 'R-FISIO-02', 'nama_ruangan' => 'Gymnasium Rehabilitasi & Mekanoterapi', 'lokasi_lantai' => 'Lantai 1 Gedung C'],
                ['kode_ruangan' => 'R-HIDRO-01', 'nama_ruangan' => 'Ruang Hidroterapi', 'lokasi_lantai' => 'Basemen Gedung C'],
                ['kode_ruangan' => 'R-OKUP-01', 'nama_ruangan' => 'Ruang Terapi Okupasi & Wicara', 'lokasi_lantai' => 'Lantai 1 Gedung C'],
            ],
            'SEK-GUDANG' => [
                ['kode_ruangan' => 'R-GUDANG-01', 'nama_ruangan' => 'Gudang Utama Inventaris Buffer', 'lokasi_lantai' => 'Basemen Gedung B'],
                ['kode_ruangan' => 'R-ATEM-WS', 'nama_ruangan' => 'Workshop Kalibrasi & Service ATEM', 'lokasi_lantai' => 'Basemen Gedung B'],
                ['kode_ruangan' => 'R-GUDANG-02', 'nama_ruangan' => 'Depo Cadangan Alkes Kritis', 'lokasi_lantai' => 'Basemen Gedung A'],
                ['kode_ruangan' => 'R-STERIL-01', 'nama_ruangan' => 'Central Sterile Supply Dept (CSSD)', 'lokasi_lantai' => 'Basemen Gedung Utama'],
            ],
        ];

        $ruanganModels = [];
        $ruanganBySeksi = [];
        foreach ($ruanganData as $kodeSeksi => $rList) {
            $seksiObj = $seksiModels[$kodeSeksi];
            $ruanganBySeksi[$seksiObj->id] = [];
            foreach ($rList as $rItem) {
                $rObj = Ruangan::updateOrCreate(
                    ['kode_ruangan' => $rItem['kode_ruangan']],
                    [
                        'seksi_id' => $seksiObj->id,
                        'nama_ruangan' => $rItem['nama_ruangan'],
                        'lokasi_lantai' => $rItem['lokasi_lantai'],
                    ]
                );
                $ruanganModels[$rItem['kode_ruangan']] = $rObj;
                $ruanganBySeksi[$seksiObj->id][] = $rObj;
            }
        }

        // 3. Seed Master Nomenklatur Alkes Standard Kemenkes
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
            ['kode' => 'NOM-OPER-01', 'nama' => 'Lampu Operasi LED Dual Arm Ceiling', 'kat' => 'Surgery', 'desk' => 'Lampu penerangan khusus meja bedah.'],
            ['kode' => 'NOM-HEMO-01', 'nama' => 'Mesin Hemodialisis Dialyzer', 'kat' => 'Renal Support', 'desk' => 'Mesin cuci darah pasien gagal ginjal.'],
            ['kode' => 'NOM-PULSE-01', 'nama' => 'Pulse Oximeter Finger Sensor', 'kat' => 'Monitoring', 'desk' => 'Pengukur kadar oksigen darah portable.'],
            ['kode' => 'NOM-CENTR-01', 'nama' => 'Centrifuge Refrigerated High Speed', 'kat' => 'Laboratory', 'desk' => 'Alat pemisah komponen sampel darah dan cairan.'],
            ['kode' => 'NOM-NEBU-01', 'nama' => 'Ultrasonic Nebulizer Heavy Duty', 'kat' => 'Therapy', 'desk' => 'Alat pengabut obat saluran pernapasan.'],
        ];

        $nomModels = [];
        $nomKeys = [];
        foreach ($nomenklaturData as $nData) {
            $nObj = Nomenklatur::updateOrCreate(
                ['kode_nomenklatur' => $nData['kode']],
                [
                    'nama_alat' => $nData['nama'],
                    'kategori' => $nData['kat'],
                    'deskripsi' => $nData['desk'],
                ]
            );
            $nomModels[$nData['kode']] = $nObj;
            $nomKeys[] = $nObj;
        }

        // 4. Seed 55 Alkes PER SEKSI (Sebagain dipindahkan lokasi fisiknya, Kepemilikan TETAP PERMANEN!)
        $brandList = ['GE Healthcare', 'Philips Medical', 'Siemens Healthineers', 'Mindray', 'Draeger', 'Zoll Medical', 'Nihon Kohden', 'Terumo', 'Olympus', 'BTL Medical', 'Fukuda Denshi', 'Stryker', 'Erbe Elektromedizin', 'Maquet Getinge'];

        $allCreatedAlkes = [];
        $globalCounter = 1;

        foreach ($seksisData as $sData) {
            $kodeSeksi = $sData['kode_seksi'];
            $seksiPemilikObj = $seksiModels[$kodeSeksi];
            $rListOwner = $ruanganBySeksi[$seksiPemilikObj->id];

            for ($i = 1; $i <= 55; $i++) {
                $invCode = sprintf('INV/ALKES/2024/%03d', $globalCounter);
                $snCode = 'SN-' . strtoupper(substr($kodeSeksi, 4, 3)) . '-' . (10000 + $globalCounter);
                $nomObj = $nomKeys[($globalCounter - 1) % count($nomKeys)];
                $brand = $brandList[($globalCounter - 1) % count($brandList)];

                // Tentukan lokasi fisik: 5 item per seksi dipindahkan sementara ke seksi lain!
                $isDipindahkan = ($i <= 5);
                if ($isDipindahkan) {
                    $lokasiSeksiObj = $seksiListArray[$seksiPemilikObj->id % count($seksiListArray)];
                    $rListTarget = $ruanganBySeksi[$lokasiSeksiObj->id] ?? $rListOwner;
                    $ruangObj = $rListTarget[0];
                    $status = StatusAlkes::SEDANG_DIGUNAKAN->value;
                    $kondisi = KondisiAlkes::BAIK->value;
                    $noteMsg = "Aset milik {$seksiPemilikObj->nama_seksi}. Saat ini dipindahkan lokasi fisiknya ke {$lokasiSeksiObj->nama_seksi} ({$ruangObj->nama_ruangan}) untuk dukungan operasional.";
                } else {
                    $lokasiSeksiObj = $seksiPemilikObj;
                    $ruangObj = $rListOwner[($i - 1) % count($rListOwner)];
                    if ($i <= 45) {
                        $status = StatusAlkes::TERSEDIA->value;
                        $kondisi = KondisiAlkes::BAIK->value;
                    } elseif ($i <= 52) {
                        $status = StatusAlkes::SEDANG_DIGUNAKAN->value;
                        $kondisi = KondisiAlkes::BAIK->value;
                    } else {
                        $status = StatusAlkes::DALAM_PERBAIKAN->value;
                        $kondisi = KondisiAlkes::RUSAK_RINGAN->value;
                    }
                    $noteMsg = "Unit aset terdaftar milik {$seksiPemilikObj->nama_seksi} di ruangan {$ruangObj->nama_ruangan}.";
                }

                $alkesObj = Alkes::updateOrCreate(
                    ['kode_inventaris' => $invCode],
                    [
                        'nomor_seri' => $snCode,
                        'nomenklatur_id' => $nomObj->id,
                        'merk' => $brand,
                        'tipe' => 'Series-' . rand(10, 99) . ' Pro',
                        'seksi_pemilik_id' => $seksiPemilikObj->id,
                        'lokasi_seksi_id' => $lokasiSeksiObj->id,
                        'ruangan_id' => $ruangObj->id,
                        'status' => $status,
                        'kondisi' => $kondisi,
                        'tanggal_pengadaan' => date('Y-m-d', strtotime("-" . rand(3, 48) . " months")),
                        'nilai_aset' => 0,
                        'tanggal_kalibrasi_terakhir' => '2025-01-15',
                        'tanggal_kalibrasi_berikutnya' => '2026-01-15',
                        'catatan' => $noteMsg,
                    ]
                );

                $allCreatedAlkes[] = $alkesObj;
                $globalCounter++;
            }
        }

        // 5. Seed 45 Riwayat Mutasi / Transfer Lokasi Alkes
        $mutasiStatusOptions = ['Disetujui', 'Disetujui', 'Diproses', 'Ditolak'];

        for ($m = 0; $m < 45; $m++) {
            $alkes = $allCreatedAlkes[($m * 7) % count($allCreatedAlkes)];
            $seksiAsal = Seksi::find($alkes->lokasi_seksi_id);
            $seksiTujuan = $seksiListArray[($seksiAsal->id % count($seksiListArray))];
            
            $ruangAsalList = $ruanganBySeksi[$seksiAsal->id] ?? [];
            $ruangTujuanList = $ruanganBySeksi[$seksiTujuan->id] ?? [];
            $rAsal = count($ruangAsalList) > 0 ? $ruangAsalList[0] : null;
            $rTujuan = count($ruangTujuanList) > 0 ? $ruangTujuanList[0] : null;

            MutasiAlkes::create([
                'alkes_id' => $alkes->id,
                'seksi_asal_id' => $seksiAsal->id,
                'seksi_tujuan_id' => $seksiTujuan->id,
                'ruangan_asal_id' => $rAsal ? $rAsal->id : null,
                'ruangan_tujuan_id' => $rTujuan ? $rTujuan->id : null,
                'tanggal_mutasi' => date('Y-m-d H:i:s', strtotime("-" . rand(1, 180) . " days")),
                'pemohon' => 'Petugas Operasional ' . $seksiAsal->nama_seksi,
                'penanggung_jawab' => $seksiAsal->penanggung_jawab,
                'alasan_mutasi' => 'Peminjaman operasional dan pemindahan lokasi fisik alat antar seksi (Kepemilikan tetap).',
                'status_persetujuan' => $mutasiStatusOptions[$m % count($mutasiStatusOptions)],
            ]);
        }

        // 6. Seed 45 Riwayat Log Pemeliharaan
        $tindakanList = [
            'Penggantian modul daya & kalibrasi BPFK. Dikembalikan ke lokasi seksi.',
            'Pembersihan optik internal & perapian kabel. Siap pakai di ruangan asal.',
            'Pemeriksaan tekanan pompa vakum. Diserahterimakan kembali ke seksi.'
        ];
        $pelaksanaList = ['Teknisi ATEM RS', 'PT. Medika Utama Vendor', 'Tim Teknisi BPFK'];

        for ($p = 0; $p < 45; $p++) {
            $alkesP = $allCreatedAlkes[($p * 5) % count($allCreatedAlkes)];
            $tglMulai = date('Y-m-d', strtotime("-" . rand(5, 120) . " days"));
            $tglSelesai = date('Y-m-d', strtotime($tglMulai . " +" . rand(1, 4) . " days"));

            LogPemeliharaan::create([
                'alkes_id' => $alkesP->id,
                'jenis_tindakan' => ($p % 2 == 0) ? 'Perbaikan' : 'Kalibrasi',
                'tanggal_mulai' => $tglMulai,
                'tanggal_selesai' => $tglSelesai,
                'pelaksana_vendor' => $pelaksanaList[$p % count($pelaksanaList)],
                'deskripsi_kerusakan' => 'Pemeriksaan kelayakan fungsi & kalibrasi tahunan.',
                'tindakan_perbaikan' => $tindakanList[$p % count($tindakanList)],
                'biaya' => 0,
                'status_hasil' => 'Selesai',
            ]);
        }

        // 7. Seed 30 Riwayat ActivityLog Audit Trail Realistis Pindah Lokasi
        $actions = ['Pindah Lokasi Alkes', 'Edit Alkes', 'Tambah Alkes', 'Lapor Perbaikan'];
        $roles = ['Seksi Operasional', 'Admin System', 'Gudang Alkes & ATEM'];

        for ($a = 0; $a < 30; $a++) {
            $action = $actions[$a % count($actions)];
            $role = $roles[$a % count($roles)];
            $seksiObj = $seksiListArray[$a % count($seksiListArray)];

            $desc = "Memindahkan lokasi fisik unit alkes ke {$seksiObj->nama_seksi}. Kepemilikan aset tetap berada di seksi pemilik asal.";

            ActivityLog::create([
                'user_role' => $role,
                'seksi_name' => $seksiObj->nama_seksi,
                'action' => $action,
                'description' => $desc,
                'ip_address' => '127.0.0.1',
                'created_at' => date('Y-m-d H:i:s', strtotime("-" . rand(1, 30) . " days")),
            ]);
        }
    }
}
