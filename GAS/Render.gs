/**
 * Daftar Resmi Unit Instalasi & Ruangan RSJKO Engku Haji Daud untuk Dropdown
 */
const RUANGAN_LIST_DROPDOWN = [
  "CSSD",
  "Fisioterapi",
  "Hemodialisa",
  "ICU",
  "IGD",
  "IGD JIWA",
  "IPSRS",
  "Irna Anak",
  "Irna Bedah",
  "Irna Penyakit Dalam",
  "Irna Perinatology",
  "Laboratorium",
  "MCU",
  "Nursestation LT4",
  "OK",
  "Poli",
  "Poli Anak",
  "Poli Bedah",
  "Poli Gigi",
  "Poli Jantung",
  "Poli Kebidanan",
  "Poli Mata",
  "Poli Penyakit Dalam",
  "Radiologi",
  "Ruang Isolasi",
  "THT",
  "UTD",
  "VK"
];

const KONDISI_LIST_DROPDOWN = [
  "Baik",
  "Rusak Ringan",
  "Rusak Berat"
];

const KALIBRASI_LIST_DROPDOWN = [
  "BELUM DIKALIBRASI",
  "SUDAH DIKALIBRASI"
];

/**
 * 1. Merender tabel data database alkes ke Sheet 'Alkes' (VIEW ONLY - 11 Kolom)
 */
function renderAlkesViewSheet(sheet, rawData) {
  const headers = [
    'No',
    'Nama Barang',
    'Merk',
    'Tipe',
    'Seri Number',
    'Tahun',
    'Ruang Pemilik',
    'Lokasi Alkes',
    'Kondisi',
    'Status Kalibrasi',
    'Keterangan'
  ];

  const rows = [headers];

  rawData.forEach(function(item, index) {
    let rawKondisi = item.kondisi || 'Baik';
    if (rawKondisi.indexOf('Baik') !== -1) rawKondisi = 'Baik';
    else if (rawKondisi.indexOf('Rusak Ringan') !== -1) rawKondisi = 'Rusak Ringan';
    else if (rawKondisi.indexOf('Rusak Berat') !== -1) rawKondisi = 'Rusak Berat';

    rows.push([
      index + 1,
      item.nama_barang || '',
      item.merk || '-',
      item.tipe || '-',
      item.seri_number || '-',
      item.tahun || '-',
      item.ruang_pemilik || 'CSSD',
      item.lokasi_alkes || item.lokasi_fisik || item.ruang_pemilik || 'CSSD',
      rawKondisi,
      item.status_kalibrasi || 'BELUM DIKALIBRASI',
      item.keterangan || '-'
    ]);
  });

  sheet.clear();
  sheet.getRange(1, 1, Math.max(sheet.getMaxRows(), 100), Math.max(sheet.getMaxColumns(), 20)).clearDataValidations();

  const totalRows = rows.length;
  const totalCols = rows[0].length;

  const startRow = 2;
  const startCol = 2; // Mulai Kolom B

  sheet.getRange(startRow, startCol, totalRows, totalCols).setValues(rows);

  sheet.setRowHeight(1, 20);
  sheet.setColumnWidth(1, 25); // Kolom A Margin

  const tableRange = sheet.getRange(startRow, startCol, totalRows, totalCols);
  tableRange.setFontFamily("Inter");

  // Styling Header (Baris 2)
  const headerRange = sheet.getRange(startRow, startCol, 1, totalCols);
  headerRange.setBackground("#064e3b") // Emerald Dark
             .setFontColor("#ffffff")
             .setFontWeight("bold")
             .setHorizontalAlignment("center")
             .setVerticalAlignment("middle")
             .setFontSize(11);

  sheet.setRowHeight(startRow, 38);

  // Styling Baris Data (Zebra Striping)
  if (totalRows > 1) {
    const dataRange = sheet.getRange(startRow + 1, startCol, totalRows - 1, totalCols);
    dataRange.setFontSize(10)
             .setFontColor("#0f172a")
             .setVerticalAlignment("middle");

    for (let r = 0; r < totalRows - 1; r++) {
      const currentRow = startRow + 1 + r;
      sheet.setRowHeight(currentRow, 28);

      const rowRange = sheet.getRange(currentRow, startCol, 1, totalCols);
      if (r % 2 === 0) {
        rowRange.setBackground("#ffffff");
      } else {
        rowRange.setBackground("#f1f5f9");
      }
    }

    sheet.getRange(startRow + 1, 2, totalRows - 1, 1).setHorizontalAlignment("center"); // No (B)
    sheet.getRange(startRow + 1, 7, totalRows - 1, 1).setHorizontalAlignment("center"); // Tahun (G)
    sheet.getRange(startRow + 1, 10, totalRows - 1, 2).setHorizontalAlignment("center"); // Kondisi, Status Kalibrasi (J, K)
  }

  tableRange.setWrap(true);

  sheet.getRange(startRow, startCol, totalRows, totalCols).setBorder(
    true, true, true, true, true, true,
    "#cbd5e1", SpreadsheetApp.BorderStyle.SOLID
  );

  const columnWidths = {
    2: 55,  // No (B)
    3: 220, // Nama Barang (C)
    4: 140, // Merk (D)
    5: 140, // Tipe (E)
    6: 150, // Seri Number (F)
    7: 65,  // Tahun (G)
    8: 140, // Ruang Pemilik (H)
    9: 140, // Lokasi Alkes (I)
    10: 120,// Kondisi (J)
    11: 150,// Status Kalibrasi (K)
    12: 180 // Keterangan (L)
  };

  for (let colIndex = 2; colIndex <= 12; colIndex++) {
    sheet.setColumnWidth(colIndex, columnWidths[colIndex] || 140);
  }

  sheet.setFrozenRows(2);
  sheet.setFrozenColumns(2);
}

/**
 * 2. Menyiapkan Sheet 'Tambah Alkes' (10 Kolom Input, 20 Baris Zebra Striping)
 */
function setupInputAlkesSheet(sheet) {
  sheet.clear();
  sheet.getRange(1, 1, Math.max(sheet.getMaxRows(), 100), Math.max(sheet.getMaxColumns(), 20)).clearDataValidations();

  sheet.setRowHeight(1, 20);
  sheet.setColumnWidth(1, 25); // Kolom A Margin

  // Header Kolom Input langsung di Baris 2 (10 Kolom)
  const inputHeaders = [
    'Nama Barang',
    'Merk',
    'Tipe',
    'Seri Number',
    'Tahun',
    'Ruang Pemilik',
    'Lokasi Alkes',
    'Kondisi',
    'Status Kalibrasi',
    'Keterangan'
  ];

  const headerRange = sheet.getRange(2, 2, 1, 10);
  headerRange.setValues([inputHeaders])
             .setBackground("#064e3b") // Emerald Dark (Identik dengan sheet Alkes)
             .setFontColor("#ffffff")
             .setFontWeight("bold")
             .setHorizontalAlignment("center")
             .setVerticalAlignment("middle")
             .setFontSize(11)
             .setFontFamily("Inter");
  sheet.setRowHeight(2, 38);

  // Siapkan persis 20 baris kosong siap isi (Baris 3 s/d 22) dengan desain Zebra Striping
  const maxInputRows = 20;
  const inputArea = sheet.getRange(3, 2, maxInputRows, 10);
  inputArea.setFontFamily("Inter")
           .setFontSize(10)
           .setFontColor("#0f172a")
           .setVerticalAlignment("middle");

  for (let r = 0; r < maxInputRows; r++) {
    const currentRow = 3 + r;
    sheet.setRowHeight(currentRow, 28);

    const rowRange = sheet.getRange(currentRow, 2, 1, 10);
    if (r % 2 === 0) {
      rowRange.setBackground("#ffffff"); // Putih
    } else {
      rowRange.setBackground("#f1f5f9"); // Slate-100 lembut
    }
  }

  // Perataan Kolom
  sheet.getRange(3, 6, maxInputRows, 1).setHorizontalAlignment("center"); // Tahun (F)
  sheet.getRange(3, 9, maxInputRows, 2).setHorizontalAlignment("center"); // Kondisi, Status Kalibrasi (I, J)

  // Border Area Input
  sheet.getRange(2, 2, maxInputRows + 1, 10).setBorder(
    true, true, true, true, true, true,
    "#cbd5e1", SpreadsheetApp.BorderStyle.SOLID
  );

  // Dropdown Validation untuk Ruangan
  const roomValidation = SpreadsheetApp.newDataValidation()
    .requireValueInList(RUANGAN_LIST_DROPDOWN, true)
    .setAllowInvalid(true)
    .build();

  // Dropdown Validation untuk Kondisi
  const kondisiValidation = SpreadsheetApp.newDataValidation()
    .requireValueInList(KONDISI_LIST_DROPDOWN, true)
    .setAllowInvalid(true)
    .build();

  // Dropdown Validation untuk Status Kalibrasi
  const kalibrasiValidation = SpreadsheetApp.newDataValidation()
    .requireValueInList(KALIBRASI_LIST_DROPDOWN, true)
    .setAllowInvalid(true)
    .build();

  // Pasang Dropdown Ruang Pemilik (Kolom G / index 7)
  sheet.getRange(3, 7, maxInputRows, 1).setDataValidation(roomValidation);

  // Pasang Dropdown Lokasi Alkes (Kolom H / index 8)
  sheet.getRange(3, 8, maxInputRows, 1).setDataValidation(roomValidation);

  // Pasang Dropdown Kondisi (Kolom I / index 9)
  sheet.getRange(3, 9, maxInputRows, 1).setDataValidation(kondisiValidation);

  // Pasang Dropdown Status Kalibrasi (Kolom J / index 10)
  sheet.getRange(3, 10, maxInputRows, 1).setDataValidation(kalibrasiValidation);

  // Lebar Kolom Form Input (Senada dengan sheet Alkes)
  const inputWidths = {
    2: 220, // Nama Barang (B)
    3: 140, // Merk (C)
    4: 140, // Tipe (D)
    5: 150, // Seri Number (E)
    6: 65,  // Tahun (F)
    7: 140, // Ruang Pemilik (G)
    8: 140, // Lokasi Alkes (H)
    9: 120, // Kondisi (I)
    10: 150,// Status Kalibrasi (J)
    11: 180 // Keterangan (K)
  };

  for (let colIndex = 2; colIndex <= 11; colIndex++) {
    sheet.setColumnWidth(colIndex, inputWidths[colIndex] || 140);
  }

  sheet.setFrozenRows(2);
}
