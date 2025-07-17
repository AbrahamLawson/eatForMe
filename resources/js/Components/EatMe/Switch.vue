<template>
  <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-4">
    <div class="p-4 bg-white border-b border-light-gray">
      <div class="flex items-center justify-between">
        <div>
          <h3 class="font-title font-bold text-lg text-charcoal">{{ label }}</h3>
          <p class="text-gray-500">{{ description }}</p>
        </div>
        <div class="relative inline-block w-14 h-7 transition duration-200 ease-in-out rounded-full cursor-pointer"
          @click="toggle">
          <input type="checkbox" class="hidden" :checked="modelValue" @change="toggle">
          <div class="absolute inset-0 transition duration-200 ease-in-out rounded-full"
            :class="modelValue ? 'bg-raspberry' : 'bg-gray-300'"></div>
          <div class="absolute left-0.5 top-0.5 w-6 h-6 transition duration-200 ease-in-out transform bg-white rounded-full shadow-md"
            :class="modelValue ? 'translate-x-7' : 'translate-x-0'"></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue';

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false
  },
  label: {
    type: String,
    default: 'Activer la recherche'
  },
  description: {
    type: String,
    default: 'Activez cette option pour permettre la recherche de personnes à proximité'
  },
  disabled: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['update:modelValue', 'change']);

const toggle = () => {
  if (props.disabled) return;
  emit('update:modelValue', !props.modelValue);
  emit('change', !props.modelValue);
};
</script>