function syncDataFromZapinAPI() {
  const data = fetchZapinData();
  if (!data) return;

  const ss = SpreadsheetApp.getActiveSpreadsheet();
  const sheet = ss.getActiveSheet();

  renderTableToSheet(sheet, data);
}

function setupAutoSyncTrigger() {
  const triggers = ScriptApp.getProjectTriggers();
  for (let i = 0; i < triggers.length; i++) {
    if (triggers[i].getHandlerFunction() === "syncDataFromZapinAPI") {
      ScriptApp.deleteTrigger(triggers[i]);
    }
  }

  ScriptApp.newTrigger("syncDataFromZapinAPI")
    .timeBased()
    .everyHours(1)
    .create();

  showAlert_("Auto-Sync Berhasil Dipasang!\n\nData Google Spreadsheet akan ter-update otomatis dari ZAPIN setiap 1 jam.");
}

function onOpen() {
  try {
    const ui = SpreadsheetApp.getUi();
    ui.createMenu("🔄 Refresh ZAPIN")
      .addItem("🔄 Refresh ZAPIN", "syncDataFromZapinAPI")
      .addToUi();
  } catch (e) {
    Logger.log("onOpen: " + e.toString());
  }
}

function RefreshZapin() {
  syncDataFromZapinAPI();
}

function refreshZapin() {
  syncDataFromZapinAPI();
}

function showAlert_(message) {
  try {
    SpreadsheetApp.getUi().alert(message);
  } catch (e) {
    Logger.log(message);
  }
}
