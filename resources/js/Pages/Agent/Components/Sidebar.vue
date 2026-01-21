<template>
  <aside
    :class="[
      'fixed top-0 left-0 h-screen pt-16 transition-all duration-300 bg-white/80 dark:bg-gray-900/80 backdrop-blur-md border-r border-gray-200/50 dark:border-gray-800/50 shadow-lg z-30',
      isSidebarOpen ? 'w-64' : 'w-16'
    ]"
    aria-label="Sidebar navigation"
  >
    <div class="h-full flex flex-col px-3 py-4 overflow-y-auto">
      <!-- Sidebar Toggle Button -->
      <button
        @click="toggleSidebar"
        class="absolute top-4 -right-3 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 p-1.5 rounded-full shadow-lg border border-gray-200/50 dark:border-gray-800/50 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-indigo-500"
        aria-label="Toggle sidebar"
      >
        <ChevronLeftIcon v-if="isSidebarOpen" class="w-4 h-4 transition-transform duration-300" />
        <ChevronRightIcon v-else class="w-4 h-4 transition-transform duration-300" />
      </button>

      <!-- Navigation Sections -->
      <div v-for="section in sections" :key="section.title" class="mt-4">
        <!-- Section Title -->
        <transition name="fade">
          <div
            v-if="isSidebarOpen"
            class="text-gray-500 dark:text-gray-400 font-semibold text-xs uppercase tracking-wider px-3 mb-2"
          >
            {{ section.title }}
          </div>
        </transition>

        <!-- Collapsible Sections -->
        <div v-if="section.collapsible">
          <button
            @click="section.isOpen = !section.isOpen"
            class="flex items-center justify-between w-full text-gray-700 dark:text-gray-200 font-medium text-sm p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            :aria-expanded="section.isOpen"
          >
            <div class="flex items-center">
              <Bars3Icon class="w-5 h-5 text-gray-500 dark:text-gray-400" />
              <span v-if="isSidebarOpen" class="ml-3">{{ section.title }}</span>
            </div>
            <ChevronDownIcon
              v-if="isSidebarOpen"
              class="w-4 h-4 text-gray-400 dark:text-gray-500 transition-transform duration-200"
              :class="{ 'rotate-180': section.isOpen }"
            />
          </button>

          <transition name="slide-fade">
            <ul
              v-show="section.isOpen"
              class="mt-1 space-y-1 overflow-hidden"
            >
              <li v-for="link in section.links" :key="link.href">
                <Link
                  :href="route(link.href)"
                  class="flex items-center p-2 rounded-lg transition-all duration-200 group relative"
                  :class="currentRoute.includes(link.href)
                    ? 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-300'
                    : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800'"
                  :aria-current="currentRoute.includes(link.href) ? 'page' : undefined"
                >
                  <div class="relative flex items-center">
                    <component
                      :is="getIcon(link.href)"
                      class="w-5 h-5 transition-colors duration-200"
                      :class="currentRoute.includes(link.href)
                        ? 'text-indigo-600 dark:text-indigo-300'
                        : 'text-gray-500 dark:text-gray-400'"
                    />
                    <span
                      v-if="link.badge"
                      class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full h-4 w-4 flex items-center justify-center"
                    >
                      {{ link.badge }}
                    </span>
                  </div>
                  <span v-if="isSidebarOpen" class="ml-3 text-sm">{{ link.label }}</span>
                  <span
                    v-else
                    class="absolute left-full ml-2 bg-gray-800 text-white text-xs font-medium px-2 py-1 rounded whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all duration-200 shadow-lg"
                  >
                    {{ link.label }}
                  </span>
                </Link>
              </li>
            </ul>
          </transition>
        </div>

        <!-- Non-Collapsible Sections -->
        <ul v-else class="space-y-1">
          <li v-for="link in section.links" :key="link.href">
            <Link
              :href="route(link.href)"
              class="flex items-center p-2 rounded-lg transition-all duration-200 group relative"
              :class="currentRoute.includes(link.href)
                ? 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-300'
                : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800'"
              :aria-current="currentRoute.includes(link.href) ? 'page' : undefined"
            >
              <div class="relative flex items-center">
                <component
                  :is="getIcon(link.href)"
                  class="w-5 h-5 transition-colors duration-200"
                  :class="currentRoute.includes(link.href)
                    ? 'text-indigo-600 dark:text-indigo-300'
                    : 'text-gray-500 dark:text-gray-400'"
                />
              </div>
              <span v-if="isSidebarOpen" class="ml-3 text-sm">{{ link.label }}</span>
              <span
                v-else
                class="absolute left-full ml-2 bg-gray-800 text-white text-xs font-medium px-2 py-1 rounded whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all duration-200 shadow-lg"
              >
                {{ link.label }}
              </span>
            </Link>
          </li>
        </ul>
      </div>
    </div>
  </aside>
</template>

<script setup>
import { ref, computed } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import {
  HomeIcon,
  UsersIcon,
  ListBulletIcon,
  WrenchScrewdriverIcon,
  CalendarDaysIcon,
  DocumentTextIcon,
  DocumentCheckIcon,
  ChevronLeftIcon,
  ChevronRightIcon,
  ChevronDownIcon,
  Bars3Icon,
} from "@heroicons/vue/24/outline";

const currentRoute = computed(() => usePage().url);
const isSidebarOpen = ref(true);
const isManagementOpen = ref(true);
const isDocumentOpen = ref(true);

const toggleSidebar = () => (isSidebarOpen.value = !isSidebarOpen.value);

const sections = [
  {
    title: "Main",
    links: [{ href: "agent.dashboard", label: "Dashboard" }],
  },
  {
    title: "Management",
    collapsible: true,
    isOpen: isManagementOpen,
    links: [{ href: "Agent.appointments.index", label: "Appointments" }],
  },
  {
    title: "Document Management",
    collapsible: true,
    isOpen: isDocumentOpen,
    links: [
     // { href: "agent.document-requests.index", label: "Documents" },
      { href: "agent.document-requests.index", label: "Requests" },
    ],
  },
];

const getIcon = (href) => {
  const iconMap = {
    "admin.dashboard": HomeIcon,
    "users.index": UsersIcon,
    "personnels.index": ListBulletIcon,
    "services.index": WrenchScrewdriverIcon,
    "tasks.index": ListBulletIcon,
    "appointments.index": CalendarDaysIcon,
    "documents.index": DocumentTextIcon,
    "document_requests.index": DocumentCheckIcon,
  };
  return iconMap[href] || DocumentTextIcon;
};
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.15s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
.slide-fade-enter-active,
.slide-fade-leave-active {
  transition: all 0.25s ease;
}
.slide-fade-enter-from,
.slide-fade-leave-to {
  opacity: 0;
  max-height: 0;
  transform: translateY(-10px);
}
</style>
