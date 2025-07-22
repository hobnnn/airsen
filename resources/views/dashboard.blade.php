<x-app-layout>
    <x-slot name="header">
        <h2 class="dashboard-title">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="container">
        <!-- Top Section: Map + Devices -->
        <div class="top-section">
            <!-- Map Area -->
            <div class="map-area">
                Map Placeholder
            </div>

            <!-- Devices Area -->
            <div class="device-list">
                <h3>Devices</h3>
                @if (count($devices) > 0)
                    <ul>
                        @foreach ($devices as $deviceId => $device)
                            <li>{{ $device['Device_Setting']['Device_Name'] }}</li>
                        @endforeach
                    </ul>
                @else
                    <p>No devices found.</p>
                @endif
            </div>
        </div>

        @php
            function levelColor($level) {
                return match(strtolower($level)) {
                    'low' => 'low-level',
                    'medium' => 'medium-level',
                    'high' => 'high-level',
                    default => 'neutral-level'
                };
            }
        @endphp

        <!-- Sensor Cards Area -->
        @if (count($devices) > 0)
            <div style="display: flex; flex-wrap: wrap;">
                @foreach ($devices as $deviceId => $device)
                    <div class="device-card">
                        <h4>{{ $device['Device_Setting']['Device_Name'] }}</h4>
                        <div class="sensor-box-grid">
                            <div class="sensor-box {{ levelColor($device['Sensor_Data']['CO2-LVL']) }}">
                                <strong>CO2</strong>
                                <div>{{ $device['Sensor_Data']['CO2'] }}</div>
                                <small>({{ $device['Sensor_Data']['CO2-LVL'] }})</small>
                            </div>
                            <div class="sensor-box {{ levelColor($device['Sensor_Data']['CO-LPG-LVL']) }}">
                                <strong>CO-LPG</strong>
                                <div>{{ $device['Sensor_Data']['CO-LPG'] }}</div>
                                <small>({{ $device['Sensor_Data']['CO-LPG-LVL'] }})</small>
                            </div>
                            <div class="sensor-box {{ levelColor($device['Sensor_Data']['General-LVL']) }}">
                                <strong>General</strong>
                                <div>{{ $device['Sensor_Data']['General'] }}</div>
                                <small>({{ $device['Sensor_Data']['General-LVL'] }})</small>
                            </div>
                            <div class="sensor-box {{ levelColor($device['Sensor_Data']['SO2-H2S-LVL']) }}">
                                <strong>SO2-H2S</strong>
                                <div>{{ $device['Sensor_Data']['SO2-H2S'] }}</div>
                                <small>({{ $device['Sensor_Data']['SO2-H2S-LVL'] }})</small>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p>No devices found.</p>
        @endif
    </div>
</x-app-layout>