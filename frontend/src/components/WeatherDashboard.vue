<script setup lang="ts">
import { ref, computed } from 'vue'

interface WeatherData {
  city: string
  temperature: number
  condition: string
  humidity: number
  windSpeed: number
  feelsLike: number
  icon: string
  isDay: boolean
  localTime?: string
}

const searchQuery = ref('')
const isLoading = ref(false)
const weatherData = ref<WeatherData | null>(null)
const error = ref('')
const isGettingLocation = ref(false)

// Sample data for demonstration
const sampleWeatherData: Record<string, WeatherData> = {
  london: {
    city: 'London',
    temperature: 12,
    condition: 'Partly Cloudy',
    humidity: 65,
    windSpeed: 15,
    feelsLike: 10,
    icon: '⛅',
  },
  paris: {
    city: 'Paris',
    temperature: 15,
    condition: 'Sunny',
    humidity: 55,
    windSpeed: 10,
    feelsLike: 14,
    icon: '☀️',
  },
  tokyo: {
    city: 'Tokyo',
    temperature: 18,
    condition: 'Rainy',
    humidity: 80,
    windSpeed: 20,
    feelsLike: 16,
    icon: '🌧️',
  },
  newyork: {
    city: 'New York',
    temperature: 8,
    condition: 'Cloudy',
    humidity: 70,
    windSpeed: 25,
    feelsLike: 5,
    icon: '☁️',
  },
}

const searchWeather = async () => {
  if (!searchQuery.value.trim()) {
    error.value = 'Please enter a city name'
    return
  }

  isLoading.value = true
  error.value = ''
  weatherData.value = null

  try {
    const response = await fetch(
      `http://localhost:8000/weather.php?city=${encodeURIComponent(searchQuery.value)}`,
    )
    const data = await response.json()

    if (!response.ok) {
      error.value = data.error || 'City not found'
      weatherData.value = null
    } else {
      weatherData.value = data
      error.value = ''
    }
  } catch (err) {
    error.value = 'Failed to fetch weather data. Make sure the backend server is running.'
    weatherData.value = null
  } finally {
    isLoading.value = false
  }
}

const getCurrentLocation = async () => {
  if (!navigator.geolocation) {
    error.value = 'Geolocation is not supported by your browser'
    return
  }

  isGettingLocation.value = true
  error.value = ''
  weatherData.value = null

  navigator.geolocation.getCurrentPosition(
    async (position) => {
      try {
        const { latitude, longitude } = position.coords
        const response = await fetch(
          `http://localhost:8000/weather.php?lat=${latitude}&lon=${longitude}`,
        )
        const data = await response.json()

        if (!response.ok) {
          error.value = data.error || 'Failed to get weather for your location'
          weatherData.value = null
        } else {
          weatherData.value = data
          searchQuery.value = data.city
          error.value = ''
        }
      } catch (err) {
        error.value = 'Failed to fetch weather data'
        weatherData.value = null
      } finally {
        isGettingLocation.value = false
      }
    },
    (err) => {
      error.value = 'Unable to retrieve your location. Please allow location access.'
      isGettingLocation.value = false
    },
  )
}

const backgroundColor = computed(() => {
  if (!weatherData.value) return '#4a90e2'

  const temp = weatherData.value.temperature
  const isDay = weatherData.value.isDay

  // Cooler colors for night
  if (!isDay) {
    if (temp < 5) return '#2d3561'
    if (temp < 15) return '#1e3a5f'
    if (temp < 25) return '#2c4875'
    return '#3d5a80'
  }

  // Warmer colors for day
  if (temp < 5) return '#5c7cfa'
  if (temp < 15) return '#4a90e2'
  if (temp < 25) return '#ffa94d'
  return '#ff6b6b'
})

const headerGradient = computed(() => {
  if (!weatherData.value) return 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)'

  return weatherData.value.isDay
    ? 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)'
    : 'linear-gradient(135deg, #4a5568 0%, #2d3748 100%)'
})

const welcomeGradient = computed(() => {
  if (!weatherData.value) return 'linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%)'

  return weatherData.value.isDay
    ? 'linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%)'
    : 'linear-gradient(135deg, #2d3748 0%, #1a202c 100%)'
})

const welcomeTextColor = computed(() => {
  if (!weatherData.value) return '#333'
  return weatherData.value.isDay ? '#333' : '#e2e8f0'
})

const welcomeSubtextColor = computed(() => {
  if (!weatherData.value) return '#666'
  return weatherData.value.isDay ? '#666' : '#a0aec0'
})

const formatLocalTime = (timeString: string) => {
  if (!timeString) return ''
  try {
    const [date, time] = timeString.split(' ')
    const [year, month, day] = date.split('-')
    const [hour, minute] = time.split(':')

    const dateObj = new Date(
      parseInt(year),
      parseInt(month) - 1,
      parseInt(day),
      parseInt(hour),
      parseInt(minute),
    )

    return dateObj.toLocaleString('en-US', {
      weekday: 'short',
      month: 'short',
      day: 'numeric',
      hour: 'numeric',
      minute: '2-digit',
      hour12: true,
    })
  } catch (e) {
    return timeString
  }
}
</script>

<template>
  <div class="weather-dashboard">
    <div class="header">
      <h1>
        {{ weatherData?.isDay === false ? '🌙' : '🌤️' }}
        <span
          class="gradient-text"
          :style="{
            background: headerGradient,
            WebkitBackgroundClip: 'text',
            backgroundClip: 'text',
          }"
          >Weather Dashboard</span
        >
      </h1>
      <p class="subtitle">Get real-time weather information for any city</p>
    </div>

    <div class="search-container">
      <div class="input-wrapper">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Enter city name..."
          class="search-input"
          @keyup.enter="searchWeather"
        />
        <button
          @click="getCurrentLocation"
          class="location-button"
          :disabled="isGettingLocation"
          title="Use current location"
        >
          {{ isGettingLocation ? '📍' : '📍' }}
        </button>
      </div>
      <button @click="searchWeather" class="search-button" :disabled="isLoading">
        {{ isLoading ? 'Searching...' : 'Search' }}
      </button>
    </div>

    <div v-if="error" class="error-message">⚠️ {{ error }}</div>

    <div v-if="weatherData" class="weather-card" :style="{ backgroundColor: backgroundColor }">
      <div class="weather-header">
        <div class="city-info">
          <h2>{{ weatherData.city }}</h2>
          <p class="condition">{{ weatherData.condition }}</p>
          <p v-if="weatherData.localTime" class="local-time-header">
            {{ weatherData.isDay ? '☀️' : '🌙' }} {{ formatLocalTime(weatherData.localTime) }}
          </p>
        </div>
        <div class="weather-icon">{{ weatherData.icon }}</div>
      </div>

      <div class="temperature-display">
        <span class="temperature">{{ weatherData.temperature }}°C</span>
        <span class="feels-like">Feels like {{ weatherData.feelsLike }}°C</span>
      </div>

      <div class="weather-details">
        <div class="detail-item">
          <span class="detail-icon">💧</span>
          <div class="detail-info">
            <span class="detail-label">Humidity</span>
            <span class="detail-value">{{ weatherData.humidity }}%</span>
          </div>
        </div>
        <div class="detail-item">
          <span class="detail-icon">💨</span>
          <div class="detail-info">
            <span class="detail-label">Wind Speed</span>
            <span class="detail-value">{{ weatherData.windSpeed }} km/h</span>
          </div>
        </div>
      </div>
    </div>

    <div
      v-else-if="!error && !isLoading"
      class="welcome-message"
      :style="{ background: welcomeGradient }"
    >
      <div class="welcome-icon">🌍</div>
      <h3 :style="{ color: welcomeTextColor }">Welcome to Weather Dashboard</h3>
      <p :style="{ color: welcomeSubtextColor }">
        Search for a city to see current weather conditions
      </p>
      <p class="hint" :style="{ color: welcomeSubtextColor }">
        Try: London, Paris, Tokyo, or New York
      </p>
    </div>
  </div>
</template>

<style scoped>
/* Mobile-first responsive design */
.weather-dashboard {
  max-width: 800px;
  margin: 0 auto;
  padding: 1rem;
  width: 100%;
  box-sizing: border-box;
}

.header {
  text-align: center;
  margin-bottom: 1.5rem;
}

.header h1 {
  font-size: 1.75rem;
  margin-bottom: 0.5rem;
  line-height: 1.2;
}

.gradient-text {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.subtitle {
  color: #666;
  font-size: 0.95rem;
  line-height: 1.4;
  padding: 0 0.5rem;
}

.search-container {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  margin-bottom: 1.5rem;
}

.input-wrapper {
  position: relative;
  width: 100%;
}

.search-input {
  width: 100%;
  padding: 0.875rem 3.5rem 0.875rem 1rem;
  font-size: 1rem;
  border: 2px solid #e0e0e0;
  border-radius: 12px;
  outline: none;
  transition: border-color 0.3s;
  box-sizing: border-box;
  /* Touch-friendly minimum height */
  min-height: 44px;
  -webkit-appearance: none;
}

.search-input:focus {
  border-color: #667eea;
}

.location-button {
  position: absolute;
  right: 4px;
  top: 50%;
  transform: translateY(-50%);
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 8px;
  width: 40px;
  height: 36px;
  cursor: pointer;
  font-size: 1.2rem;
  display: flex;
  align-items: center;
  justify-content: center;
  transition:
    transform 0.2s,
    opacity 0.3s;
  -webkit-tap-highlight-color: transparent;
}

.location-button:active {
  transform: translateY(-50%) scale(0.95);
}

.location-button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.search-button {
  width: 100%;
  padding: 0.875rem 1.5rem;
  font-size: 1rem;
  font-weight: 600;
  color: white;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border: none;
  border-radius: 12px;
  cursor: pointer;
  transition:
    transform 0.2s,
    opacity 0.3s;
  /* Touch-friendly minimum height */
  min-height: 48px;
  -webkit-tap-highlight-color: transparent;
}

.search-button:active {
  transform: scale(0.98);
}

.search-button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.error-message {
  padding: 1rem 1.5rem;
  background-color: #fff3cd;
  border: 1px solid #ffc107;
  border-radius: 12px;
  color: #856404;
  margin-bottom: 2rem;
  text-align: center;
}

.weather-card {
  padding: 1.25rem;
  border-radius: 20px;
  color: white;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
  transition: background-color 0.5s;
}

.weather-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
  gap: 1rem;
}

.city-info h2 {
  font-size: 1.5rem;
  margin: 0 0 0.25rem 0;
  line-height: 1.2;
}

.condition {
  margin: 0;
  opacity: 0.9;
  font-size: 0.95rem;
}

.weather-icon {
  font-size: 3rem;
  flex-shrink: 0;
}

.temperature-display {
  text-align: center;
  margin-bottom: 1.5rem;
  padding: 1.5rem 0;
  border-top: 1px solid rgba(255, 255, 255, 0.3);
  border-bottom: 1px solid rgba(255, 255, 255, 0.3);
}

.temperature {
  display: block;
  font-size: 3rem;
  font-weight: bold;
  margin-bottom: 0.5rem;
  line-height: 1;
}

.feels-like {
  display: block;
  opacity: 0.9;
  font-size: 1rem;
}

.local-time {
  display: block;
  opacity: 0.85;
  font-size: 0.9rem;
  margin-top: 0.75rem;
  font-style: italic;
}

.weather-details {
  display: grid;
  grid-template-columns: 1fr;
  gap: 0.75rem;
}

.detail-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  background-color: rgba(255, 255, 255, 0.2);
  padding: 0.875rem 1rem;
  border-radius: 12px;
  min-height: 44px;
}

.detail-icon {
  font-size: 1.75rem;
  flex-shrink: 0;
}

.detail-info {
  display: flex;
  flex-direction: column;
  flex: 1;
}

.detail-label {
  opacity: 0.9;
  font-size: 0.85rem;
  margin-bottom: 0.125rem;
}

.detail-value {
  font-size: 1.15rem;
  font-weight: bold;
}

.welcome-message {
  text-align: center;
  padding: 2.5rem 1.25rem;
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  border-radius: 20px;
}

.welcome-icon {
  font-size: 3.5rem;
  margin-bottom: 1rem;
}

.welcome-message h3 {
  font-size: 1.4rem;
  margin-bottom: 0.75rem;
  color: #333;
  line-height: 1.3;
}

.welcome-message p {
  color: #666;
  font-size: 0.95rem;
  margin-bottom: 0.5rem;
  line-height: 1.5;
}

.hint {
  color: #999;
  font-size: 0.875rem;
  font-style: italic;
}

/* Tablet and larger screens */
@media (min-width: 640px) {
  .weather-dashboard {
    padding: 1.5rem;
  }

  .header h1 {
    font-size: 2.25rem;
  }

  .subtitle {
    font-size: 1rem;
  }

  .search-container {
    flex-direction: row;
    gap: 1rem;
  }

  .input-wrapper {
    flex: 1;
  }

  .search-input {
    padding: 1rem 3.5rem 1rem 1.5rem;
  }

  .location-button {
    right: 8px;
    width: 44px;
    height: 40px;
  }

  .search-button {
    width: auto;
    padding: 1rem 2rem;
  }

  .weather-card {
    padding: 1.75rem;
  }

  .city-info h2 {
    font-size: 1.75rem;
  }

  .condition {
    font-size: 1rem;
  }

  .welcome-message {
    padding: 3.5rem 2rem;
  }

  .welcome-icon {
    font-size: 4.5rem;
  }

  .welcome-message h3 {
    font-size: 1.6rem;
  }

  .welcome-message p {
    font-size: 1rem;
  }
}

/* Desktop screens */
@media (min-width: 1024px) {
  .weather-dashboard {
    padding: 2rem;
  }

  .header {
    margin-bottom: 2rem;
  }

  .header h1 {
    font-size: 2.5rem;
  }

  .subtitle {
    font-size: 1.1rem;
  }

  .search-container {
    margin-bottom: 2rem;
  }

  .weather-card {
    padding: 2rem;
    border-radius: 24px;
  }

  .weather-header {
    margin-bottom: 2rem;
  }

  .city-info h2 {
    font-size: 2rem;
  }

  .condition {
    font-size: 1.1rem;
  }

  .weather-icon {
    font-size: 4rem;
  }

  .temperature-display {
    margin-bottom: 2rem;
    padding: 2rem 0;
  }

  .temperature {
    font-size: 4rem;
  }

  .feels-like {
    font-size: 1.1rem;
  }

  .weather-details {
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
  }

  .detail-item {
    padding: 1rem;
  }

  .detail-icon {
    font-size: 2rem;
  }

  .detail-label {
    font-size: 0.9rem;
  }

  .detail-value {
    font-size: 1.3rem;
  }

  .location-button:hover:not(:disabled) {
    transform: translateY(-50%) scale(1.05);
  }

  .welcome-message {
    padding: 4rem 2rem;
    border-radius: 24px;
  }

  .welcome-icon {
    font-size: 5rem;
  }

  .welcome-message h3 {
    font-size: 1.8rem;
  }

  .welcome-message p {
    font-size: 1.1rem;
  }

  .hint {
    font-size: 0.95rem;
  }

  .search-button:hover:not(:disabled) {
    transform: translateY(-2px);
  }
}
</style>
