<template>
    <AdminLayout>
      <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-200 dark:from-gray-950 dark:to-gray-900 p-8">
        <!-- Header Section -->
        <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 mb-10">
          <div>
            <h1 class="text-4xl font-extrabold text-gray-900 dark:text-gray-100 tracking-tight">Service Dashboard</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2 text-lg">Organize and manage your service offerings</p>
          </div>
          <Button
            @click="openCreateModal"
            class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-6 py-3 rounded-xl transition-all duration-200 shadow-md hover:shadow-lg"
          >
            <PlusIcon class="w-5 h-5" />
            Add New Service
          </Button>
        </header>

        <!-- Search and Filter Section -->
        <div class="flex flex-col md:flex-row gap-4 mb-8">
          <div class="relative flex-grow">
            <input
              v-model="searchTerm"
              type="text"
              placeholder="Search by name or description..."
              class="w-full pl-12 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 outline-none transition-all shadow-sm"
            />
            <MagnifyingGlassIcon class="absolute left-4 top-3.5 h-5 w-5 text-gray-400 dark:text-gray-500" />
          </div>
          <select
            v-model="sortBy"
            class="px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 outline-none transition-all shadow-sm"
          >
            <option value="name_asc">Sort by Name (A-Z)</option>
            <option value="name_desc">Sort by Name (Z-A)</option>
            <option value="created_asc">Sort by Created (Oldest)</option>
            <option value="created_desc">Sort by Created (Newest)</option>
          </select>
        </div>

        <!-- Service List Table -->
        <div class="overflow-hidden rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
          <table class="w-full border-collapse">
            <thead class="bg-gray-100 dark:bg-gray-800/50">
              <tr class="text-left text-gray-600 dark:text-gray-300 text-sm font-semibold">
                <th class="p-5">Name</th>
                <th class="p-5">Description</th>
                <th class="p-5">Created At</th>
                <th class="p-5 text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
              <tr
                v-for="item in filteredItems"
                :key="item.id"
                class="hover:bg-gray-50 dark:hover:bg-gray-700/20 transition-all duration-150"
              >
                <td class="p-5 font-medium text-gray-900 dark:text-gray-100">{{ item.name }}</td>
                <td class="p-5 text-gray-600 dark:text-gray-300 line-clamp-2">{{ item.description }}</td>
                <td class="p-5 text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ formatDate(item.created_at) }}</td>
                <td class="p-5 flex justify-end gap-3">
                  <Button
                    @click="openEditModal(item)"
                    class="flex items-center gap-2 text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 px-4 py-2 rounded-lg hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition-all duration-200"
                  >
                    <PencilSquareIcon class="w-4 h-4" />
                    Edit
                  </Button>
                  <Button
                    @click="deleteItem(item)"
                    class="flex items-center gap-2 text-sm text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 px-4 py-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/30 transition-all duration-200"
                  >
                    <TrashIcon class="w-4 h-4" />
                    Delete
                  </Button>
                </td>
              </tr>
            </tbody>
          </table>
          <div v-if="filteredItems.length === 0" class="p-10 text-center text-gray-500 dark:text-gray-400 text-lg">
            No services found. Try adjusting your search or create a new service.
          </div>
        </div>

        <!-- Modal: Create Service -->
        <transition name="modal">
          <div v-if="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" @click="closeCreateModal"></div>
            <div class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-2xl transition-all transform overflow-hidden">
              <div class="absolute top-0 right-0 pt-4 pr-4">
                <button @click="closeCreateModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                  <XMarkIcon class="h-6 w-6" />
                </button>
              </div>
              <div class="p-8">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-3">Create New Service</h2>
                <p class="text-gray-500 dark:text-gray-400 mb-6">Add details for your new service</p>

                <form @submit.prevent="createService" class="space-y-5">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Service Name</label>
                    <input
                      v-model="newService.name"
                      type="text"
                      required
                      class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all shadow-sm"
                    />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Description</label>
                    <textarea
                      v-model="newService.description"
                      rows="4"
                      required
                      class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all shadow-sm"
                    ></textarea>
                  </div>
                  <div class="flex justify-end gap-4 pt-4">
                    <Button
                      @click="closeCreateModal"
                      type="button"
                      class="px-5 py-3 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-all duration-200"
                    >
                      Cancel
                    </Button>
                    <Button
                      type="submit"
                      class="px-5 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg shadow-sm transition-all duration-200"
                    >
                      Create Service
                    </Button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </transition>

        <!-- Modal: Edit Service -->
        <transition name="modal">
          <div v-if="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" @click="closeEditModal"></div>
            <div class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-2xl transition-all transform overflow-hidden">
              <div class="absolute top-0 right-0 pt-4 pr-4">
                <button @click="closeEditModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                  <XMarkIcon class="h-6 w-6" />
                </button>
              </div>
              <div class="p-8">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-3">Edit Service</h2>
                <p class="text-gray-500 dark:text-gray-400 mb-6">Update the details for this service</p>

                <form @submit.prevent="updateService" class="space-y-5">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Service Name</label>
                    <input
                      v-model="editService.name"
                      type="text"
                      required
                      class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all shadow-sm"
                    />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Description</label>
                    <textarea
                      v-model="editService.description"
                      rows="4"
                      required
                      class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all shadow-sm"
                    ></textarea>
                  </div>
                  <div class="flex justify-end gap-4 pt-4">
                    <Button
                      @click="closeEditModal"
                      type="button"
                      class="px-5 py-3 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-all duration-200"
                    >
                      Cancel
                    </Button>
                    <Button
                      type="submit"
                      class="px-5 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg shadow-sm transition-all duration-200"
                    >
                      Update Service
                    </Button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </transition>
      </div>
    </AdminLayout>
  </template>

  <script setup>
  import { ref, computed, onMounted } from 'vue';
  import { usePage } from '@inertiajs/vue3';
  import { Inertia } from '@inertiajs/inertia';
  import AdminLayout from '../Components/AdminLayout.vue';
  import Swal from 'sweetalert2';
  import {
    MagnifyingGlassIcon,
    PencilSquareIcon,
    TrashIcon,
    PlusIcon,
    XMarkIcon
  } from '@heroicons/vue/24/outline';

  const searchTerm = ref('');
  const sortBy = ref('name_asc');
  const items = ref([]);
  const showCreateModal = ref(false);
  const showEditModal = ref(false);
  const newService = ref({ name: '', description: '' });
  const editService = ref({ id: null, name: '', description: '' });

  // Fetch services from backend
  const { services } = usePage().props;
  onMounted(() => {
    items.value = services;
  });

  // Filter and sort logic
  const filteredItems = computed(() => {
    let result = items.value.filter(item =>
      item.name.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
      item.description.toLowerCase().includes(searchTerm.value.toLowerCase())
    );

    switch (sortBy.value) {
      case 'name_asc':
        return result.sort((a, b) => a.name.localeCompare(b.name));
      case 'name_desc':
        return result.sort((a, b) => b.name.localeCompare(a.name));
      case 'created_asc':
        return result.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
      case 'created_desc':
        return result.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
      default:
        return result;
    }
  });

  // Modal Handlers
  const openCreateModal = () => {
    showCreateModal.value = true;
  };

  const closeCreateModal = () => {
    showCreateModal.value = false;
    newService.value = { name: '', description: '' };
  };

  const openEditModal = (item) => {
    editService.value = { id: item.id, name: item.name, description: item.description };
    showEditModal.value = true;
  };

  const closeEditModal = () => {
    showEditModal.value = false;
    editService.value = { id: null, name: '', description: '' };
  };

  // Create Service
  const createService = async () => {
    try {
      await Inertia.post('/services', newService.value, {
        onSuccess: () => {
          Swal.fire({
            icon: 'success',
            title: 'Service Created',
            toast: true,
            position: 'top-end',
            timer: 2000,
            showConfirmButton: false,
            customClass: {
              popup: 'rounded-xl bg-white dark:bg-gray-800',
              title: 'text-gray-900 dark:text-gray-100',
              htmlContainer: 'text-gray-600 dark:text-gray-300'
            }
          });
          closeCreateModal();
        }
      });
    } catch (error) {
      Swal.fire({
        icon: 'error',
        title: 'Failed to create service',
        customClass: {
          popup: 'rounded-xl bg-white dark:bg-gray-800',
          title: 'text-gray-900 dark:text-gray-100',
          htmlContainer: 'text-gray-600 dark:text-gray-300'
        }
      });
    }
  };

  // Update Service
  const updateService = async () => {
    try {
      await Inertia.put(`/services/${editService.value.id}`, editService.value, {
        onSuccess: () => {
          Swal.fire({
            icon: 'success',
            title: 'Service Updated',
            toast: true,
            position: 'top-end',
            timer: 2000,
            showConfirmButton: false,
            customClass: {
              popup: 'rounded-xl bg-white dark:bg-gray-800',
              title: 'text-gray-900 dark:text-gray-100',
              htmlContainer: 'text-gray-600 dark:text-gray-300'
            }
          });
          closeEditModal();
        }
      });
    } catch (error) {
      Swal.fire({
        icon: 'error',
        title: 'Failed to update service',
        customClass: {
          popup: 'rounded-xl bg-white dark:bg-gray-800',
          title: 'text-gray-900 dark:text-gray-100',
          htmlContainer: 'text-gray-600 dark:text-gray-300'
        }
      });
    }
  };

  // Delete Service
  const deleteItem = async (item) => {
    const result = await Swal.fire({
      title: 'Delete Service?',
      text: `Are you sure you want to delete "${item.name}"? This action cannot be undone.`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#4F46E5',
      cancelButtonColor: '#EF4444',
      confirmButtonText: 'Yes, delete it',
      cancelButtonText: 'Cancel',
      reverseButtons: true,
      customClass: {
        popup: 'rounded-xl bg-white dark:bg-gray-800',
        title: 'text-gray-900 dark:text-gray-100',
        htmlContainer: 'text-gray-600 dark:text-gray-300',
        confirmButton: 'rounded-lg',
        cancelButton: 'rounded-lg'
      }
    });

    if (result.isConfirmed) {
      await Inertia.delete(`/services/${item.id}`, {
        onSuccess: () => {
          items.value = items.value.filter(i => i.id !== item.id);
          Swal.fire({
            icon: 'success',
            title: 'Service Deleted',
            toast: true,
            position: 'top-end',
            timer: 2000,
            showConfirmButton: false,
            customClass: {
              popup: 'rounded-xl bg-white dark:bg-gray-800',
              title: 'text-gray-900 dark:text-gray-100',
              htmlContainer: 'text-gray-600 dark:text-gray-300'
            }
          });
        }
      });
    }
  };

  // Format Date Function
  const formatDate = (date) => {
    if (!date) return 'N/A';
    return new Date(date).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    });
  };
  </script>

  <style scoped>
  /* Modal Transition */
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

  /* Line clamp for description */
  .line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  /* Enhanced transitions and shadows */
  button, input, select {
    transition: all 0.3s ease;
  }

  /* Custom focus styles */
  input:focus, select:focus {
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