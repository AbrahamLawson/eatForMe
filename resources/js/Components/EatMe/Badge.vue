<template>
  <span
    :class="[
      'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
      variantClasses,
      className
    ]"
  >
    <slot />
  </span>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  variant: {
    type: String,
    default: 'available',
    validator: (value) => ['available', 'pending', 'info', 'success', 'warning', 'danger'].includes(value),
  },
  className: {
    type: String,
    default: '',
  },
});

// Classes basées sur la variante
const variantClasses = computed(() => {
  switch (props.variant) {
    case 'available':
      return 'bg-mint/20 text-mint border border-mint/30';
    case 'pending':
      return 'bg-soft-orange/20 text-soft-orange border border-soft-orange/30';
    case 'info':
      return 'bg-blue-100 text-blue-800 border border-blue-200';
    case 'success':
      return 'bg-green-100 text-green-800 border border-green-200';
    case 'warning':
      return 'bg-yellow-100 text-yellow-800 border border-yellow-200';
    case 'danger':
      return 'bg-red-100 text-red-800 border border-red-200';
    default:
      return 'bg-mint/20 text-mint border border-mint/30';
  }
});
</script>
