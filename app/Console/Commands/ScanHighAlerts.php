<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ScanHighAlerts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:scan-high-alerts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
public function handle()
{
    $firebaseUrl = 'https://airsentinel-6d53a-default-rtdb.asia-southeast1.firebasedatabase.app/DEVICES.json';
    $devices = Http::get($firebaseUrl)->json();

    $alertLevels = ["Unhealthy", "Very Unhealthy", "Critical"];

    foreach ($devices ?? [] as $deviceId => $device) {
        $UID = $device['DEVICE_SETTING']['Device_UID'] ?? $deviceId;
        $safeUID = preg_replace('/[.#$[\]\/]/', '_', $UID);
        $sensors = $device['SENSOR_DATA'] ?? [];

        foreach ($sensors as $sensorType => $sensorData) {
            if (!is_array($sensorData)) continue;

            $level = $sensorData['Level'] ?? null;
            if (!in_array($level, $alertLevels)) continue;

            $value = $sensorData['Value'] ?? 'N/A';
            $label = $sensorData['Label'] ?? $sensorType;
            $timestamp = now()->toIso8601String();
            $safeTimestamp = preg_replace('/[.#$[\]\/:]/', '_', $timestamp);
            $safeSensor = preg_replace('/[.#$[\]\/]/', '_', $sensorType);

            $logData = [
                'Sensor_Label' => $label,
                'Sensor_Value' => $value,
                'Sensor_Level' => $level,
                'Timestamp' => $timestamp
            ];

            // 👇 Write to HIGH_ALERT_LOGS using raw HTTP PUT
            $logUrl = "https://airsentinel-6d53a-default-rtdb.asia-southeast1.firebasedatabase.app/HIGH_ALERT_LOGS/{$safeUID}/{$safeSensor}/{$safeTimestamp}.json";
            Http::put($logUrl, $logData);
        }
    }

    \Log::info('High alert scan (via HTTP) completed at ' . now());
}
}
