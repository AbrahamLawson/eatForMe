<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
      <!-- Overlay de fond -->
      <div class="fixed inset-0 transition-opacity" @click="closeModal">
        <div class="absolute inset-0 bg-charcoal opacity-75"></div>
      </div>

      <!-- Modal -->
      <div 
        class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
        role="dialog"
        aria-modal="true"
        aria-labelledby="modal-headline"
      >
        <!-- En-tête du modal -->
        <div class="bg-raspberry text-white px-4 py-3 flex justify-between items-center">
          <h3 class="text-xl font-title font-bold" id="modal-headline">
            {{ title }}
          </h3>
          <button @click="closeModal" class="focus:outline-none">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Corps du modal -->
        <div class="bg-white px-4 py-5 sm:p-6">
          <!-- Si aucun match n'est disponible -->
          <div v-if="!matches || matches.length === 0" class="text-center py-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <p class="text-lg font-medium text-charcoal">Aucun match potentiel trouvé</p>
            <p class="text-gray-500 mt-1">Essayez d'élargir vos critères de recherche</p>
          </div>

          <!-- Liste des matchs potentiels -->
          <div v-else class="space-y-6">
            <div v-for="(match, index) in matches" :key="index" class="bg-white rounded-lg shadow-md overflow-hidden border border-light-gray">
              <div class="flex items-center p-4">
                <!-- Photo de profil -->
                <div class="flex-shrink-0">
                  <img 
                    :src="match.profile_picture || '/images/default-avatar.png'" 
                    :alt="match.name" 
                    class="h-16 w-16 rounded-full object-cover border-2 border-mint"
                  >
                </div>
                
                <!-- Informations du match -->
                <div class="ml-4 flex-grow">
                  <h4 class="font-title font-bold text-lg text-charcoal">{{ match.name }}</h4>
                  <div class="flex flex-wrap gap-2 mt-1">
                    <!-- Score de compatibilité -->
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-mint text-white">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                      </svg>
                      {{ Math.round(match.compatibility_score * 100) }}% compatible
                    </span>
                    
                    <!-- Distance -->
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-charcoal">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                      </svg>
                      {{ formatDistance(match.distance) }}
                    </span>
                    
                    <!-- Activité -->
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                      {{ translateActivity(match.activity) }}
                    </span>
                  </div>
                </div>
                
                <!-- Boutons d'action -->
                <div class="ml-4 flex-shrink-0 flex space-x-2">
                  <!-- Refuser -->
                  <button 
                    @click="rejectMatch(match)" 
                    class="p-2 rounded-full bg-white border border-light-gray hover:bg-gray-100 transition-colors"
                    title="Refuser"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  </button>
                  
                  <!-- Accepter -->
                  <button 
                    @click="acceptMatch(match)" 
                    class="p-2 rounded-full bg-raspberry text-white hover:bg-raspberry-dark transition-colors"
                    title="Accepter"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                  </button>
                </div>
              </div>
              
              <!-- Disponibilités -->
              <div class="px-4 py-3 bg-gray-50 border-t border-light-gray">
                <h5 class="text-sm font-medium text-charcoal mb-2">Disponibilités</h5>
                <div class="flex flex-wrap gap-2">
                  <span 
                    v-for="(availability, i) in match.availabilities" 
                    :key="i"
                    class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-white border border-light-gray text-charcoal"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-mint" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    {{ formatDate(availability.date) }} {{ availability.start_time }} - {{ availability.end_time }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Pied du modal -->
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
          <button 
            @click="closeModal" 
            type="button" 
            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-gray-300 text-base font-medium text-charcoal hover:bg-gray-400 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm"
          >
            Fermer
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, defineProps, defineEmits, watch } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  matches: {
    type: Array,
    default: () => []
  },
  title: {
    type: String,
    default: 'Matchs potentiels'
  }
});

const emit = defineEmits(['close', 'match-accepted', 'match-rejected']);

// Fermer le modal
const closeModal = () => {
  emit('close');
};

// Accepter un match
const acceptMatch = (match) => {
  router.post(route('matches.create'), {
    match_user_id: match.id,
    availability_id: match.availability_id
  }, {
    preserveState: true,
    onSuccess: () => {
      emit('match-accepted', match);
    },
    onError: (errors) => {
      console.error('Erreur lors de la création du match:', errors);
      alert('Une erreur est survenue lors de la création du match. Veuillez réessayer.');
    }
  });
};

// Refuser un match
const rejectMatch = (match) => {
  // Simplement émettre l'événement pour que le composant parent puisse gérer le rejet
  emit('match-rejected', match);
};

// Formater la distance
const formatDistance = (distance) => {
  if (distance < 1) {
    return `${Math.round(distance * 1000)} m`;
  }
  return `${Math.round(distance * 10) / 10} km`;
};

// Traduire le type d'activité
const translateActivity = (activityType) => {
  const activities = {
    'coffee': 'Café',
    'lunch': 'Déjeuner',
    'dinner': 'Dîner',
    'drinks': 'Apéro',
    'any': 'Peu importe'
  };
  
  return activities[activityType] || activityType;
};

// Formater une date
const formatDate = (dateString) => {
  const options = { weekday: 'long', day: 'numeric', month: 'long' };
  const date = new Date(dateString);
  return date.toLocaleDateString('fr-FR', options);
};
</script>