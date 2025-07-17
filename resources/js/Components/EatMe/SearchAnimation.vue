<template>
  <div class="search-animation-container" :class="{ 'active': active }">
    <div class="search-animation-overlay">
      <div class="search-animation-pulse-container">
        <div class="search-animation-pulse"></div>
        <div class="search-animation-pulse delay-1"></div>
        <div class="search-animation-pulse delay-2"></div>
      </div>
      <div class="search-animation-text">
        <slot>Recherche en cours...</slot>
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineProps } from 'vue';

const props = defineProps({
  active: {
    type: Boolean,
    default: false
  }
});
</script>

<style scoped>
.search-animation-container {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 10;
  opacity: 0;
  pointer-events: none;
  transition: opacity 0.5s ease;
}

.search-animation-container.active {
  opacity: 1;
  pointer-events: auto;
}

.search-animation-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(255, 255, 255, 0.7);
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}

.search-animation-pulse-container {
  position: relative;
  width: 100px;
  height: 100px;
}

.search-animation-pulse {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 50px;
  height: 50px;
  border-radius: 50%;
  background-color: rgba(255, 64, 129, 0.6);
  opacity: 0;
  animation: pulse 2s infinite ease-out;
}

.search-animation-pulse.delay-1 {
  animation-delay: 0.5s;
}

.search-animation-pulse.delay-2 {
  animation-delay: 1s;
}

.search-animation-text {
  margin-top: 20px;
  font-size: 18px;
  font-weight: 600;
  color: #333;
}

@keyframes pulse {
  0% {
    transform: translate(-50%, -50%) scale(0);
    opacity: 1;
  }
  100% {
    transform: translate(-50%, -50%) scale(2);
    opacity: 0;
  }
}
</style>