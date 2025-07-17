<template>
  <div class="bg-white rounded-xl shadow-md overflow-hidden border border-light-gray hover:shadow-lg transition-shadow duration-300">
    <!-- Image de profil et badge de disponibilité -->
    <div class="relative">
      <img 
        :src="availability.user.avatar || '/images/default-avatar.jpg'" 
        :alt="availability.user.name"
        class="w-full h-48 object-cover"
      />
      <div class="absolute top-3 right-3">
        <Badge variant="available">Dispo pour un resto</Badge>
      </div>
      
      <!-- Badge de compatibilité -->
      <div v-if="availability.compatibility !== undefined" class="absolute top-3 left-3">
        <div class="bg-white rounded-full px-2 py-1 text-xs font-semibold shadow-md" :class="compatibilityColorClass">
          {{ availability.compatibility }}% match
        </div>
      </div>
      <!-- Halo vert pour les utilisateurs disponibles maintenant -->
      <div v-if="isAvailableNow" class="absolute inset-0 border-4 border-mint rounded-t-xl"></div>
    </div>
    
    <!-- Informations de l'utilisateur -->
    <div class="p-4">
      <div class="flex justify-between items-center mb-2">
        <h3 class="font-title font-bold text-lg text-charcoal">{{ availability.user.name }}</h3>
        <span class="text-sm text-gray-500">{{ formatDistance }}</span>
      </div>
      
      <!-- Préférences de restaurant -->
      <div class="flex items-center mb-3 text-sm text-gray-600">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
        <span>{{ formatPreferences }}</span>
      </div>
      
      <!-- Type de cuisine préféré -->
      <CuisinePreference 
        v-if="userPreferences?.favorite_cuisine" 
        :cuisine="userPreferences.favorite_cuisine" 
        class="mb-3" 
      />
      
      <!-- Centres d'intérêt -->
      <div class="mt-2">
        <InterestBadges :interests="availability.user.interests" />
      </div>
      
      <!-- Horaire -->
      <div class="flex items-center mb-3 text-sm text-gray-600">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>{{ formatTimeRange }}</span>
      </div>
      
      <!-- Lieu -->
      <div class="flex items-center mb-4 text-sm text-gray-600">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
        <span>{{ availability.location_name }}</span>
        <span v-if="userCity" class="ml-1 text-xs text-gray-500">({{ userCity }})</span>
      </div>
      
      <!-- Boutons d'action -->
      <div class="flex space-x-2">
        <Button variant="primary" size="sm" class="flex-1" @click="proposeMeeting">Proposer</Button>
        <Button variant="outline" size="sm" class="flex-1" @click="showProfileModal = true">Profil</Button>
      </div>
      
      <!-- Modal de profil utilisateur -->
      <UserProfileModal 
        :show="showProfileModal" 
        :user="availability.user" 
        :availability="availability" 
        :compatibility="availability.compatibility"
        @close="showProfileModal = false"
        @propose="proposeMeeting"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import Badge from './Badge.vue';
import Button from './Button.vue';
import UserProfileModal from './UserProfileModal.vue';
import InterestBadges from './InterestBadges.vue';
import CuisinePreference from './CuisinePreference.vue';

const props = defineProps({
  availability: {
    type: Object,
    required: true
  },
  distance: {
    type: Number,
    default: null
  }
});

const emit = defineEmits(['propose']);

// État pour le modal de profil
const showProfileModal = ref(false);

// Fonction pour proposer un rendez-vous
const proposeMeeting = () => {
  emit('propose', props.availability);
};

// Formater la distance
const formatDistance = computed(() => {
  if (props.distance === null) return '';
  return props.distance < 1 
    ? `${Math.round(props.distance * 1000)} m` 
    : `${props.distance.toFixed(1)} km`;
});

// Vérifier si l'utilisateur est disponible maintenant
const isAvailableNow = computed(() => {
  const now = new Date();
  const startTime = new Date(`${props.availability.date}T${props.availability.start_time}`);
  const endTime = new Date(`${props.availability.date}T${props.availability.end_time}`);
  return now >= startTime && now <= endTime;
});

// Formater la plage horaire
const formatTimeRange = computed(() => {
  // Convertir les heures au format 24h en format 12h
  const formatTime = (timeStr) => {
    const [hours, minutes] = timeStr.split(':');
    const h = parseInt(hours);
    const ampm = h >= 12 ? 'PM' : 'AM';
    const hour12 = h % 12 || 12;
    return `${hour12}:${minutes} ${ampm}`;
  };
  
  return `${formatTime(props.availability.start_time)} - ${formatTime(props.availability.end_time)}`;
});

// Formater les préférences
const formatPreferences = computed(() => {
  if (!props.availability.preferences) return 'Pas de préférences';
  
  let prefs = props.availability.preferences;
  
  // Si les préférences sont une chaîne JSON, les parser
  if (typeof prefs === 'string') {
    try {
      prefs = JSON.parse(prefs);
    } catch (e) {
      return 'Pas de préférences';
    }
  }
  
    // Émojis pour les types de cuisine
    const cuisineEmojis = {
      'italian': '🍝',
      'japanese': '🍣',
      'french': '🥐',
      'american': '🍔',
      'mexican': '🌮',
      'chinese': '🥢',
      'indian': '🍛',
      'thai': '🍜',
      'vegetarian': '🥗',
      'vegan': '🌱'
    };
    
    // Formater qui paie
    let paymentInfo = '';
    if (prefs.payment) {
      if (prefs.payment === 'dutch') paymentInfo = '(Chacun paie)';
      else if (prefs.payment === 'invite') paymentInfo = '(Invite)';
      else if (prefs.payment === 'invited') paymentInfo = '(Souhaite être invité)';
    }
    
    // Formater le type de cuisine
    let cuisineInfo = '';
    if (prefs.cuisine && prefs.cuisine.length > 0) {
      cuisineInfo = prefs.cuisine.map(c => cuisineEmojis[c] || c).join(' ');
    }
    
    return [cuisineInfo, paymentInfo].filter(Boolean).join(' ');
});

// Déterminer la classe de couleur en fonction du score de compatibilité
const compatibilityColorClass = computed(() => {
  if (!props.availability.compatibility) return '';
  
  const score = props.availability.compatibility;
  
  if (score >= 80) return 'text-green-600';
  if (score >= 60) return 'text-blue-600';
  if (score >= 40) return 'text-yellow-600';
  return 'text-gray-600';
});

// Récupérer les préférences utilisateur normalisées
const userPreferences = computed(() => {
  if (!props.availability.user || !props.availability.user.preferences) return {};
  
  const preferences = props.availability.user.preferences;
  // Si les préférences sont une chaîne JSON, les parser
  if (typeof preferences === 'string') {
    try {
      return JSON.parse(preferences);
    } catch (e) {
      return {};
    }
  }
  
  return preferences;
});

// Récupérer les centres d'intérêt de l'utilisateur
const userInterests = computed(() => {
  return userPreferences.value?.interests || [];
});

// Récupérer le type de cuisine préféré
const userCuisine = computed(() => {
  // Utiliser directement userPreferences
  const prefs = userPreferences.value;
  if (!prefs) return '';
  
  // Émojis pour les types de cuisine
  const cuisineEmojis = {
    'italian': '🍝 Italien',
    'japanese': '🍣 Japonais',
    'french': '🥐 Français',
    'american': '🍔 Américain',
    'mexican': '🌮 Mexicain',
    'chinese': '🥢 Chinois',
    'indian': '🍛 Indien',
    'thai': '🍜 Thaï',
    'vegetarian': '🥗 Végétarien',
    'vegan': '🌱 Végan'
  };
  
  if (prefs.favorite_cuisine) {
    return cuisineEmojis[prefs.favorite_cuisine] || prefs.favorite_cuisine;
  }
  
  return '';
});

// Récupérer la ville de l'utilisateur basée sur la localisation
const userCity = computed(() => {
  if (!props.availability.location_address) return '';
  
  // Extraire la ville de l'adresse (format attendu: "Code postal Ville")
  const addressParts = props.availability.location_address.split(' ');
  if (addressParts.length >= 2) {
    // Supposer que le premier élément est le code postal et le reste est la ville
    return addressParts.slice(1).join(' ');
  }
  
  return props.availability.location_address;
});
</script>
