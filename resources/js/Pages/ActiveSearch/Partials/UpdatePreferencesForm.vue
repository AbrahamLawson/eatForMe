<template>
  <section>
    <header>
      <h2 class="text-lg font-medium text-gray-900">Préférences de recherche</h2>
      <p class="mt-1 text-sm text-gray-600">
        Mettez à jour vos préférences de rencontre pour améliorer la qualité de vos matchs.
      </p>
    </header>

    <form @submit.prevent="form.patch(route('active-search.preferences.update'))" class="mt-6 space-y-6">
      <div>
        <InputLabel for="gender_preference" value="Préférence de genre" />
        <select
          id="gender_preference"
          v-model="form.gender_preference"
          class="mt-1 block w-full border-gray-300 focus:border-raspberry focus:ring focus:ring-raspberry focus:ring-opacity-50 rounded-md shadow-sm"
        >
          <option v-for="option in genderOptions" :key="option.value" :value="option.value">
            {{ option.label }}
          </option>
        </select>
        <InputError :message="form.errors.gender_preference" class="mt-2" />
      </div>

      <div class="flex gap-4">
        <div class="flex-1">
          <InputLabel for="age_range_min" value="Âge minimum" />
          <TextInput
            id="age_range_min"
            type="number"
            class="mt-1 block w-full"
            v-model="form.age_range_min"
            min="18"
            max="99"
          />
          <InputError :message="form.errors.age_range_min" class="mt-2" />
        </div>
        <div class="flex-1">
          <InputLabel for="age_range_max" value="Âge maximum" />
          <TextInput
            id="age_range_max"
            type="number"
            class="mt-1 block w-full"
            v-model="form.age_range_max"
            min="18"
            max="99"
          />
          <InputError :message="form.errors.age_range_max" class="mt-2" />
        </div>
      </div>

      <div>
        <InputLabel for="payment_preference" value="Qui paie ?" />
        <select
          id="payment_preference"
          v-model="form.payment_preference"
          class="mt-1 block w-full border-gray-300 focus:border-raspberry focus:ring focus:ring-raspberry focus:ring-opacity-50 rounded-md shadow-sm"
        >
          <option v-for="option in paymentOptions" :key="option.value" :value="option.value">
            {{ option.label }}
          </option>
        </select>
        <InputError :message="form.errors.payment_preference" class="mt-2" />
      </div>

      <div>
        <InputLabel for="interests" value="Centres d'intérêt" />
        <div class="mt-2 flex flex-wrap gap-2">
          <div
            v-for="interest in availableInterests"
            :key="interest.value"
            @click="toggleInterest(interest.value)"
            :class="[
              'px-3 py-1 rounded-full border cursor-pointer transition-colors',
              form.interests.includes(interest.value)
                ? 'bg-raspberry text-white border-raspberry'
                : 'bg-white text-charcoal border-light-gray hover:border-raspberry'
            ]"
          >
            {{ interest.label }}
          </div>
        </div>
        <InputError :message="form.errors.interests" class="mt-2" />
      </div>

      <div class="flex items-center gap-4">
        <PrimaryButton :disabled="form.processing">Enregistrer</PrimaryButton>

        <Transition
          enter-active-class="transition ease-in-out"
          enter-from-class="opacity-0"
          leave-active-class="transition ease-in-out"
          leave-to-class="opacity-0"
        >
          <p v-if="form.recentlySuccessful" class="text-sm text-green-600">Enregistré.</p>
        </Transition>
      </div>
    </form>
  </section>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
  preferences: {
    type: Object,
    default: () => ({
      gender_preference: 'any',
      age_range_min: 18,
      age_range_max: 99,
      interests: [],
      payment_preference: 'any',
    }),
  },
});

const form = useForm({
  gender_preference: props.preferences.gender_preference || 'any',
  age_range_min: props.preferences.age_range_min || 18,
  age_range_max: props.preferences.age_range_max || 99,
  interests: props.preferences.interests || [],
  payment_preference: props.preferences.payment_preference || 'any',
});

const genderOptions = [
  { value: 'any', label: 'Tous' },
  { value: 'male', label: 'Homme' },
  { value: 'female', label: 'Femme' },
];

const paymentOptions = [
  { value: 'any', label: 'Peu importe' },
  { value: 'i_pay', label: "J'invite" },
  { value: 'they_pay', label: 'Je veux être invité(e)' },
  { value: 'split', label: 'Chacun paie sa part' },
];

const availableInterests = [
  { value: 'culture', label: 'Culture' },
  { value: 'sport', label: 'Sport' },
  { value: 'music', label: 'Musique' },
  { value: 'cinema', label: 'Cinéma' },
  { value: 'travel', label: 'Voyage' },
  { value: 'cooking', label: 'Cuisine' },
  { value: 'art', label: 'Art' },
  { value: 'gaming', label: 'Jeux vidéo' },
  { value: 'reading', label: 'Lecture' },
  { value: 'nature', label: 'Nature' },
  { value: 'tech', label: 'Technologie' },
];

const toggleInterest = (interest) => {
  const index = form.interests.indexOf(interest);
  if (index === -1) {
    form.interests.push(interest);
  } else {
    form.interests.splice(index, 1);
  }
};
</script>
