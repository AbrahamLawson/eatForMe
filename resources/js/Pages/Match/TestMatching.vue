<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Test de Matching
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Tester le matching entre deux utilisateurs</h3>
          
          <!-- Formulaire de test -->
          <form @submit.prevent="testMatching">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
              <!-- Utilisateur 1 -->
              <div>
                <label for="user1" class="block text-sm font-medium text-gray-700">Utilisateur 1</label>
                <select
                  id="user1"
                  v-model="form.user1_id"
                  class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                  required
                >
                  <option value="" disabled>Sélectionner un utilisateur</option>
                  <option v-for="user in users" :key="user.id" :value="user.id">
                    {{ user.name }} ({{ user.email }})
                    {{ user.has_availability ? '✅' : '❌' }}
                  </option>
                </select>
              </div>
              
              <!-- Utilisateur 2 -->
              <div>
                <label for="user2" class="block text-sm font-medium text-gray-700">Utilisateur 2</label>
                <select
                  id="user2"
                  v-model="form.user2_id"
                  class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                  required
                >
                  <option value="" disabled>Sélectionner un utilisateur</option>
                  <option v-for="user in users" :key="user.id" :value="user.id">
                    {{ user.name }} ({{ user.email }})
                    {{ user.has_availability ? '✅' : '❌' }}
                  </option>
                </select>
              </div>
              
              <!-- Activité utilisateur 1 -->
              <div>
                <label for="activity1" class="block text-sm font-medium text-gray-700">Activité (Utilisateur 1)</label>
                <select
                  id="activity1"
                  v-model="form.activity1"
                  class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                  required
                >
                  <option value="" disabled>Sélectionner une activité</option>
                  <option value="eat">Manger</option>
                  <option value="drink">Boire</option>
                  <option value="chat">Discuter</option>
                </select>
              </div>
              
              <!-- Distance utilisateur 1 -->
              <div class="mb-4">
                <label for="distance1" class="block text-sm font-medium text-gray-700">Distance max utilisateur 1 (km)</label>
                <input
                  id="distance1"
                  v-model="form.distance1"
                  type="number"
                  min="0.1"
                  max="500"
                  step="0.1"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
              </div>
              
              <!-- Activité utilisateur 2 -->
              <div>
                <label for="activity2" class="block text-sm font-medium text-gray-700">Activité (Utilisateur 2)</label>
                <select
                  id="activity2"
                  v-model="form.activity2"
                  class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                  required
                >
                  <option value="" disabled>Sélectionner une activité</option>
                  <option value="eat">Manger</option>
                  <option value="drink">Boire</option>
                  <option value="chat">Discuter</option>
                </select>
              </div>
              
              <!-- Distance utilisateur 2 -->
              <div>
                <label for="distance2" class="block text-sm font-medium text-gray-700">Distance max (km) - Utilisateur 2</label>
                <input
                  id="distance2"
                  type="number"
                  v-model="form.distance2"
                  min="0.1"
                  max="500"
                  step="0.1"
                  class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                  required
                />
              </div>
            </div>
            
            <div class="flex justify-end">
              <button
                type="submit"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                :disabled="processing"
              >
                <span v-if="processing">Traitement en cours...</span>
                <span v-else>Tester le matching</span>
              </button>
            </div>
          </form>
          
          <!-- Message d'erreur -->
          <div v-if="error" class="mt-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
            {{ error }}
          </div>
          
          <!-- Résultats du test -->
          <div v-if="testResults" class="mt-6 border-t border-gray-200 pt-6">
            <h4 class="text-lg font-medium text-gray-900 mb-4">Résultats du test</h4>
            
            <div class="bg-gray-50 p-4 rounded-md">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                  <h5 class="font-medium">Utilisateur 1</h5>
                  <p>{{ testResults.user1.name }}</p>
                  <p class="text-sm">Activité: {{ activityLabel(testResults.user1.activity) }}</p>
                  <p class="text-sm">Distance max: {{ testResults.user1.distance }} km</p>
                  <p class="text-sm" :class="testResults.user1.inRange ? 'text-green-600' : 'text-red-600'">
                    {{ testResults.user1.inRange ? 'Distance acceptable' : 'Distance trop grande' }}
                  </p>
                </div>
                <div>
                  <h5 class="font-medium">Utilisateur 2</h5>
                  <p>{{ testResults.user2.name }}</p>
                  <p class="text-sm">Activité: {{ activityLabel(testResults.user2.activity) }}</p>
                  <p class="text-sm">Distance max: {{ testResults.user2.distance }} km</p>
                  <p class="text-sm" :class="testResults.user2.inRange ? 'text-green-600' : 'text-red-600'">
                    {{ testResults.user2.inRange ? 'Distance acceptable' : 'Distance trop grande' }}
                  </p>
                </div>
                <div>
                  <h5 class="font-medium">Compatibilité d'activité</h5>
                  <p :class="testResults.activitiesMatch ? 'text-green-600' : 'text-red-600'">
                    {{ testResults.activitiesMatch ? 'Activités compatibles' : 'Activités différentes' }}
                  </p>
                </div>
                <div>
                  <h5 class="font-medium">Distance réelle</h5>
                  <p>{{ testResults.distance.toFixed(2) }} km</p>
                </div>
              </div>
              
              <div class="mt-4">
                <h4 class="font-medium text-lg">Résultat du test</h4>
                <div class="mt-2">
                  <div :class="testResults.isCompatible ? 'text-green-600' : 'text-red-600'" class="font-medium">
                    {{ testResults.isCompatible ? 'Compatible' : 'Non compatible' }}
                  </div>
                  <div class="mt-1">
                    Score de compatibilité: 
                    <span :class="{
                      'text-green-600': testResults.compatibilityScore >= 70,
                      'text-yellow-600': testResults.compatibilityScore >= 40 && testResults.compatibilityScore < 70,
                      'text-red-600': testResults.compatibilityScore < 40
                    }">
                      {{ testResults.compatibilityScore }}/100
                    </span>
                    <span class="text-sm text-gray-500 ml-2">(Utilisé pour classer les matchs potentiels)</span>
                  </div>
                </div>
              </div>
              
              <div class="mt-4 p-4" :class="testResults.isCompatible ? 'bg-green-100' : 'bg-red-100'">
                <h5 class="font-medium">Compatibilité</h5>
                <p v-if="testResults.isCompatible">
                  Les utilisateurs sont compatibles avec un score de {{ testResults.compatibilityScore }}/100
                </p>
                <p v-else>
                  Les utilisateurs ne sont pas compatibles
                </p>
              </div>
              
              <div v-if="testResults.matchCreated" class="mt-4 p-4 bg-blue-100">
                <h5 class="font-medium">Match créé</h5>
                <p>ID: {{ testResults.matchDetails.id }}</p>
                <p>Statut: {{ testResults.matchDetails.status }}</p>
                <p>Créé le: {{ testResults.matchDetails.created_at }}</p>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Gestion des disponibilités -->
        <div class="mt-8 bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Gérer ma disponibilité</h3>
          
          <!-- Message de succès -->
          <div v-if="$page.props.flash && $page.props.flash.success" class="mb-4 p-4 bg-green-100 text-green-700 rounded">
            {{ $page.props.flash.success }}
          </div>
          
          <!-- Formulaire de disponibilité -->
          <form @submit.prevent="updateAvailability">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
              <!-- Utilisateur -->
              <div>
                <label for="availability_user" class="block text-sm font-medium text-gray-700">Utilisateur</label>
                <select
                  id="availability_user"
                  v-model="availabilityForm.user_id"
                  class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                  required
                >
                  <option value="" disabled>Sélectionner un utilisateur</option>
                  <option v-for="user in users" :key="user.id" :value="user.id">
                    {{ user.name }} ({{ user.email }})
                    {{ user.has_availability ? '✅' : '❌' }}
                  </option>
                </select>
              </div>
              
              <!-- Statut de disponibilité -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                <div class="flex items-center">
                  <label class="inline-flex items-center mr-6">
                    <input type="radio" v-model="availabilityForm.is_active" :value="true" class="form-radio h-5 w-5 text-indigo-600">
                    <span class="ml-2 text-gray-700">Disponible</span>
                  </label>
                  <label class="inline-flex items-center">
                    <input type="radio" v-model="availabilityForm.is_active" :value="false" class="form-radio h-5 w-5 text-indigo-600">
                    <span class="ml-2 text-gray-700">Non disponible</span>
                  </label>
                </div>
              </div>
              
              <!-- Activité -->
              <div>
                <label for="availability_activity" class="block text-sm font-medium text-gray-700">Activité</label>
                <select
                  id="availability_activity"
                  v-model="availabilityForm.activity"
                  class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                  required
                >
                  <option value="" disabled>Sélectionner une activité</option>
                  <option value="eat">Manger</option>
                  <option value="drink">Boire</option>
                  <option value="chat">Discuter</option>
                </select>
              </div>
              
              <!-- Position géographique -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Position géographique</label>
                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <label class="block text-xs text-gray-600 mb-1">Latitude</label>
                    <input 
                      type="number" 
                      v-model="availabilityForm.latitude" 
                      step="0.000001" 
                      class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                      required
                    >
                  </div>
                  <div>
                    <label class="block text-xs text-gray-600 mb-1">Longitude</label>
                    <input 
                      type="number" 
                      v-model="availabilityForm.longitude" 
                      step="0.000001" 
                      class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                      required
                    >
                  </div>
                </div>
                <p class="text-xs text-gray-500 mt-1">
                  <button type="button" @click="useCurrentLocation" class="text-indigo-600 hover:text-indigo-800">
                    Utiliser ma position actuelle
                  </button>
                  ou utiliser les coordonnées de l'utilisateur sélectionné
                </p>
              </div>
            </div>
            
            <div class="flex justify-end">
              <button
                type="submit"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                :disabled="availabilityProcessing"
              >
                <span v-if="availabilityProcessing">Traitement en cours...</span>
                <span v-else>Mettre à jour la disponibilité</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

// Props
const props = defineProps({
  users: Array,
  error: String,
  testResults: Object
});

// État du formulaire de test de matching
const form = useForm({
  user1_id: '',
  user2_id: '',
  activity1: '',
  activity2: '',
  distance1: 5,
  distance2: 5
});

const processing = ref(false);

// État du formulaire de disponibilité
const availabilityForm = useForm({
  user_id: '',
  is_active: true,
  activity: 'eat',
  latitude: 48.8566, // Paris par défaut
  longitude: 2.3522
});

const availabilityProcessing = ref(false);

// Méthodes pour le test de matching
const testMatching = () => {
  processing.value = true;
  form.post(route('match.test'), {
    preserveScroll: true,
    onFinish: () => {
      processing.value = false;
    }
  });
};

// Méthodes pour la gestion des disponibilités
const updateAvailability = () => {
  availabilityProcessing.value = true;
  availabilityForm.post(route('match.test.availability'), {
    preserveScroll: true,
    onFinish: () => {
      availabilityProcessing.value = false;
    }
  });
};

// Utiliser la position actuelle du navigateur
const useCurrentLocation = () => {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
      (position) => {
        availabilityForm.latitude = position.coords.latitude;
        availabilityForm.longitude = position.coords.longitude;
      },
      (error) => {
        alert('Impossible de récupérer votre position: ' + error.message);
      }
    );
  } else {
    alert('La géolocalisation n\'est pas supportée par votre navigateur.');
  }
};

// Observer les changements d'utilisateur pour mettre à jour les coordonnées
watch(() => availabilityForm.user_id, (newUserId) => {
  if (newUserId) {
    const selectedUser = props.users.find(user => user.id === parseInt(newUserId));
    if (selectedUser && selectedUser.latitude && selectedUser.longitude) {
      availabilityForm.latitude = selectedUser.latitude;
      availabilityForm.longitude = selectedUser.longitude;
    }
  }
});

const activityLabel = (activity) => {
  const labels = {
    eat: 'Manger',
    drink: 'Boire',
    chat: 'Discuter'
  };
  return labels[activity] || activity;
};
</script>
