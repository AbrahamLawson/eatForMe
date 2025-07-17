<template>
  <Head title="Recherche active" />
  <AuthenticatedLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-title font-bold text-xl text-charcoal leading-tight">Recherche active</h2>
      </div>
    </template>
    
    <!-- Modal des matchs potentiels -->
    <MatchModal 
      :show="showMatchModal" 
      :matches="potentialMatches" 
      title="Matchs potentiels pour vous"
      @close="closeMatchModal"
      @match-accepted="handleMatchAccepted"
      @match-rejected="handleMatchRejected"
    />

    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Statut de recherche -->
        <div v-if="isSearching" class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-4 bg-white border-b border-light-gray">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="font-title font-bold text-lg text-charcoal">Recherche en cours...</h3>
                <p class="text-gray-500">Nous cherchons des personnes correspondant à vos critères</p>
              </div>
              <div class="animate-pulse">
                <div class="h-10 w-10 bg-raspberry rounded-full flex items-center justify-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                  </svg>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Carte -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-4 bg-white border-b border-light-gray">
            <div class="relative">
              <div class="w-full h-96 rounded-lg">
                <!-- Utilisation des balises personnalisées Google Maps -->
                <gmp-map v-if="userPosition" 
                  :center="`${userPosition.lat},${userPosition.lng}`" 
                  :zoom="14" 
                  map-id="eat-me-map"
                  class="w-full h-full rounded-lg">
                  <gmp-advanced-marker 
                    :position="`${userPosition.lat},${userPosition.lng}`" 
                    title="Votre position">
                  </gmp-advanced-marker>
                </gmp-map>
              </div>
              <SearchAnimation :active="isSearching">
                Recherche de personnes à proximité...
              </SearchAnimation>
            </div>
          </div>
        </div>

        <!-- Formulaire de recherche -->
        <!-- Préférences de recherche active -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-6 bg-white border-b border-light-gray">
            <UpdatePreferencesForm :preferences="userPreferences" />
          </div>
        </div>

        <!-- Critères de recherche -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-light-gray">
            <h3 class="font-title font-bold text-lg text-charcoal mb-4">Critères de recherche</h3>

            <form @submit.prevent="startSearch">
              <!-- Type d'activité -->
              <div class="mb-4">
                <label class="block text-sm font-medium text-charcoal mb-2">Que voulez-vous faire ?</label>
                <div class="flex flex-wrap gap-3">
                  <button
                    type="button"
                    v-for="activity in activities"
                    :key="activity.value"
                    @click="searchCriteria.activity = activity.value"
                    :class="[
                      'px-4 py-2 rounded-full border transition-colors',
                      searchCriteria.activity === activity.value
                        ? 'bg-raspberry text-white border-raspberry'
                        : 'bg-white text-charcoal border-light-gray hover:border-raspberry'
                    ]"
                  >
                    {{ activity.label }}
                  </button>
                </div>
              </div>

              <!-- Type de profil -->
              <div class="mb-4">
                <label class="block text-sm font-medium text-charcoal mb-2">Quel type de profil ?</label>
                <div class="flex flex-wrap gap-3">
                  <button
                    type="button"
                    v-for="profile in profiles"
                    :key="profile.value"
                    @click="searchCriteria.profile = profile.value"
                    :class="[
                      'px-4 py-2 rounded-full border transition-colors',
                      searchCriteria.profile === profile.value
                        ? 'bg-raspberry text-white border-raspberry'
                        : 'bg-white text-charcoal border-light-gray hover:border-raspberry'
                    ]"
                  >
                    {{ profile.label }}
                  </button>
                </div>
              </div>

              <!-- Distance -->
              <div class="mb-6">
                <label for="distance" class="block text-sm font-medium text-charcoal mb-2">Distance maximale</label>
                <div class="flex items-center gap-4">
                  <input
                    type="range"
                    id="distance"
                    v-model="searchCriteria.distance"
                    min="1"
                    max="20"
                    step="1"
                    class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer"
                  />
                  <span class="text-sm font-medium text-charcoal">{{ searchCriteria.distance }} km</span>
                </div>
              </div>
              
              <!-- Switch pour activer/désactiver la recherche -->
              <Switch 
                v-model="searchEnabled" 
                label="Activer la recherche" 
                description="Activez cette option pour permettre la recherche de personnes à proximité"
                @change="onSearchEnabledChange" 
                class="mb-6"
              />
              
              <!-- Bouton de recherche -->
              <div class="flex justify-center">
                <Button
                  type="submit"
                  variant="primary"
                  size="lg"
                  :disabled="isSearching || !searchEnabled"
                >
                  {{ isSearching ? 'Recherche en cours...' : 'Lancer la recherche' }}
                </Button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import UpdatePreferencesForm from './Partials/UpdatePreferencesForm.vue';
import MatchModal from '@/Components/EatMe/MatchModal.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Button from '@/Components/EatMe/Button.vue';
import Switch from '@/Components/EatMe/Switch.vue';
import SearchAnimation from '@/Components/EatMe/SearchAnimation.vue';

// Récupérer la clé API Google Maps et les préférences utilisateur depuis les props
const props = defineProps({
  googleMapsApiKey: String,
  userPreferences: Object
});

// Accéder aux props via usePage() si nécessaire
const page = usePage();

// État de la recherche
const isSearching = ref(false);
const searchEnabled = ref(false);
const userPosition = ref(null);
const map = ref(null);
const userMarker = ref(null); // Ajout de la déclaration de userMarker
const searchCircle = ref(null);
const nearbyUsersMarkers = ref([]);
const showMatchModal = ref(false);
const potentialMatches = ref([]);

// Critères de recherche
const searchCriteria = ref({
  activity: 'eat',
  profile: 'any',
  distance: 5
});

// Options pour les activités
const activities = [
  { value: 'eat', label: 'Manger' },
  { value: 'drink', label: 'Boire un verre' },
  { value: 'chat', label: 'Se changer les idées' }
];

// Options pour les profils
const profiles = [
  { value: 'any', label: 'Aléatoire' },
  { value: 'male', label: 'Homme' },
  { value: 'female', label: 'Femme' }
];

// Fonction pour démarrer la recherche
const startSearch = () => {
  if (!searchEnabled.value) {
    alert('Veuillez activer la recherche en utilisant le switch en haut de la page');
    return;
  }
  
  // Vérifier si la position de l'utilisateur est disponible
  if (!userPosition.value) {
    alert('Impossible de déterminer votre position. Veuillez autoriser la géolocalisation.');
    return;
  }
  
  isSearching.value = true;
  clearNearbyUsersMarkers(); // Effacer les marqueurs existants

  // Appel à l'API de recherche active
  router.post(route('active-search.search'), {
    latitude: userPosition.value.lat,
    longitude: userPosition.value.lng,
    distance: searchCriteria.value.distance,
    activity: searchCriteria.value.activity,
    profile: searchCriteria.value.profile
  }, {
    preserveState: true,
    onSuccess: (response) => {
      // Ajouter les utilisateurs trouvés sur la carte
      const nearbyUsers = response.props.data;
      
      if (nearbyUsers && nearbyUsers.length > 0) {
        addNearbyUsersToMap(nearbyUsers);
        
        // Récupérer les matchs potentiels depuis la réponse
        if (response.props.potentialMatches && response.props.potentialMatches.length > 0) {
          potentialMatches.value = response.props.potentialMatches;
          // Afficher automatiquement le modal des matchs potentiels
          showMatchModal.value = true;
        }
      } else {
        alert('Aucun utilisateur correspondant à vos critères n\'a été trouvé à proximité.');
      }
      
      // Terminer la recherche avec un délai pour le fade out de l'animation
      setTimeout(() => {
        isSearching.value = false;
      }, 1000);
    },
    onError: (errors) => {
      console.error('Erreur lors de la recherche:', errors);
      alert('Une erreur est survenue lors de la recherche. Veuillez réessayer.');
      isSearching.value = false;
    }
  });
};

// Fonction pour arrêter la recherche
const stopSearch = () => {
  isSearching.value = false;
  searchEnabled.value = false;
  clearNearbyUsersMarkers();
};

// Fonction pour fermer le modal des matchs
const closeMatchModal = () => {
  showMatchModal.value = false;
};

// Fonction pour gérer l'acceptation d'un match
const handleMatchAccepted = (match) => {
  // Retirer le match accepté de la liste
  potentialMatches.value = potentialMatches.value.filter(m => m.id !== match.id);
  
  // Afficher un message de confirmation
  alert(`Vous avez accepté le match avec ${match.name}. Vous pouvez maintenant communiquer avec cette personne.`);
  
  // Fermer le modal si plus aucun match potentiel
  if (potentialMatches.value.length === 0) {
    closeMatchModal();
  }
};

// Fonction pour gérer le rejet d'un match
const handleMatchRejected = (match) => {
  // Retirer le match rejeté de la liste
  potentialMatches.value = potentialMatches.value.filter(m => m.id !== match.id);
  
  // Fermer le modal si plus aucun match potentiel
  if (potentialMatches.value.length === 0) {
    closeMatchModal();
  }
};

// Fonction pour obtenir la position de l'utilisateur
const getUserPosition = () => {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
      (position) => {
        userPosition.value = {
          lat: position.coords.latitude,
          lng: position.coords.longitude
        };
        // Avec la nouvelle approche, nous n'avons plus besoin d'initialiser la carte manuellement
        // car elle est gérée par les balises <gmp-map>
        console.log('Position utilisateur obtenue:', userPosition.value);
      },
      (error) => {
        console.error('Erreur de géolocalisation:', error);
        // Position par défaut (Paris)
        userPosition.value = { lat: 48.8566, lng: 2.3522 };
        console.log('Position par défaut utilisée:', userPosition.value);
      }
    );
  } else {
    console.error('La géolocalisation n\'est pas supportée par ce navigateur.');
    // Position par défaut (Paris)
    userPosition.value = { lat: 48.8566, lng: 2.3522 };
    console.log('Position par défaut utilisée:', userPosition.value);
  }
};

// Fonction pour charger l'API Google Maps avec la nouvelle approche
const loadGoogleMapsApi = () => {
  // Vérifier si le script est déjà chargé
  if (document.querySelector('script[src*="maps.googleapis.com/maps/api/js"]')) {
    return;
  }

  // Récupérer la clé API Google Maps depuis les props ou depuis page.props
  const apiKey = props.googleMapsApiKey || page.props.googleMapsApiKey;
  
  // Vérifier si la clé API est définie
  if (!apiKey) {
    console.error('Erreur: Clé API Google Maps non définie. Vérifiez votre fichier .env');
    return;
  }

  // Créer le script avec loading=async pour de meilleures performances
  const script = document.createElement('script');
  script.async = true;
  script.src = `https://maps.googleapis.com/maps/api/js?key=${apiKey}&callback=console.debug&libraries=maps,marker&v=beta&loading=async`;
  document.head.appendChild(script);
  
  console.log('Script Google Maps chargé avec la clé API et loading=async');
};

// Fonction pour mettre à jour le cercle de recherche
const updateSearchCircle = () => {
  // Avec la nouvelle approche, nous devrons implémenter le cercle de recherche différemment
  // Pour l'instant, nous affichons simplement un message dans la console
  console.log(`Zone de recherche mise à jour : ${searchCriteria.value.distance} km`);
};

// Fonction pour ajouter des utilisateurs à proximité sur la carte
const addNearbyUsersToMap = (users) => {
  // Avec la nouvelle approche, nous devrons implémenter l'ajout de marqueurs différemment
  console.log(`${users.length} utilisateurs trouvés à proximité`);
  
  // Stocker les utilisateurs pour pouvoir les afficher dans le modal
  nearbyUsersMarkers.value = users;
  
  // Si nous avons des utilisateurs, nous pouvons les afficher dans le modal
  if (users.length > 0) {
    // Mettre à jour la liste des matchs potentiels
    potentialMatches.value = users.map(user => ({
      id: user.id,
      name: user.name,
      age: user.age || 'Non spécifié',
      distance: user.distance || 'Proche',
      profilePicture: user.profile_picture || '/images/default-avatar.png',
      bio: user.bio || 'Aucune bio disponible'
    }));
    
    // Afficher le modal des matchs
    showMatchModal.value = true;
  }
};

// Fonction pour afficher le dialogue de match pour un utilisateur spécifique
const showMatchDialog = (user) => {
  // Créer un objet match pour l'utilisateur sélectionné
  const match = {
    id: user.id,
    name: user.name,
    age: user.age || 'Non spécifié',
    distance: user.distance || 'Proche',
    profilePicture: user.profile_picture || '/images/default-avatar.png',
    bio: user.bio || 'Aucune bio disponible'
  };
  
  // Mettre à jour la liste des matchs potentiels avec cet utilisateur uniquement
  potentialMatches.value = [match];
  
  // Afficher le modal
  showMatchModal.value = true;
};

// Fonction pour effacer tous les marqueurs d'utilisateurs à proximité
const clearNearbyUsersMarkers = () => {
  // Avec la nouvelle approche, nous n'avons plus besoin de nettoyer les marqueurs manuellement
  // car ils sont gérés par les balises <gmp-advanced-marker>
  nearbyUsersMarkers.value = [];
};

// Fonction pour traduire le type d'activité
const translateActivity = (activityType) => {
  const activityMap = {
    'eat': 'Manger',
    'drink': 'Boire un verre',
    'chat': 'Se changer les idées'
  };
  return activityMap[activityType] || activityType;
};

// Fonction pour formater une date
const formatDate = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('fr-FR', { 
    weekday: 'long', 
    day: 'numeric', 
    month: 'long', 
    hour: '2-digit', 
    minute: '2-digit' 
  });
};

// La fonction updateSearchCircle est déjà définie plus haut dans le code

// Fonction appelée lorsque le switch de recherche change d'état
const onSearchEnabledChange = (enabled) => {
  if (!enabled && isSearching.value) {
    stopSearch();
  }
};

// Surveiller les changements de distance pour mettre à jour le cercle
watch(() => searchCriteria.value.distance, updateSearchCircle);

// Charger la carte au montage du composant
onMounted(() => {
  // Charger l'API Google Maps avec la nouvelle approche
  loadGoogleMapsApi();
  
  // Obtenir la position de l'utilisateur
  getUserPosition();
});

// Nettoyer au démontage du composant
onUnmounted(() => {
  if (isSearching.value) {
    stopSearch();
  }
});
</script>
