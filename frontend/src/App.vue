<script setup lang="ts">
import WeatherDashboard from './components/WeatherDashboard.vue'
</script>

<template>
  <div class="app-container">
    <WeatherDashboard />
  </div>
</template>

<style scoped>
.app-container {
  min-height: 100vh;
  padding: 1rem 0;
  background: linear-gradient(to bottom, #e0f7fa, #ffffff);
}

@media (min-width: 640px) {
  .app-container {
    padding: 1.5rem 0;
  }
}

@media (min-width: 1024px) {
  .app-container {
    padding: 2rem 0;
  }
}
</style>
