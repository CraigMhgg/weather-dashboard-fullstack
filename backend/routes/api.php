<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/weather', function (Request $request) {
    \Log::info('Weather API called', ['city' => $request->input('city')]);

    $request->validate([
        'city' => 'required|string|max:100'
    ]);

    $city = $request->input('city');
    $apiKey = env('WEATHERSTACK_API_KEY');

    \Log::info('API Key loaded', ['key' => $apiKey ? 'EXISTS' : 'MISSING']);

    try {
        $response = Http::get('http://api.weatherstack.com/current', [
            'access_key' => $apiKey,
            'query' => $city
        ]);

        \Log::info('Weatherstack response', ['status' => $response->status()]);

        if (!$response->successful()) {
            return response()->json([
                'error' => 'Failed to fetch weather data'
            ], 500);
        }

        $data = $response->json();

        \Log::info('Weatherstack data', ['has_error' => isset($data['error']), 'data' => $data]);

        if (isset($data['error'])) {
            return response()->json([
                'error' => $data['error']['info'] ?? 'City not found'
            ], 404);
        }

        // Map weather code to emoji
        $weatherCode = $data['current']['weather_code'];
        $iconMap = [
            113 => '☀️',
            116 => '⛅',
            119 => '☁️',
            122 => '☁️',
            143 => '🌫️',
            176 => '🌦️',
            179 => '🌨️',
            200 => '⛈️',
            296 => '🌧️',
            302 => '🌧️',
            308 => '🌧️',
            326 => '❄️',
            332 => '❄️',
            338 => '❄️',
            353 => '🌦️',
            356 => '🌧️',
            386 => '⛈️',
            389 => '⛈️'
        ];

        // Map Weatherstack response to our format
        $weatherData = [
            'city' => $data['location']['name'],
            'temperature' => $data['current']['temperature'],
            'condition' => $data['current']['weather_descriptions'][0] ?? 'Unknown',
            'humidity' => $data['current']['humidity'],
            'windSpeed' => $data['current']['wind_speed'],
            'feelsLike' => $data['current']['feelslike'],
            'icon' => $iconMap[$weatherCode] ?? '🌤️'
        ];

        return response()->json($weatherData);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'An error occurred while fetching weather data: ' . $e->getMessage()
        ], 500);
    }
});
