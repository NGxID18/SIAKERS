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
        // 1. Pemetaan Ruangan Master Dokumen RS + Ruangan Elektromedis (Admin Utama SIAKER)
        $ruanganData = [
            ['nama' => 'Elektromedis', 'kode' => 'R-ELEKTROMEDIS'],
            ['nama' => 'CSSD', 'kode' => 'R-CSSD'],
            ['nama' => 'Laboratorium', 'kode' => 'R-LAB'],
            ['nama' => 'Radiologi', 'kode' => 'R-RAD'],
            ['nama' => 'UTD', 'kode' => 'R-UTD'],
            ['nama' => 'MCU', 'kode' => 'R-MCU'],
            ['nama' => 'G. Penunjang', 'kode' => 'R-PENUNJANG'],

            ['nama' => 'IGD', 'kode' => 'R-IGD'],
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
            $ruanganModels[$r['nama']] = Ruangan::updateOrCreate(
                ['nama_ruangan' => $r['nama']],
                [
                    'kode_ruangan' => $r['kode'],
                ]
            );
        }

        // 2. Master Nomenklatur Alkes Standard Kemenkes
        $nomenklaturData = [
            ['kode' => 'NOM-VENT-01', 'nama' => 'Ventilator Intensive Care Unit', 'kat' => 'Life Support'],
            ['kode' => 'NOM-DEF-01', 'nama' => 'Defibrillator Biphasic dengan Monitor', 'kat' => 'Emergency'],
            ['kode' => 'NOM-EKG-01', 'nama' => 'Elektrokardiograf (EKG 12-Lead)', 'kat' => 'Diagnostic'],
            ['kode' => 'NOM-USG-01', 'nama' => 'USG Color Doppler 4D Imaging', 'kat' => 'Radiology'],
            ['kode' => 'NOM-SYP-01', 'nama' => 'Syringe Infusion Pump Digital', 'kat' => 'Drug Delivery'],
            ['kode' => 'NOM-INFP-01', 'nama' => 'Volumetric Infusion Pump', 'kat' => 'Drug Delivery'],
            ['kode' => 'NOM-MON-01', 'nama' => 'Bedside Patient Monitor 5-Para', 'kat' => 'Monitoring'],
            ['kode' => 'NOM-SUC-01', 'nama' => 'Medical Suction Pump Portable', 'kat' => 'Airway Support'],
            ['kode' => 'NOM-CT-01', 'nama' => 'CT-Scan 128 Slice Scanner', 'kat' => 'Radiology'],
            ['kode' => 'NOM-INC-01', 'nama' => 'Infant Incubator Transport', 'kat' => 'Pediatric'],
            ['kode' => 'NOM-SWD-01', 'nama' => 'Shortwave Diathermy (SWD) Unit', 'kat' => 'Rehabilitation'],
            ['kode' => 'NOM-TENS-01', 'nama' => 'TENS & Electrotherapy Combo Unit', 'kat' => 'Rehabilitation'],
            ['kode' => 'NOM-ANA-01', 'nama' => 'Mesin Anestesi dengan Vaporizer', 'kat' => 'Surgery'],
            ['kode' => 'NOM-AUTO-01', 'nama' => 'Autoclave Sterilizer Steam 150L', 'kat' => 'Sterilization'],
            ['kode' => 'NOM-OPER-01', 'nama' => 'Lampu Operasi LED Dual Arm Ceiling', 'kat' => 'Surgery'],
            ['kode' => 'NOM-HEMO-01', 'nama' => 'Mesin Hemodialisis Dialyzer', 'kat' => 'Renal Support'],
            ['kode' => 'NOM-CENTR-01', 'nama' => 'Centrifuge Refrigerated High Speed', 'kat' => 'Laboratory'],
        ];

        $nomModels = [];
        foreach ($nomenklaturData as $nData) {
            $nomModels[$nData['nama']] = Nomenklatur::updateOrCreate(
                ['kode_nomenklatur' => $nData['kode']],
                ['nama_alat' => $nData['nama'], 'kategori' => $nData['kat']]
            );
        }
        $defaultNom = array_values($nomModels)[0];

        // 3. Import 380 Real Alkes Records (100% Dari Dokumen PDF Asli RS)
        $realAlkesRaw = [
            ['Instrumen Sterilizer', 'Steris', '', '3KOL 502100AO', '2010', 1, 'HIBAH APBN 2022', 1672500000, 'CSSD', '', 'RUSAK BERAT', 'TERDATA', false, ''],
            ['Autoclave', '', '', '', '2026', 1, '', 0, 'CSSD', '', 'BAIK', 'TERDATA', false, ''],
            ['Bed Patient', 'Paramount', 'PA12-0807F', '3KOL 502100AO', '2007', 1, '', 0, 'Fisioterapi', '', 'BAIK', 'TERDATA', false, ''],
            ['Elektrikal Simulator', 'Chattanoga', 'Intelect Advanced', '1314', '2007', 1, '', 0, 'Fisioterapi', '', 'RUSAK BERAT', 'TERDATA', false, ''],
            ['Infra red Therapy', 'Beurer', 'IL21', '213323', '2007', 1, '', 0, 'Fisioterapi', '', 'BAIK', 'TERDATA', false, ''],
            ['Infra red Therapy', 'Beurer', 'IL21', '213221', '2007', 1, '', 0, 'Fisioterapi', '', 'BAIK', 'TERDATA', false, ''],
            ['Infra Red Therapy', 'Beurer', 'IL21', '213222', '2007', 1, '', 0, 'Fisioterapi', '', 'BAIK', 'TERDATA', false, ''],
            ['Infrared Standing 3 Lampu', 'Heuser', 'TGS 3.1 Onhe Dimmer', '233.73.0907.432', '2007', 1, '', 0, 'Fisioterapi', '', 'RUSAK BERAT', 'TERDATA', false, ''],
            ['Laser Therapy', 'Chattanoga', 'Intelec Model 2779', '173', '2007', 1, '', 0, 'Fisioterapi', '', 'RUSAK RINGAN', 'TERDATA', false, ''],
            ['Micro Wave Diathermy', 'Fysiomed', 'Microwave 25P Tpe BF', '10291', '2012', 1, '', 0, 'Fisioterapi', '', 'BAIK', 'TERDATA', false, ''],
            ['Paralel Bar', '', '', '', '', 1, '', 0, 'Fisioterapi', '', 'RUSAK BERAT', 'TIDAK TERDATA', false, ''],
            ['Respionic', 'Philips Respironic', 'REF CA-3200', '16202', '', 1, '', 0, 'Fisioterapi', '', 'BAIK', 'TERDATA', false, ''],
            ['Statis Bycle', '', '', '', '', 1, '', 0, 'Fisioterapi', '', 'RUSAK BERAT', 'TIDAK TERDATA', false, ''],
            ['Tens Unit', 'EME', 'Therapic 9400', 'EM 08070918', '2018', 1, '', 0, 'Fisioterapi', '', 'BAIK', 'TERDATA', false, ''],
            ['TENS UNIT', 'CHATTANOGA', 'Intelect', 'T2740', '2012', 1, '', 0, 'Fisioterapi', '', 'RUSAK BERAT', 'TERDATA', false, ''],
            ['Traksi Unit', '', '', '', '2007', 1, '', 0, 'Fisioterapi', '', 'RUSAK BERAT', 'TIDAK TERDATA', false, ''],
            ['Troly Tindakan', 'Mak Instrument Trolley', '35101', '0755-11-06', '', 2, '', 0, 'Fisioterapi', '', 'BAIK', 'TERDATA', false, ''],
            ['Ultra Sound Therapy', 'EME', '', 'EM07340718', '2018', 1, '', 0, 'Fisioterapi', '', 'BAIK', 'TERDATA', false, ''],
            ['Ultra Sound Unit', 'Chattanoga', 'Intelect', '', '2007', 1, '', 0, 'Fisioterapi', '', 'RUSAK BERAT', 'TERDATA', false, ''],
            ['Hemodialisa', '', '', '', '', 11, '', 0, 'Hemodialisa', '', 'BAIK', 'TERDATA', false, 'KSO'],
            ['Timbangan', '', '', '', '', 1, '', 0, 'Hemodialisa', '', 'BAIK', 'TERDATA', false, ''],
            ['Timbangan', '', '', '', '', 1, '', 0, 'Hemodialisa', '', 'BAIK', 'TERDATA', false, ''],
            ['Alat pengukur gula darah', 'Gluco Dr.', 'AGM-4000', '2021.01EC21AA04563', '2021', 1, '', 0, 'ICU', '', 'BAIK', 'TERDATA', false, ''],
            ['Alat pengukur gula darah', 'Gluco Dr.', 'AGM-4001', '2021.01EC21AA02289', '2021', 1, '', 0, 'ICU', '', 'BAIK', 'TERDATA', false, ''],
            ['Bed Elektrik', 'Paramount', '', '', '2008', 4, '', 0, 'ICU', '', 'BAIK', 'TERDATA', false, ''],
            ['Defibrilation', 'Bexen', '', '20026170', '2016', 1, '', 0, 'ICU', '', 'BAIK', 'TERDATA', false, ''],
            ['EKG', 'Innomed Heartscreen', '', '20239288', '2023', 1, 'DAK 2023', 56000000, 'ICU', '', 'RUSAK BERAT', 'TERDATA', false, ''],
            ['EKG', 'Innomed Heartscreen', '', '22239242', '2023', 1, '', 0, 'ICU', '', 'BAIK', 'TERDATA', false, ''],
            ['Examination Lamp', 'Famed', '', '', '', 1, '', 0, 'ICU', '', 'BAIK', 'TERDATA', false, ''],
            ['Film View', 'KArixa', '', '', '', 1, '', 0, 'ICU', '', 'BAIK', 'TERDATA', false, ''],
            ['Gelas ukur urin', '', '', '', '', 1, '', 0, 'ICU', '', 'BAIK', 'TERDATA', false, ''],
            ['HFNC (High Flow Nasal Canul)', 'Aervo2', '', '', '2020', 2, 'HIBAH 2020', 121090000, 'ICU', '', 'BAIK', 'TERDATA', false, ''],
            ['Infus Pump', 'Mindray', '', 'SK 10308902', '2021', 1, 'APBD 2021', 18250000, 'ICU', '', 'BAIK', 'TERDATA', false, ''],
            ['Infus Pump', 'Mindray', '', 'SK 10308929', '2021', 1, 'APBD 2021', 18250000, 'ICU', '', 'BAIK', 'TERDATA', false, ''],
            ['Infus Pump', 'Mindray', '', 'SK 10308913', '2021', 1, 'APBD 2021', 18250000, 'ICU', '', 'RUSAK BERAT', 'TERDATA', false, ''],
            ['Infus Pump', 'B-Braun', '', '8713070', '', 1, '', 0, 'ICU', '', 'RUSAK BERAT', 'TERDATA', false, ''],
            ['Laringoskop set adult', 'Karl storz', '', '', '', 1, '', 0, 'ICU', '', 'BAIK', 'TERDATA', false, ''],
            ['Laringoskop set adult', 'Hofbauer', '', '', '', 1, '', 0, 'ICU', '', 'BAIK', 'TERDATA', false, ''],
            ['Monitor Lengkap', 'Philips', '', 'DE 67IR3743', '2020', 1, '', 0, 'ICU', '', 'BAIK', 'TERDATA', false, ''],
            ['Monitor Lengkap', 'Philips', '', 'DE 67IR3819', '2020', 1, '', 0, 'ICU', '', 'BAIK', 'TERDATA', false, ''],
            ['Monitor Lengkap', 'Dixion', '', 'D5/09/2205/5193', '2022', 1, '', 0, 'ICU', '', 'BAIK', 'TERDATA', false, ''],
            ['Monitor Lengkap', 'Dixion', '', '05/092211/5318', '2022', 1, '', 0, 'ICU', '', 'BAIK', 'TERDATA', false, ''],
            ['O2 portable', 'Krober O2', '', '', '', 1, '', 0, 'ICU', '', 'BAIK', 'TERDATA', false, ''],
            ['Oxymetri Portable', 'Oxy9wave', '', '', '', 2, '', 0, 'ICU', '', 'BAIK', 'TERDATA', false, ''],
            ['Regulator O2', 'Mak', '', '', '', 3, '', 0, 'ICU', '', 'BAIK', 'TERDATA', false, ''],
            ['Regulator oksigen central', 'RMS', '', '', '', 1, '', 0, 'ICU', '', 'BAIK', 'TERDATA', false, ''],
            ['Regulator oksigen ventilator', 'RMS', '', '', '2017', 1, '', 0, 'ICU', '', 'BAIK', 'TERDATA', false, ''],
            ['Stetoskop Anak', '', '', '', '2017', 1, '', 0, 'ICU', '', 'BAIK', 'TERDATA', false, ''],
            ['Stetoskop dewasa', 'GEA', '', '', '2017', 2, '', 0, 'ICU', '', 'BAIK', 'TERDATA', false, ''],
            ['Suction pump', 'Industries Med LTD', 'AC 500', '9056236', '', 1, '', 0, 'ICU', '', 'BAIK', 'TERDATA', false, ''],
            ['Suction pump', 'Electric Suction Apparatus', 'SN703A-D', 'SANMO94-23-010', '2023', 1, 'DAK 2023', 34894500, 'ICU', '', 'BAIK', 'TERDATA', false, ''],
            ['Suction pump', 'Electric Suction Apparatus', 'SN703A-D', 'SANMO94-23-017', '2023', 1, 'DAK 2023', 34894500, 'ICU', '', 'BAIK', 'TERDATA', false, ''],
            ['Syringe pump', 'Mindray', '', 'SK 10307825', '', 1, '', 0, 'ICU', '', 'BAIK', 'TERDATA', false, ''],
            ['Syringe pump', 'Mindray', '', 'SK10307823', '', 1, '', 0, 'ICU', '', 'BAIK', 'TERDATA', false, ''],
            ['Syringe pump', 'Frenesius Kabi', '', '018094/23196959', '', 1, '', 0, 'ICU', '', 'BAIK', 'TERDATA', false, ''],
            ['Syringe pump', 'Frenesius Kabi', '', '018094/23227676', '', 1, '', 0, 'ICU', '', 'BAIK', 'TERDATA', false, ''],
            ['Syringe pump', 'Frenesius Kabi', '', '018094/23227675', '', 1, '', 0, 'ICU', '', 'BAIK', 'TERDATA', false, ''],
            ['Syringe pump', 'Rchestra', '', '082594/21452196', '', 1, '', 0, 'ICU', '', 'BAIK', 'TERDATA', false, ''],
            ['Tensimeter Dewasa', 'Regal', 'ABN', '', '2020', 1, '', 0, 'ICU', '', 'BAIK', 'TERDATA', false, ''],
            ['Termometer Digital', 'HuBDIC', 'FS-300', '', '', 1, '', 0, 'ICU', '', 'BAIK', 'TERDATA', false, ''],
            ['Termometer Digital', 'Omron', 'MC-246', '', '', 1, '', 0, 'ICU', '', 'BAIK', 'TERDATA', false, ''],
            ['THT set', '', '', '', '', 1, '', 0, 'ICU', '', 'BAIK', 'TERDATA', false, ''],
            ['Timbangan pempers', '', '', '', '', 1, '', 0, 'ICU', '', 'BAIK', 'TERDATA', false, ''],
            ['USG', 'Mindray', '', 'MR-2B004895', '', 1, '', 0, 'ICU', '', 'BAIK', 'TERDATA', false, ''],
            ['UV Room sterilizer', 'Medivent', '', '', '', 1, '', 0, 'ICU', '', 'BAIK', 'TERDATA', false, ''],
            ['Ventilator', 'Bellavista', '', 'MB230883', '2020', 1, '', 0, 'ICU', '', 'BAIK', 'TERDATA', false, ''],
            ['Ventilator', 'Bellavista', '', 'MB231220', '2020', 1, '', 0, 'ICU', '', 'BAIK', 'TERDATA', false, ''],
            ['Ventilator', 'MonalT75', '', 'MT75-07648', '2018', 1, 'HIBAH', 0, 'ICU', '', 'BAIK', 'TERDATA', false, ''],
            ['Ventilator', 'Hamilton C2', '', '160150', '', 1, '', 0, 'ICU', '', 'BAIK', 'TERDATA', false, ''],
            ['Ventilator', 'Siaretron 4000', '', '', '2024', 1, '', 0, 'IGD', '', 'BAIK', 'TERDATA', false, ''],
            ['Ventilator mobile', 'RESMED', '', '', '2020', 2, '', 0, 'IGD', '', 'BAIK', 'TERDATA', false, ''],
            ['Ambu Bag', 'Ambu', '', '', '', 3, '', 0, 'IGD', '', 'BAIK', 'TERDATA', false, ''],
            ['Ambu Bag', 'Greatcare', '', '', '', 6, 'DAK 2023', 96100000, 'IGD', '', 'BAIK', 'TERDATA', false, ''],
            ['Bed dengan Bedrails', 'Paramount', '', '', '', 4, '', 0, 'IGD', '', 'BAIK', 'TERDATA', false, ''],
            ['Bed Ginekologi Electric', 'Nutritex', 'NT 037-B', '', '2023', 1, '', 0, 'IGD', '', 'BAIK', 'TERDATA', false, ''],
            ['Bed Side Monitor', 'Innomed', 'Inno Care T11', '', '', 5, '', 0, 'IGD', '', 'BAIK', 'TERDATA', false, ''],
            ['Bed Side Monitor', 'Innomed', 'Inno Care T12', '', '', 5, '', 0, 'IGD', '', 'BAIK', 'TERDATA', false, ''],
            ['Bed Side Monitor', 'Dixion', 'STORM 5900-05', '', '2023', 2, 'DAK 2023', 105896000, 'IGD', '', 'BAIK', 'TERDATA', false, ''],
            ['Defibrilator', 'Innomed', 'Cardiac Aid 200 B', '', '2022', 1, '', 0, 'IGD', '', 'BAIK', 'TERDATA', false, ''],
            ['Defibrilator', 'Innomed', 'Cardiac Aid 360 B', '', '2021', 2, '', 0, 'IGD', '', 'BAIK', 'TERDATA', false, ''],
            ['Doppler', 'IMN Terravizion', 'Fetal Doppler', '', '2023', 1, '', 0, 'IGD', '', 'RUSAK BERAT', 'TERDATA', false, ''],
            ['Electric suction', 'SANI', 'SN 703A-D / AC-500', '', '2023', 2, '', 0, 'IGD', '', 'RUSAK BERAT', 'TERDATA', false, ''],
            ['Electric suction', 'Tidak ada merk', '', '', '', 1, 'BLUD 2023', 10675675, 'IGD', '', 'BAIK', 'TERDATA', false, ''],
            ['HEPA Filter Isolasi IGD', '', '', '', '', 1, '', 0, 'IGD', '', 'BAIK', 'TERDATA', false, ''],
            ['High Flow Nasal Canula', 'Poly Medical', 'Aircov-19', '21-0701', '2024', 1, 'DAK 2023', 34894500, 'IGD', '', 'BAIK', 'TERDATA', false, ''],
            ['Infant Radiant Warmer', 'SANI', 'COMP.T10-AS', '810040007', '2023', 1, '', 0, 'IGD', '', 'BAIK', 'TERDATA', false, ''],
            ['Infus Pump', 'B-Braun', 'Perfusor Space', '', '2013', 1, '', 0, 'IGD', '', 'BAIK', 'TERDATA', false, ''],
            ['Lampu Tindakan', 'Famed', '', '', '2023', 1, 'HIBAH', 0, 'IGD', '', 'BAIK', 'TERDATA', false, ''],
            ['Mesin EKG 12 Lead', 'Mindray', 'Beneheart R 12A', '', '2021', 2, '', 0, 'IGD', '', 'BAIK', 'TERDATA', false, ''],
            ['Mesin EKG 12 Lead', 'Mindray', 'Beneheart R 12A', '', '2021', 2, '', 0, 'IGD', '', 'RUSAK BERAT', 'TERDATA', false, ''],
            ['Mesin EKG 12 Lead', 'Innomed', 'HeartScreen', '', '2023', 1, '', 0, 'IGD', '', 'BAIK', 'TERDATA', false, ''],
            ['Nebulizer', 'Omron', 'Oxy 9 Wave', '', '2023', 3, '', 0, 'IGD', '', 'BAIK', 'TERDATA', false, ''],
            ['Pulse oximeter', 'Bionet', 'PM-60', 'CR 19250581', '2021', 2, 'DAK 2023', 58500000, 'IGD', '', 'BAIK', 'TERDATA', false, ''],
            ['Pulse oximeter', 'Mindray', '', '', '2018/2021', 2, '', 0, 'IGD', '', 'BAIK', 'TERDATA', false, ''],
            ['Syringe Pump', 'B-Braun', 'Infusomat Space P', '227124', '2013', 1, 'DAK 2023', 56000000, 'IGD', '1 Berada di Poli', 'BAIK', 'TERDATA', false, ''],
            ['Tensimeter Dewasa', 'Regal', '', '', '2019', 4, '', 0, 'IGD', '', 'BAIK', 'TERDATA', false, ''],
            ['USG', 'Mindray', '', '', '', 1, '', 0, 'IGD', '', 'RUSAK BERAT', 'TERDATA', false, ''],
            ['UV Room sterilizer', 'Medivent', '', '', '', 4, '', 0, 'IGD', '', 'BAIK', 'TERDATA', false, ''],
            ['Ventilator', 'Bellavista', '', '', '', 1, '', 0, 'IGD', '', 'RUSAK RINGAN', 'TERDATA', false, ''],
            ['Ventilator', 'Hamilton C2', '', '', '', 1, '', 0, 'IGD', '1 di Perinatology', 'BAIK', 'TERDATA', false, ''],
            ['Ventilator', 'Siaretron 4000', '', '', '', 1, '', 0, 'IGD', '', 'RUSAK BERAT', 'TERDATA', false, ''],
            ['Autoclave', 'Steris', 'ER 7220', '202101069', '', 1, '', 0, 'IGD', '', 'BAIK', 'TERDATA', false, ''],
            ['Defibrilator', 'Innomed', '', '', '', 1, '', 0, 'IGD', '', 'RUSAK BERAT', 'TERDATA', false, ''],
            ['Alat CT-Scan', 'Siemens Somatom Perspective', 'TSN 014D', '463/1/11 T 014 D', '', 1, 'BLUD 2024', 414414414, 'OK', '', 'BAIK', 'TERDATA', false, 'SI DINKES BELUM ADA BERITA ACARA SERAH TERIMA'],
            ['USG', 'GE', 'UNB400', 'C413.0630', '', 1, '', 0, 'Poli Anak', '', 'BAIK', 'TERDATA', false, ''],
            ['USG', 'PATSAMED', 'MD360CS', '2470074', '', 1, '', 0, 'Poli Bedah', '', 'BAIK', 'TERDATA', false, ''],
            ['X-ray Konvensional', 'WON', '', '', '2016', 2, '', 0, 'Poli Bedah', '', 'BAIK', 'TERDATA', false, ''],
            ['Xray Portable', 'Fuji Film', 'Y100', '220', '', 1, '', 0, 'Poli Bedah', '', 'BAIK', 'TERDATA', false, ''],
            ['Xray Mobile', 'Sitec', '', '', '', 4, '', 0, 'Poli gigi', '', 'BAIK', 'TERDATA', false, ''],
            ['Medical Head Lamp', 'Bistos', '', '', '', 1, '', 0, 'Poli gigi', '', 'BAIK', 'TERDATA', false, ''],
            ['Tensi Meter Digital', 'Omron', 'T', '', '', 1, '', 0, 'Poli gigi', '', 'BAIK', 'TERDATA', false, ''],
            ['THT', 'Stardy', '', '', '', 1, 'DAK 2023', 56000000, 'Poli Jantung', '', 'BAIK', 'TERDATA', false, 'DAK ADA DI REALISASI BLUD MAUPUN AOBD 2024'],
            ['BLOOD BAG TUBE SEALER', 'DOCON SEALM', 'CLOCK/HUMIDITY HTC 2', '', '2023', 2, '', 0, 'Poli Jantung', '', 'BAIK', 'TERDATA', false, ''],
            ['BLOOD BANK', 'KIRSCH', '', '', '2023', 2, '', 0, 'Poli Jantung', '', 'BAIK', 'TERDATA', false, ''],
            ['BLOOD BANK', 'SANYO', '', '', '', 4, '', 0, 'Poli Kebidanan', '', 'BAIK', 'TERDATA', false, ''],
            ['CENTRIFUGE', 'HETTICH', '', '', '2018', 2, '', 0, 'Poli Kebidanan', '', 'BAIK', 'TERDATA', false, ''],
            ['Diagnostoc Lamp', 'Solis 30F', '', '', '2018', 2, '', 0, 'Poli Kebidanan', '', 'BAIK', 'TERDATA', false, ''],
            ['Belmont', 'AV12J0479', '', '', '2016', 1, '', 0, 'Poli Kebidanan', '', 'BAIK', 'TERDATA', false, ''],
            ['ZTP80A', '', 'NB20', '22.286', '', 1, '', 0, 'Poli Kebidanan', '', 'BAIK', 'TERDATA', false, ''],
            ['HS 112C-1', '20239321', 'PA59815-0608L', '', '', 9, '', 0, 'Poli Mata', '', 'BAIK', 'TERDATA', false, ''],
            ['IPX2', '', 'Digital Gynecologi Bed', '', '2018', 1, '', 0, 'Poli Mata', '', 'BAIK', 'TERDATA', false, ''],
            ['Edan/F9 Express', '314026-M12802560007', 'Hospital Bed Multipose', '2.32E+18', '2023', 1, '', 0, 'Poli Mata', '', 'BAIK', 'TERDATA', false, ''],
            ['Accuref.k 9001', '33HA6419', '', '', '', 2, '', 0, 'Poli Mata', '', 'BAIK', 'TERDATA', false, ''],
            ['SL-102', '', '', '', '', 3, '', 0, 'Poli Penyakit Dalam', '', 'BAIK', 'TERDATA', false, ''],
            ['NCT.10', '22460917', '', '', '', 2, '', 0, 'Poli Penyakit Dalam', '', 'BAIK', 'TERDATA', false, ''],
            ['UPP 110', '', '', '', '', 2, '', 0, 'Radiologi', '', 'BAIK', 'TERDATA', false, ''],
            ['M-CT-172', '644501871', '3.0 Mhz Waterproof IPX8', 'H17A004702587', '', 1, '', 0, 'Radiologi', '', 'BAIK', 'TERDATA', false, ''],
            ['Prossesor 2000', '11961-17107-0848', 'ASo 0001230004', '', '', 1, '', 0, 'Radiologi', '', 'BAIK', 'TERDATA', false, ''],
            ['CS 8100SC', '31487', 'IN.2412205002', '', '', 1, '', 0, 'Radiologi', '', 'BAIK', 'TERDATA', false, ''],
            ['Logiq E', '120516 WXO', 'HS112C-1', '22239247', '2023', 1, '', 0, 'Radiologi', '', 'BAIK', 'TERDATA', false, ''],
            ['PM-XL8', 'PMXL823811720002', '', '', '', 1, '', 0, 'Radiologi', '', 'BAIK', 'TERDATA', false, ''],
            ['WSR 40 Plus', 'WIV 23 CO 3001', '', '', '', 1, '', 0, 'Radiologi', '', 'BAIK', 'TERDATA', false, ''],
            ['FDR X-AIR', '', '8713070', '', '2013', 1, '', 0, 'Radiologi', '', 'BAIK', 'TERDATA', false, ''],
            ['BT-410', 'SN ECN40608', '', '11.32.00.07.03.2023.05.19', '', 1, 'DAK 2023', 2250000000, 'Radiologi', '', 'BAIK', 'TERDATA', false, ''],
            ['HBT-9030', '07006313LF', 'Silicone manual rersuscitator', '', '2023', 2, 'HIBAH APBN 2024', 1310000000, 'Radiologi', '', 'BAIK', 'TERDATA', false, ''],
            ['BL-300', '280 09 20031', '', '', '2018', 2, 'APBD 2024', 450000000, 'Radiologi', '', 'BAIK', 'TERDATA', false, ''],
            ['MBR-107 D(H)', '60814446', '', '', '', 1, '', 0, 'THT', '', 'BAIK', 'TERDATA', false, ''],
            ['EBA20', '0127406-7', 'BH-132', '20050385', '', 1, '', 0, 'THT', '', 'BAIK', 'TERDATA', false, ''],
            ['ELEKTRIK BALANCE', 'COMPOGUARD', '', '', '', 1, '', 0, 'THT', '', 'BAIK', 'TERDATA', false, ''],
            ['INKUBATOR', 'MEMMERT', 'Dexion Patient Monitor Storm 5900', '50922055192', '2023', 1, '', 0, 'THT', '', 'BAIK', 'TERDATA', false, ''],
            ['KULKAS REAGEN DAN FREEZER', 'DOMESTIK', '', '', '', 3, '', 0, 'UTD', '', 'BAIK', 'TERDATA', false, ''],
            ['KURSI DONOR DARAH', '', 'OXY 9 wave pulse oxymeter', 'ii.32.00.07.03.2025.05.19', '2023', 1, '', 0, 'UTD', '', 'BAIK', 'TERDATA', false, ''],
            ['MICROSCOPE', 'REX VISION', '', 'ZTP78E', '2016', 1, '', 0, 'UTD', '', 'BAIK', 'TERDATA', false, ''],
            ['MIKROPIPET', 'THERMO', '', '', '', 1, '', 0, 'UTD', '', 'BAIK', 'TERDATA', false, ''],
            ['PLASMA EXTRACTOR', 'FRESENIUS KABI', '', '', '', 1, '', 0, 'UTD', '', 'BAIK', 'TERDATA', false, ''],
            ['PLASMA EXTRACTOR', 'BAXTER', '', '', '', 1, '', 0, 'UTD', '', 'BAIK', 'TERDATA', false, ''],
            ['PLATELET AGITATOR', 'FRESENIUS GABI', '', '', '', 1, '', 0, 'UTD', '', 'BAIK', 'TERDATA', false, ''],
            ['TEMPERATURE SUHU', 'ONE HEALTH MEDICAL', '', '', '', 1, '', 0, 'UTD', '', 'BAIK', 'TERDATA', false, ''],
            ['TENSI DIGITAL', 'OMRON', '', '', '', 1, '', 0, 'UTD', '', 'BAIK', 'TERDATA', false, ''],
            ['TENSI JARUM', 'ONEMED', '', '', '', 1, '', 0, 'UTD', '', 'BAIK', 'TERDATA', false, ''],
            ['TIMBANGAN BADAN DIGITAL', 'KRIS', '', '', '', 1, '', 0, 'UTD', '', 'BAIK', 'TERDATA', false, ''],
            ['TIMBANGAN BADAN MANUAL', 'LAICA', '', '', '', 1, '', 0, 'UTD', '', 'BAIK', 'TERDATA', false, ''],
            ['WATERBATH', 'NUVE', '', '', '', 1, '', 0, 'UTD', '', 'BAIK', 'TERDATA', false, ''],
            ['Bed Manual', 'Paramount', '', '', '', 1, 'DAK 2023', 96100000, 'VK', '', 'BAIK', 'TERDATA', false, ''],
            ['Bedgyn Digital', 'MAK', '', '', '', 1, '', 0, 'VK', '', 'BAIK', 'TERDATA', false, ''],
            ['Bedgyn Digital', 'Nuritek Indonesia', '', '', '', 1, '', 0, 'VK', '', 'BAIK', 'TERDATA', false, ''],
            ['Digital Paramount', 'Paramount Bed', '', '', '', 1, '', 0, 'VK', '', 'BAIK', 'TERDATA', false, ''],
            ['Dopler', 'MED Gyn', '', '', '', 1, '', 0, 'VK', '', 'BAIK', 'TERDATA', false, ''],
            ['Dopler', 'Terravizion by IMN', '', '', '', 1, '', 0, 'VK', '', 'BAIK', 'TERDATA', false, ''],
            ['Dopler', 'INCREAMED', '', '', '', 1, '', 0, 'VK', '', 'BAIK', 'TERDATA', false, ''],
            ['EKG', 'INNOMED Heartscreen', '', '', '', 1, 'DAK 2023', 56000000, 'VK', '', 'BAIK', 'TERDATA', false, ''],
            ['Infus Pump', 'B Braun Infusmat Scape', '', '', '', 1, '', 0, 'VK', '', 'BAIK', 'TERDATA', false, ''],
            ['Infuse pump', 'Precisionmed', '', '', '', 1, '', 0, 'VK', '', 'BAIK', 'TERDATA', false, ''],
            ['Intubasi set', 'Greatcare / Blue Cross', '', '', '', 1, '', 0, 'VK', '', 'BAIK', 'TERDATA', false, ''],
            ['Lampu Rontgen', 'KA Rixa', '', '', '', 1, '', 0, 'VK', '', 'BAIK', 'TERDATA', false, ''],
            ['Lampu Sorot', 'Famed', '', '', '', 1, '', 0, 'VK', '', 'BAIK', 'TERDATA', false, ''],
            ['Lampu sorot', 'Dyna Lighting DNA 100 LED', '', '', '', 1, '', 0, 'VK', '', 'BAIK', 'TERDATA', false, ''],
            ['Monitor Lengkap', 'Medical Equitment', '', '', '', 1, '', 0, 'VK', '', 'BAIK', 'TERDATA', false, ''],
            ['Saturasi Digital', 'Bionet', '', '', '', 1, '', 0, 'VK', '', 'BAIK', 'TERDATA', false, ''],
            ['Sterilitator Kering', 'Fortune Light Wave', '', '', '', 1, '', 0, 'VK', '', 'BAIK', 'TERDATA', false, ''],
            ['Stetoskop', 'Smartcare original', '', '', '', 1, 'DAK 2023', 105896000, 'VK', '', 'BAIK', 'TERDATA', false, ''],
            ['Suction Unit', 'saNi Electric Suction', '', '', '2023', 1, 'APBD 2023', 8258832, 'VK', '', 'BAIK', 'TERDATA', false, ''],
            ['Syringe pump', 'precisionmed', '', '11.32.00.07.03.2023.05.19', '', 1, '', 0, 'VK', '', 'BAIK', 'TERDATA', false, ''],
            ['Tensi meter', 'ABN Regal Clock Aneroid', '', '2020', '', 2, '', 0, 'VK', '', 'BAIK', 'TERDATA', false, ''],
            ['Termometer Digital', 'Onehealth', '', '', '', 1, '', 0, 'VK', '', 'BAIK', 'TERDATA', false, ''],
            ['Tiang infus', 'MAK', '', '', '', 2, '', 0, 'VK', '', 'BAIK', 'TERDATA', false, ''],
            ['Troli com', 'MAK', '', '', '', 1, '', 0, 'VK', '', 'BAIK', 'TERDATA', false, ''],
            ['Troli Dorong', 'KA Rixa', '', '', '2018', 10, '', 0, 'VK', '', 'BAIK', 'TERDATA', false, ''],
            ['Troly Dorong Pakai Laci', 'MAK', '', '36302', '', 1, '', 0, 'VK', '', 'BAIK', 'TERDATA', false, ''],
            ['Troly Emergency', 'MAK', 'KA016-00BSS', '', '', 1, '', 0, 'VK', '', 'BAIK', 'TERDATA', false, ''],
            ['Troly Emergency', 'MAK', '35102', '2530807', '2008', 1, 'DAK 2023', 34894500, 'VK', '', 'BAIK', 'TERDATA', false, ''],
            ['Troly Emergency', 'MAK', '36603', '130900005', '2013', 1, '', 0, 'VK', '', 'BAIK', 'TERDATA', false, ''],
        ];

        $globalCounter = 1;

        foreach ($realAlkesRaw as $row) {
            $namaBarang = $row[0];
            $merk = $row[1];
            $tipe = $row[2];
            $sn = $row[3];
            $tahun = $row[4];
            $jumlah = $row[5] ?: 1;
            $perolehan = $row[6];
            $harga = $row[7];
            $ruangName = $row[8];
            $noteLokasi = $row[9];
            $rawKondisi = strtoupper($row[10] ?: 'BAIK');
            $aspak = $row[11] ?: 'TERDATA';
            $kib = $row[12] ?: false;
            $ket = $row[13];

            // Map Ruangan
            if (isset($ruanganModels[$ruangName])) {
                $rObj = $ruanganModels[$ruangName];
                $ruanganId = $rObj->id;
            } else {
                $ruanganId = $ruanganModels['CSSD']->id;
            }

            $lokasiRuanganId = $ruanganId;

            // Map Kondisi Enum
            if (str_contains($rawKondisi, 'RUSAK BERAT')) {
                $kondisi = KondisiAlkes::RUSAK_BERAT->value;
                $status = StatusAlkes::DALAM_PERBAIKAN->value;
            } elseif (str_contains($rawKondisi, 'RUSAK RINGAN')) {
                $kondisi = KondisiAlkes::RUSAK_RINGAN->value;
                $status = StatusAlkes::DALAM_PERBAIKAN->value;
            } else {
                $kondisi = KondisiAlkes::BAIK->value;
                $status = StatusAlkes::TERSEDIA->value;
            }

            $invCode = sprintf('INV/ALKES/%s/%04d', ($tahun ?: '2024'), $globalCounter);

            Alkes::updateOrCreate(
                ['kode_inventaris' => $invCode],
                [
                    'nama_barang' => $namaBarang,
                    'nomenklatur_id' => $defaultNom->id,
                    'merk' => $merk ?: null,
                    'tipe' => $tipe ?: null,
                    'nomor_seri' => $sn ?: null,
                    'tahun_pengadaan' => $tahun ?: null,
                    'jumlah' => $jumlah,
                    'cara_perolehan' => $perolehan ?: 'Pengadaan RS',
                    'nilai_perolehan' => $harga ?: 0,
                    'ruangan_id' => $ruanganId,
                    'lokasi_ruangan_id' => $lokasiRuanganId,
                    'lokasi_saat_ini_note' => $noteLokasi ?: null,
                    'status' => $status,
                    'kondisi' => $kondisi,
                    'aspak_status' => $aspak,
                    'kib_status' => $kib,
                    'keterangan' => $ket ?: null,
                ]
            );

            $globalCounter++;
        }
    }
}
