<template>
  <nav
    class="fixed top-0 left-0 right-0 z-50 flex items-center justify-between bg-white/80 dark:bg-gray-900/80 backdrop-blur-md border-b border-gray-200 dark:border-gray-700 px-6 py-3 shadow-md transition-all"
  >
    <!-- Left Section -->
    <div class="flex items-center space-x-4">
      <!-- Sidebar Toggle (Mobile) -->
      <button
        @click="$emit('toggle-sidebar')"
        class="md:hidden p-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"
      >
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
      </button>

      <!-- Logo -->
      <Link :href="route('agent.dashboard')" class="flex items-center space-x-2">
        <img src="/images/logo.png" alt="Leoni Logo" class="h-10 w-auto drop-shadow-sm" />
        <span class="hidden md:block font-bold text-lg text-gray-800 dark:text-white tracking-wide">
          Dashboard
        </span>
      </Link>
    </div>

    <!-- Right Section -->
    <div class="flex items-center space-x-4">
      <!-- Notifications -->
      <button
        class="relative p-2 rounded-full text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"
      >
        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
          <path
            d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"
          />
        </svg>
        <span
          class="absolute top-1 right-1 w-2.5 h-2.5 bg-red-500 border-2 border-white dark:border-gray-900 rounded-full animate-pulse"
        ></span>
      </button>

      <!-- Profile Dropdown -->
      <div class="relative" @mouseleave="showProfileMenu = false">
        <button
          @click="toggleProfileMenu"
          class="flex items-center space-x-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded-full"
        >
          <img
            class="w-9 h-9 rounded-full ring-2 ring-gray-300 dark:ring-gray-700 hover:ring-indigo-400 transition"
            src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/avatars/michael-gough.png"
            alt="User Avatar"
          />
        </button>

        <transition name="fade">
          <div
            v-show="showProfileMenu"
            class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 shadow-xl rounded-lg overflow-hidden ring-1 ring-black/10 dark:ring-white/10 origin-top-right"
          >
            <div class="px-4 py-3">
              <p class="text-sm font-medium text-gray-900 dark:text-white">
                {{ userName }}
              </p>
              <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                {{ email }}
              </p>
            </div>
            <div class="border-t border-gray-100 dark:border-gray-700">
              <Link
                :href="route('logout')"
                method="post"
                as="button"
                class="block w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition"
              >
                Sign Out
              </Link>
            </div>
          </div>
        </transition>
      </div>
    </div>
  </nav>
</template>

<script setup lang="ts">
import { ref } from "vue";
import { Link, usePage } from "@inertiajs/vue3";

const showProfileMenu = ref(false);
const toggleProfileMenu = () => (showProfileMenu.value = !showProfileMenu.value);

// Get user info from Inertia shared props
const userName = usePage().props?.auth?.user?.name || "Agent";
const email = usePage().props?.auth?.user?.email || "agent@example.com";
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.15s ease, transform 0.15s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: scale(0.95);
}
</style>
