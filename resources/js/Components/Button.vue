<template>
    <button
      :class="['btn', variantClass, sizeClass, { 'btn-disabled': disabled }]"
      :disabled="disabled"
      @click="$emit('click')"
    >
      <slot></slot>
    </button>
  </template>

  <script setup>
  defineProps({
    variant: {
      type: String,
      default: 'primary'
    },
    size: {
      type: String,
      default: 'md'
    },
    disabled: {
      type: Boolean,
      default: false
    }
  });

  const variantClass = computed(() => {
    return {
      primary: 'bg-blue-500 text-white hover:bg-blue-600',
      secondary: 'bg-gray-500 text-white hover:bg-gray-600',
      danger: 'bg-red-500 text-white hover:bg-red-600',
      success: 'bg-green-500 text-white hover:bg-green-600',
    }[props.variant] || 'bg-blue-500 text-white';
  });

  const sizeClass = computed(() => {
    return {
      sm: 'px-2 py-1 text-xs',
      md: 'px-4 py-2 text-sm',
      lg: 'px-6 py-3 text-lg',
    }[props.size] || 'px-4 py-2 text-sm';
  });
  </script>

  <style scoped>
  .btn {
    border: none;
    border-radius: 0.375rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
  }

  .btn-disabled {
    cursor: not-allowed;
    opacity: 0.6;
  }
  </style>
