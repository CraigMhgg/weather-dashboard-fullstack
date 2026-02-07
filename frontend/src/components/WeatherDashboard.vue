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
}

const searchQuery = ref('')
const isLoading = ref(false)
const weatherData = ref<WeatherData | null>(null)
const error = ref('')

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

  // Simulate API call
  setTimeout(() => {
    const cityKey = searchQuery.value.toLowerCase().replace(/\s+/g, '')
    const data = sampleWeatherData[cityKey]

    if (data) {
      weatherData.value = data
      error.value = ''
    } else {
      error.value = 'City not found. Try: London, Paris, Tokyo, or New York'
      weatherData.value = null
    }
    isLoading.value = false
  }, 500)
}

const backgroundColor = computed(() => {
  if (!weatherData.value) return '#4a90e2'

  const temp = weatherData.value.temperature
  if (temp < 5) return '#5c7cfa'
  if (temp < 15) return '#4a90e2'
  if (temp < 25) return '#ffa94d'
  return '#ff6b6b'
})
</script>

<template>
  <div class="weather-dashboard">
    <div class="header">
      <h1>🌤️ Weather Dashboard</h1>
      <p class="subtitle">Get real-time weather information for any city</p>
    </div>

    <div class="search-container">
      <input
        v-model="searchQuery"
        type="text"
        placeholder="Enter city name..."
        class="search-input"
        @keyup.enter="searchWeather"
      />
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

    <div v-else-if="!error && !isLoading" class="welcome-message">
      <div class="welcome-icon">🌍</div>
      <h3>Welcome to Weather Dashboard</h3>
      <p>Search for a city to see current weather conditions</p>
      <p class="hint">Try: London, Paris, Tokyo, or New York</p>
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
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  line-height: 1.2;
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

.search-input {
  width: 100%;
  padding: 0.875rem 1rem;
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

  .search-input {
    flex: 1;
    padding: 1rem 1.5rem;
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
