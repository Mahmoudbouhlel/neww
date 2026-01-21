<template>
    <nav class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 px-6 py-3 fixed top-0 left-0 right-0 z-50 shadow-md">
      <div class="flex items-center justify-between">
        <!-- Left Section -->
        <div class="flex items-center space-x-4">
          <!-- Mobile menu button (only shown on mobile) -->
          <button
            @click="$emit('toggle-sidebar')"
            class="md:hidden p-2 rounded-lg text-gray-500 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700"
            aria-label="Toggle sidebar"
          >
            <Bars3Icon class="w-6 h-6" />
          </button>

          <!-- Logo -->
          <Link :href="route('admin.dashboard')" class="flex items-center space-x-2">
            <img src="/images/logo.png" alt="Leoni Logo" class="h-10 w-auto" />
          </Link>
        </div>

        <!-- Right Section (unchanged) -->
        <div class="flex items-center space-x-4">
          <!-- Notifications -->
          <button
            class="relative p-2 rounded-lg text-gray-500 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700"
            @click="showNotifications = !showNotifications"
          >
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
              <path
                d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"
              />
            </svg>
            <span
              class="absolute top-0 right-0 w-3 h-3 bg-red-500 rounded-full"
              v-if="true"
            ></span>
          </button>

          <!-- Profile Dropdown -->
          <div class="relative">
            <button
              class="flex items-center space-x-2 text-sm focus:ring-2 focus:ring-gray-300"
              @click="showProfileMenu = !showProfileMenu"
            >
              <img
                class="w-8 h-8 rounded-full"
                src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/avatars/michael-gough.png"
                alt="User Avatar"
              />
            </button>

            <!-- Profile Dropdown Menu -->
            <div
              v-show="showProfileMenu"
              class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden transition-all transform origin-top-right"
            >
              <div class="py-2 px-4">
                <p class="font-medium text-gray-900 dark:text-white">{{ authUser ? authUser.name : "Guest" }}</p>
                <span class="block text-xs text-gray-500 dark:text-gray-400">
                  {{ authUser ? authUser.email : "Guest" }}
                </span>
              </div>
              <hr class="border-gray-200 dark:border-gray-700" />

              <!-- Logout -->
              <Link
                :href="route('logout')"
                method="post"
                as="button"
                class="w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700"
              >
                Sign Out
              </Link>
            </div>
          </div>
        </div>
      </div>
    </nav>
  </template>

  <script setup>
  import { ref } from "vue";
  import { Link } from "@inertiajs/vue3";
  import { Bars3Icon } from '@heroicons/vue/24/outline';

  const props = defineProps({
    authUser: Object,
  });

  const showNotifications = ref(false);
  const showProfileMenu = ref(false);
  </script>