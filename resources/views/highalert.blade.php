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

#sensorChart {
  max-height: 250px;
  width: 100%;
}
  </style>

<!-- 🔷 Main Content Section with Device List and Alerts -->
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
          <div class="tab-button" data-sensor="SO2_H2S">SO2-H2S</div>
        </div>

        <!-- Alert Logs -->
        <div class="bg-white rounded-lg shadow p-4 h-[250px] overflow-y-auto" id="sensor-logs"></div>

      </div>
    </div>
  </div>
</div>

<!-- 📊 Sensor Value Timeline Section (Detached Below) -->
<div class="py-6">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow p-6">
      <h3 class="text-md font-semibold text-gray-800 mb-2">Sensor Value Timeline</h3>
      <select id="sensor-type-filter" class="mb-4 px-3 py-2 rounded-md border">
  <option value="All">All Types</option>
  <option value="CO-LPG">CO-LPG</option>
  <option value="CO2">CO2</option>
  <option value="Gen_Air_Qua">General Air Quality</option>
  <option value="SO2_H2S">SO2-H2S</option>
</select>
      <canvas id="sensorChart" height="250"></canvas>
    </div>
  </div>
</div>

  <script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-app-compat.js"></script>
  <script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-database-compat.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns"></script>

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
  "SO2_H2S": ["Sulfur Dioxide / Hydrogen Sulfide"] // ✅ use underscore here
};

    const deviceList = document.getElementById("device-list");
    const deviceLabel = document.getElementById("device-label");
    const logsContainer = document.getElementById("sensor-logs");
    const tabs = document.querySelectorAll(".tab-button");

    let currentUID = null;
    let allAlerts = {};

      let sensorChartInstance = null;

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
renderSensorChart(uid);
    }

    function loadDeviceAlerts(uid) {
  db.ref(`HIGH_ALERT_LOGS/${uid}`).once("value").then(snapshot => {
    const logs = snapshot.val() || {};
    allAlerts = {};

    Object.entries(logs).forEach(([sensor, entries]) => {
      allAlerts[sensor] = Object.values(entries);
    });

    renderAlerts("All");
    renderSensorChart(uid); // ✅ Move here so it waits for data
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

    document.getElementById("sensor-type-filter").addEventListener("change", e => {
  const selectedType = e.target.value;
  renderSensorChart(currentUID, selectedType);
});



function renderSensorChart(uid, sensorFilter = "All") {
  db.ref(`HIGH_ALERT_LOGS/${uid}`).once("value").then(snapshot => {
    const logs = snapshot.val();
    if (!logs) return;

    const WEEKDAYS = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
    const SENSOR_KEYS = ["CO-LPG", "CO2", "Gen_Air_Qua", "SO2_H2S"];
    const ALERT_LEVEL_COLORS = {
      Safe: "#A3E635",
      Moderate: "#FACC15",
      Unhealthy: "#F97316",
      "Very Unhealthy": "#EF4444",
      Critical: "#7F1D1D"
    };

    const chartData = {};
    WEEKDAYS.forEach(day => chartData[day] = {});

    Object.entries(logs).forEach(([sensorType, entries]) => {
      if (sensorFilter !== "All" && sensorType !== sensorFilter) return;
      if (!SENSOR_KEYS.includes(sensorType)) return;

      Object.values(entries).forEach(entry => {
        const date = new Date(entry.Timestamp);
        const weekday = date.toLocaleDateString("en-US", { weekday: "long" });
        const value = parseFloat(entry.Sensor_Value);
        const level = entry.Sensor_Level;

        if (!isNaN(value)) {
          if (!chartData[weekday][level]) chartData[weekday][level] = 0;
          chartData[weekday][level] += value;
        }
      });
    });

    const datasets = Object.entries(ALERT_LEVEL_COLORS).map(([level, color]) => {
      const data = WEEKDAYS.map(day => chartData[day][level] || 0);
      return {
        label: level,
        data,
        backgroundColor: color
      };
    });

    if (sensorChartInstance && typeof sensorChartInstance.destroy === 'function') {
      sensorChartInstance.destroy();
    }

    const ctx = document.getElementById('sensorChart').getContext('2d');
    sensorChartInstance = new Chart(ctx, {
      type: "bar",
      data: {
        labels: WEEKDAYS,
        datasets
      },
      options: {
        responsive: true,
        plugins: {
          tooltip: { mode: "index", intersect: false },
          legend: { position: "bottom" }
        },
        scales: {
          x: {
            stacked: false, // 🔄 Clustered columns enabled
            title: { display: true, text: "Day of the Week" }
          },
          y: {
            stacked: false,
            beginAtZero: true,
            title: { display: true, text: "Sensor Value" }
          }
        }
      }
    });
  });
}

  </script>

</x-app-layout>
