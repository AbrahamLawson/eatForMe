<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <!-- Overlay de fond -->
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="close"></div>

      <!-- Contenu du modal -->
      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
        <!-- En-tête avec bouton de fermeture -->
        <div class="absolute top-0 right-0 pt-4 pr-4 z-10">
          <button type="button" @click="close" class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none">
            <span class="sr-only">Fermer</span>
            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Carrousel de photos/vidéos -->
        <MediaCarousel 
          :mediaItems="userMedia" 
          :altText="user.name"
        />

        <!-- Informations du profil -->
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="sm:flex sm:items-start">
            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
              <h3 class="text-lg leading-6 font-medium text-gray-900 flex items-center justify-between" id="modal-title">
                {{ user.name }}
                <span v-if="compatibility !== undefined" class="text-sm font-semibold px-2 py-1 rounded-full" :class="compatibilityColorClass">
                  {{ compatibility }}% match
                </span>
              </h3>
              
              <!-- Centres d'intérêt -->
              <div v-if="userInterests.length > 0" class="mt-4">
                <h4 class="text-sm font-medium text-gray-700">Centres d'intérêt</h4>
                <div class="flex flex-wrap gap-2 mt-2">
                  <span v-for="(interest, index) in userInterests" :key="index" class="bg-gray-100 rounded-full px-3 py-1 text-sm">
                    {{ interest }}
                  </span>
                </div>
              </div>
              
              <!-- Préférences culinaires -->
              <div class="mt-4">
                <h4 class="text-sm font-medium text-gray-700">Préférences culinaires</h4>
                <div class="mt-2 text-sm text-gray-600">
                  <p v-if="userCuisine">{{ userCuisine }}</p>
                  <p v-else>Pas de préférence spécifique</p>
                  <p v-if="paymentPreference" class="mt-1">{{ paymentPreference }}</p>
                </div>
              </div>
              
              <!-- Disponibilité -->
              <div class="mt-4">
                <h4 class="text-sm font-medium text-gray-700">Disponibilité</h4>
                <div class="mt-2 text-sm text-gray-600">
                  <p>{{ formatDate }}</p>
                  <p>{{ formatTimeRange }}</p>
                  <p>{{ availability.location_name }} <span v-if="userCity" class="text-xs text-gray-500">({{ userCity }})</span></p>
                </div>
              </div>
              
              <!-- Notes -->
              <div v-if="availability.notes" class="mt-4">
                <h4 class="text-sm font-medium text-gray-700">Notes</h4>
                <p class="mt-2 text-sm text-gray-600">{{ availability.notes }}</p>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Boutons d'action -->
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
          <button 
            type="button" 
            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-mint text-base font-medium text-white hover:bg-mint-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-mint sm:ml-3 sm:w-auto sm:text-sm"
            @click="proposeMeeting"
          >
            Proposer un rendez-vous
          </button>
          <button 
            type="button" 
            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
            @click="close"
          >
            Fermer
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import MediaCarousel from './MediaCarousel.vue';

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  user: {
    type: Object,
    required: true
  },
  availability: {
    type: Object,
    required: true
  },
  compatibility: {
    type: Number,
    default: undefined
  }
});

const emit = defineEmits(['close', 'propose']);

// État du carrousel
const currentSlide = ref(0);

// Réinitialiser le carrousel quand le modal s'ouvre
watch(() => props.show, (newVal) => {
  if (newVal) {
    currentSlide.value = 0;
  }
});

// Navigation dans le carrousel
const nextSlide = () => {
  currentSlide.value = (currentSlide.value + 1) % userMedia.value.length;
};

const prevSlide = () => {
  currentSlide.value = (currentSlide.value - 1 + userMedia.value.length) % userMedia.value.length;
};

// Fermer le modal
const close = () => {
  emit('close');
};

// Proposer un rendez-vous
const proposeMeeting = () => {
  emit('propose', props.availability);
  close();
};

// Médias de l'utilisateur (photos/vidéos)
const userMedia = computed(() => {
  // Simuler des médias pour le moment
  // Dans une implémentation réelle, ces données viendraient du backend
  const avatar = props.user.avatar || '/images/default-avatar.jpg';
  
  // Par défaut, au moins l'avatar
  const media = [
    { type: 'image/jpeg', url: avatar }
  ];
  
  // Ajouter des médias supplémentaires s'ils existent
  if (props.user.media && Array.isArray(props.user.media)) {
    media.push(...props.user.media);
  }
  
  return media;
});

// Vérifier le type de média
const isImage = (media) => {
  return media.type && media.type.startsWith('image/');
};

const isVideo = (media) => {
  return media.type && media.type.startsWith('video/');
};

// Récupérer les centres d'intérêt de l'utilisateur
const userInterests = computed(() => {
  if (!props.user || !props.user.preferences) return [];
  
  const preferences = props.user.preferences;
  // Si les préférences sont une chaîne JSON, les parser
  if (typeof preferences === 'string') {
    try {
      const parsedPrefs = JSON.parse(preferences);
      return parsedPrefs.interests || [];
    } catch (e) {
      return [];
    }
  }
  
  return preferences.interests || [];
});

// Récupérer le type de cuisine préféré
const userCuisine = computed(() => {
  if (!props.user || !props.user.preferences) return '';
  
  const preferences = props.user.preferences;
  let prefs;
  
  // Si les préférences sont une chaîne JSON, les parser
  if (typeof preferences === 'string') {
    try {
      prefs = JSON.parse(preferences);
    } catch (e) {
      return '';
    }
  } else {
    prefs = preferences;
  }
  
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

// Récupérer la préférence de paiement
const paymentPreference = computed(() => {
  if (!props.availability.preferences) return '';
  
  let prefs = props.availability.preferences;
  
  // Si les préférences sont une chaîne JSON, les parser
  if (typeof prefs === 'string') {
    try {
      prefs = JSON.parse(prefs);
    } catch (e) {
      return '';
    }
  }
  
  if (prefs.payment) {
    if (prefs.payment === 'dutch') return '💰 Chacun paie sa part';
    if (prefs.payment === 'invite') return '🎁 Souhaite inviter';
    if (prefs.payment === 'invited') return '🙏 Souhaite être invité(e)';
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

// Formater la date
const formatDate = computed(() => {
  if (!props.availability.date) return '';
  
  const date = new Date(props.availability.date);
  const options = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
  return date.toLocaleDateString('fr-FR', options);
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

// Déterminer la classe de couleur en fonction du score de compatibilité
const compatibilityColorClass = computed(() => {
  if (!props.compatibility) return '';
  
  const score = props.compatibility;
  
  if (score >= 80) return 'bg-green-100 text-green-800';
  if (score >= 60) return 'bg-blue-100 text-blue-800';
  if (score >= 40) return 'bg-yellow-100 text-yellow-800';
  return 'bg-gray-100 text-gray-800';
});
</script>
