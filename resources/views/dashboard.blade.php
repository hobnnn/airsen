<x-app-layout>
    <x-slot name="header">
        <h2 class="dashboard-title">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="container">
        <!-- Top Section: Map + Devices -->
        <div class="top-section">
            <div class="map-area">Map Placeholder</div>

            <div class="device-list">
                <h3>Devices</h3>
                <ul id="device-list"></ul>
            </div>
        </div>

        <!-- Sensor Cards Area: Populated from Firebase -->
        <div id="device-container" style="display: flex; flex-wrap: wrap;"></div>
    </div>

    <!-- Firebase Compat SDK -->
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

        function getLevelClass(level) {
            switch (level?.toLowerCase()) {
                case 'low': return 'low-level';
                case 'medium': return 'medium-level';
                case 'high': return 'high-level';
                default: return 'neutral-level';
            }
        }

        function buildDeviceCard(deviceId, device) {
            const sensorData = device.Sensor_Data;
            const container = document.getElementById('device-container');

            // Create or update sensor card
            let card = document.getElementById(`device-${deviceId}`);
            if (!card) {
                card = document.createElement('div');
                card.id = `device-${deviceId}`;
                card.className = 'device-card';
                container.appendChild(card);
            }

            card.innerHTML = `
                <h4>${device.Device_Setting.Device_Name}</h4>
                <div class="sensor-box-grid">
                    ${['CO2', 'CO-LPG', 'General', 'SO2-H2S'].map(key => `
                        <div id="box-${key.toLowerCase()}-${deviceId}" class="sensor-box ${getLevelClass(sensorData[`${key}-LVL`])}">
                            <strong>${key}</strong>
                            <div id="${key.toLowerCase()}-${deviceId}">${sensorData[key]}</div>
                            <small id="${key.toLowerCase()}lvl-${deviceId}">(${sensorData[`${key}-LVL`]})</small>
                        </div>
                    `).join('')}
                </div>
            `;
        }

        function updateDeviceList(devices) {
            const deviceList = document.getElementById('device-list');
            deviceList.innerHTML = ''; // Clear current list

            Object.entries(devices).forEach(([deviceId, device]) => {
                const li = document.createElement('li');
                li.textContent = device.Device_Setting.Device_Name;
                li.id = `device-name-${deviceId}`;
                deviceList.appendChild(li);
            });
        }

        db.ref("DEVICES").on("value", (snapshot) => {
            const devices = snapshot.val();
            if (!devices) return;

            updateDeviceList(devices);
            Object.entries(devices).forEach(([deviceId, device]) => {
                buildDeviceCard(deviceId, device);
            });
        });
    </script>
</x-app-layout>