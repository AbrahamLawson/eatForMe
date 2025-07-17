<template>
  <div class="flex items-start text-sm text-gray-600">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
    </svg>
    <div>
      <span class="mr-1">{{ cuisineEmoji }}</span>
      <span>{{ cuisineName }}</span>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  cuisine: {
    type: String,
    default: ''
  }
});

// Mapping des types de cuisine avec leurs émojis
const cuisineTypes = {
  italian: { name: 'Italienne', emoji: '🍝' },
  japanese: { name: 'Japonaise', emoji: '🍣' },
  french: { name: 'Française', emoji: '🥐' },
  american: { name: 'Américaine', emoji: '🍔' },
  mexican: { name: 'Mexicaine', emoji: '🌮' },
  chinese: { name: 'Chinoise', emoji: '🥢' },
  indian: { name: 'Indienne', emoji: '🍛' },
  thai: { name: 'Thaïlandaise', emoji: '🍲' },
  mediterranean: { name: 'Méditerranéenne', emoji: '🫒' },
  vegan: { name: 'Végétalienne', emoji: '🥗' },
  vegetarian: { name: 'Végétarienne', emoji: '🥦' },
  seafood: { name: 'Fruits de mer', emoji: '🦞' },
  bbq: { name: 'Barbecue', emoji: '🍖' },
  dessert: { name: 'Desserts', emoji: '🍰' },
  breakfast: { name: 'Petit-déjeuner', emoji: '🥞' },
  coffee: { name: 'Café', emoji: '☕' },
  wine: { name: 'Vin', emoji: '🍷' },
  beer: { name: 'Bière', emoji: '🍺' },
  cocktail: { name: 'Cocktails', emoji: '🍸' },
  fastfood: { name: 'Fast-food', emoji: '🍟' }
};

// Obtenir le nom et l'emoji pour le type de cuisine
const cuisineName = computed(() => {
  if (!props.cuisine) return 'Non spécifié';
  return cuisineTypes[props.cuisine]?.name || props.cuisine;
});

const cuisineEmoji = computed(() => {
  if (!props.cuisine) return '🍽️';
  return cuisineTypes[props.cuisine]?.emoji || '🍽️';
});
</script>
