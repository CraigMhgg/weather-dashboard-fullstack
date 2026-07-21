<?php

namespace Tests\Unit;

use App\Services\WeatherService;
use PHPUnit\Framework\TestCase;

class WeatherServiceTest extends TestCase
{
  private WeatherService $weatherService;
  private string $testApiKey = 'test_api_key_123';

  protected function setUp(): void
  {
    parent::setUp();
    $this->weatherService = new WeatherService($this->testApiKey);
  }

  private function createServiceWithoutApiKey(): WeatherService
  {
    return new WeatherService('');
  }

  private function createWeatherApiResponse(
    string $city,
    int $temperature,
    int $weatherCode,
    string $condition,
    int $humidity,
    int $windSpeed,
    int $feelsLike,
    string $isDay = 'yes',
    ?string $localTime = null
  ): array {
    return [
      'location' => [
        'name' => $city,
        'localtime' => $localTime,
      ],
      'current' => [
        'temperature' => $temperature,
        'weather_code' => $weatherCode,
        'weather_descriptions' => $condition ? [$condition] : [],
        'humidity' => $humidity,
        'wind_speed' => $windSpeed,
        'feelslike' => $feelsLike,
        'is_day' => $isDay,
      ],
    ];
  }

  private function createApiErrorResponse(int $errorCode, ?string $errorInfo = null): array
  {
    $error = ['code' => $errorCode];
    if ($errorInfo !== null) {
      $error['info'] = $errorInfo;
    }
    return ['error' => $error];
  }

  private function assertValidationError(array $result, string $expectedError, int $expectedCode): void
  {
    $this->assertIsArray($result);
    $this->assertEquals($expectedError, $result['error']);
    $this->assertEquals($expectedCode, $result['code']);
  }

  /**
   * @test
   */
  public function it_should_validate_city_parameter_successfully()
  {
    $result = $this->weatherService->validateParameters('London', null, null);

    $this->assertNull($result);
  }

  /**
   * @test
   */
  public function it_should_validate_coordinate_parameters_successfully()
  {
    $result = $this->weatherService->validateParameters(null, '51.5074', '-0.1278');

    $this->assertNull($result);
  }

  /**
   * @test
   */
  public function it_should_return_error_when_no_parameters_provided()
  {
    $result = $this->weatherService->validateParameters(null, null, null);

    $this->assertValidationError($result, 'City name or coordinates (lat, lon) are required', 400);
  }

  /**
   * @test
   */
  public function it_should_return_error_when_only_latitude_provided()
  {
    $result = $this->weatherService->validateParameters(null, '51.5074', null);

    $this->assertValidationError($result, 'City name or coordinates (lat, lon) are required', 400);
  }

  /**
   * @test
   */
  public function it_should_return_error_when_api_key_missing()
  {
    $serviceWithoutKey = $this->createServiceWithoutApiKey();
    $result = $serviceWithoutKey->validateParameters('London', null, null);

    $this->assertValidationError($result, 'API key not configured', 500);
  }

  /**
   * @test
   */
  public function it_should_build_query_string_for_city()
  {
    $result = $this->weatherService->buildQuery('New York', null, null);

    $this->assertEquals('New+York', $result);
  }

  /**
   * @test
   */
  public function it_should_build_query_string_for_coordinates()
  {
    $result = $this->weatherService->buildQuery(null, '51.5074', '-0.1278');

    $this->assertEquals('51.5074,-0.1278', $result);
  }

  /**
   * @test
   */
  public function it_should_prefer_city_over_coordinates_when_both_provided()
  {
    $result = $this->weatherService->buildQuery('London', '51.5074', '-0.1278');

    $this->assertEquals('London', $result);
  }

  /**
   * @test
   */
  public function it_should_build_correct_api_url()
  {
    $query = 'London';

    $result = $this->weatherService->buildApiUrl($query);

    // Then: URL should contain API key and query
    $this->assertStringContainsString('api.weatherstack.com/current', $result);
    $this->assertStringContainsString('access_key=test_api_key_123', $result);
    $this->assertStringContainsString('query=London', $result);
  }

  /**
   * @test
   */
  public function it_should_return_correct_weather_icon_for_sunny()
  {
    $result = $this->weatherService->getWeatherIcon(113);

    $this->assertEquals('☀️', $result);
  }

  /**
   * @test
   */
  public function it_should_return_correct_weather_icon_for_cloudy()
  {
    $result = $this->weatherService->getWeatherIcon(119);

    $this->assertEquals('☁️', $result);
  }

  /**
   * @test
   */
  public function it_should_return_default_icon_for_unknown_weather_code()
  {
    $result = $this->weatherService->getWeatherIcon(999);

    $this->assertEquals('🌤️', $result);
  }

  /**
   * @test
   */
  public function it_should_transform_api_response_correctly()
  {
    $apiResponse = $this->createWeatherApiResponse(
      'London',
      15,
      116,
      'Partly cloudy',
      65,
      20,
      13,
      'yes',
      '2026-02-07 14:30'
    );

    $result = $this->weatherService->transformWeatherData($apiResponse);

    $this->assertEquals('London', $result['city']);
    $this->assertEquals(15, $result['temperature']);
    $this->assertEquals('Partly cloudy', $result['condition']);
    $this->assertEquals(65, $result['humidity']);
    $this->assertEquals(20, $result['windSpeed']);
    $this->assertEquals(13, $result['feelsLike']);
    $this->assertEquals('⛅', $result['icon']);
    $this->assertTrue($result['isDay']);
    $this->assertEquals('2026-02-07 14:30', $result['localTime']);
  }

  /**
   * @test
   */
  public function it_should_handle_nighttime_weather_data()
  {
    $apiResponse = $this->createWeatherApiResponse(
      'Sydney',
      18,
      113,
      'Clear',
      70,
      10,
      16,
      'no',
      '2026-02-07 03:00'
    );

    $result = $this->weatherService->transformWeatherData($apiResponse);

    $this->assertFalse($result['isDay']);
  }

  /**
   * @test
   */
  public function it_should_use_default_condition_when_missing()
  {
    $apiResponse = $this->createWeatherApiResponse('Paris', 12, 119, '', 60, 15, 10);

    $result = $this->weatherService->transformWeatherData($apiResponse);

    $this->assertEquals('Unknown', $result['condition']);
  }

  /**
   * @test
   */
  public function it_should_detect_api_error_response()
  {
    $apiResponse = $this->createApiErrorResponse(615, 'City not found');

    $result = $this->weatherService->checkApiError($apiResponse);

    $this->assertIsArray($result);
    $this->assertEquals('City not found', $result['error']);
    $this->assertEquals(404, $result['code']);
  }

  /**
   * @test
   */
  public function it_should_return_null_when_no_api_error()
  {
    $apiResponse = [
      'location' => ['name' => 'London'],
      'current' => ['temperature' => 15],
    ];

    $result = $this->weatherService->checkApiError($apiResponse);

    $this->assertNull($result);
  }

  /**
   * @test
   */
  public function it_should_handle_api_error_without_info()
  {
    $apiResponse = $this->createApiErrorResponse(500);

    $result = $this->weatherService->checkApiError($apiResponse);

    $this->assertValidationError($result, 'City not found', 404);
  }

  /**
   * @test
   */
  public function it_should_return_rainy_icon_for_rainy_weather()
  {
    $result = $this->weatherService->getWeatherIcon(296);

    $this->assertEquals('🌧️', $result);
  }

  /**
   * @test
   */
  public function it_should_return_snow_icon_for_snowy_weather()
  {
    $result = $this->weatherService->getWeatherIcon(326);

    $this->assertEquals('❄️', $result);
  }

  /**
   * @test
   */
  public function it_should_return_thunderstorm_icon_for_stormy_weather()
  {
    $result = $this->weatherService->getWeatherIcon(200);

    $this->assertEquals('⛈️', $result);
  }
}