<template>
    <AdminLayout>
      <div class="p-6 min-h-screen bg-gray-50 dark:bg-gray-900">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
          <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-white">Personnel Management</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Manage all personnel records and information</p>
          </div>
          <div class="flex gap-3">
            <button
              @click="triggerFileUpload"
              class="flex items-center gap-2 bg-gradient-to-r from-green-600 to-green-500 text-white px-4 py-2.5 rounded-lg hover:from-green-700 hover:to-green-600 shadow-md transition-all"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
              </svg>
              Upload XLSX
            </button>
            <input type="file" ref="fileInput" accept=".xlsx,.xls" class="hidden" @change="handleFileUpload" />
          </div>
        </div>

        <!-- Notification -->
        <transition name="fade">
          <div v-if="notification.message" class="fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg" :class="notification.type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'">
            {{ notification.message }}
          </div>
        </transition>

        <!-- Loading Modal with Progress Bar -->
        <transition name="fade">
          <div v-if="uploading" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-xl w-96">
              <div class="flex items-center gap-3 mb-4">
                <div class="animate-spin">
                  <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                  </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-800 dark:text-white">Uploading File</h3>
              </div>
              <div class="relative w-full h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                <div
                  class="absolute h-2 bg-gradient-to-r from-blue-500 to-blue-400 rounded-full transition-all duration-300"
                  :style="{ width: uploadProgress + '%' }"
                ></div>
              </div>
              <p class="mt-2 text-right text-sm font-medium text-gray-600 dark:text-gray-300">{{ uploadProgress }}%</p>
            </div>
          </div>
        </transition>

        <!-- Enhanced Filters Section -->
        <div class="mb-6 p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                  </svg>
                </div>
                <input
                  v-model="filters.search"
                  type="text"
                  placeholder="Search by name, personnel no, or phone..."
                  class="pl-10 w-full px-3 py-2 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                />
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Job Title</label>
              <select
                v-model="filters.job"
                class="w-full px-3 py-2 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
              >
                <option value="">All Jobs</option>
                <option v-for="job in uniqueJobs" :key="job" :value="job">{{ job }}</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Organizational Unit</label>
              <select
                v-model="filters.organizational_unit"
                class="w-full px-3 py-2 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
              >
                <option value="">All Units</option>
                <option v-for="unit in uniqueUnits" :key="unit" :value="unit">{{ unit }}</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Gender</label>
              <select
                v-model="filters.gender"
                class="w-full px-3 py-2 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
              >
                <option value="">All Genders</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
              </select>
            </div>
          </div>

          <!-- Advanced Filters -->
          <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Employment Status</label>
              <select
                v-model="filters.employment_status"
                class="w-full px-3 py-2 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
              >
                <option value="">All Statuses</option>
                <option v-for="status in uniqueStatuses" :key="status" :value="status">{{ status }}</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">City</label>
              <input
                v-model="filters.city"
                type="text"
                placeholder="Filter by city..."
                class="w-full px-3 py-2 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Birth Date Range</label>
              <div class="flex gap-2">
                <input
                  v-model="filters.birth_date_start"
                  type="date"
                  class="w-full px-3 py-2 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                />
                <input
                  v-model="filters.birth_date_end"
                  type="date"
                  class="w-full px-3 py-2 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                />
              </div>
            </div>
          </div>

          <div class="flex justify-end mt-4 gap-3">
            <button
              @click="resetFilters"
              class="px-4 py-2 rounded-lg border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
            >
              Reset Filters
            </button>
          </div>
        </div>

        <!-- Personnel Table -->
        <div class="overflow-hidden rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800">
          <div class="overflow-x-auto">
            <table class="w-full border-collapse">
              <thead class="bg-gray-50 dark:bg-gray-700">
                <tr class="text-left text-gray-500 dark:text-gray-300 text-sm font-medium">
                  <th class="p-4">Personnel No</th>
                  <th class="p-4">Name</th>
                  <th class="p-4">Job Title</th>
                  <th class="p-4">Unit</th>
                  <th class="p-4">Gender</th>
                  <th class="p-4">Phone</th>
                  <th class="p-4">Birth Date</th>
                  <th class="p-4 text-right">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                <tr
                  v-for="item in paginatedPersonnel"
                  :key="item.pers_no"
                  class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
                >
                  <td class="p-4 font-medium text-gray-800 dark:text-white">{{ item.pers_no }}</td>
                  <td class="p-4">
                    <div class="flex items-center gap-3">
                      <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center">
                        <span class="text-blue-600 dark:text-blue-300 font-medium text-sm">
                          {{ item.first_name.charAt(0) }}{{ item.last_name.charAt(0) }}
                        </span>
                      </div>
                      <span>{{ item.first_name }} {{ item.last_name }}</span>
                    </div>
                  </td>
                  <td class="p-4 text-gray-600 dark:text-gray-300">{{ item.job }}</td>
                  <td class="p-4 text-gray-600 dark:text-gray-300">{{ item.organizational_unit }}</td>
                  <td class="p-4">
                    <span
                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                      :class="{
                        'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300': item.gender_key === 'Male',
                        'bg-pink-100 text-pink-800 dark:bg-pink-900/30 dark:text-pink-300': item.gender_key === 'Female'
                      }"
                    >
                      {{ item.gender_key }}
                    </span>
                  </td>
                  <td class="p-4 text-gray-600 dark:text-gray-300">{{ item.phone || 'N/A' }}</td>
                  <td class="p-4 text-gray-600 dark:text-gray-300">{{ formatDate(item.birth_date) }}</td>
                  <td class="p-4 text-right">
                    <button
                      @click="viewPersonnel(item)"
                      class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                      </svg>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Empty State -->
          <div v-if="paginatedPersonnel.length === 0" class="flex flex-col items-center justify-center py-12">
            <div class="w-24 h-24 mb-4 text-gray-300 dark:text-gray-600">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
              </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-500 dark:text-gray-400 mb-1">No personnel records found</h3>
            <p class="text-gray-400 dark:text-gray-500 text-sm">Try adjusting your filters or upload new data</p>
          </div>
        </div>

        <!-- Client-Side Pagination -->
        <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
          <div class="text-sm text-gray-500 dark:text-gray-400">
            Showing {{ paginatedPersonnel.length }} of {{ filteredPersonnel.length }} records (Page {{ currentPage }} of {{ totalPages }})
          </div>
          <div class="flex gap-2">
            <button
              :disabled="currentPage === 1"
              @click="currentPage = Math.max(1, currentPage - 1)"
              class="flex items-center gap-1 px-4 py-2 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
              </svg>
              Previous
            </button>
            <button
              :disabled="currentPage === totalPages"
              @click="currentPage = Math.min(totalPages, currentPage + 1)"
              class="flex items-center gap-1 px-4 py-2 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Next
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </button>
          </div>
        </div>

        <!-- Personnel Details Modal -->
        <transition name="modal">
          <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50 backdrop-blur-sm">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-2xl overflow-hidden">
              <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                <div class="flex items-center justify-between">
                  <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Personnel Details</h2>
                  <button @click="cancelEdit" class="p-1 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                  </button>
                </div>
              </div>

              <div class="p-6 overflow-y-auto max-h-[70vh]">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div class="space-y-4">
                    <div>
                      <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Personal Information</h3>
                      <div class="mt-1 space-y-2">
                        <p class="text-gray-900 dark:text-white"><strong>Name:</strong> {{ selectedPersonnel.first_name }} {{ selectedPersonnel.last_name }}</p>
                        <p class="text-gray-900 dark:text-white"><strong>Personnel No:</strong> {{ selectedPersonnel.pers_no }}</p>
                        <p class="text-gray-900 dark:text-white"><strong>Gender:</strong> {{ selectedPersonnel.gender_key }}</p>
                        <div>
                          <strong class="text-gray-900 dark:text-white">Phone:</strong>
                          <input
                            v-model="selectedPersonnel.phone"
                            type="text"
                            placeholder="Enter phone number"
                            class="mt-1 w-full px-3 py-2 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                          />
                          <p v-if="phoneError" class="text-red-500 text-sm mt-1">{{ phoneError }}</p>
                        </div>
                        <p class="text-gray-900 dark:text-white"><strong>Birth Date:</strong> {{ formatDate(selectedPersonnel.birth_date) }}</p>
                        <p class="text-gray-900 dark:text-white"><strong>City:</strong> {{ selectedPersonnel.city || 'N/A' }}</p>
                      </div>
                    </div>

                    <div>
                      <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Employment Status</h3>
                      <div class="mt-1 space-y-2">
                        <p class="text-gray-900 dark:text-white"><strong>Status:</strong> {{ selectedPersonnel.employment_status || 'N/A' }}</p>
                        <p class="text-gray-900 dark:text-white"><strong>Manager ID:</strong> {{ selectedPersonnel.manager_id || 'N/A' }}</p>
                      </div>
                    </div>
                  </div>

                  <div class="space-y-4">
                    <div>
                      <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Position Details</h3>
                      <div class="mt-1 space-y-2">
                        <p class="text-gray-900 dark:text-white"><strong>Job Title:</strong> {{ selectedPersonnel.job }}</p>
                        <p class="text-gray-900 dark:text-white"><strong>Position Name:</strong> {{ selectedPersonnel.position_name || 'N/A' }}</p>
                        <p class="text-gray-900 dark:text-white"><strong>Position Code:</strong> {{ selectedPersonnel.position_code || 'N/A' }}</p>
                      </div>
                    </div>

                    <div>
                      <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Organizational Details</h3>
                      <div class="mt-1 space-y-2">
                        <p class="text-gray-900 dark:text-white"><strong>Unit:</strong> {{ selectedPersonnel.organizational_unit }}</p>
                        <p class="text-gray-900 dark:text-white"><strong>Org Unit:</strong> {{ selectedPersonnel.org_unit || 'N/A' }}</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="p-4 border-t border-gray-100 dark:border-gray-700 flex justify-end gap-3">
                <button
                  @click="cancelEdit"
                  class="px-4 py-2 rounded-lg border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                >
                  Cancel
                </button>
                <button
                  @click="savePhoneNumber"
                  class="px-4 py-2 rounded-lg bg-gradient-to-r from-blue-600 to-blue-500 text-white hover:from-blue-700 hover:to-blue-600 shadow-md transition-all"
                >
                  Save
                </button>
              </div>
            </div>
          </div>
        </transition>
      </div>
    </AdminLayout>
  </template>

  <script setup>
  import { ref, computed, onMounted } from 'vue';
  import { Inertia } from '@inertiajs/inertia';
  import AdminLayout from '../Components/AdminLayout.vue';
  import { usePage } from '@inertiajs/vue3';

  const showModal = ref(false);
  const selectedPersonnel = ref({});
  const originalPersonnel = ref(null); // Store original data for cancel
  const personnel = ref([]);
  const currentPage = ref(1);
  const perPage = 100; // Adjustable per-page value
  const fileInput = ref(null);
  const uploading = ref(false);
  const uploadProgress = ref(0);
  const phoneError = ref('');
  const notification = ref({ message: '', type: '' });
  const { personnels } = usePage().props;

  const filters = ref({
    search: '',
    job: '',
    organizational_unit: '',
    gender: '',
    employment_status: '',
    city: '',
    birth_date_start: '',
    birth_date_end: '',
  });

  // Computed properties for unique filter options
  const uniqueJobs = computed(() => {
    const jobs = new Set(personnel.value.map(item => item.job).filter(job => job));
    return [...jobs].sort();
  });

  const uniqueUnits = computed(() => {
    const units = new Set(personnel.value.map(item => item.organizational_unit).filter(unit => unit));
    return [...units].sort();
  });

  const uniqueStatuses = computed(() => {
    const statuses = new Set(personnel.value.map(item => item.employment_status).filter(status => status));
    return [...statuses].sort();
  });

  // Client-side filtering
  const filteredPersonnel = computed(() => {
    let result = personnel.value;

    if (filters.value.search) {
      const searchTerm = filters.value.search.toLowerCase();
      result = result.filter(item =>
        (item.first_name?.toLowerCase().includes(searchTerm) ||
         item.last_name?.toLowerCase().includes(searchTerm) ||
         item.pers_no?.toLowerCase().includes(searchTerm) ||
         item.phone?.toLowerCase().includes(searchTerm) ||
         item.job?.toLowerCase().includes(searchTerm))
      );
    }

    if (filters.value.job) {
      result = result.filter(item => item.job === filters.value.job);
    }

    if (filters.value.organizational_unit) {
      result = result.filter(item => item.organizational_unit === filters.value.organizational_unit);
    }

    if (filters.value.gender) {
      result = result.filter(item => item.gender_key === filters.value.gender);
    }

    if (filters.value.employment_status) {
      result = result.filter(item => item.employment_status === filters.value.employment_status);
    }

    if (filters.value.city) {
      const cityTerm = filters.value.city.toLowerCase();
      result = result.filter(item => item.city?.toLowerCase().includes(cityTerm));
    }

    if (filters.value.birth_date_start || filters.value.birth_date_end) {
      result = result.filter(item => {
        if (!item.birth_date) return false;
        const birthDate = new Date(item.birth_date);
        const startDate = filters.value.birth_date_start ? new Date(filters.value.birth_date_start) : null;
        const endDate = filters.value.birth_date_end ? new Date(filters.value.birth_date_end) : null;

        if (startDate && endDate) {
          return birthDate >= startDate && birthDate <= endDate;
        } else if (startDate) {
          return birthDate >= startDate;
        } else if (endDate) {
          return birthDate <= endDate;
        }
        return true;
      });
    }

    return result;
  });

  // Client-side pagination
  const totalPages = computed(() => Math.ceil(filteredPersonnel.value.length / perPage));
  const paginatedPersonnel = computed(() => {
    const start = (currentPage.value - 1) * perPage;
    const end = start + perPage;
    return filteredPersonnel.value.slice(start, end);
  });

  onMounted(() => {
    personnel.value = personnels.data;
  });

  const triggerFileUpload = () => fileInput.value.click();

  const viewPersonnel = (person) => {
    selectedPersonnel.value = { ...person }; // Create a copy to edit
    originalPersonnel.value = { ...person }; // Store original for cancel
    showModal.value = true;
  };

  const handleFileUpload = (event) => {
    const file = event.target.files[0];
    if (!file) return;

    uploading.value = true;
    uploadProgress.value = 0;

    const formData = new FormData();
    formData.append('file', file);

    Inertia.post('/admin/personnels/upload', formData, {
      onProgress: (event) => {
        uploadProgress.value = Math.round((event.loaded / event.total) * 100);
      },
      onSuccess: (page) => {
        uploading.value = false;
        personnel.value = page.props.personnels.data; // Update personnel data after upload
        showNotification('File uploaded successfully', 'success');
      },
      onError: () => {
        uploading.value = false;
        showNotification('Failed to upload file', 'error');
      },
    });
  };

  const savePhoneNumber = () => {
    // Basic phone number validation
    const phone = selectedPersonnel.value.phone?.trim();
    if (phone && !/^\+?[\d\s-]{7,15}$/.test(phone)) {
      phoneError.value = 'Please enter a valid phone number (7-15 digits, optional +, spaces, or hyphens)';
      return;
    }
    phoneError.value = '';

    Inertia.put(`/admin/personnels/${selectedPersonnel.value.pers_no}/phone`, { phone }, {
      onSuccess: () => {
        // Update local personnel data
        const index = personnel.value.findIndex(p => p.pers_no === selectedPersonnel.value.pers_no);
        if (index !== -1) {
          personnel.value[index].phone = phone;
        }
        showModal.value = false;
        showNotification('Phone number updated successfully', 'success');
      },
      onError: (errors) => {
        phoneError.value = errors.phone || 'Failed to update phone number';
      },
    });
  };

  const cancelEdit = () => {
    selectedPersonnel.value = { ...originalPersonnel.value }; // Restore original data
    phoneError.value = '';
    showModal.value = false;
  };

  const resetFilters = () => {
    filters.value = {
      search: '',
      job: '',
      organizational_unit: '',
      gender: '',
      employment_status: '',
      city: '',
      birth_date_start: '',
      birth_date_end: '',
    };
    currentPage.value = 1; // Reset to first page
  };

  const showNotification = (message, type) => {
    notification.value = { message, type };
    setTimeout(() => {
      notification.value = { message: '', type: '' };
    }, 3000); // Clear notification after 3 seconds
  };

  const formatDate = (date) => date ? new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  }) : 'N/A';
  </script>

  <style>
  .fade-enter-active,
  .fade-leave-active {
    transition: opacity 0.3s ease;
  }

  .fade-enter-from,
  .fade-leave-to {
    opacity: 0;
  }

  .modal-enter-active,
  .modal-leave-active {
    transition: all 0.3s ease;
  }

  .modal-enter-from,
  .modal-leave-to {
    opacity: 0;
    transform: translateY(-20px);
  }
  </style>