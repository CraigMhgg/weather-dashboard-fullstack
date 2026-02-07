# Weather Dashboard - Full Stack Application

A modern, mobile-responsive weather dashboard built with Vue.js and Laravel.

## 📁 Project Structure

```
weather-dashboard-fullstack/
├── frontend/                    # Vue.js Application
│   ├── src/
│   │   ├── components/
│   │   │   └── WeatherDashboard.vue
│   │   ├── App.vue
│   │   └── main.ts
│   ├── public/
│   ├── package.json
│   └── vite.config.ts
│
├── backend/                     # Laravel API
│   ├── app/
│   │   └── Http/
│   │       └── Controllers/
│   │           └── WeatherController.php
│   ├── routes/
│   │   └── api.php
│   ├── config/
│   │   └── cors.php
│   ├── artisan
│   └── composer.json
│
└── README.md
```

## 🚀 Quick Start

### Frontend Setup

```bash
cd frontend
npm install
npm run dev
```

The frontend will be available at: **http://localhost:5173**

### Backend Setup

```bash
cd backend
php artisan serve
```

The API will be available at: **http://localhost:8000**

## ✨ Features

- **Mobile-First Design**: Fully responsive, optimized for all devices
- **Touch-Friendly Controls**: 44-48px touch targets for better mobile UX
- **Real-Time Weather**: Search for weather in multiple cities
- **Modern UI**: Gradient designs and smooth animations
- **RESTful API**: Clean Laravel backend with CORS support

## 📱 Mobile Optimizations

The frontend includes extensive mobile optimizations:

- Responsive breakpoints at 640px (tablet) and 1024px (desktop)
- Minimum touch target sizes for accessibility
- Optimized typography scaling across devices
- Smooth scrolling on iOS
- PWA-ready meta tags

## 🔧 Development

### Frontend Commands

```bash
npm run dev          # Start development server
npm run build        # Build for production
npm run test         # Run tests
npm run lint         # Lint code
```

### Backend Commands

```bash
php artisan serve              # Start development server
php artisan key:generate       # Generate app key
php artisan config:clear       # Clear config cache
```

## 🌐 API Endpoints

### Get Weather Data

```
GET /api/weather?city={cityName}
```

**Example:**

```bash
curl "http://localhost:8000/api/weather?city=London"
```

**Response:**

```json
{
  "city": "London",
  "temperature": 12,
  "condition": "Partly Cloudy",
  "humidity": 65,
  "windSpeed": 15,
  "feelsLike": 10,
  "icon": "⛅"
}
```

**Supported Cities:** London, Paris, Tokyo, New York

## 🔐 Configuration

### Frontend (.env)

Create a `.env` file in the `frontend` directory:

```env
VITE_API_URL=http://localhost:8000
```

### Backend (.env)

The `.env` file is created automatically. Key variables:

```env
FRONTEND_URL=http://localhost:5173
WEATHER_API_KEY=your_api_key_here
```

## 📚 Technology Stack

**Frontend:**

- Vue 3 (Composition API)
- TypeScript
- Vite
- Vitest

**Backend:**

- Laravel 12
- PHP 8.5
- Composer

## 🛠️ Next Steps

1. **Connect Frontend to Backend API**
   - Update WeatherDashboard.vue to call Laravel API
   - Replace sample data with real API calls

2. **Implement Real Weather API**
   - Integrate OpenWeatherMap or similar service
   - Add API key management

3. **Add Features**
   - User authentication
   - Save favorite locations
   - Weather history tracking
   - Forecast data

4. **Deploy**
   - Frontend: Vercel, Netlify, or similar
   - Backend: Laravel Forge, Heroku, or DigitalOcean

## 📄 License

MIT

## 👨‍💻 Author

Craig - Weather Dashboard Full Stack

---

**Happy Coding!** 🌤️
