const ZAPIN_API_BASE_URL = "https://siakers.biz.id/api";
const ZAPIN_API_KEY = "zapin_secret_key_rsjko_2026";

/**
 * Mengambil seluruh data alkes terkini dari sistem ZAPIN (PULL)
 */
function fetchZapinData() {
  try {
    const response = UrlFetchApp.fetch(ZAPIN_API_BASE_URL + "/alkes", {
      method: "GET",
      muteHttpExceptions: true,
      headers: {
        "User-Agent": "ZAPIN-Google-Apps-Script-Sync",
        "X-ZAPIN-KEY": ZAPIN_API_KEY,
        "Accept": "application/json"
      }
    });

    if (response.getResponseCode() !== 200) {
      showAlert_("Gagal terhubung ke ZAPIN API. HTTP: " + response.getResponseCode());
      return null;
    }

    const json = JSON.parse(response.getContentText());
    if (!json || !json.data) {
      showAlert_("Data dari ZAPIN API kosong.");
      return null;
    }

    return json.data;
  } catch (err) {
    Logger.log("Error Fetch API: " + err.toString());
    showAlert_("Terjadi kesalahan Fetch API: " + err.toString());
    return null;
  }
}

/**
 * Mengirim data alkes yang baru diinput di sheet Tambah Alkes ke sistem ZAPIN (PUSH)
 */
function pushZapinData(payloadData) {
  try {
    const response = UrlFetchApp.fetch(ZAPIN_API_BASE_URL + "/alkes/sync", {
      method: "POST",
      contentType: "application/json",
      muteHttpExceptions: true,
      headers: {
        "User-Agent": "ZAPIN-Google-Apps-Script-Sync",
        "X-ZAPIN-KEY": ZAPIN_API_KEY,
        "Accept": "application/json"
      },
      payload: JSON.stringify({
        api_key: ZAPIN_API_KEY,
        data: payloadData
      })
    });

    const responseCode = response.getResponseCode();
    const responseText = response.getContentText();

    if (responseCode !== 200) {
      Logger.log("Gagal Push Data ke ZAPIN API: HTTP " + responseCode + " - " + responseText);
      showAlert_("Gagal menyimpan ke ZAPIN. HTTP " + responseCode + "\n\n" + responseText);
      return null;
    }

    return JSON.parse(responseText);
  } catch (err) {
    Logger.log("Error Push API: " + err.toString());
    showAlert_("Terjadi kesalahan saat mengirim data ke ZAPIN: " + err.toString());
    return null;
  }
}
