<template>
    <div class="antialiased bg-gray-50 dark:bg-gray-900">
      <!-- Navbar start -->
      <Navbar />
      <!-- end -->

      <!-- Sidebar start -->
      <Sidebar />
      <!-- end -->

      <main class="p-4 md:ml-64 h-auto pt-20">
        <!-- Slot for dynamic content -->
        <slot />
      </main>
    </div>
  </template>

  <script setup>
  import Navbar from './Navbar.vue';
  import Sidebar from './Sidebar.vue';
  </script>


