const ZAPIN_API_URL = "https://siakers.biz.id/api/alkes";

function fetchZapinData() {
  try {
    const response = UrlFetchApp.fetch(ZAPIN_API_URL, {
      muteHttpExceptions: true,
      headers: { "User-Agent": "ZAPIN-Google-Apps-Script-Sync" }
    });

    if (response.getResponseCode() !== 200) {
      showAlert_("Gagal terhubung ke ZAPIN API! HTTP: " + response.getResponseCode());
      return null;
    }

    const json = JSON.parse(response.getContentText());
    if (!json || !json.data || json.data.length === 0) {
      showAlert_("Data dari ZAPIN API kosong.");
      return null;
    }

    return json.data;
  } catch (err) {
    Logger.log("Error Fetch API: " + err.toString());
    showAlert_("Terjadi Kesalahan Fetch API: " + err.toString());
    return null;
  }
}
