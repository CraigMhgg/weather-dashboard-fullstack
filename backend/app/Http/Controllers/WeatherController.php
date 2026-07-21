<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WeatherController extends Controller
{
    public function getWeather(Request $request)
    {
        $request->validate([
            'city' => 'required|string|max:100'
        ]);

        $city = $request->input('city');
        $apiKey = env('WEATHERSTACK_API_KEY');

        try {
            $response = Http::get('http://api.weatherstack.com/current', [
                'access_key' => $apiKey,
                'query' => $city
            ]);

            if (!$response->successful()) {
                return response()->json([
                    'error' => 'Failed to fetch weather data'
                ], 500);
            }

            $data = $response->json();

            if (isset($data['error'])) {
                return response()->json([
                    'error' => $data['error']['info'] ?? 'City not found'
                ], 404);
            }

            // Map Weatherstack response to our format
            $weatherData = [
                'city' => $data['location']['name'],
                'temperature' => $data['current']['temperature'],
                'condition' => $data['current']['weather_descriptions'][0] ?? 'Unknown',
                'humidity' => $data['current']['humidity'],
                'windSpeed' => $data['current']['wind_speed'],
                'feelsLike' => $data['current']['feelslike'],
                'icon' => $this->getWeatherIcon($data['current']['weather_code'])
            ];

            return response()->json($weatherData);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while fetching weather data'
            ], 500);
        }
    }

    private function getWeatherIcon($weatherCode)
    {
        // Map Weatherstack weather codes to emoji icons
        $iconMap = [
            113 => '☀️',  // Sunny
            116 => '⛅',  // Partly cloudy
            119 => '☁️',  // Cloudy
            122 => '☁️',  // Overcast
            143 => '🌫️',  // Mist
            176 => '🌦️',  // Patchy rain possible
            179 => '🌨️',  // Patchy snow possible
            182 => '🌧️',  // Patchy sleet possible
            185 => '🌧️',  // Patchy freezing drizzle possible
            200 => '⛈️',  // Thundery outbreaks possible
            227 => '🌨️',  // Blowing snow
            230 => '❄️',  // Blizzard
            248 => '🌫️',  // Fog
            260 => '🌫️',  // Freezing fog
            263 => '🌦️',  // Patchy light drizzle
            266 => '🌧️',  // Light drizzle
            281 => '🌧️',  // Freezing drizzle
            284 => '🌧️',  // Heavy freezing drizzle
            293 => '🌧️',  // Patchy light rain
            296 => '🌧️',  // Light rain
            299 => '🌧️',  // Moderate rain at times
            302 => '🌧️',  // Moderate rain
            305 => '🌧️',  // Heavy rain at times
            308 => '🌧️',  // Heavy rain
            311 => '🌧️',  // Light freezing rain
            314 => '🌧️',  // Moderate or heavy freezing rain
            317 => '🌨️',  // Light sleet
            320 => '🌨️',  // Moderate or heavy sleet
            323 => '🌨️',  // Patchy light snow
            326 => '❄️',  // Light snow
            329 => '🌨️',  // Patchy moderate snow
            332 => '❄️',  // Moderate snow
            335 => '🌨️',  // Patchy heavy snow
            338 => '❄️',  // Heavy snow
            350 => '🌧️',  // Ice pellets
            353 => '🌦️',  // Light rain shower
            356 => '🌧️',  // Moderate or heavy rain shower
            359 => '🌧️',  // Torrential rain shower
            362 => '🌨️',  // Light sleet showers
            365 => '🌨️',  // Moderate or heavy sleet showers
            368 => '🌨️',  // Light snow showers
            371 => '❄️',  // Moderate or heavy snow showers
            374 => '🌧️',  // Light showers of ice pellets
            377 => '🌧️',  // Moderate or heavy showers of ice pellets
            386 => '⛈️',  // Patchy light rain with thunder
            389 => '⛈️',  // Moderate or heavy rain with thunder
            392 => '⛈️',  // Patchy light snow with thunder
            395 => '⛈️',  // Moderate or heavy snow with thunder
        ];

        return $iconMap[$weatherCode] ?? '🌤️';
    }

    private function getSampleWeatherData($city)
    {
        $sampleData = [
            'london' => [
                'city' => 'London',
                'temperature' => 12,
                'condition' => 'Partly Cloudy',
                'humidity' => 65,
                'windSpeed' => 15,
                'feelsLike' => 10,
                'icon' => '⛅'
            ],
            'paris' => [
                'city' => 'Paris',
                'temperature' => 15,
                'condition' => 'Sunny',
                'humidity' => 55,
                'windSpeed' => 10,
                'feelsLike' => 14,
                'icon' => '☀️'
            ],
            'tokyo' => [
                'city' => 'Tokyo',
                'temperature' => 18,
                'condition' => 'Rainy',
                'humidity' => 80,
                'windSpeed' => 20,
                'feelsLike' => 16,
                'icon' => '🌧️'
            ],
            'new york' => [
                'city' => 'New York',
                'temperature' => 8,
                'condition' => 'Cloudy',
                'humidity' => 70,
                'windSpeed' => 25,
                'feelsLike' => 5,
                'icon' => '☁️'
            ]
        ];

        $cityKey = strtolower(trim($city));
        return $sampleData[$cityKey] ?? null;
    }
}
