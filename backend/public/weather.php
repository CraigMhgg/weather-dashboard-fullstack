<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  exit(0);
}

$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
  $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
  foreach ($lines as $line) {
    if (strpos(trim($line), '#') === 0) continue;
    list($key, $value) = explode('=', $line, 2);
    $_ENV[trim($key)] = trim($value);
  }
}

$apiKey = $_ENV['WEATHERSTACK_API_KEY'] ?? null;
$city = $_GET['city'] ?? null;
$lat = $_GET['lat'] ?? null;
$lon = $_GET['lon'] ?? null;

if (!$city && (!$lat || !$lon)) {
  http_response_code(400);
  echo json_encode(['error' => 'City name or coordinates (lat, lon) are required']);
  exit;
}

if (!$apiKey) {
  http_response_code(500);
  echo json_encode(['error' => 'API key not configured']);
  exit;
}

// Build query string - use coordinates if provided, otherwise use city
$query = $city ? urlencode($city) : "{$lat},{$lon}";
$url = "http://api.weatherstack.com/current?access_key={$apiKey}&query={$query}";

if (function_exists('curl_init')) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_TIMEOUT, 10);
  $response = curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);
} else {
  $context = stream_context_create([
    'http' => [
      'method' => 'GET',
      'timeout' => 10,
      'ignore_errors' => true,
    ],
  ]);

  $response = @file_get_contents($url, false, $context);
  $statusLine = $http_response_header[0] ?? '';
  preg_match('/\s(\d{3})\s/', $statusLine, $matches);
  $httpCode = isset($matches[1]) ? (int) $matches[1] : 500;
}

if ($httpCode !== 200) {
  http_response_code(500);
  echo json_encode(['error' => 'Failed to fetch weather data']);
  exit;
}

$data = json_decode($response, true);

if (isset($data['error'])) {
  http_response_code(404);
  echo json_encode(['error' => $data['error']['info'] ?? 'City not found']);
  exit;
}

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

$weatherData = [
  'city' => $data['location']['name'],
  'temperature' => $data['current']['temperature'],
  'condition' => $data['current']['weather_descriptions'][0] ?? 'Unknown',
  'humidity' => $data['current']['humidity'],
  'windSpeed' => $data['current']['wind_speed'],
  'feelsLike' => $data['current']['feelslike'],
  'icon' => $iconMap[$weatherCode] ?? '🌤️',
  'isDay' => $data['current']['is_day'] === 'yes',
  'localTime' => $data['location']['localtime'] ?? null
];

echo json_encode($weatherData);
