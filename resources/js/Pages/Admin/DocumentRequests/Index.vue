<template>
    <AdminLayout>
      <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-200 dark:from-gray-950 dark:to-gray-900 p-8">
        <!-- Header Section -->
        <header class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 mb-10">
          <div>
            <h1 class="text-4xl font-extrabold text-gray-900 dark:text-gray-100 tracking-tight">Document Requests</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2 text-lg">Manage and track all document requests from users</p>
          </div>
        </header>

        <!-- Search and Filter Section -->
        <div class="flex flex-col md:flex-row gap-4 mb-8">
          <div class="relative flex-grow">
            <MagnifyingGlassIcon class="absolute left-4 top-3.5 h-5 w-5 text-gray-400 dark:text-gray-500" />
            <input
              v-model="search"
              type="text"
              placeholder="Search by user or document..."
              class="w-full pl-12 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 outline-none transition-all shadow-sm"
            />
          </div>
          <select
            v-model="serviceFilter"
            class="px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 outline-none transition-all shadow-sm"
          >
            <option value="">All Services</option>
            <option v-for="service in services" :key="service.id" :value="service.id">{{ service.name }}</option>
          </select>
        </div>

        <!-- Request Table -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
              <thead class="bg-gray-100 dark:bg-gray-800/50">
                <tr>
                  <th scope="col" class="px-6 py-4 text-left text-sm font-semibold text-gray-600 dark:text-gray-300">User</th>
                  <th scope="col" class="px-6 py-4 text-left text-sm font-semibold text-gray-600 dark:text-gray-300">Document</th>
                  <th scope="col" class="px-6 py-4 text-left text-sm font-semibold text-gray-600 dark:text-gray-300">Service</th>
                  <th scope="col" class="px-6 py-4 text-left text-sm font-semibold text-gray-600 dark:text-gray-300">Status</th>
                  <th scope="col" class="px-6 py-4 text-left text-sm font-semibold text-gray-600 dark:text-gray-300">Agent</th>
                  <th scope="col" class="px-6 py-4 text-left text-sm font-semibold text-gray-600 dark:text-gray-300">Comment</th>
                  <th scope="col" class="px-6 py-4 text-right text-sm font-semibold text-gray-600 dark:text-gray-300">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <tr v-for="request in filteredRequests" :key="request.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/20 transition-all duration-150">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100" :title="request.user ? '' : 'User data missing'">
                        {{ request.personnel?.first_name }} {{ request.personnel?.last_name }}

                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900 dark:text-gray-100" :title="request.template ? '' : 'Template data missing'">
                      {{ request.template?.name || request.template_id || 'Unknown Document' }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900 dark:text-gray-100">{{ request.service?.name || 'N/A' }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="statusClasses(request.status)" class="px-3 py-1 rounded-full text-sm font-medium capitalize">
                      {{ request.status || 'N/A' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900 dark:text-gray-100">
                      {{ request.agent?.name || 'Unassigned' }}
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-sm text-gray-900 dark:text-gray-100 truncate max-w-xs">{{ request.agent_comment || 'N/A' }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <div class="flex space-x-3 justify-end">
                      <Button
                        @click="openModal(request)"
                        class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 transition-all duration-200"
                      >
                        <PencilSquareIcon class="w-5 h-5" />
                      </Button>
                      <Button
                        @click="viewData(request)"
                        class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300 transition-all duration-200"
                      >
                        <EyeIcon class="w-5 h-5" />
                      </Button>
                    </div>
                  </td>
                </tr>
                <tr v-if="filteredRequests.length === 0">
                  <td colspan="7" class="px-6 py-6 text-center text-lg text-gray-500 dark:text-gray-400">
                    No requests found. Try adjusting your search or filter.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Status Update Modal -->
        <transition name="modal">
          <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" @click="showModal = false"></div>
            <div class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-2xl transition-all transform overflow-hidden">
              <div class="absolute top-0 right-0 pt-4 pr-4">
                <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                  <XMarkIcon class="h-6 w-6" />
                </button>
              </div>
              <div class="p-8">
                <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-3">Update Request Status</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-6">Modify the status and add comments for this request</p>
                <form @submit.prevent="updateStatus" class="space-y-5">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Status</label>
                    <select
                      v-model="form.status"
                      class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all shadow-sm"
                    >
                      <option value="pending">Pending</option>
                      <option value="processing">Processing</option>
                      <option value="completed">Completed</option>
                      <option value="rejected">Rejected</option>
                    </select>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Agent Comment</label>
                    <textarea
                      v-model="form.agent_comment"
                      rows="4"
                      class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all shadow-sm"
                    ></textarea>
                  </div>
                  <div class="flex justify-end gap-4 pt-4">
                    <Button
                      @click="showModal = false"
                      type="button"
                      class="px-5 py-3 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-all duration-200"
                    >
                      Cancel
                    </Button>
                    <Button
                      type="submit"
                      class="px-5 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg shadow-sm transition-all duration-200"
                    >
                      Update Status
                    </Button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </transition>

        <!-- View Data Modal -->
        <transition name="modal">
          <div v-if="showDataModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" @click="showDataModal = false"></div>
            <div class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-2xl transition-all transform overflow-hidden">
              <div class="absolute top-0 right-0 pt-4 pr-4">
                <button @click="showDataModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                  <XMarkIcon class="h-6 w-6" />
                </button>
              </div>
              <div class="p-8">
                <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-3">Request Data</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-6">View the submitted data for this request</p>
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                  <div v-if="selectedRequest?.data && Object.keys(selectedRequest.data).length" class="space-y-4">
                    <div v-for="(value, key) in selectedRequest.data" :key="key" class="grid grid-cols-3 gap-4">
                      <div class="col-span-1 text-sm font-medium text-gray-700 dark:text-gray-300 capitalize">{{ key }}</div>
                      <div class="col-span-2 text-sm text-gray-900 dark:text-gray-100 break-words">{{ value || '-' }}</div>
                    </div>
                  </div>
                  <div v-else class="text-center text-gray-500 dark:text-gray-400 py-4 text-lg">
                    No data available
                  </div>
                </div>
                <div class="flex justify-end pt-6">
                  <Button
                    @click="showDataModal = false"
                    type="button"
                    class="px-5 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg shadow-sm transition-all duration-200"
                  >
                    Close
                  </Button>
                </div>
              </div>
            </div>
          </div>
        </transition>
      </div>
    </AdminLayout>
  </template>

  <script setup>
  import AdminLayout from "@/Pages/Admin/Components/AdminLayout.vue";
  import { ref, computed, onMounted } from "vue";
  import { router } from "@inertiajs/vue3";
  import Swal from 'sweetalert2';
  import {
    MagnifyingGlassIcon,
    PencilSquareIcon,
    EyeIcon,
    XMarkIcon
  } from '@heroicons/vue/24/outline';

  const props = defineProps({
    requests: Array,
    services: Array
  });

  const search = ref('');
  const serviceFilter = ref('');
  const showModal = ref(false);
  const showDataModal = ref(false);
  const selectedRequest = ref(null);
  const form = ref({ status: '', agent_comment: '' });

  // Debug: Log requests prop to inspect data structure
  onMounted(() => {
    console.log('Requests Prop:', props.requests);
  });

  const filteredRequests = computed(() => {
    return props.requests.filter(req => {
      const matchesSearch = (
        String(req.user?.name || req.user_id || '').toLowerCase().includes(search.value.toLowerCase()) ||
        String(req.template?.name || req.template_id || '').toLowerCase().includes(search.value.toLowerCase())
      );
      const matchesService = serviceFilter.value ? req.service_id === serviceFilter.value : true;
      return matchesSearch && matchesService;
    });
  });

  const statusClasses = (status) => {
    const base = "inline-flex items-center";
    switch (status) {
      case 'pending': return `${base} bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200`;
      case 'processing': return `${base} bg-indigo-100 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-200`;
      case 'completed': return `${base} bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200`;
      case 'rejected': return `${base} bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200`;
      default: return `${base} bg-gray-100 dark:bg-gray-700/30 text-gray-800 dark:text-gray-300`;
    }
  };

  const openModal = (request) => {
    selectedRequest.value = request;
    form.value.status = request.status || 'pending';
    form.value.agent_comment = request.agent_comment || '';
    showModal.value = true;
  };

  const updateStatus = () => {
    router.put(route('document_requests.update', selectedRequest.value.id), form.value, {
      onSuccess: () => {
        showModal.value = false;
        Swal.fire({
          icon: 'success',
          title: 'Status Updated',
          toast: true,
          position: 'top-end',
          timer: 2000,
          showConfirmButton: false,
          customClass: {
            popup: 'rounded-xl bg-white dark:bg-gray-800',
            title: 'text-gray-900 dark:text-gray-100'
          }
        });
      },
      onError: () => {
        Swal.fire({
          icon: 'error',
          title: 'Failed to update status',
          customClass: {
            popup: 'rounded-xl bg-white dark:bg-gray-800',
            title: 'text-gray-900 dark:text-gray-100'
          }
        });
      }
    });
  };

  const viewData = (request) => {
    selectedRequest.value = request;
    showDataModal.value = true;
  };
  </script>

  <style scoped>
  .modal-enter-active,
  .modal-leave-active {
    transition: opacity 0.3s ease;
  }

  .modal-enter-from,
  .modal-leave-to {
    opacity: 0;
  }

  .modal-enter-active .modal-container,
  .modal-leave-active .modal-container {
    transition: transform 0.3s ease, opacity 0.3s ease;
  }

  .modal-enter-from .modal-container,
  .modal-leave-to .modal-container {
    transform: scale(0.95);
    opacity: 0;
  }

  /* Enhanced transitions and shadows */
  button,
  input,
  select {
    transition: all 0.3s ease;
  }

  /* Custom focus styles */
  input:focus,
  select:focus {
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
  }

  /* Table row hover effect */
  tbody tr:hover {
    transform: translateY(-1px);
  }

  /* Improved button hover effects */
  button:hover {
    transform: translateY(-1px);
  }
  </style>