/**
 * Link URL Laporan Google Looker Studio / Data Studio
 */
const DATA_STUDIO_URL = "https://datastudio.google.com/reporting/5024dd9b-a5c5-46b5-b914-638c03f7acd8";

/**
 * Helper untuk mendapatkan atau membuat Sheet berdasarkan nama
 */
function getOrCreateSheet_(name) {
  const ss = SpreadsheetApp.getActiveSpreadsheet();
  let sheet = ss.getSheetByName(name);
  if (!sheet) {
    sheet = ss.insertSheet(name);
  }
  return sheet;
}

/**
 * Menu Utama pada Google Spreadsheet (RefreshData)
 */
function onOpen() {
  try {
    const ui = SpreadsheetApp.getUi();
    ui.createMenu("RefreshData")
      .addItem("Alkes ZAPIN", "refreshAlkesZAPIN")
      .addItem("Tambah Alkes", "submitTambahAlkes")
      .addSeparator()
      .addItem("Data Studio", "openDataStudio")
      .addToUi();
  } catch (e) {
    Logger.log("onOpen: " + e.toString());
  }
}

/**
 * Pilihan 3: Data Studio
 * Membuka dashboard visual Google Data Studio di tab baru
 */
function openDataStudio() {
  const html = HtmlService.createHtmlOutput(
    '<!DOCTYPE html>' +
    '<html>' +
    '<head>' +
    '<base target="_blank">' +
    '<style>' +
    'body { font-family: Inter, sans-serif; text-align: center; padding: 25px 15px; margin: 0; background-color: #f8fafc; }' +
    'h3 { color: #064e3b; margin: 0 0 10px 0; font-size: 16px; }' +
    'p { color: #475569; font-size: 12px; margin-bottom: 20px; line-height: 1.5; }' +
    '.btn { background-color: #064e3b; color: #ffffff !important; padding: 10px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 13px; display: inline-block; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }' +
    '.btn:hover { background-color: #04382a; }' +
    '</style>' +
    '</head>' +
    '<body>' +
    '<h3>Dashboard Data Studio</h3>' +
    '<p>Dashboard visual grafik dan persentase alkes terbuka di tab baru. Jika pop-up diblokir, klik tombol di bawah:</p>' +
    '<a href="' + DATA_STUDIO_URL + '" class="btn" target="_blank">Buka Dashboard Data Studio</a>' +
    '<script>' +
    'window.open("' + DATA_STUDIO_URL + '", "_blank");' +
    '</script>' +
    '</body>' +
    '</html>'
  ).setWidth(380).setHeight(170);

  SpreadsheetApp.getUi().showModalDialog(html, "Membuka Data Studio");
}

/**
 * Pilihan 1: Alkes ZAPIN
 * Hanya merefresh data pada sheet 'Alkes' dari database ZAPIN
 */
function refreshAlkesZAPIN() {
  const ss = SpreadsheetApp.getActiveSpreadsheet();
  const viewSheet = getOrCreateSheet_("Alkes");

  // Pastikan sheet 'Tambah Alkes' sudah disiapkan jika belum ada
  let inputSheet = ss.getSheetByName("Tambah Alkes");
  if (!inputSheet) {
    inputSheet = ss.insertSheet("Tambah Alkes");
    setupInputAlkesSheet(inputSheet);
  }

  const data = fetchZapinData();
  if (!data) return;

  renderAlkesViewSheet(viewSheet, data);
  ss.setActiveSheet(viewSheet);

  showAlert_("Data pada sheet Alkes berhasil diperbarui dari database ZAPIN.");
}

/**
 * Pilihan 2: Tambah Alkes
 * Memproses dan mengirim data baru dari sheet 'Tambah Alkes' ke database ZAPIN
 */
function submitTambahAlkes() {
  const ss = SpreadsheetApp.getActiveSpreadsheet();
  const inputSheet = getOrCreateSheet_("Tambah Alkes");
  const lastRow = inputSheet.getLastRow();

  if (lastRow < 2) {
    showAlert_("Form Tambah Alkes masih kosong. Silakan isi data alkes baru pada tabel di sheet Tambah Alkes.");
    return;
  }

  // Baca data input dari baris 2, kolom A (1) sampai J (10) -> 10 kolom input
  const numRows = lastRow - 1;
  const values = inputSheet.getRange(2, 1, numRows, 10).getValues();
  const payload = [];

  for (let i = 0; i < values.length; i++) {
    const rowData = values[i];
    const namaBarang = rowData[0];

    if (namaBarang && String(namaBarang).trim() !== "") {
      const cleanName = String(namaBarang).trim();
      const lowerName = cleanName.toLowerCase();
      if (lowerName === "nama barang" || lowerName.indexOf("nama barang") === 0 || lowerName.indexOf("ketik data") !== -1 || lowerName.indexOf("form tambah") !== -1) {
        continue;
      }

      payload.push({
        nama_barang: cleanName,
        merk: rowData[1] || "-",
        tipe: rowData[2] || "-",
        seri_number: rowData[3] || "-",
        tahun: rowData[4] || "-",
        ruang_pemilik: rowData[5] || "CSSD",
        lokasi_alkes: rowData[6] || "CSSD",
        kondisi: rowData[7] || "Baik",
        status_kalibrasi: rowData[8] || "BELUM DIKALIBRASI",
        keterangan: rowData[9] || "-"
      });
    }
  }

  if (payload.length === 0) {
    showAlert_("Tidak ada data alkes baru yang diisi. Pastikan kolom Nama Barang telah terisi.");
    return;
  }

  // Kirim data baru ke database ZAPIN
  const result = pushZapinData(payload);

  if (result && result.status === "success") {
    // 1. Bersihkan kembali form input Tambah Alkes
    setupInputAlkesSheet(inputSheet);

    // 2. Refresh sheet Alkes agar data baru langsung tampak di urutan ruangan yang sesuai
    const viewSheet = getOrCreateSheet_("Alkes");
    const data = fetchZapinData();
    if (data) {
      renderAlkesViewSheet(viewSheet, data);
    }

    // 3. Pindahkan tampilan ke sheet Alkes
    ss.setActiveSheet(viewSheet);

    showAlert_("Sebanyak " + payload.length + " data alat kesehatan baru berhasil ditambahkan ke database ZAPIN dan tampil di sheet Alkes.");
  }
}

/**
 * Fungsi khusus Trigger Otomatis Per 1 Jam
 * Hanya merefresh sheet 'Alkes' tanpa menyentuh sheet 'Tambah Alkes'
 */
function autoRefreshAlkes() {
  const ss = SpreadsheetApp.getActiveSpreadsheet();
  const viewSheet = getOrCreateSheet_("Alkes");

  const data = fetchZapinData();
  if (!data) return;

  renderAlkesViewSheet(viewSheet, data);
}

function showAlert_(message) {
  try {
    SpreadsheetApp.getUi().alert(message);
  } catch (e) {
    Logger.log(message);
  }
}
