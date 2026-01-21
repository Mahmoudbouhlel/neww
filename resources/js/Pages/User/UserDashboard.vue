<template>
    <Head title="User Dashboard" />

    <div class="min-h-screen flex flex-col lg:flex-row bg-gray-50 text-gray-900">
      <!-- Mobile Menu Toggle -->
      <button
        @click="isSidebarOpen = !isSidebarOpen"
        class="lg:hidden fixed top-4 left-4 z-50 p-2 bg-indigo-600 text-white rounded-lg shadow-md focus:outline-none"
      >
        <svg v-if="!isSidebarOpen" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
        </svg>
        <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>

      <!-- Sidebar -->
      <aside
        :class="[
          'w-full lg:w-64 bg-white shadow-sm p-4 sm:p-6 space-y-6 border-r border-gray-200 lg:static fixed top-0 left-0 h-full z-40 transform transition-transform duration-300',
          isSidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
        ]"
      >
        <div class="flex items-center space-x-2">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 sm:h-8 sm:w-8 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
          </svg>
          <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Dashboard</h2>
        </div>

        <div class="space-y-3">
          <button
            @click="showModal = true; isSidebarOpen = false"
            class="flex items-center justify-center space-x-2 bg-gradient-to-r from-indigo-600 to-blue-600 text-white py-2 sm:py-3 w-full rounded-lg hover:from-indigo-700 hover:to-blue-700 transition-colors shadow-md text-sm sm:text-base"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span>Prendre RDV</span>
          </button>

          <button
            @click="showDocumentModal = true; isSidebarOpen = false"
            class="flex items-center justify-center space-x-2 bg-gradient-to-r from-emerald-600 to-green-600 text-white py-2 sm:py-3 w-full rounded-lg hover:from-emerald-700 hover:to-green-700 transition-colors shadow-md text-sm sm:text-base"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span>Demander un Document</span>
          </button>
        </div>
      </aside>

      <!-- Main Content -->
      <main class="flex-1 p-4 sm:p-6 overflow-auto">
        <!-- User Info -->
        <section class="bg-white p-4 sm:p-6 rounded-xl shadow-sm border border-gray-100 mb-6">
          <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4 sm:mb-6">
            <div>
              <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Bienvenue, {{ user.first_name }}</h1>
              <p class="text-gray-500 text-sm sm:text-base">Voici votre tableau de bord personnel</p>
            </div>
            <button
              @click="logout"
              class="mt-4 sm:mt-0 flex items-center space-x-2 bg-red-50 text-red-600 px-3 sm:px-4 py-2 rounded-lg hover:bg-red-100 transition-colors text-sm sm:text-base"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
              </svg>
              <span>Déconnexion</span>
            </button>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <div
              v-for="(value, key) in userInfos"
              :key="key"
              class="bg-gray-50 p-3 sm:p-4 rounded-lg border border-gray-100 hover:bg-gray-100 transition-colors"
            >
              <h3 class="text-xs font-medium text-gray-500 uppercase tracking-wider">{{ key }}</h3>
              <p class="font-semibold text-gray-800 mt-1 text-sm sm:text-base">{{ value || 'N/A' }}</p>
            </div>
          </div>
        </section>

        <!-- RDV List -->
        <section class="bg-white p-4 sm:p-6 rounded-xl shadow-sm border border-gray-100 mb-6">
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 sm:mb-6">
            <h3 class="text-lg sm:text-xl font-bold text-gray-800 flex items-center space-x-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              <span>Mes Rendez-vous</span>
            </h3>

            <div class="mt-3 sm:mt-0">
              <select
                v-model="selectedStatus"
                class="w-full sm:w-auto bg-gray-50 border border-gray-200 text-gray-700 rounded-lg px-3 sm:px-4 py-2 text-sm sm:text-base focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500"
              >
                <option value="">Tous les statuts</option>
                <option value="Completed">Complété</option>
                <option value="In_progress">En cours</option>
                <option value="Pending">En attente</option>
              </select>
            </div>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full text-left text-sm sm:text-base">
              <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                <tr>
                  <th class="px-3 sm:px-6 py-2 sm:py-3">Service</th>
                  <th class="px-3 sm:px-6 py-2 sm:py-3">Tâche</th>
                  <th class="px-3 sm:px-6 py-2 sm:py-3">Date</th>
                  <th class="px-3 sm:px-6 py-2 sm:py-3">Heure</th>
                  <th class="px-3 sm:px-6 py-2 sm:py-3">Statut</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <tr
                  v-for="appointment in filteredAppointments"
                  :key="appointment.id"
                  class="hover:bg-gray-50 transition-colors"
                >
                  <td class="px-3 sm:px-6 py-3 sm:py-4">{{ appointment.service_name }}</td>
                  <td class="px-3 sm:px-6 py-3 sm:py-4">{{ appointment.task_name }}</td>
                  <td class="px-3 sm:px-6 py-3 sm:py-4">{{ formatDate(appointment.date.split(' ')[0]) }}</td>
                  <td class="px-3 sm:px-6 py-3 sm:py-4">{{ appointment.date.split(' ')[1] }}</td>
                  <td class="px-3 sm:px-6 py-3 sm:py-4">
                    <span
                      :class="statusColor(appointment.status)"
                      class="inline-flex items-center px-2 sm:px-3 py-1 rounded-full text-xs sm:text-sm font-medium"
                    >
                      {{ formatStatus(appointment.status) }}
                    </span>
                  </td>
                </tr>
                <tr v-if="filteredAppointments.length === 0">
                  <td colspan="5" class="px-3 sm:px-6 py-3 sm:py-4 text-center text-gray-500 text-sm sm:text-base">
                    Aucun rendez-vous trouvé
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>

        <!-- Document Requests List -->
        <section class="bg-white p-4 sm:p-6 rounded-xl shadow-sm border border-gray-100">
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 sm:mb-6">
            <h3 class="text-lg sm:text-xl font-bold text-gray-800 flex items-center space-x-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              <span>Mes Demandes de Documents</span>
            </h3>

            <div class="mt-3 sm:mt-0">
              <select
                v-model="selectedDocumentStatus"
                class="w-full sm:w-auto bg-gray-50 border border-gray-200 text-gray-700 rounded-lg px-3 sm:px-4 py-2 text-sm sm:text-base focus:ring-2 focus:ring-emerald-200 focus:border-emerald-500"
              >
                <option value="">Tous les statuts</option>
                <option value="pending">En attente</option>
                <option value="processing">En traitement</option>
                <option value="completed">Complété</option>
                <option value="rejected">Rejeté</option>
              </select>
            </div>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full text-left text-sm sm:text-base">
              <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                <tr>
                  <th class="px-3 sm:px-6 py-2 sm:py-3">Type de Document</th>
                  <th class="px-3 sm:px-6 py-2 sm:py-3">Service</th>
                  <th class="px-3 sm:px-6 py-2 sm:py-3">Date de Demande</th>
                  <th class="px-3 sm:px-6 py-2 sm:py-3">Statut</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <tr
                  v-for="request in filteredDocumentRequests"
                  :key="request.id"
                  class="hover:bg-gray-50 transition-colors"
                >
                  <td class="px-3 sm:px-6 py-3 sm:py-4">{{ request.document_template_name }}</td>
                  <td class="px-3 sm:px-6 py-3 sm:py-4">{{ request.service_name }}</td>
                  <td class="px-3 sm:px-6 py-3 sm:py-4">{{ formatDate(request.created_at.split(' ')[0]) }}</td>
                  <td class="px-3 sm:px-6 py-3 sm:py-4">
                    <span
                      :class="statusColor(request.status)"
                      class="inline-flex items-center px-2 sm:px-3 py-1 rounded-full text-xs sm:text-sm font-medium"
                    >
                      {{ formatStatus(request.status) }}
                    </span>
                  </td>
                </tr>
                <tr v-if="filteredDocumentRequests.length === 0">
                  <td colspan="4" class="px-3 sm:px-6 py-3 sm:py-4 text-center text-gray-500 text-sm sm:text-base">
                    Aucune demande de document trouvée
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>
      </main>

      <!-- Appointment Modal -->
      <Modal :show="showModal" @close="showModal = false">
        <div class="p-4 sm:p-6 max-w-lg w-full mx-auto">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg sm:text-xl font-bold text-gray-800 flex items-center space-x-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              <span>Prendre un Rendez-vous</span>
            </h3>
            <button @click="showModal = false" class="text-gray-400 hover:text-gray-500">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <form @submit.prevent="submitAppointment" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Service</label>
              <select
                v-model="appointmentForm.service_id"
                class="w-full p-2 sm:p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500 text-sm sm:text-base"
              >
                <option value="" disabled>Choisir un service</option>
                <option v-for="service in services" :key="service.id" :value="service.id">
                  {{ service.name }}
                </option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Tâche</label>
              <select
                v-model="appointmentForm.task_id"
                class="w-full p-2 sm:p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500 text-sm sm:text-base"
                :disabled="!tasks.length"
              >
                <option value="" disabled>Choisir une tâche</option>
                <option v-for="task in tasks" :key="task.id" :value="task.id">
                  {{ task.name }}
                </option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
              <input
                type="date"
                v-model="appointmentForm.date"
                class="w-full p-2 sm:p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500 text-sm sm:text-base"
                :min="new Date().toISOString().split('T')[0]"
              />
            </div>

            <div v-if="availableTimes.length > 0">
              <label class="block text-sm font-medium text-gray-700 mb-2">Heures disponibles</label>
              <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                <button
                  v-for="time in availableTimes"
                  :key="time"
                  @click.prevent="appointmentForm.time = time"
                  :class="[
                    'p-2 rounded-lg border transition-colors text-sm sm:text-base',
                    appointmentForm.time === time
                      ? 'bg-indigo-600 text-white border-indigo-700'
                      : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                  ]"
                >
                  {{ time }}
                </button>
              </div>
            </div>

            <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3 pt-4">
              <button
                type="submit"
                :disabled="isLoading"
                class="flex-1 bg-indigo-600 text-white py-2 sm:py-3 rounded-lg hover:bg-indigo-700 transition-colors disabled:opacity-50 flex items-center justify-center text-sm sm:text-base"
              >
                <svg v-if="isLoading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ isLoading ? 'En cours...' : 'Confirmer le RDV' }}
              </button>

              <button
                @click="showModal = false"
                type="button"
                class="flex-1 bg-white text-gray-700 py-2 sm:py-3 rounded-lg border border-gray-300 hover:bg-gray-50 transition-colors text-sm sm:text-base"
              >
                Annuler
              </button>
            </div>
          </form>
        </div>
      </Modal>

      <!-- Document Request Modal -->
      <Modal :show="showDocumentModal" @close="showDocumentModal = false">
        <div class="p-4 sm:p-6 max-w-lg w-full mx-auto">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg sm:text-xl font-bold text-gray-800 flex items-center space-x-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              <span>Demande de Document</span>
            </h3>
            <button @click="showDocumentModal = false" class="text-gray-400 hover:text-gray-500">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <form @submit.prevent="submitDocumentRequest" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Type de document</label>
              <select
                v-model="documentForm.document_template_id"
                class="w-full p-2 sm:p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-200 focus:border-emerald-500 text-sm sm:text-base"
              >
                <option value="" disabled>Choisir un document</option>
                <option v-for="template in documentTemplates" :key="template.id" :value="template.id">
                  {{ template.name }}
                </option>
              </select>
            </div>

            <div v-if="selectedTemplateFields.length" class="space-y-4 p-3 sm:p-4 bg-gray-50 rounded-lg border border-gray-200">
              <h4 class="font-medium text-gray-700 text-sm sm:text-base">Informations requises</h4>
              <div v-for="(field, index) in selectedTemplateFields" :key="index" class="space-y-1">
                <label class="block text-sm text-gray-600">{{ field.label }} <span v-if="field.required" class="text-red-500">*</span></label>
                <input
                  v-model="dynamicData[field.label]"
                  :type="field.type"
                  :required="field.required"
                  class="w-full p-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-emerald-200 focus:border-emerald-500 text-sm sm:text-base"
                />
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Commentaires (optionnel)</label>
              <textarea
                v-model="documentForm.comment"
                rows="3"
                class="w-full p-2 sm:p-3 border border-gray-300 rounded-lg focus:ring-1 focus:ring-emerald-200 focus:border-emerald-500 text-sm sm:text-base"
              ></textarea>
            </div>

            <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3 pt-4">
              <button
                type="submit"
                :disabled="isLoading"
                class="flex-1 bg-emerald-600 text-white py-2 sm:py-3 rounded-lg hover:bg-emerald-700 transition-colors disabled:opacity-50 flex items-center justify-center text-sm sm:text-base"
              >
                <svg v-if="isLoading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ isLoading ? 'En cours...' : 'Envoyer la demande' }}
              </button>

              <button
                @click="showDocumentModal = false"
                type="button"
                class="flex-1 bg-white text-gray-700 py-2 sm:py-3 rounded-lg border border-gray-300 hover:bg-gray-50 transition-colors text-sm sm:text-base"
              >
                Annuler
              </button>
            </div>
          </form>
        </div>
      </Modal>
    </div>
</template>

<script setup>
import { Head, useForm, router } from "@inertiajs/vue3";
import { ref, computed, watch } from "vue";
import axios from "axios";
import Modal from '@/Components/Modal.vue';

const props = defineProps({
  user: Object,
  services: Array,
  appointments: Array,
  document_requests: Array,
  document_templates: Array
});

// State for sidebar toggle
const isSidebarOpen = ref(false);

// User information
const userInfos = computed(() => ({
  "Personnel No": props.user.personnel_number,
  "Organizational Unit": props.user.organizational_unit,
  "Position Name": props.user.position_name,
  "Job": props.user.job
}));

// Appointment data
const appointmentForm = useForm({
  service_id: "",
  task_id: "",
  date: "",
  time: ""
});
const tasks = ref([]);
const availableTimes = ref([]);
const isLoading = ref(false);
const showModal = ref(false);
const selectedStatus = ref("");

// Document request data
const showDocumentModal = ref(false);
const documentTemplates = ref(props.document_templates);
const documentForm = useForm({
  document_template_id: "",
  comment: ""
});
const dynamicData = ref({});
const selectedTemplateFields = ref([]);
const selectedDocumentStatus = ref("");

// Format date for display
const formatDate = (dateString) => {
  const options = { year: 'numeric', month: 'short', day: 'numeric' };
  return new Date(dateString).toLocaleDateString('fr-FR', options);
};

// Format status for display
const formatStatus = (status) => {
  const statusMap = {
    'Completed': 'Complété',
    'In_progress': 'En cours',
    'Pending': 'En attente',
    'pending': 'En attente',
    'processing': 'En traitement',
    'completed': 'Complété',
    'rejected': 'Rejeté'
  };
  return statusMap[status] || status;
};

// Status color mapping
const statusColor = (status) => {
  const colors = {
    'Completed': 'bg-green-100 text-green-800',
    'In_progress': 'bg-yellow-100 text-yellow-800',
    'Pending': 'bg-red-100 text-red-800',
    'pending': 'bg-gray-100 text-gray-800',
    'processing': 'bg-blue-100 text-blue-800',
    'completed': 'bg-green-100 text-green-800',
    'rejected': 'bg-red-100 text-red-800'
  };
  return colors[status] || 'bg-gray-100 text-gray-800';
};

// Load tasks when service is selected
watch(() => appointmentForm.service_id, (serviceId) => {
  appointmentForm.task_id = "";
  tasks.value = props.services.find(s => s.id === serviceId)?.tasks || [];
});

// Fetch available times when date or service changes
watch(() => [appointmentForm.date, appointmentForm.service_id], async ([date, serviceId]) => {
  if (!date || !serviceId) {
    availableTimes.value = [];
    return;
  }

  try {
    const response = await axios.get(route("user.appointments.available_times"), {
      params: { service_id: serviceId, date: date }
    });
    availableTimes.value = response.data;
  } catch (error) {
    console.error("Error fetching available times:", error);
    availableTimes.value = [];
  }
}, { immediate: false });

// Submit appointment form
const submitAppointment = () => {
  if (!appointmentForm.time) {
    alert("Veuillez sélectionner une heure");
    return;
  }

  isLoading.value = true;
  appointmentForm.post(route("user.appointments.store"), {
    onSuccess: () => {
      appointmentForm.reset();
      showModal.value = false;
      router.reload({ only: ['appointments'] });
    },
    onFinish: () => isLoading.value = false,
  });
};

// Watch document template changes
watch(() => documentForm.document_template_id, (templateId) => {
  const template = documentTemplates.value.find(t => t.id === templateId);
  try {
    selectedTemplateFields.value = template?.fields ? JSON.parse(template.fields) : [];
    dynamicData.value = {};
    selectedTemplateFields.value.forEach(field => {
      dynamicData.value[field.label] = field.default || '';
    });
  } catch (err) {
    console.error('Invalid JSON in template fields:', err);
    selectedTemplateFields.value = [];
  }
});

// Submit document request
const submitDocumentRequest = () => {
  isLoading.value = true;
  router.post(route("user.document_requests.store"), {
    document_template_id: documentForm.document_template_id,
    comment: documentForm.comment,
    data: dynamicData.value
  }, {
    onSuccess: () => {
      documentForm.reset();
      dynamicData.value = {};
      showDocumentModal.value = false;
      router.reload({ only: ['document_requests'] });
    },
    onFinish: () => isLoading.value = false,
  });
};

// Filter appointments by status
const filteredAppointments = computed(() => {
  if (!selectedStatus.value) return props.appointments;
  return props.appointments.filter(a => a.status === selectedStatus.value);
});

// Filter document requests by status
const filteredDocumentRequests = computed(() => {
  if (!selectedDocumentStatus.value) return props.document_requests;
  return props.document_requests.filter(r => r.status === selectedDocumentStatus.value);
});

// Logout function
const logout = () => {
  router.post(route("logout"));
};
</script>

<style>
/* Smooth transitions for modal */
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

/* Animation for loading spinner */
@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}
.animate-spin {
  animation: spin 1s linear infinite;
}

/* Ensure table content doesn't overflow on mobile */
table {
  min-width: 100%;
}

/* Improve touch targets for buttons */
button {
  touch-action: manipulation;
}
</style>