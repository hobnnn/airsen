<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      {{ __('High Alert History') }}
    </h2>
  </x-slot>

  <style>
    .alert-item {
      padding: 10px 16px;
      border-radius: 10px;
      background-color: #e4e9f7;
      color: #333;
      font-weight: 600;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }
    .alert-item.active {
      background-color: #d6e0f1;
    }
    .tab-button {
      padding: 6px 10px;
      margin-right: 8px;
      border-radius: 6px;
      font-size: 14px;
      background-color: #e4e9f7;
      cursor: pointer;
    }
    .tab-button.active {
      background-color: #3b82f6;
      color: white;
    }
    .log-entry {
      font-size: 14px;
      margin-bottom: 10px;
      padding-bottom: 6px;
      border-bottom: 1px solid #eee;
    }
    .log-entry .timestamp {
      font-size: 13px;
      color: #777;
    }
    #sensor-logs {
  display: flex;
  flex-direction: column-reverse;
  overflow-y: auto;
  max-height: 250px; /* Existing fixed height */
  scrollbar-width: thin; /* Firefox */
}
.log-entry:nth-child(n + 8) {
  margin-top: auto; /* triggers scroll effect visually */
}
  </style>

  <div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="bg-indigo-50 sm:rounded-lg flex overflow-hidden" style="min-height: 400px; height: 500px;">
        <!-- Left: Device List -->
        <div class="w-64 p-4 bg-indigo-50 border-r border-indigo-100">
          <h3 class="text-md font-semibold text-gray-700 mb-3">Devices</h3>
          <div id="device-list" class="flex flex-col gap-2 h-full overflow-y-auto"></div>
        </div>

        <!-- Right: Alert Content -->
        <div class="flex-1 p-6 bg-indigo-50 flex flex-col">
          <h3 class="text-lg font-semibold text-gray-800 mb-2" id="device-label">Select a device</h3>

          <!-- Tabs -->
          <div class="flex mb-4" id="sensor-tabs">
            <div class="tab-button active" data-sensor="All">All</div>
            <div class="tab-button" data-sensor="CO-LPG">CO-LPG</div>
            <div class="tab-button" data-sensor="CO2">CO2</div>
            <div class="tab-button" data-sensor="Gen_Air_Qua">General</div>
            <div class="tab-button" data-sensor="SO2-H2S">SO2-H2S</div>
          </div>

          <!-- Alert Logs -->
          <div class="bg-white rounded-lg shadow p-4 h-[250px] overflow-y-auto" id="sensor-logs"></div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-app-compat.js"></script>
  <script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-database-compat.js"></script>

  <script>
    const firebaseConfig = {
      apiKey: "{{ env('FIREBASE_CLIENT_API_KEY') }}",
      authDomain: "{{ env('FIREBASE_CLIENT_AUTH_DOMAIN') }}",
      databaseURL: "{{ env('FIREBASE_CLIENT_DATABASE_URL') }}",
      projectId: "{{ env('FIREBASE_CLIENT_PROJECT_ID') }}",
      storageBucket: "{{ env('FIREBASE_CLIENT_STORAGE_BUCKET') }}",
      messagingSenderId: "{{ env('FIREBASE_CLIENT_MESSAGING_SENDER_ID') }}",
      appId: "{{ env('FIREBASE_CLIENT_APP_ID') }}"
    };

    firebase.initializeApp(firebaseConfig);
    const db = firebase.database();
    const ALERT_LEVELS = ["Unhealthy", "Very Unhealthy", "Critical"];

    const SENSOR_LABEL_MAP = {
      "CO-LPG": ["Carbon Monoxide / LPG"],
      "CO2": ["Carbon Dioxide"],
      "Gen_Air_Qua": ["Air Quality (General Pollution)"],
      "SO2-H2S": ["Sulfur Dioxide / Hydrogen Sulfide"]
    };

    const deviceList = document.getElementById("device-list");
    const deviceLabel = document.getElementById("device-label");
    const logsContainer = document.getElementById("sensor-logs");
    const tabs = document.querySelectorAll(".tab-button");

    let currentUID = null;
    let allAlerts = {};

    function loadHighAlertDevices() {
      db.ref("DEVICES").once("value").then(snapshot => {
        const devices = snapshot.val();
        if (!devices) return;
        deviceList.innerHTML = "";

        let firstSelected = false;

        Object.entries(devices).forEach(([deviceId, device]) => {
          const UID = device?.DEVICE_SETTING?.Device_UID || deviceId;
          const sensors = device?.SENSOR_DATA || {};
          const hasHighAlert = Object.values(sensors).some(sensor =>
            ALERT_LEVELS.includes(sensor.Level)
          );

          if (!hasHighAlert) return;

          const btn = document.createElement("div");
          btn.className = "alert-item";
          btn.textContent = UID;
          btn.onclick = () => selectDevice(UID, device);
          deviceList.appendChild(btn);

          if (!firstSelected) {
            firstSelected = true;
            selectDevice(UID, device);
          }
        });
      });
    }

    function selectDevice(uid, device) {
      currentUID = uid;
      allAlerts = {};

      const uidFull = uid; // Full UID like 'AIRSENTI_39dd8c'
const name = device?.DEVICE_SETTING?.Device_Name || uid;
const radius = device?.DEVICE_SETTING?.Radius || 'N/A';
deviceLabel.textContent = `${uidFull} — ${name} — Radius: ${radius}m`;

      document.querySelectorAll(".alert-item").forEach(btn => btn.classList.remove("active"));
      [...deviceList.children].forEach(child => {
        if (child.textContent === uid) child.classList.add("active");
      });

      tabs.forEach(tab => tab.classList.remove("active"));
      tabs[0].classList.add("active");

      loadDeviceAlerts(uid);
    }

    function loadDeviceAlerts(uid) {
      db.ref(`HIGH_ALERT_LOGS/${uid}`).once("value").then(snapshot => {
        const logs = snapshot.val() || {};
        allAlerts = {};

        Object.entries(logs).forEach(([sensor, entries]) => {
          allAlerts[sensor] = Object.values(entries);
        });

        renderAlerts("All");
      });
    }

    function renderAlerts(sensorType) {
      logsContainer.innerHTML = "";

      let logs = [];
      if (sensorType === "All") {
        logs = Object.values(allAlerts).flat();
      } else {
        const targets = SENSOR_LABEL_MAP[sensorType] || [];
        logs = Object.values(allAlerts).flat().filter(log =>
          targets.includes(log.Sensor_Label)
        );
      }

      logs.sort((a, b) => new Date(b.Timestamp) - new Date(a.Timestamp));

      logs.forEach(log => {
        const entry = document.createElement("div");
        entry.className = "log-entry";
        entry.innerHTML = `
          <div><strong>${log.Sensor_Label}</strong> → ${log.Sensor_Value} (${log.Sensor_Level})</div>
          <div class="timestamp">${new Date(log.Timestamp).toLocaleString()}</div>
        `;
        logsContainer.appendChild(entry);
      });
    }

    tabs.forEach(tab => {
      tab.addEventListener("click", () => {
        tabs.forEach(t => t.classList.remove("active"));
        tab.classList.add("active");
        renderAlerts(tab.dataset.sensor);
      });
    });

    loadHighAlertDevices();
  </script>

  <script>
  const ALERT_SCAN_INTERVAL_MS = 1800000; // ⏱ 30 1800000 60000

  function checkAndLogAlerts() {
    db.ref("DEVICES").once("value").then(snapshot => {
      const devices = snapshot.val();
      if (!devices) return;

      Object.entries(devices).forEach(([deviceId, device]) => {
        const UID = device?.DEVICE_SETTING?.Device_UID || deviceId;
        const safeUID = UID.replace(/[.#$[\]/]/g, "_");
        const sensors = device?.SENSOR_DATA || {};

        Object.entries(sensors).forEach(([sensorType, sensorData]) => {
          if (typeof sensorData !== "object") return;

          const level = sensorData.Level;
          const value = sensorData.Value;
          const label = sensorData.Label || sensorType;

          if (ALERT_LEVELS.includes(level)) {
            const timestamp = new Date().toISOString();
            const safeTimestamp = timestamp.replace(/[.#$[\]/:]/g, "_");
            const safeSensor = sensorType.replace(/[.#$[\]/]/g, "_");

            const logData = {
              Sensor_Label: label,
              Sensor_Value: value,
              Sensor_Level: level,
              Timestamp: timestamp
            };

            const path = `HIGH_ALERT_LOGS/${safeUID}/${safeSensor}/${safeTimestamp}`;
            db.ref(path).set(logData);
          }
        });
      });
    });
  }

  // 🔁 Start scanning loop every 30 minutes — no initial trigger
  setInterval(checkAndLogAlerts, ALERT_SCAN_INTERVAL_MS);

  
</script>
</x-app-layout>
