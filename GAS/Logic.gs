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
      .addToUi();
  } catch (e) {
    Logger.log("onOpen: " + e.toString());
  }
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

  if (lastRow < 3) {
    showAlert_("Form Tambah Alkes masih kosong. Silakan isi data alkes baru pada tabel di sheet Tambah Alkes.");
    return;
  }

  // Baca data input dari baris 3, kolom B (2) sampai K (11) -> 10 kolom input
  const numRows = lastRow - 2;
  const values = inputSheet.getRange(3, 2, numRows, 10).getValues();
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
