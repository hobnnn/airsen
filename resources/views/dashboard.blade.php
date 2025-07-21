<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1>All AIRSENTI Devices</h1>

@foreach ($devices as $deviceId => $device)
    <div style="border: 1px solid #ccc; padding: 10px; margin-bottom: 20px;">
        <h2>{{ $device['Device_Setting']['Device_Name'] }} ({{ $deviceId }})</h2>
        <p>Location: {{ $device['Device_Setting']['Location_Name'] }}</p>
        <p>Latitude: {{ $device['Device_Setting']['Latitude'] }}</p>
        <p>Longitude: {{ $device['Device_Setting']['Longitude'] }}</p>
        <p>Radius: {{ $device['Device_Setting']['Radius'] }}</p>

        <h4>Sensor Data:</h4>
        <ul>
            @foreach ($device['Sensor_Data'] as $key => $value)
                <li>{{ $key }}: {{ $value }}</li>
            @endforeach
        </ul>
    </div>
@endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
