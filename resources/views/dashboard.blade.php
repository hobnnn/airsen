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
            width: 360px;
            max-width: 90vw;
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
            const ref = db.ref(`DEVICES/${deviceId}/DEVICE_SETTING`);
            ref.once("value").then((snapshot) => {
                const setting = snapshot.val();
                if (!setting) return;

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


                document.getElementById("modal-overlay").classList.add("show");
                document.getElementById("settings-modal").classList.add("show");
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

    </script>

    <script async
        src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&loading=async&v=beta&libraries=marker,geometry">
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

    <script>
        document.getElementById('modal-overlay').addEventListener('click', () => {
            // Close all modals when clicking the backdrop
            document.querySelectorAll('.modal').forEach(modal => modal.classList.remove('show'));
            document.getElementById('modal-overlay').classList.remove('show');
        });
    </script>


</x-app-layout>
