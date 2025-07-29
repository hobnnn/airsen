<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Air Sentinel</title>

        <link rel="icon" href="{{ asset('icons/Logo.png') }}" type="image/png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
.container {
    max-width: 1440px;
    margin: 0 auto;
    padding: 24px;
    box-sizing: border-box;
}


/* Header Title */
.dashboard-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #333;
    padding: 16px;
}

/* Top Layout: Map + Devices */
.top-section {
    display: flex;
    gap: 24px;
    margin-bottom: 32px;
}

/* Map Placeholder */
.map-area {
    flex: 2;
    height: 400px;
    background: #e2e8f0;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #555;
    font-size: 18px;
}

/* Device List Styling */
.device-list {
    flex: 1;
    background: #fff;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.device-list h3 {
    margin-bottom: 16px;
    font-size: 16px;
    font-weight: 600;
}

.device-list ul {
    list-style: none;
    padding: 0;
}

.device-list li {
    background: #f1f5f9;
    padding: 10px;
    margin-bottom: 8px;
    border-radius: 8px;
}

/* Device Card with Nested Sensor Grid */
.device-card {
    width: 250px;
    height: 250px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    padding: 16px;
    margin: 16px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.device-card h4 {
    font-size: 16px;
    font-weight: 600;
    text-align: center;
    margin-bottom: 12px;
    color: #333;
}

/* Inner Grid for Sensor Boxes */
.sensor-box-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;
    flex-grow: 1;
}

/* Individual Sensor Box */
.sensor-box {
    background: #f3f4f6;
    border-radius: 8px;
    padding: 10px;
    text-align: center;
    font-size: 13px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.04);
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 4px;
}

.sensor-box strong {
    font-size: 14px;
    color: #111;
}

.sensor-box small {
    font-size: 12px;
    color: #6b7280;
}

.low-level {
    background-color: #d1fae5; /* soft green */
    border: 1px solid #10b981;
}

.medium-level {
    background-color: #fef9c3; /* soft yellow */
    border: 1px solid #f59e0b;
}

.high-level {
    background-color: #fee2e2; /* soft red */
    border: 1px solid #ef4444;
}

.neutral-level {
    background-color: #f3f4f6;
    border: 1px solid #d1d5db;
}
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>

    <script>
        fetch('/hello-log')
  .then(res => res.json())
  .then(data => {
    data.messages.forEach((msg, i) => console.log(`[${i}] ${msg}`));
  });
    </script>
</html>
