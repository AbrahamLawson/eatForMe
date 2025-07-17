<template>
  <div class="flex flex-wrap gap-2">
    <Badge 
      v-for="interest in normalizedInterests" 
      :key="interest" 
      :text="interest"
      variant="secondary"
      size="sm"
    />
    <span v-if="normalizedInterests.length === 0" class="text-gray-400 text-sm">
      Aucun centre d'intérêt
    </span>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import Badge from './Badge.vue';

const props = defineProps({
  interests: {
    type: [Array, Object, String],
    default: () => []
  }
});

// Normaliser les intérêts qui peuvent être sous différentes formes (array, object, JSON string)
const normalizedInterests = computed(() => {
  if (!props.interests) return [];
  
  // Si c'est une chaîne JSON, essayer de la parser
  if (typeof props.interests === 'string') {
    try {
      const parsed = JSON.parse(props.interests);
      if (Array.isArray(parsed)) return parsed;
      if (typeof parsed === 'object') return Object.values(parsed);
      return [];
    } catch (e) {
      // Si ce n'est pas du JSON valide, considérer comme une seule valeur
      return [props.interests];
    }
  }
  
  // Si c'est déjà un tableau
  if (Array.isArray(props.interests)) {
    return props.interests;
  }
  
  // Si c'est un objet
  if (typeof props.interests === 'object') {
    return Object.values(props.interests);
  }
  
  return [];
});
</script>
