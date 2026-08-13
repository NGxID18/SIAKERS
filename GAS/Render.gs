function renderTableToSheet(sheet, rawData) {
  const rows = [
    [
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
      'Kalibrasi Terakhir',
      'Keterangan'
    ]
  ];

  rawData.forEach(function(item) {
    rows.push([
      item.no,
      item.nama_barang,
      item.merk,
      item.tipe,
      item.seri_number,
      item.tahun,
      item.ruang_pemilik,
      item.lokasi_fisik,
      item.kondisi,
      item.status_kalibrasi,
      item.tanggal_kalibrasi_terakhir,
      item.keterangan
    ]);
  });

  sheet.clear();

  const totalRows = rows.length;
  const totalCols = rows[0].length;

  sheet.getRange(2, 2, totalRows, totalCols).setValues(rows);

  sheet.setRowHeight(1, 20);
  sheet.setColumnWidth(1, 25);

  const tableRange = sheet.getRange(2, 2, totalRows, totalCols);
  tableRange.setFontFamily("Times New Roman");

  const headerRange = sheet.getRange(2, 2, 1, totalCols);
  headerRange.setBackground("#064e3b")
             .setFontColor("#ffffff")
             .setFontWeight("bold")
             .setHorizontalAlignment("center")
             .setVerticalAlignment("middle")
             .setFontSize(12);

  sheet.setRowHeight(2, 40);

  const dataRange = sheet.getRange(3, 2, totalRows - 1, totalCols);
  dataRange.setFontSize(12).setVerticalAlignment("middle");

  for (let r = 3; r <= totalRows + 1; r++) {
    sheet.setRowHeight(r, 28);
  }

  sheet.getRange(3, 2, totalRows - 1, 1).setHorizontalAlignment("center");
  sheet.getRange(3, 7, totalRows - 1, 1).setHorizontalAlignment("center");
  sheet.getRange(3, 10, totalRows - 1, 2).setHorizontalAlignment("center");
  sheet.getRange(3, 12, totalRows - 1, 1).setHorizontalAlignment("center");

  tableRange.setWrap(true);

  sheet.getRange(2, 2, totalRows, totalCols).setBorder(
    true, true, true, true, true, true,
    "#cbd5e1", SpreadsheetApp.BorderStyle.SOLID
  );

  const maxWidths = {
    2: 55,
    3: 220,
    4: 150,
    5: 140,
    6: 160,
    7: 65,
    8: 140,
    9: 140,
    10: 120,
    11: 150,
    12: 130,
    13: 180
  };

  for (let colIndex = 2; colIndex <= totalCols + 1; colIndex++) {
    sheet.autoResizeColumn(colIndex);
    const autoW = sheet.getColumnWidth(colIndex) + 15;
    const maxW = maxWidths[colIndex] || 180;
    sheet.setColumnWidth(colIndex, Math.min(autoW, maxW));
  }

  sheet.setFrozenRows(2);
  sheet.setFrozenColumns(1);
}
