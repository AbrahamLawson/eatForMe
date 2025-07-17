<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    preferences: {
        type: Object,
        default: () => ({
            gender_preference: 'both',
            age_range_min: 18,
            age_range_max: 99,
            interests: []
        }),
    },
});

const form = useForm({
    gender_preference: props.preferences.gender_preference || 'both',
    age_range_min: props.preferences.age_range_min || 18,
    age_range_max: props.preferences.age_range_max || 99,
    interests: props.preferences.interests || [],
});

const interestInput = ref('');

const addInterest = () => {
    if (interestInput.value.trim() && !form.interests.includes(interestInput.value.trim())) {
        form.interests.push(interestInput.value.trim());
        interestInput.value = '';
    }
};

const removeInterest = (interest) => {
    form.interests = form.interests.filter(i => i !== interest);
};

const submit = () => {
    form.patch(route('profile.preferences.update'), {
        preserveScroll: true,
        onSuccess: () => {
            // Success notification could be added here
        },
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">Préférences de Rencontre</h2>
            <p class="mt-1 text-sm text-gray-600">
                Mettez à jour vos préférences pour personnaliser votre expérience de rencontre.
            </p>
        </header>

        <form @submit.prevent="submit" class="mt-6 space-y-6">
            <!-- Préférence de genre -->
            <div>
                <InputLabel for="gender_preference" value="Je souhaite rencontrer" />
                
                <div class="mt-2 flex flex-wrap gap-3">
                    <button
                        type="button"
                        @click="form.gender_preference = 'male'"
                        :class="[
                            'px-4 py-2 rounded-md border transition-colors',
                            form.gender_preference === 'male' 
                                ? 'bg-raspberry text-white border-raspberry' 
                                : 'bg-white text-charcoal border-light-gray hover:border-raspberry'
                        ]"
                    >
                        Des hommes
                    </button>
                    
                    <button
                        type="button"
                        @click="form.gender_preference = 'female'"
                        :class="[
                            'px-4 py-2 rounded-md border transition-colors',
                            form.gender_preference === 'female' 
                                ? 'bg-raspberry text-white border-raspberry' 
                                : 'bg-white text-charcoal border-light-gray hover:border-raspberry'
                        ]"
                    >
                        Des femmes
                    </button>
                    
                    <button
                        type="button"
                        @click="form.gender_preference = 'both'"
                        :class="[
                            'px-4 py-2 rounded-md border transition-colors',
                            form.gender_preference === 'both' 
                                ? 'bg-raspberry text-white border-raspberry' 
                                : 'bg-white text-charcoal border-light-gray hover:border-raspberry'
                        ]"
                    >
                        Les deux
                    </button>
                </div>
            </div>

            <!-- Tranche d'âge -->
            <div>
                <InputLabel value="Tranche d'âge" />
                
                <div class="mt-2 flex items-center space-x-4">
                    <div class="w-1/3">
                        <InputLabel for="age_range_min" value="Âge minimum" class="text-sm" />
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
                    
                    <div class="w-1/3">
                        <InputLabel for="age_range_max" value="Âge maximum" class="text-sm" />
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
            </div>

            <!-- Centres d'intérêt -->
            <div>
                <InputLabel for="interests" value="Centres d'intérêt" />
                <p class="text-sm text-gray-500 mt-1">
                    Ajoutez vos centres d'intérêt pour trouver des personnes qui partagent vos passions
                </p>
                
                <div class="mt-2 flex">
                    <TextInput
                        id="interest_input"
                        type="text"
                        class="block w-full"
                        v-model="interestInput"
                        placeholder="Ex: Cuisine, Cinéma, Sport..."
                        @keyup.enter.prevent="addInterest"
                    />
                    <PrimaryButton type="button" class="ml-2" @click="addInterest">
                        Ajouter
                    </PrimaryButton>
                </div>
                
                <div class="mt-3 flex flex-wrap gap-2">
                    <div 
                        v-for="interest in form.interests" 
                        :key="interest"
                        class="bg-gray-100 rounded-full px-3 py-1 text-sm flex items-center"
                    >
                        <span>{{ interest }}</span>
                        <button 
                            type="button" 
                            @click="removeInterest(interest)" 
                            class="ml-2 text-gray-500 hover:text-red-500"
                        >
                            &times;
                        </button>
                    </div>
                </div>
                <InputError :message="form.errors.interests" class="mt-2" />
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Enregistrer</PrimaryButton>

                <p v-if="form.recentlySuccessful" class="text-sm text-green-600">Enregistré.</p>
            </div>
        </form>
    </section>
</template>
