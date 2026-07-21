<?php

namespace App\Services;

class WeatherService
{
  private string $apiKey;
  private array $iconMap = [
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
    389 => '⛈️',
  ];

  public function __construct(string $apiKey)
  {
    $this->apiKey = $apiKey;
  }

  /**
   * Validate request parameters
   *
   * @param string|null $city
   * @param string|null $lat
   * @param string|null $lon
   * @return array|null Returns error array if validation fails, null if valid
   */
  public function validateParameters(?string $city, ?string $lat, ?string $lon): ?array
  {
    if (!$city && (!$lat || !$lon)) {
      return ['error' => 'City name or coordinates (lat, lon) are required', 'code' => 400];
    }

    if (!$this->apiKey) {
      return ['error' => 'API key not configured', 'code' => 500];
    }

    return null;
  }

  /**
   * Build query string for API request
   *
   * @param string|null $city
   * @param string|null $lat
   * @param string|null $lon
   * @return string
   */
  public function buildQuery(?string $city, ?string $lat, ?string $lon): string
  {
    return $city ? urlencode($city) : "{$lat},{$lon}";
  }

  /**
   * Build API URL
   *
   * @param string $query
   * @return string
   */
  public function buildApiUrl(string $query): string
  {
    return "http://api.weatherstack.com/current?access_key={$this->apiKey}&query={$query}";
  }

  /**
   * Get weather icon based on weather code
   *
   * @param int $weatherCode
   * @return string
   */
  public function getWeatherIcon(int $weatherCode): string
  {
    return $this->iconMap[$weatherCode] ?? '🌤️';
  }

  /**
   * Transform API response to application format
   *
   * @param array $apiResponse
   * @return array
   */
  public function transformWeatherData(array $apiResponse): array
  {
    $weatherCode = $apiResponse['current']['weather_code'];

    return [
      'city' => $apiResponse['location']['name'],
      'temperature' => $apiResponse['current']['temperature'],
      'condition' => $apiResponse['current']['weather_descriptions'][0] ?? 'Unknown',
      'humidity' => $apiResponse['current']['humidity'],
      'windSpeed' => $apiResponse['current']['wind_speed'],
      'feelsLike' => $apiResponse['current']['feelslike'],
      'icon' => $this->getWeatherIcon($weatherCode),
      'isDay' => $apiResponse['current']['is_day'] === 'yes',
      'localTime' => $apiResponse['location']['localtime'] ?? null,
    ];
  }

  /**
   * Check if API response contains an error
   *
   * @param array $apiResponse
   * @return array|null Returns error array if error exists, null otherwise
   */
  public function checkApiError(array $apiResponse): ?array
  {
    if (isset($apiResponse['error'])) {
      return [
        'error' => $apiResponse['error']['info'] ?? 'City not found',
        'code' => 404,
      ];
    }

    return null;
  }
}
