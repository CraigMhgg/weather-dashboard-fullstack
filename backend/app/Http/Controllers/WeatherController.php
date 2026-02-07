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
        
        // Sample weather data - replace with actual API call
        $weatherData = $this->getSampleWeatherData($city);
        
        if (!$weatherData) {
            return response()->json([
                'error' => 'City not found'
            ], 404);
        }
        
        return response()->json($weatherData);
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
