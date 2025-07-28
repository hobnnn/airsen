<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class AssignNearestSchoolDetails extends Command
{
    protected $signature = 'evacuation:schooldetails';
    protected $description = 'Assign nearest school name and coordinates based on device location';

    public function handle()
    {
        $firebaseUrl = 'https://airsentinel-6d53a-default-rtdb.asia-southeast1.firebasedatabase.app/DEVICES.json';
        $apiKey = env('GOOGLE_MAPS_API_KEY');
        $devices = Http::get($firebaseUrl)->json();

        foreach ($devices as $deviceId => $device) {
            $setting = $device['DEVICE_SETTING'] ?? [];

            if (!isset($setting['Latitude'], $setting['Longitude'])) continue;

            $lat = $setting['Latitude'];
            $lng = $setting['Longitude'];

            $apiUrl = "https://maps.googleapis.com/maps/api/place/nearbysearch/json"
                    . "?location={$lat},{$lng}&rankby=distance&type=school&key={$apiKey}";

            $response = Http::get($apiUrl)->json();
            $nearest = $response['results'][0] ?? null;

            if ($nearest && isset($nearest['geometry']['location'], $nearest['name'])) {
                $schoolData = [
                    'SchoolName' => $nearest['name'],
                    'Latitude' => $nearest['geometry']['location']['lat'],
                    'Longitude' => $nearest['geometry']['location']['lng'],
                ];

                $evacUrl = "https://airsentinel-6d53a-default-rtdb.asia-southeast1.firebasedatabase.app/DEVICES/{$deviceId}/EVACUATION_DATA.json";
                Http::put($evacUrl, $schoolData);
            }
        }

        $this->info('✅ School name and location assignment done.');
    }
}