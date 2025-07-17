<template>
  <div class="relative">
    <!-- Carrousel de médias -->
    <div class="w-full h-64 sm:h-80 overflow-hidden">
      <div class="flex transition-transform duration-300 ease-in-out" :style="{ transform: `translateX(-${currentSlide * 100}%)` }">
        <div v-for="(media, index) in mediaItems" :key="index" class="w-full h-64 sm:h-80 flex-shrink-0">
          <img v-if="isImage(media)" :src="media.url" :alt="altText" class="w-full h-full object-cover">
          <video v-else-if="isVideo(media)" controls class="w-full h-full object-cover">
            <source :src="media.url" :type="media.type">
            Votre navigateur ne supporte pas la lecture de vidéos.
          </video>
        </div>
      </div>
    </div>

    <!-- Indicateurs de navigation -->
    <div v-if="mediaItems.length > 1" class="absolute bottom-4 left-0 right-0 flex justify-center space-x-2">
      <button 
        v-for="(_, index) in mediaItems" 
        :key="index" 
        @click="currentSlide = index"
        class="w-2 h-2 rounded-full transition-colors duration-200 ease-in-out"
        :class="currentSlide === index ? 'bg-white' : 'bg-gray-400'"
      ></button>
    </div>

    <!-- Boutons précédent/suivant -->
    <button 
      v-if="mediaItems.length > 1" 
      @click="prevSlide" 
      class="absolute left-2 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-50 rounded-full p-1 focus:outline-none"
    >
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
      </svg>
    </button>
    <button 
      v-if="mediaItems.length > 1" 
      @click="nextSlide" 
      class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-50 rounded-full p-1 focus:outline-none"
    >
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
      </svg>
    </button>
  </div>
</template>

<script setup>
import { ref, watch, onBeforeUnmount } from 'vue';

const props = defineProps({
  mediaItems: {
    type: Array,
    required: true,
    default: () => []
  },
  altText: {
    type: String,
    default: 'Media'
  },
  autoplay: {
    type: Boolean,
    default: false
  },
  autoplayInterval: {
    type: Number,
    default: 5000
  }
});

const emit = defineEmits(['slide-change']);

// État du carrousel
const currentSlide = ref(0);

// Émettre un événement lorsque la diapositive change
watch(currentSlide, (newValue) => {
  emit('slide-change', newValue);
});

// Navigation dans le carrousel
const nextSlide = () => {
  currentSlide.value = (currentSlide.value + 1) % props.mediaItems.length;
};

const prevSlide = () => {
  currentSlide.value = (currentSlide.value - 1 + props.mediaItems.length) % props.mediaItems.length;
};

// Vérifier le type de média
const isImage = (media) => {
  return media.type && media.type.startsWith('image/');
};

const isVideo = (media) => {
  return media.type && media.type.startsWith('video/');
};

// Autoplay si activé
let autoplayInterval = null;

// Définir les fonctions avant de les utiliser
const startAutoplay = () => {
  if (props.autoplay && props.mediaItems.length > 1) {
    autoplayInterval = setInterval(() => {
      nextSlide();
    }, props.autoplayInterval);
  }
};

const stopAutoplay = () => {
  if (autoplayInterval) {
    clearInterval(autoplayInterval);
    autoplayInterval = null;
  }
};

// Maintenant on peut utiliser ces fonctions dans le watch
watch(() => props.autoplay, (newValue) => {
  if (newValue) {
    startAutoplay();
  } else {
    stopAutoplay();
  }
}, { immediate: true });

// Nettoyage lors de la destruction du composant
onBeforeUnmount(() => {
  stopAutoplay();
});
</script>
