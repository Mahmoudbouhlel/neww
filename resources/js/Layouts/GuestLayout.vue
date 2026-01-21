<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
  <div class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-blue-700 via-indigo-600 to-purple-700 px-4 sm:px-6 lg:px-8 text-white">
    <!-- Logo -->
    <div class="">
      <Link href="/">
        <ApplicationLogo class="w-24 h-24 text-white drop-shadow-xl" />
      </Link>
    </div>

    <!-- Form Content (slot) -->
    <div class="w-full max-w-md animate-fade-in">
      <slot />
    </div>
  </div>
</template>

<style scoped>
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in {
  animation: fadeIn 0.7s ease-out both;
}
</style>
