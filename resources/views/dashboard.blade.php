<x-app-layout>

    <style>
        body {
            position: relative;
            overflow-x: hidden;
            font-family: 'Segoe UI', sans-serif;
        }

        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.25);
            backdrop-filter: blur(2px);
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
            z-index: 998;
        }

        .modal {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0.95);
            background-color: #fff;
            padding: 24px;
             width: 480px;      /* ← Increased from 360px */
            max-width: 96vw;
            min-height: 460px;
            border-radius: 12px;
            box-shadow: 0 6px 30px rgba(0, 0, 0, 0.35);
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease, transform 0.3s ease;
            z-index: 999;
        }

        .modal.show,
        .modal-overlay.show {
            opacity: 1;
            pointer-events: auto;
        }

        .modal.show {
            transform: translate(-50%, -50%) scale(1);
        }

        .modal-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 24px;
        }

        .modal-actions .delete-btn {
            background-color: #ff4d4d;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
        }

        .modal-actions .delete-btn:hover {
            background-color: #e64444;
        }

        .modal-actions .close-btn {
            background-color: #e0e0e0;
            color: #333;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
        }

        .modal-actions .close-btn:hover {
            background-color: #d2d2d2;
        }

        #settings-content p {
            margin-bottom: 16px;
            font-size: 14px;
            line-height: 1.4;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        #settings-content strong {
            font-weight: 600;
            color: #333;
        }

        #settings-content span {
            max-width: 180px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        #settings-content input[type="text"],
        #settings-content input[type="number"] {
            padding: 6px 8px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 6px;
            width: 140px;
        }

        #settings-content button {
            background-color: #e0e0e0;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
            margin-left: 6px;
        }

        #settings-content button:hover {
            background-color: #d2d2d2;
        }

        #settings-content button.delete-btn {
            background-color: #ff4d4d;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
        }

        #settings-content button.delete-btn:hover {
            background-color: #e64444;
        }

        .toggle-section {
            margin-top: 20px;
            border-top: 1px solid #eee;
            padding-top: 16px;
        }

        .toggle-section p {
            margin: 8px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .device-list li.alert {
            background-color: #ffe6e6;
            color: #333;
        }

        .device-list li {
  padding: 8px 12px;
  border-radius: 8px;
  transition: background-color 0.2s ease, transform 0.2s ease;
  color: #333; /* 👈 Ensures consistent text color */
}

.device-list li span {
  color: inherit; /* Inherit the neutral color */
}

       .device-list li:hover {
  filter: brightness(1.05); /* 🌟 Slight brightening instead */
  transform: translateX(4px);
  cursor: pointer;
}

.device-list li.selected {
  outline: 2px dashed #333;
  filter: brightness(1.08);
}


.device-list-item.sensor-critical {
  background-color: #e9a4a4;
}

.device-list-item.sensor-very-unhealthy {
  background-color: #fbd4d4;
}

.device-list-item.sensor-unhealthy {
  background-color: #ffe2cc;
}

.device-list-item.sensor-moderate {
  background-color: #fff8cc;
}

.device-list-item.sensor-safe {
  background-color: #d9fdd9;
}


        .device-card {
            width: 400px;
            min-height: 300px;
            margin: 16px;
            padding: 18px;
            border-radius: 10px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
            background-color: white;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .device-card.hovered {
            outline: 2px dashed #4d90fe;
            background-color: #f0f8ff;
            transition: outline 0.2s ease, background-color 0.2s ease;
        }

        .device-card.alert {
            border: 2px solid #f5aaaa;
        }

        label img[alt="Info"] {
            filter: grayscale(100%);
            opacity: 0.6;
            transition: opacity 0.2s ease;
        }

        label img[alt="Info"]:hover {
            opacity: 1;
        }

        .sensor-box-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(160px, 1fr));
            gap: 12px;
            margin-top: 12px;
        }

        .map-area {
            height: 400px;
            width: 100%;
            background-color: #eee;
            border-radius: 12px;
            margin-bottom: 24px;
        }

        .sensor-critical {
            background-color: #e9a4a4;
            color: #5a0000;
        }

        .sensor-very-unhealthy {
            background-color: #fbd4d4;
            color: #b30000;
        }

        .sensor-unhealthy {
            background-color: #ffe2cc;
            color: #cc3300;
        }

        .sensor-moderate {
            background-color: #fff8cc;
            color: #665500;
        }

        .sensor-safe {
            background-color: #d9fdd9;
            color: #006600;
        }

.button-basic {
  padding: 10px 20px;
  font-size: 14px;
  border-radius: 8px;
  border: 1px solid #ccc;
  background-color: #f5f5f5;
  color: #333;
  cursor: pointer;
  font-weight: 500;
  transition: background-color 0.2s ease, box-shadow 0.2s ease;
}

.button-basic:hover {
  background-color: #e5e5e5;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.06);
}

.button-basic:active {
  background-color: #dcdcdc;
}

.button-basic.save {
  border: 1px solid #3c8dbc;
  background-color: #3c8dbc;
  color: #fff;
}

.button-basic.save:hover {
  background-color: #337ab7;
}
    </style>


    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="container">
        <div class="top-section">
            <div id="map" class="map-area"></div>
            <div class="device-list">
                <h3>Devices</h3>
                <ul id="device-list"></ul>
            </div>
        </div>

        <div id="device-container" style="display: flex; flex-wrap: wrap;"></div>
    </div>

    <!-- Firebase SDK -->

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

        const getLevelClass = (level) => {
            switch ((level || "").toLowerCase().trim()) {
                case "safe":
                    return "sensor-safe";
                case "moderate":
                    return "sensor-moderate";
                case "unhealthy":
                    return "sensor-unhealthy";
                case "very unhealthy":
                    return "sensor-very-unhealthy";
                case "critical":
                    return "sensor-critical";
                default:
                    return "sensor-neutral";
            }
        };

        const buildDeviceCard = (deviceId, device) => {
            const sensorData = device.SENSOR_DATA || {};
            const setting = device.DEVICE_SETTING || {};
            const container = document.getElementById("device-container");

            let card = document.getElementById(`device-${deviceId}`);
            if (!card) {
                card = document.createElement("div");
                card.id = `device-${deviceId}`;
                card.className = "device-card";
                container.appendChild(card);
            }

            const displayNameMode = setting.Display_Name_Mode || "device";
            const displayName =
                displayNameMode === "location" ? setting.Location_Name : setting.Device_Name;

            const hasAlert = Object.values(sensorData).some(
                (sensor) =>
                sensor?.Level && ["Unhealthy", "Very Unhealthy", "Critical"].includes(sensor.Level)
            );

            card.className = `device-card${hasAlert ? " alert" : ""}`;

            card.innerHTML = `
    <div style="display: flex; justify-content: space-between; align-items: center;">
      <h4 style="margin: 0;">${displayName}</h4>
      <div style="display: flex; align-items: center; gap: 6px;">
        <button onclick="openSettings('${deviceId}')" style="background: none; border: none; cursor: pointer;">
          <img src="/icons/gear.png" alt="Settings" style="width: 18px; height: 18px;" />
        </button>
      </div>
    </div>
    <div class="sensor-box-grid">
      ${Object.entries(sensorData).map(([type, info]) => `
            <div class="sensor-box ${getLevelClass(info.Level)}">
              <strong>${info.Label}</strong>
              <div>${info.Value}</div>
              <small>(${info.Level})</small>
            </div>
          `).join("")}
    </div>
  `;
        };

        function updateDeviceList(devices) {
  const deviceList = document.getElementById("device-list");
  deviceList.innerHTML = "";

  const sorted = Object.entries(devices).sort(([, a], [, b]) => {
    const sa = a.DEVICE_SETTING || {};
    const sb = b.DEVICE_SETTING || {};
    const la = sa.Lock_Device === true;
    const lb = sb.Lock_Device === true;
    const aa = Object.values(a.SENSOR_DATA || {}).some(
      (sensor) => ["Unhealthy", "Very Unhealthy", "Critical"].includes(sensor?.Level)
    );
    const ab = Object.values(b.SENSOR_DATA || {}).some(
      (sensor) => ["Unhealthy", "Very Unhealthy", "Critical"].includes(sensor?.Level)
    );
    const sortScore = (locked, alert) => {
      if (locked && alert) return 3;
      if (locked) return 2;
      if (alert) return 1;
      return 0;
    };
    return sortScore(lb, ab) - sortScore(la, aa);
  });

  sorted.forEach(([deviceId, device]) => {
    const sensorData = device.SENSOR_DATA || {};
    const topLevel = getHighestLevel(sensorData); // 🔥 Use priority logic

    const setting = device.DEVICE_SETTING || {};
    const name =
      setting.Display_Name_Mode === "location"
        ? setting.Location_Name
        : setting.Device_Name;

    const li = document.createElement("li");
    li.id = `device-name-${deviceId}`;
    li.className = `device-list-item ${getLevelClass(topLevel)}`; // 🎨 Apply sensor color class
    li.style.display = "flex";
    li.style.justifyContent = "space-between";
    li.style.alignItems = "center";
    li.innerHTML = `<span>${name}</span>`;

    li.addEventListener("mouseenter", () => {
      const pin = markerMap[deviceId];
      const radiusRaw = device.DEVICE_SETTING?.Radius;
      const radius = parseFloat(radiusRaw);
      const validRadius = isNaN(radius) || radius <= 0 ? 100 : radius;

      if (pin?.position) {
        map.panTo(pin.position);
        map.setZoom(getZoomFromRadius(validRadius));
      }

      const card = document.getElementById(`device-${deviceId}`);
      if (card) card.classList.add("hovered");
    });

    li.addEventListener("mouseleave", () => {
      const card = document.getElementById(`device-${deviceId}`);
      if (card) card.classList.remove("hovered");
    });

    deviceList.appendChild(li);

    li.addEventListener("click", () => {
  clearMapVisuals();

  const device = devices[deviceId];
  const deviceSetting = device.DEVICE_SETTING || {};
  const evacData = device.EVACUATION_DATA || {};

  const deviceLatLng = new google.maps.LatLng(parseFloat(deviceSetting.Latitude), parseFloat(deviceSetting.Longitude));
  const evacLatLng = new google.maps.LatLng(parseFloat(evacData.Latitude), parseFloat(evacData.Longitude));

if (!evacData || !evacData.Latitude || !evacData.Longitude || !evacData.Evacuation_Center) {
  showEvacInfoBox("Please assign an evacuation zone to view this device's route.");
  return; // 🚫 Exit early to avoid errors
}

  focusDeviceAndEvacuation(deviceId, device); // Optional centering
  fetchAnimatedPath(deviceLatLng, evacLatLng); // 🌀 Animated path

  // 🏷️ Add fresh labels
activeDeviceLabel = createInlineLabel(deviceLatLng, deviceSetting.Location_Name || "Device", map);
activeEvacLabel = createInlineLabel(evacLatLng, evacData.Evacuation_Center  || "Evacuation Zone", map);

});

  });
}
        db.ref("DEVICES").on("value", (snapshot) => {
            const devices = snapshot.val() || {};
            const deviceList = document.getElementById("device-list");
            const container = document.getElementById("device-container");

            deviceList.innerHTML = "";
            container.innerHTML = "";

            if (Object.keys(devices).length === 0) {
                deviceList.innerHTML = "<li>No devices found.</li>";
                container.innerHTML = "<p style='padding: 20px; color: #888;'>No devices to display.</p>";
                return;
            }

            updateDeviceList(devices);
            Object.entries(devices).forEach(([deviceId, device]) => buildDeviceCard(deviceId, device));
        });




function openSettings(deviceId) {
  const deviceSettingRef = db.ref(`DEVICES/${deviceId}/DEVICE_SETTING`);
  const evacDataRef = db.ref(`DEVICES/${deviceId}/EVACUATION_DATA`);

  deviceSettingRef.once("value").then((snapshot) => {
    const setting = snapshot.val() || {};

    // ⚙️ Set basic device settings
    document.getElementById("setting-device-name-text").textContent = setting.Device_Name || "";
    document.getElementById("setting-device-name-input").value = setting.Device_Name || "";

    document.getElementById("edit-device-name-btn").setAttribute("data-id", deviceId);
    document.getElementById("save-device-name-btn").setAttribute("data-id", deviceId);

    document.getElementById("setting-location-text").textContent = setting.Location_Name || "";
    document.getElementById("setting-location-input").value = setting.Location_Name || "";

    document.getElementById("setting-lat").textContent = setting.Latitude || "";
    document.getElementById("setting-lng").textContent = setting.Longitude || "";

    document.getElementById("setting-radius-text").textContent = setting.Radius || "";
    document.getElementById("setting-radius-input").value = setting.Radius || "";

    document.getElementById("edit-location-btn").setAttribute("data-id", deviceId);
    document.getElementById("save-location-btn").setAttribute("data-id", deviceId);
    document.getElementById("edit-radius-btn").setAttribute("data-id", deviceId);
    document.getElementById("save-radius-btn").setAttribute("data-id", deviceId);

    // 🧭 Load evacuation center info
    evacDataRef.once("value").then(evacSnap => {
      const evacData = evacSnap.val() || {};
      const evacName = evacData.Evacuation_Center || "";
      const evacLat = evacData.Latitude;
      const evacLng = evacData.Longitude;

      const evacDisplay = document.getElementById("evacuation-display");
      const evacButton = document.getElementById("evacuation-button");

      if (evacDisplay && evacButton) {
        const hasEvacData = evacName && evacLat && evacLng;

        evacDisplay.textContent = evacName || "—";
        evacButton.textContent = hasEvacData ? "Edit Evacuation Center" : "Add Evacuation Center";
      }
    });

    // 🪟 Show modal
    document.getElementById("modal-overlay").classList.add("show");
    document.getElementById("settings-modal").classList.add("show");
  });

  window.currentDeviceId = deviceId;

  const clearButton = document.querySelector("#settings-content .btn.clear");

evacDataRef.once("value").then(evacSnap => {
  const evacData = evacSnap.val() || {};
  const hasEvacData = evacData.Evacuation_Center && evacData.Latitude && evacData.Longitude;

  if (clearButton) {
    clearButton.disabled = !hasEvacData;
    clearButton.style.opacity = hasEvacData ? "1" : "0.5";
    clearButton.style.cursor = hasEvacData ? "pointer" : "not-allowed";
  }
});

}

        function closeModal() {
            document.getElementById("modal-overlay").classList.remove("show");
            document.getElementById("settings-modal").classList.remove("show");
        }

        function confirmDeleteDevice() {
            document.getElementById('settings-modal').classList.remove('show');
            document.getElementById('delete-confirm-modal').classList.add('show');
            window.currentDeviceId = window.currentDeviceId || document.getElementById('edit-location-btn').getAttribute(
                'data-id');
        }

        function performDelete() {
            const deviceId = window.currentDeviceId;
            if (!deviceId) return;

            db.ref(`DEVICES/${deviceId}`).remove().then(() => {
                document.getElementById('delete-confirm-modal').classList.remove('show');
                document.getElementById('modal-overlay').classList.remove('show');
                window.currentDeviceId = null;
            });
        }

        function cancelDelete() {
            document.getElementById('delete-confirm-modal').classList.remove('show');
            document.getElementById('modal-overlay').classList.remove('show');
        }

        function toggleRadiusEdit() {
            document.getElementById('setting-radius-text').style.display = "none";
            document.getElementById('setting-radius-input').style.display = "inline-block";
            document.getElementById('edit-radius-btn').style.display = "none";
            document.getElementById('save-radius-btn').style.display = "inline-block";
        }

        function saveRadius() {
            const deviceId = document.getElementById('save-radius-btn').getAttribute('data-id');
            const newRadius = parseInt(document.getElementById('setting-radius-input').value);
            const ref = db.ref(`DEVICES/${deviceId}/DEVICE_SETTING`);

            if (!isNaN(newRadius)) {
                ref.update({
                    Radius: newRadius
                }).then(() => {
                    document.getElementById('setting-radius-text').textContent = newRadius;
                    document.getElementById('setting-radius-input').style.display = "none";
                    document.getElementById('setting-radius-text').style.display = "inline-block";
                    document.getElementById('edit-radius-btn').style.display = "inline-block";
                    document.getElementById('save-radius-btn').style.display = "none";
                });
            }
        }

        function toggleLocationEdit() {
            document.getElementById('setting-location-text').style.display = "none";
            document.getElementById('setting-location-input').style.display = "inline-block";
            document.getElementById('edit-location-btn').style.display = "none";
            document.getElementById('save-location-btn').style.display = "inline-block";
        }

        function saveLocationName() {
            const deviceId = document.getElementById('save-location-btn').getAttribute('data-id');
            const newLocation = document.getElementById('setting-location-input').value;
            const ref = db.ref(`DEVICES/${deviceId}/DEVICE_SETTING`);

            ref.update({
                Location_Name: newLocation
            }).then(() => {
                document.getElementById('setting-location-text').textContent = newLocation;
                document.getElementById('setting-location-input').style.display = "none";
                document.getElementById('setting-location-text').style.display = "inline-block";
                document.getElementById('edit-location-btn').style.display = "inline-block";
                document.getElementById('save-location-btn').style.display = "none";
            });
        }

        function toggleDeviceNameEdit() {
            document.getElementById('setting-device-name-text').style.display = "none";
            document.getElementById('setting-device-name-input').style.display = "inline-block";
            document.getElementById('edit-device-name-btn').style.display = "none";
            document.getElementById('save-device-name-btn').style.display = "inline-block";
        }

        function saveDeviceName() {
            const deviceId = document.getElementById('save-device-name-btn').getAttribute('data-id');
            const newName = document.getElementById('setting-device-name-input').value;
            const ref = db.ref(`DEVICES/${deviceId}/DEVICE_SETTING`);

            ref.update({
                Device_Name: newName
            }).then(() => {
                document.getElementById('setting-device-name-text').textContent = newName;
                document.getElementById('setting-device-name-input').style.display = "none";
                document.getElementById('setting-device-name-text').style.display = "inline-block";
                document.getElementById('edit-device-name-btn').style.display = "inline-block";
                document.getElementById('save-device-name-btn').style.display = "none";
            });
        }

        //maps

        let map;
        const markerMap = {};
        const circleMap = {};

        let activePathPolyline = null;
        let activeEvacMarker = null;

        let activeDeviceLabel = null;
        let activeEvacLabel = null;

        async function initMap() {
            const {
                Map
            } = await google.maps.importLibrary("maps");
            const {
                AdvancedMarkerElement
            } = await google.maps.importLibrary("marker");

            map = new Map(document.getElementById("map"), {
                center: {
                    lat: 14.2695,
                    lng: 121.0994
                },
                zoom: 14,
                mapId: "YOUR_MAP_ID",
                mapTypeControl: false,
                streetViewControl: false,
                fullscreenControl: false,
            });

            const ref = firebase.database().ref("DEVICES");

            // 🔁 Initial map render (accurate radius from DEVICE_SETTING)
            ref.once("value").then((snapshot) => {
                const devices = snapshot.val();
                if (!devices) return;

                Object.entries(devices).forEach(([deviceId, device]) => {
                    drawCircleAndMarker(deviceId, device);
                });
            });

            // 🔄 Realtime radius-only listener
            ref.once("value").then((snapshot) => {
                const devices = snapshot.val();
                if (!devices) return;

                Object.keys(devices).forEach((deviceId) => {
                    firebase.database()
                        .ref(`DEVICES/${deviceId}/DEVICE_SETTING/Radius`)
                        .on("value", (snap) => {
                            const radius = parseFloat(snap.val());
                            const validRadius = isNaN(radius) || radius <= 0 ? 100 : radius;

                            if (circleMap[deviceId]) {
                                circleMap[deviceId].setOptions({
                                    radius: validRadius
                                });
                            }
                        });
                });
            });

            // 🔁 Realtime device updates for position / color changes
            ref.on("value", (snapshot) => {
                const devices = snapshot.val();
                if (!devices) return;

                Object.entries(devices).forEach(([deviceId, device]) => {
                    drawCircleAndMarker(deviceId, device);
                });
            });
        }

        // Trigger map init after page load
        window.addEventListener("load", initMap);

        function getHighestLevel(sensorData) {
            const priority = ["critical", "very unhealthy", "unhealthy", "moderate", "safe"];
            let detectedLevel = "safe"; // start at lowest

            Object.values(sensorData || {}).forEach(value => {
                const level = value?.Level?.toLowerCase();
                if (priority.includes(level)) {
                    if (priority.indexOf(level) < priority.indexOf(detectedLevel)) {
                        detectedLevel = level;
                    }
                }
            });

            return detectedLevel;
        }

        function drawCircleAndMarker(deviceId, device) {
            const setting = device.DEVICE_SETTING;
            if (!setting) return;

            const lat = parseFloat(setting.Latitude);
            const lng = parseFloat(setting.Longitude);
            const radius = parseFloat(setting.Radius);
            const label = setting.Location_Name || setting.Device_Name || deviceId;
            const color = getCircleColor(getHighestLevel(device.SENSOR_DATA));

            if (isNaN(lat) || isNaN(lng)) return;
            const validRadius = isNaN(radius) || radius <= 0 ? 100 : radius;

            if (circleMap[deviceId]) {
                circleMap[deviceId].setOptions({
                    center: {
                        lat,
                        lng
                    },
                    radius: validRadius,
                    strokeColor: color,
                    fillColor: color,
                });
            } else {
                const circle = new google.maps.Circle({
                    strokeColor: color,
                    strokeOpacity: 0.6,
                    strokeWeight: 2,
                    fillColor: color,
                    fillOpacity: 0.25,
                    map,
                    center: {
                        lat,
                        lng
                    },
                    radius: validRadius,
                });
                circleMap[deviceId] = circle;
            }

            if (markerMap[deviceId]?.marker) {
                markerMap[deviceId].marker.map = null;
            }

            const marker = new google.maps.marker.AdvancedMarkerElement({
                map,
                position: {
                    lat,
                    lng
                },
                title: label,
            });

            markerMap[deviceId] = {
                marker,
                position: {
                    lat,
                    lng
                }
            };
        }

        function getCircleColor(level) {
            switch (level?.toLowerCase().trim()) {
                case "critical":
                    return "#8B0000"; // Dark Red
                case "very unhealthy":
                    return "#FF0000"; // Bright Red
                case "unhealthy":
                    return "#FF4500"; // Orange-Red
                case "moderate":
                    return "#FFFF00"; // Yellow
                case "safe":
                    return "#00FF00"; // Green
                default:
                    return "#cccccc"; // Gray fallback
            }
        }

        function getZoomFromRadius(radiusMeters) {
          if (radiusMeters <= 50) return 19;
          if (radiusMeters <= 100) return 18;
          if (radiusMeters <= 300) return 17;
          if (radiusMeters <= 500) return 16;
          if (radiusMeters <= 1000) return 15;
          return 14;
        }
        
    function focusDeviceAndEvacuation(deviceId, device) {
    const deviceSetting = device.DEVICE_SETTING || {};
    const evacData = device.EVACUATION_DATA || {};
    const mapBounds = new google.maps.LatLngBounds();

    // Device position
    if (deviceSetting.Latitude && deviceSetting.Longitude) {
        const deviceLatLng = new google.maps.LatLng(
            parseFloat(deviceSetting.Latitude),
            parseFloat(deviceSetting.Longitude)
        );
        mapBounds.extend(deviceLatLng);
    }

    // Evacuation zone position
    if (evacData.Latitude && evacData.Longitude) {
        const evacLatLng = new google.maps.LatLng(
            parseFloat(evacData.Latitude),
            parseFloat(evacData.Longitude)
        );
        mapBounds.extend(evacLatLng);

        // 💡 Create marker for evacuation zone
        if (!markerMap[`evac-${deviceId}`]) {
            const evacMarker = new google.maps.Marker({
                map,
                position: evacLatLng,
                title: evacData.Evacuation_Center  || "Evacuation Zone",
                icon: {
                    url: "https://maps.google.com/mapfiles/ms/icons/blue-dot.png", // 🎯 Differentiate from device
                    scaledSize: new google.maps.Size(32, 32)
                }
            });
            markerMap[`evac-${deviceId}`] = { marker: evacMarker };
        } else {
            markerMap[`evac-${deviceId}`].marker.setPosition(evacLatLng);
        }

        // Optional: Draw circle for evac zone
        if (!circleMap[`evac-${deviceId}`]) {
            const evacCircle = new google.maps.Circle({
                map,
                center: evacLatLng,
                radius: 50,
                strokeColor: "#337ab7",
                fillColor: "#cce5ff",
                fillOpacity: 0.25,
                strokeOpacity: 0.7,
                strokeWeight: 2,
            });
            circleMap[`evac-${deviceId}`] = evacCircle;
        } else {
            circleMap[`evac-${deviceId}`].setCenter(evacLatLng);
        }
    }

    // Zoom & focus both
    map.fitBounds(mapBounds);
}

function createInlineLabel(position, text, map) {
  const label = new google.maps.OverlayView();

  label.onAdd = function () {
    const div = document.createElement("div");
    div.style.position = "absolute";
    div.style.background = "white";
    div.style.border = "1px solid #666";
    div.style.borderRadius = "4px";
    div.style.padding = "2px 6px";
    div.style.fontSize = "12px";
    div.style.color = "#333";
    div.style.whiteSpace = "nowrap";
    div.style.transform = "translateX(-50%)";
    div.style.boxShadow = "0 1px 4px rgba(0,0,0,0.3)";
    div.style.zIndex = "999999"; // 🚀 Higher than default marker
    div.innerText = text;

    this.div = div;

    // 👆 Use the floatPane for highest visual stacking
    this.getPanes().floatPane.appendChild(div);
  };

  label.draw = function () {
    const projection = this.getProjection();
    const point = projection.fromLatLngToDivPixel(position);
    if (point && this.div) {
      this.div.style.left = `${point.x}px`;
      this.div.style.top = `${point.y - 35}px`; // Adjust to rise above the marker
    }
  };

  label.onRemove = function () {
    if (this.div && this.div.parentNode) {
      this.div.parentNode.removeChild(this.div);
      this.div = null;
    }
  };

  label.setMap(map);
  return label;
}

function clearMapVisuals() {
  // Clear previous path
  if (activePathPolyline) {
    activePathPolyline.setMap(null);
    activePathPolyline = null;
  }

  // Clear previous evacuation marker
  Object.keys(markerMap)
    .filter(key => key.startsWith("evac-"))
    .forEach(key => {
      markerMap[key].marker.setMap(null);
      delete markerMap[key];
    });

  // Clear previous evacuation circle
  Object.keys(circleMap)
    .filter(key => key.startsWith("evac-"))
    .forEach(key => {
      circleMap[key].setMap(null);
      delete circleMap[key];
    });

  // 🧼 Clear previous labels
  if (activeDeviceLabel) {
    activeDeviceLabel.setMap(null);
    activeDeviceLabel = null;
  }
  if (activeEvacLabel) {
    activeEvacLabel.setMap(null);
    activeEvacLabel = null;
  }
}
        
function fetchAnimatedPath(deviceLatLng, evacLatLng) {
  const directionsService = new google.maps.DirectionsService();

  directionsService.route({
    origin: deviceLatLng,
    destination: evacLatLng,
    travelMode: google.maps.TravelMode.WALKING
  }, (result, status) => {
    if (status === "OK") {
      const path = result.routes[0].overview_path;
      drawAnimatedPolyline(path);
    }
  });
}

let isAnimating = false;

function drawAnimatedPolyline(path) {
  if (isAnimating) return;
  isAnimating = true;

  step = 0;
  activePathPolyline = new google.maps.Polyline({
    path: [],
    geodesic: true,
    strokeColor: "#4A90E2",
    strokeOpacity: 0.6,
    strokeWeight: 6,
    map: map,
  });

  function animateTrail() {
    if (!activePathPolyline || step >= path.length) {
      isAnimating = false;
      return;
    }
    activePathPolyline.setPath([...activePathPolyline.getPath().getArray(), path[step]]);
    step++;
    setTimeout(animateTrail, 120);
  }

  animateTrail();
}

function closeManualEvacModal() {
  const modal = document.getElementById("manual-evac-modal");
  const overlay = document.getElementById("modal-overlay");

  modal.classList.remove("show");
  overlay.classList.remove("show");
}

function confirmManualEvac() {
  const deviceId = window.currentDeviceId;
  if (!deviceId) return;

  const evacName = document.getElementById("manual-evac-name").value.trim();
  const lat = parseFloat(document.getElementById("manual-evac-lat").textContent);
  const lng = parseFloat(document.getElementById("manual-evac-lng").textContent);

  if (!evacName || isNaN(lat) || isNaN(lng)) return;

  const evacRef = db.ref(`DEVICES/${deviceId}/EVACUATION_DATA`);
  evacRef.set({
    Evacuation_Center: evacName,
    Latitude: lat,
    Longitude: lng
  }).then(() => {
    closeManualEvacModal(); // Hide modal after saving
  });
}

function showManualEvacModal(lat = null, lng = null) {
  const settingsModal = document.getElementById("settings-modal");
  if (settingsModal) settingsModal.classList.remove("show");

  const overlay = document.getElementById("modal-overlay");
  const modal = document.getElementById("manual-evac-modal");

  overlay.classList.add("show");
  modal.classList.add("show");

  const mapContainer = document.getElementById("manual-evac-map");
  mapContainer.innerHTML = ""; // 🧼 Clean slate for map

  document.getElementById("evac-search-box").value = ""; // 🔄 Reset search input

  const deviceId = window.currentDeviceId;
  if (!deviceId) return;

  db.ref(`DEVICES/${deviceId}/EVACUATION_DATA`).once("value").then(snapshot => {
    const evacData = snapshot.val();
    initManualEvacMap(lat, lng, evacData); // Pass evac data if exists
  });
}

function initManualEvacMap(lat, lng, evacData = null) {
  const deviceId = window.currentDeviceId;
  const mapContainer = document.getElementById("manual-evac-map");
  const evacNameInput = document.getElementById("manual-evac-name");
  const evacLatDisplay = document.getElementById("manual-evac-lat");
  const evacLngDisplay = document.getElementById("manual-evac-lng");
  const searchBoxInput = document.getElementById("evac-search-box");

  db.ref(`DEVICES/${deviceId}/DEVICE_SETTING`).once("value").then(snapshot => {
    const setting = snapshot.val() || {};
    const Device_Name = setting.Device_Name || "Unnamed Device";
    const Location_Name = setting.Location_Name || "Unknown Location";

    const finalLat = lat ?? parseFloat(setting.Latitude);
    const finalLng = lng ?? parseFloat(setting.Longitude);
    const radius = parseFloat(setting.Radius) || 100;

    const safeLat = isNaN(finalLat) ? 14.5995 : finalLat;
    const safeLng = isNaN(finalLng) ? 120.9842 : finalLng;

    const map = new google.maps.Map(mapContainer, {
      center: { lat: safeLat, lng: safeLng },
      zoom: getZoomFromRadius(radius),
    });

    new google.maps.Circle({
      center: { lat: safeLat, lng: safeLng },
      radius,
      map,
      strokeColor: "#2196f3",
      fillColor: "#bbdefb",
      fillOpacity: 0.2,
    });

    const deviceLatLng = new google.maps.LatLng(safeLat, safeLng);

    new google.maps.Marker({
      position: deviceLatLng,
      map,
      icon: {
        url: "https://maps.google.com/mapfiles/ms/icons/red-dot.png",
        scaledSize: new google.maps.Size(32, 32)
      },
      title: `${Device_Name} - ${Location_Name}`
    });

    createInlineLabel(deviceLatLng, `${Device_Name} - ${Location_Name}`, map);

    let evacMarker = null;

    // 📦 Preload evacData if available
    if (evacData?.Latitude && evacData?.Longitude && evacData?.Evacuation_Center) {
      const evacLatLng = new google.maps.LatLng(
        parseFloat(evacData.Latitude),
        parseFloat(evacData.Longitude)
      );

      evacMarker = new google.maps.Marker({
        position: evacLatLng,
        map,
        icon: {
          url: "https://maps.google.com/mapfiles/ms/icons/blue-dot.png",
          scaledSize: new google.maps.Size(32, 32)
        },
        title: evacData.Evacuation_Center
      });

      createInlineLabel(evacLatLng, evacData.Evacuation_Center, map);

      // 🛣️ Realistic walking route
      const directionsService = new google.maps.DirectionsService();
      directionsService.route({
        origin: deviceLatLng,
        destination: evacLatLng,
        travelMode: google.maps.TravelMode.WALKING
      }, (result, status) => {
        if (status === "OK") {
          const routePath = result.routes[0].overview_path;
          new google.maps.Polyline({
            path: routePath,
            map,
            strokeColor: "#2196f3",
            strokeWeight: 4,
            strokeOpacity: 0.8
          });
        }
      });

      evacNameInput.value = evacData.Evacuation_Center;
      evacLatDisplay.textContent = evacLatLng.lat().toFixed(6);
      evacLngDisplay.textContent = evacLatLng.lng().toFixed(6);
    }

    // 🔵 Manual click — no label
    map.addListener("click", e => {
      if (evacMarker) evacMarker.setMap(null);

      evacMarker = new google.maps.Marker({
        position: e.latLng,
        map,
        icon: {
          url: "https://maps.google.com/mapfiles/ms/icons/blue-dot.png",
          scaledSize: new google.maps.Size(32, 32)
        },
        title: "Selected Location"
      });

      evacLatDisplay.textContent = e.latLng.lat().toFixed(6);
      evacLngDisplay.textContent = e.latLng.lng().toFixed(6);
      evacNameInput.value = "";
    });

    // 🔍 SearchBox — blue marker with floating label
    if (searchBoxInput) {
      const searchBox = new google.maps.places.SearchBox(searchBoxInput);
      map.addListener("bounds_changed", () => {
        searchBox.setBounds(map.getBounds());
      });

      searchBox.addListener("places_changed", () => {
        const places = searchBox.getPlaces();
        if (!places || places.length === 0) return;

        const place = places[0];
        const location = place.geometry?.location;
        if (!location) return;

        if (evacMarker) evacMarker.setMap(null);

        const locationTitle = place.name || "Evacuation Location";

        evacMarker = new google.maps.Marker({
          position: location,
          map,
          icon: {
            url: "https://maps.google.com/mapfiles/ms/icons/blue-dot.png",
            scaledSize: new google.maps.Size(32, 32)
          },
          title: locationTitle
        });

        createInlineLabel(location, locationTitle, map);

        map.panTo(location);
        map.setZoom(17);

        evacLatDisplay.textContent = location.lat().toFixed(6);
        evacLngDisplay.textContent = location.lng().toFixed(6);
        evacNameInput.value = locationTitle;
      });
    }

    // 🧼 Reset inputs if no evacData
    if (!evacData) {
      evacNameInput.value = "";
      evacLatDisplay.textContent = "--";
      evacLngDisplay.textContent = "--";
    }
  });
}

function clearEvacuationData() {
  const deviceId = window.currentDeviceId;
  if (!deviceId) return;

  // Show custom confirmation modal
  document.getElementById("evac-clear-confirm").classList.add("show");
}

function closeEvacConfirm() {
  document.getElementById("evac-clear-confirm").classList.remove("show");
}

function executeEvacClear() {
  const deviceId = window.currentDeviceId;
  if (!deviceId) return;

  db.ref(`DEVICES/${deviceId}/EVACUATION_DATA`).remove()
    .then(() => {
      // ❌ Close all modals
      document.getElementById("evac-clear-confirm")?.classList.remove("show");
      document.getElementById("manual-evac-modal")?.classList.remove("show");
      document.getElementById("settings-modal")?.classList.remove("show");
      document.getElementById("modal-overlay")?.classList.remove("show");

      // 🧹 Reset input fields
      document.getElementById("evac-search-box").value = "";
      document.getElementById("manual-evac-name").value = "";
      document.getElementById("manual-evac-lat").textContent = "--";
      document.getElementById("manual-evac-lng").textContent = "--";

      // 🔄 Refresh UI text
      const evacDisplay = document.getElementById("evacuation-display");
      const evacButton = document.getElementById("evacuation-button");
      if (evacDisplay) evacDisplay.textContent = "—";
      if (evacButton) evacButton.textContent = "Add Evacuation Center";

      // 🧽 Fully clear map visuals
      clearMapVisuals();

      // ✅ Re-show device marker and circle only
      db.ref(`DEVICES/${deviceId}`).once("value").then(snapshot => {
        const device = snapshot.val();
        if (device) {
          drawCircleAndMarker(deviceId, device);
          const radiusRaw = device.DEVICE_SETTING?.Radius;
          const radius = parseFloat(radiusRaw);
          const validRadius = isNaN(radius) || radius <= 0 ? 100 : radius;

          const deviceLat = parseFloat(device.DEVICE_SETTING?.Latitude);
          const deviceLng = parseFloat(device.DEVICE_SETTING?.Longitude);

          if (!isNaN(deviceLat) && !isNaN(deviceLng)) {
            const deviceLatLng = new google.maps.LatLng(deviceLat, deviceLng);
            map.panTo(deviceLatLng);
            map.setZoom(getZoomFromRadius(validRadius));
          }c
        }
      });
    })
    .catch(error => {
      console.error("Error clearing evacuation data:", error);
      alert("Something went wrong. Please try again.");
    });
}


function showEvacInfoBox(message) {
  let infoBox = document.getElementById("evac-info-box");
  if (!infoBox) {
    infoBox = document.createElement("div");
    infoBox.id = "evac-info-box";
    infoBox.style.position = "fixed";
    infoBox.style.top = "16px";
    infoBox.style.left = "50%";
    infoBox.style.transform = "translateX(-50%)";
    infoBox.style.backgroundColor = "#fff3cd";
    infoBox.style.color = "#856404";
    infoBox.style.padding = "12px 20px";
    infoBox.style.border = "1px solid #ffeeba";
    infoBox.style.borderRadius = "6px";
    infoBox.style.boxShadow = "0 2px 6px rgba(0,0,0,0.1)";
    infoBox.style.zIndex = "9999";
    infoBox.style.fontSize = "14px";
    infoBox.textContent = message;
    document.body.appendChild(infoBox);

    setTimeout(() => {
      infoBox.remove();
    }, 5000);
  }
}



    </script>

<script async
  src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&loading=async&v=beta&libraries=places,marker,geometry">
</script>




    <div id="modal-overlay" class="modal-overlay" onclick="closeModal()"></div>

    <div id="settings-modal" class="modal">
        <h3 style="margin-top: 0; margin-bottom: 20px;">Device Settings</h3>
        <div id="settings-content">
            <p>
                <strong>Device Name:</strong>
                <span id="setting-device-name-text"></span>
                <input type="text" id="setting-device-name-input" style="display: none;" />
                <button id="edit-device-name-btn" onclick="toggleDeviceNameEdit()">Change</button>
                <button id="save-device-name-btn" onclick="saveDeviceName()" style="display: none;">Save</button>
            </p>
            <p>
                <strong>Location:</strong>
                <span id="setting-location-text"></span>
                <input type="text" id="setting-location-input" style="display: none;" />
                <button id="edit-location-btn" onclick="toggleLocationEdit()">Change</button>
                <button id="save-location-btn" onclick="saveLocationName()" style="display: none;">Save</button>
            </p>
            <p>
                <strong>Latitude:</strong>
                <span id="setting-lat"></span>
            </p>
            <p>
                <strong>Longitude:</strong>
                <span id="setting-lng"></span>
            </p>
            <p>
                <strong>Radius (m):</strong>
                <span id="setting-radius-text"></span>
                <input type="number" id="setting-radius-input" style="display: none;" min="0" />
                <button id="edit-radius-btn" onclick="toggleRadiusEdit()">Change</button>
                <button id="save-radius-btn" onclick="saveRadius()" style="display: none;">Save</button>
            </p>
            <p>
  <strong>Evacuation Center:</strong>
  <span id="evacuation-display">—</span>
  <button id="evacuation-button" onclick="showManualEvacModal()">Add Evacuation Center</button>
        </p>
        <button onclick="clearEvacuationData()" class="btn clear">Clear</button>
            <div class="modal-actions">
                <button onclick="confirmDeleteDevice()" class="delete-btn">Delete Device</button>
                <button onclick="closeModal()" class="close-btn">Close</button>
            </div>
        </div>
    </div>

    <div id="delete-confirm-modal" class="modal" onclick="event.stopPropagation()">
        <h3>Confirm Device Deletion</h3>
        <p style="margin-bottom: 20px;">
            This action will permanently delete the selected device from the dashboard. All geofence configurations and
            associated air sensor data will be removed.
            <br><br>
            You will stop receiving alerts and air sensor readings from this device. To bring the device back, simply
            power it on and reconnect it to the network.
        </p>
        <div style="display: flex; justify-content: flex-end; gap: 10px;">
            <button onclick="cancelDelete()" class="close-btn">Cancel</button>
            <button onclick="performDelete()" class="delete-btn">Confirm</button>
        </div>
    </div>

<div id="manual-evac-modal" class="modal">
  <h3>Assign Evacuation Center</h3>
  <input id="evac-search-box" type="text" placeholder="Search for a place..." style="width: 100%; padding: 8px; margin-bottom: 10px;" />
  <div id="manual-evac-map" style="width: 100%; height: 380px; margin: 10px 0; border: 1px solid #ccc;"></div>

   <p>Evacuation Name:</p> <input type="text" id="manual-evac-name" placeholder="Evacuation name..." />
  <p>Selected: 
    <span id="manual-evac-lat">--</span>, 
    <span id="manual-evac-lng">--</span>
  </p>

  <button class="button-basic" onclick="confirmManualEvac()">Save</button>
  <button class="button-basic save" onclick="closeManualEvacModal()">Cancel</button>
</div>

<div id="evac-clear-confirm" class="modal">
  <div class="modal-content">
    <h3>Confirm Clear</h3>
    <p>Are you sure you want to remove this device's evacuation data?</p>
    <div class="modal-actions">
      <button onclick="executeEvacClear()" class="btn danger">Yes, Clear</button>
      <button onclick="closeEvacConfirm()" class="btn neutral">Cancel</button>
    </div>
  </div>

    <script>
        document.getElementById('modal-overlay').addEventListener('click', () => {
            // Close all modals when clicking the backdrop
            document.querySelectorAll('.modal').forEach(modal => modal.classList.remove('show'));
            document.getElementById('modal-overlay').classList.remove('show');
        });
    </script>


</x-app-layout>
