import { describe, it, expect, beforeEach, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import WeatherDashboard from '../WeatherDashboard.vue'

interface MockWeatherData {
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

describe('WeatherDashboard', () => {
  let wrapper: any

  const createMockWeatherData = (overrides?: Partial<MockWeatherData>): MockWeatherData => ({
    city: 'London',
    temperature: 15,
    condition: 'Partly Cloudy',
    humidity: 65,
    windSpeed: 20,
    feelsLike: 13,
    icon: '⛅',
    isDay: true,
    localTime: '2026-02-07 14:30',
    ...overrides,
  })

  const mockSuccessfulFetch = (weatherData: MockWeatherData) => {
    global.fetch = vi.fn().mockResolvedValue({
      ok: true,
      json: async () => weatherData,
    })
  }

  const mockFailedFetch = (errorMessage: string) => {
    global.fetch = vi.fn().mockResolvedValue({
      ok: false,
      json: async () => ({ error: errorMessage }),
    })
  }

  const mockNetworkError = () => {
    global.fetch = vi.fn().mockRejectedValue(new Error('Network error'))
  }

  const waitForAsyncUpdates = async () => {
    await wrapper.vm.$nextTick()
    await new Promise((resolve) => setTimeout(resolve, 0))
  }

  beforeEach(() => {
    vi.clearAllMocks()
    global.fetch = vi.fn()
  })

  describe('Component Rendering', () => {
    it('should render the dashboard with welcome message', () => {
      wrapper = mount(WeatherDashboard)

      const header = wrapper.find('h1')
      const welcomeMessage = wrapper.find('.welcome-message')

      expect(header.exists()).toBe(true)
      expect(header.text()).toContain('Weather Dashboard')
      expect(welcomeMessage.exists()).toBe(true)
      expect(welcomeMessage.text()).toContain('Search for a city')
    })

    it('should render search input and buttons', () => {
      wrapper = mount(WeatherDashboard)

      const searchInput = wrapper.find('.search-input')
      const searchButton = wrapper.find('.search-button')
      const locationButton = wrapper.find('.location-button')

      expect(searchInput.exists()).toBe(true)
      expect(searchButton.exists()).toBe(true)
      expect(locationButton.exists()).toBe(true)
    })
  })

  describe('Search Functionality', () => {
    it('should show error when searching with empty input', async () => {
      wrapper = mount(WeatherDashboard)
      const searchButton = wrapper.find('.search-button')

      await searchButton.trigger('click')
      await wrapper.vm.$nextTick()

      const errorMessage = wrapper.find('.error-message')
      expect(errorMessage.exists()).toBe(true)
      expect(errorMessage.text()).toContain('Please enter a city name')
    })

    it('should fetch weather data when searching with valid city', async () => {
      const mockWeatherData = createMockWeatherData()
      mockSuccessfulFetch(mockWeatherData)

      wrapper = mount(WeatherDashboard)
      const searchInput = wrapper.find('.search-input')
      const searchButton = wrapper.find('.search-button')

      await searchInput.setValue('London')
      await searchButton.trigger('click')
      await waitForAsyncUpdates()

      expect(global.fetch).toHaveBeenCalledWith(expect.stringContaining('weather.php?city=London'))
      const weatherCard = wrapper.find('.weather-card')
      expect(weatherCard.exists()).toBe(true)
      expect(weatherCard.text()).toContain('London')
      expect(weatherCard.text()).toContain('15°C')
    })

    it('should handle API errors gracefully', async () => {
      mockFailedFetch('City not found')

      wrapper = mount(WeatherDashboard)
      const searchInput = wrapper.find('.search-input')
      const searchButton = wrapper.find('.search-button')

      await searchInput.setValue('InvalidCity')
      await searchButton.trigger('click')
      await waitForAsyncUpdates()

      const errorMessage = wrapper.find('.error-message')
      expect(errorMessage.exists()).toBe(true)
      expect(errorMessage.text()).toContain('City not found')
    })

    it('should handle network errors', async () => {
      mockNetworkError()

      wrapper = mount(WeatherDashboard)
      const searchInput = wrapper.find('.search-input')
      const searchButton = wrapper.find('.search-button')

      await searchInput.setValue('London')
      await searchButton.trigger('click')
      await waitForAsyncUpdates()

      const errorMessage = wrapper.find('.error-message')
      expect(errorMessage.exists()).toBe(true)
      expect(errorMessage.text()).toContain('Failed to fetch weather data')
    })
  })

  describe('Current Location Functionality', () => {
    it('should show error when geolocation is not supported', async () => {
      const originalNavigator = global.navigator
      Object.defineProperty(global, 'navigator', {
        value: {},
        writable: true,
        configurable: true,
      })

      wrapper = mount(WeatherDashboard)
      const locationButton = wrapper.find('.location-button')

      await locationButton.trigger('click')
      await wrapper.vm.$nextTick()

      const errorMessage = wrapper.find('.error-message')
      expect(errorMessage.exists()).toBe(true)
      expect(errorMessage.text()).toContain('Geolocation is not supported')

      Object.defineProperty(global, 'navigator', {
        value: originalNavigator,
        writable: true,
        configurable: true,
      })
    })
  })

  describe('Loading States', () => {
    it('should show loading state when searching', async () => {
      global.fetch = vi.fn().mockImplementation(
        () =>
          new Promise((resolve) =>
            setTimeout(
              () =>
                resolve({
                  ok: true,
                  json: async () => createMockWeatherData({ condition: 'Sunny' }),
                }),
              100,
            ),
          ),
      )

      wrapper = mount(WeatherDashboard)
      const searchInput = wrapper.find('.search-input')
      const searchButton = wrapper.find('.search-button')

      await searchInput.setValue('London')
      await searchButton.trigger('click')
      await wrapper.vm.$nextTick()

      expect(searchButton.text()).toContain('Searching...')
      expect(searchButton.attributes('disabled')).toBeDefined()
    })
  })

  describe('Day/Night Theme', () => {
    it('should display day icon and colors when isDay is true', async () => {
      const mockWeatherData = createMockWeatherData({
        temperature: 20,
        condition: 'Sunny',
        icon: '☀️',
      })
      mockSuccessfulFetch(mockWeatherData)

      wrapper = mount(WeatherDashboard)
      const searchInput = wrapper.find('.search-input')
      const searchButton = wrapper.find('.search-button')

      await searchInput.setValue('London')
      await searchButton.trigger('click')
      await waitForAsyncUpdates()

      const header = wrapper.find('h1')
      expect(header.text()).toContain('🌤️')
    })

    it('should display night icon when isDay is false', async () => {
      const mockWeatherData = createMockWeatherData({
        city: 'Sydney',
        temperature: 18,
        condition: 'Clear',
        humidity: 70,
        windSpeed: 10,
        feelsLike: 16,
        icon: '🌙',
        isDay: false,
        localTime: '2026-02-07 03:00',
      })
      mockSuccessfulFetch(mockWeatherData)

      wrapper = mount(WeatherDashboard)
      const searchInput = wrapper.find('.search-input')
      const searchButton = wrapper.find('.search-button')

      await searchInput.setValue('Sydney')
      await searchButton.trigger('click')
      await waitForAsyncUpdates()

      const header = wrapper.find('h1')
      expect(header.text()).toContain('🌙')
    })
  })

  describe('Temperature Display', () => {
    it('should display all weather metrics correctly', async () => {
      const mockWeatherData = createMockWeatherData({
        city: 'Paris',
        temperature: 12,
        humidity: 75,
        windSpeed: 25,
        feelsLike: 10,
        localTime: '2026-02-07 15:00',
      })
      mockSuccessfulFetch(mockWeatherData)

      wrapper = mount(WeatherDashboard)
      const searchInput = wrapper.find('.search-input')
      const searchButton = wrapper.find('.search-button')

      await searchInput.setValue('Paris')
      await searchButton.trigger('click')
      await waitForAsyncUpdates()

      const weatherCard = wrapper.find('.weather-card')
      expect(weatherCard.text()).toContain('Paris')
      expect(weatherCard.text()).toContain('12°C')
      expect(weatherCard.text()).toContain('Feels like 10°C')
      expect(weatherCard.text()).toContain('75%')
      expect(weatherCard.text()).toContain('25 km/h')
      expect(weatherCard.text()).toContain('Partly Cloudy')
    })
  })
})
