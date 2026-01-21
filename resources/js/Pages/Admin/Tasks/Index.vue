<template>
    <AdminLayout>
      <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-200 dark:from-gray-950 dark:to-gray-900 p-8">
        <!-- Header Section -->
        <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 mb-10">
          <div>
            <h1 class="text-4xl font-extrabold text-gray-900 dark:text-gray-100 tracking-tight">Task Dashboard</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2 text-lg">Manage and track your service tasks</p>
          </div>
          <Button
            @click="openCreateModal"
            class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-6 py-3 rounded-xl transition-all duration-200 shadow-md hover:shadow-lg"
          >
            <PlusIcon class="w-5 h-5" />
            New Task
          </Button>
        </header>

        <!-- Search and Filter Section -->
        <div class="flex flex-col md:flex-row gap-4 mb-8">
          <div class="relative flex-grow">
            <MagnifyingGlassIcon class="absolute left-4 top-3.5 h-5 w-5 text-gray-400 dark:text-gray-500" />
            <input
              v-model="searchTerm"
              type="text"
              placeholder="Search by task or service..."
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

        <!-- Task List Table -->
        <div class="overflow-hidden rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
          <table class="w-full border-collapse">
            <thead class="bg-gray-100 dark:bg-gray-800/50">
              <tr class="text-left text-gray-600 dark:text-gray-300 text-sm font-semibold">
                <th class="p-5">Task Name</th>
                <th class="p-5">Service</th>
                <th class="p-5">Time (min)</th>
                <th class="p-5">Created</th>
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
                <td class="p-5">
                  <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-200 capitalize">
                    {{ item.service.name }}
                  </span>
                </td>
                <td class="p-5 text-gray-600 dark:text-gray-300">{{ item.processing_time }}</td>
                <td class="p-5 text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ formatDate(item.created_at) }}</td>
                <td class="p-5 flex justify-end gap-3">
                  <Button
                    @click="editTask(item)"
                    class="flex items-center gap-2 text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 px-4 py-2 rounded-lg hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition-all duration-200"
                  >
                    <PencilSquareIcon class="w-4 h-4" />
                    Edit
                  </Button>
                  <Button
                    @click="deleteTask(item)"
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
            No tasks found. Try adjusting your search or create a new task.
          </div>
        </div>

        <!-- Modal: Create/Edit Task -->
        <transition name="modal">
          <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" @click="closeModal"></div>
            <div class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-2xl transition-all transform overflow-hidden">
              <div class="absolute top-0 right-0 pt-4 pr-4">
                <button @click="closeModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                  <XMarkIcon class="h-6 w-6" />
                </button>
              </div>
              <div class="p-8">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-3">
                  {{ isEditing ? 'Edit Task' : 'Create New Task' }}
                </h2>
                <p class="text-gray-500 dark:text-gray-400 mb-6">
                  {{ isEditing ? 'Update task details' : 'Add details for your new task' }}
                </p>

                <form @submit.prevent="saveTask" class="space-y-5">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Task Name</label>
                    <input
                      v-model="taskData.name"
                      type="text"
                      required
                      class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all shadow-sm"
                    />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Service</label>
                    <select
                      v-model="taskData.service_id"
                      required
                      class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all appearance-none"
                    >
                      <option v-for="service in services" :key="service.id" :value="service.id">
                        {{ service.name }}
                      </option>
                    </select>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Processing Time (minutes)</label>
                    <input
                      v-model="taskData.processing_time"
                      type="number"
                      min="1"
                      required
                      class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all shadow-sm"
                    />
                  </div>
                  <div class="flex justify-end gap-4 pt-4">
                    <Button
                      @click="closeModal"
                      type="button"
                      class="px-5 py-3 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-all duration-200"
                    >
                      Cancel
                    </Button>
                    <Button
                      type="submit"
                      class="px-5 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg shadow-sm transition-all duration-200"
                    >
                      {{ isEditing ? 'Update Task' : 'Create Task' }}
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
  import AdminLayout from '../Components/AdminLayout.vue';
  import { Inertia } from '@inertiajs/inertia';
  import { usePage } from '@inertiajs/vue3';
  import Swal from 'sweetalert2';
  import {
    MagnifyingGlassIcon,
    PencilSquareIcon,
    TrashIcon,
    PlusIcon,
    XMarkIcon
  } from '@heroicons/vue/24/outline';

  const searchTerm = ref('');
  const serviceFilter = ref('');
  const tasks = ref([]);
  const services = ref([]);
  const showModal = ref(false);
  const isEditing = ref(false);
  const taskData = ref({ name: '', service_id: '', processing_time: '' });

  const { tasks: taskList, services: serviceList } = usePage().props;
  onMounted(() => {
    tasks.value = taskList;
    services.value = serviceList;
  });

  const filteredItems = computed(() => {
    return tasks.value.filter(item => {
      const matchesSearch = (
        item.name.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
        item.service.name.toLowerCase().includes(searchTerm.value.toLowerCase())
      );
      const matchesService = serviceFilter.value ? item.service_id === serviceFilter.value : true;
      return matchesSearch && matchesService;
    });
  });

  const openCreateModal = () => {
    showModal.value = true;
    isEditing.value = false;
    taskData.value = { name: '', service_id: '', processing_time: '' };
  };

  const editTask = (item) => {
    showModal.value = true;
    isEditing.value = true;
    taskData.value = { ...item };
  };

  const saveTask = async () => {
    try {
      if (isEditing.value) {
        await Inertia.put(`/tasks/${taskData.value.id}`, taskData.value, {
          onSuccess: () => showSuccessToast('Task updated successfully')
        });
      } else {
        await Inertia.post('/tasks', taskData.value, {
          onSuccess: () => showSuccessToast('Task created successfully')
        });
      }
      closeModal();
    } catch (error) {
      showErrorToast(isEditing.value ? 'Failed to update task' : 'Failed to create task');
    }
  };

  const deleteTask = async (item) => {
    const result = await Swal.fire({
      title: 'Delete Task?',
      text: `Are you sure you want to delete "${item.name}"? This action cannot be undone.`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#4F46E5',
      cancelButtonColor: '#EF4444',
      confirmButtonText: 'Delete',
      cancelButtonText: 'Cancel',
      reverseButtons: true,
      customClass: {
        popup: 'rounded-xl bg-white dark:bg-gray-800',
        title: 'text-gray-900 dark:text-white',
        htmlContainer: 'text-gray-600 dark:text-gray-300',
        confirmButton: 'rounded-lg',
        cancelButton: 'rounded-lg'
      }
    });

    if (result.isConfirmed) {
      try {
        await Inertia.delete(`/tasks/${item.id}`, {
          onSuccess: () => showSuccessToast('Task deleted successfully')
        });
      } catch (error) {
        showErrorToast('Failed to delete task');
      }
    }
  };

  const closeModal = () => {
    showModal.value = false;
  };

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

  const showSuccessToast = (message) => {
    Swal.fire({
      position: 'top-end',
      icon: 'success',
      title: message,
      toast: true,
      showConfirmButton: false,
      timer: 2000,
      customClass: {
        popup: 'rounded-xl bg-white dark:bg-gray-800',
        title: 'text-gray-900 dark:text-gray-100'
      }
    });
  };

  const showErrorToast = (message) => {
    Swal.fire({
      position: 'top-end',
      icon: 'error',
      title: message,
      toast: true,
      showConfirmButton: false,
      timer: 2000,
      customClass: {
        popup: 'rounded-xl bg-white dark:bg-gray-800',
        title: 'text-gray-900 dark:text-gray-100'
      }
    });
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