<template>
  <button
    :type="type"
    :class="[
      'inline-flex items-center justify-center px-4 py-2 rounded-lg font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150',
      variantClasses,
      sizeClasses,
      { 'opacity-25 cursor-not-allowed': disabled },
      className
    ]"
    :disabled="disabled"
  >
    <slot />
  </button>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  type: {
    type: String,
    default: 'button',
  },
  variant: {
    type: String,
    default: 'primary',
    validator: (value) => ['primary', 'secondary', 'outline', 'text'].includes(value),
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg'].includes(value),
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  className: {
    type: String,
    default: '',
  },
});

// Classes basées sur la variante
const variantClasses = computed(() => {
  switch (props.variant) {
    case 'primary':
      return 'bg-raspberry hover:bg-raspberry/90 text-white focus:ring-raspberry/50';
    case 'secondary':
      return 'bg-soft-orange hover:bg-soft-orange/90 text-white focus:ring-soft-orange/50';
    case 'outline':
      return 'bg-transparent border border-raspberry text-raspberry hover:bg-raspberry/10 focus:ring-raspberry/30';
    case 'text':
      return 'bg-transparent text-raspberry hover:bg-raspberry/10 focus:ring-raspberry/30';
    default:
      return 'bg-raspberry hover:bg-raspberry/90 text-white focus:ring-raspberry/50';
  }
});

// Classes basées sur la taille
const sizeClasses = computed(() => {
  switch (props.size) {
    case 'sm':
      return 'text-xs py-1.5 px-3';
    case 'md':
      return 'text-sm py-2 px-4';
    case 'lg':
      return 'text-base py-2.5 px-5';
    default:
      return 'text-sm py-2 px-4';
  }
});
</script>
