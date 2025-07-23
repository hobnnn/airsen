<x-app-layout>
    
    <style>
    body {
        position: relative;
        overflow-x: hidden;
        font-family: 'Segoe UI', sans-serif;
    }

    .modal-overlay {
        position: fixed;
        top: 0; left: 0; bottom: 0; right: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.25);
        backdrop-filter: blur(2px);
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s ease;
        z-index: 998;
    }

    .modal {
        position: fixed;
        top: 50%; left: 50%;
        transform: translate(-50%, -50%) scale(0.95);
        background-color: #fff;
        padding: 24px;
        width: 360px;
        max-width: 90vw;
        border-radius: 12px;
        box-shadow: 0 6px 30px rgba(0,0,0,0.35);
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
</style>


    <x-slot name="header">
        <h2 class="dashboard-title">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="container">
        <div class="top-section">
            <div class="map-area">Map Placeholder</div>
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

    let card = document.getElementById(`device-${deviceId}`);
    if (!card) {
        card = document.createElement('div');
        card.id = `device-${deviceId}`;
        card.className = 'device-card';
        container.appendChild(card);
    }

    const isLocked = device.Device_Setting.Lock_Device === true;
    const alert = Object.values(sensorData).some(val => val === "High");

    // Apply alert class for light red border
    card.className = `device-card${alert ? ' alert' : ''}`;

    const mode = device.Device_Setting.Display_Name_Mode || "device";
    const displayName = mode === "location"
    ? device.Device_Setting.Location_Name
    : device.Device_Setting.Device_Name;

    card.innerHTML = `
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h4 style="margin: 0;">${displayName}</h4>
            <div style="display: flex; align-items: center; gap: 6px;">
                ${isLocked ? '<img src="/icons/lock.png" alt="Locked" style="width: 16px;" />' : ''}
                <button onclick="openSettings('${deviceId}')" title="Device Settings" style="background: none; border: none; cursor: pointer;">
                    <img src="/icons/gear.png" alt="Settings" style="width: 18px; height: 18px;" />
                </button>
            </div>
        </div>
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
    deviceList.innerHTML = '';

    const sortedDevices = Object.entries(devices).sort(([, a], [, b]) => {
    const aLock = a.Device_Setting.Lock_Device === true;
    const bLock = b.Device_Setting.Lock_Device === true;
    const aAlert = Object.values(a.Sensor_Data).some(val => val === "High");
    const bAlert = Object.values(b.Sensor_Data).some(val => val === "High");

    const score = (locked, alert) => {
        if (locked && alert) return 3;   // 🔐 Locked + Alert
        if (locked) return 2;            // 🔐 Locked Only
        if (alert) return 1;             // ⚠️ Alert Only
        return 0;                        // 🟢 Normal
    };

    return score(bLock, bAlert) - score(aLock, aAlert);
});

    sortedDevices.forEach(([deviceId, device]) => {
        const { Device_Name, Location_Name, Display_Name_Mode, Lock_Device } = device.Device_Setting;
        const alert = Object.values(device.Sensor_Data).some(val => val === "High");

        // Choose display name based on mode
        const displayName = Display_Name_Mode === "location" ? Location_Name : Device_Name;

        const li = document.createElement('li');
        li.id = `device-name-${deviceId}`;
        li.className = alert ? "alert" : "";
        li.style.display = "flex";
        li.style.justifyContent = "space-between";
        li.style.alignItems = "center";

        li.innerHTML = `
            <span>${displayName}</span>
            ${Lock_Device ? '<img src="/icons/lock.png" alt="Locked" style="width: 16px;" />' : ''}
        `;

        deviceList.appendChild(li);
    });
}

        db.ref("DEVICES").on("value", (snapshot) => {
    const devices = snapshot.val();
    const deviceList = document.getElementById('device-list');
    const container = document.getElementById('device-container');

    deviceList.innerHTML = '';
    container.innerHTML = '';

    if (!devices) {
        deviceList.innerHTML = '<li>No devices found.</li>';
        container.innerHTML = '<p style="padding: 20px; color: #888;">No devices to display.</p>';
        return;
    }

    updateDeviceList(devices);

    const sortedDevices = Object.entries(devices).sort(([, a], [, b]) => {
    const aLock = a.Device_Setting.Lock_Device === true;
    const bLock = b.Device_Setting.Lock_Device === true;

    const aAlert = Object.values(a.Sensor_Data).some(val => val === "High");
    const bAlert = Object.values(b.Sensor_Data).some(val => val === "High");

    const getPriority = (lock, alert) => {
        if (lock && alert) return 3;
        if (lock) return 2;
        if (alert) return 1;
        return 0;
    };

    return getPriority(bLock, bAlert) - getPriority(aLock, aAlert);
});

    sortedDevices.forEach(([deviceId, device]) => {
        buildDeviceCard(deviceId, device);
    });
});

        function openSettings(deviceId) {
            const dbRef = db.ref(`DEVICES/${deviceId}/Device_Setting`);
            dbRef.once("value").then(snapshot => {
        const settings = snapshot.val();
        if (!settings) return;

        document.getElementById('setting-name').textContent = settings.Device_Name || '';

        document.getElementById('setting-location-text').textContent = settings.Location_Name || '';
        document.getElementById('setting-location-input').value = settings.Location_Name || '';
        document.getElementById('edit-location-btn').setAttribute('data-id', deviceId);
        document.getElementById('save-location-btn').setAttribute('data-id', deviceId);

        document.getElementById('setting-lat').textContent = settings.Latitude || '';
        document.getElementById('setting-lng').textContent = settings.Longitude || '';
        document.getElementById('setting-radius-text').textContent = settings.Radius || '';
        document.getElementById('setting-radius-input').value = settings.Radius || '';
        document.getElementById('edit-radius-btn').setAttribute('data-id', deviceId);
        document.getElementById('save-radius-btn').setAttribute('data-id', deviceId);
        document.getElementById('lock-status').textContent = settings.Lock_Device ? "On" : "Off";
        document.getElementById('lock-toggle').setAttribute('data-id', deviceId);

        document.getElementById('modal-overlay').classList.add('show');
        document.getElementById('settings-modal').classList.add('show');

        document.getElementById('name-status').textContent = 
        settings.Display_Name_Mode === "location" ? "Location" : "Device";

        document.getElementById('name-toggle').setAttribute('data-id', deviceId);

        });
    }

function confirmDeleteDevice() {
    document.getElementById('settings-modal').classList.remove('show');
    document.getElementById('delete-confirm-modal').classList.add('show');
    window.currentDeviceId = window.currentDeviceId || document.getElementById('edit-location-btn').getAttribute('data-id');
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
    const ref = db.ref(`DEVICES/${deviceId}/Device_Setting`);

    if (!isNaN(newRadius)) {
        ref.update({ Radius: newRadius }).then(() => {
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
    const ref = db.ref(`DEVICES/${deviceId}/Device_Setting`);

    ref.update({ Location_Name: newLocation }).then(() => {
        document.getElementById('setting-location-text').textContent = newLocation;
        document.getElementById('setting-location-input').style.display = "none";
        document.getElementById('setting-location-text').style.display = "inline-block";
        document.getElementById('edit-location-btn').style.display = "inline-block";
        document.getElementById('save-location-btn').style.display = "none";
    });
}

function toggleNameMode() {
    const deviceId = document.getElementById('name-toggle').getAttribute('data-id');
    const ref = db.ref(`DEVICES/${deviceId}/Device_Setting`);

    ref.once("value").then(snapshot => {
        const current = snapshot.val().Display_Name_Mode || "device";
        const newMode = current === "device" ? "location" : "device";

        ref.update({ Display_Name_Mode: newMode });
        document.getElementById('name-status').textContent = 
            newMode === "location" ? "Location" : "Device";
    });
}

function toggleLock() {
    const deviceId = document.getElementById('lock-toggle').getAttribute('data-id');
    const ref = db.ref(`DEVICES/${deviceId}/Device_Setting`);

    ref.once("value").then(snapshot => {
        const current = snapshot.val().Lock_Device || false;
        ref.update({ Lock_Device: !current });

        document.getElementById('lock-status').textContent = !current ? "On" : "Off";
    });
}

        function closeModal() {
            document.getElementById('modal-overlay').classList.remove('show');
            document.getElementById('settings-modal').classList.remove('show');
        }

        
    </script>

    
    <div id="modal-overlay" class="modal-overlay" onclick="closeModal()"></div>

    <div id="settings-modal" class="modal">
    <h3 style="margin-top: 0; margin-bottom: 20px;">Device Settings</h3>
    <div id="settings-content">
        <p>
            <strong>Name:</strong> 
            <span id="setting-name"></span>
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
        <div class="toggle-section">
            <p>
                <label style="display: flex; align-items: center; gap: 6px;">
                    <strong>Lock Device:</strong>
                    <img src="/icons/question.png" alt="Info" title="This toggle prioritizes how devices are sorted: Locked + High Alert > Locked > High Alert > Everything else." 
                        style="width: 16px; cursor: help;" />
                    
                </label>
                <button onclick="toggleLock()" id="lock-toggle">Toggle</button>
                <span id="lock-status" style="font-weight: bold;"></span>
            </p>
            <p>
                <label style="display: flex; align-items: center; gap: 6px;">
                    <strong>Name Display:</strong>
                    <img src="/icons/question.png" alt="Info" title="This toggle controls whether you see the device ID or its location name in the dashboard." 
                        style="width: 16px; cursor: help;" />
                    
                </label>
                <button onclick="toggleNameMode()" id="name-toggle">Toggle</button>
                <span id="name-status" style="font-weight: bold;"></span>
            </p>
        </div>
        
        <div class="modal-actions">
    <button onclick="confirmDeleteDevice()" class="delete-btn">Delete Device</button>
    <button onclick="closeModal()" class="close-btn">Close</button>
        </div>


    </div>
</div>

<div id="delete-confirm-modal" class="modal" onclick="event.stopPropagation()">
    <h3>Confirm Device Deletion</h3>
    <p style="margin-bottom: 20px;">
    This action will permanently delete the selected device from the dashboard. All geofence configurations and associated air sensor data will be removed.  
    <br><br>
    You will stop receiving alerts and air sensor readings from this device. To bring the device back, simply power it on and reconnect it to the network.
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
