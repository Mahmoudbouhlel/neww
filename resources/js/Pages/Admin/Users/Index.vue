<template>
    <AdminLayout>
      <div class="p-6 min-h-screen bg-gray-50 dark:bg-gray-900">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
          <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-white">User Management</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Manage all system users and permissions</p>
          </div>
          <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
            <div class="relative flex-grow">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
              </div>
              <input
                v-model="searchTerm"
                type="text"
                placeholder="Search users..."
                class="pl-10 w-full px-4 py-2.5 border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
              />
            </div>
            <Button @click="openCreateModal" class="flex items-center gap-2 bg-gradient-to-r from-blue-600 to-blue-500 text-white px-4 py-2.5 rounded-lg hover:from-blue-700 hover:to-blue-600 shadow-md transition-all">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
              </svg>
              Add User
            </Button>
          </div>
        </div>

        <!-- User List Table -->
        <div class="overflow-hidden rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800">
          <div class="overflow-x-auto">
            <table class="w-full border-collapse">
              <thead class="bg-gray-50 dark:bg-gray-700">
                <tr class="text-left text-gray-500 dark:text-gray-300 text-sm font-medium">
                  <th class="p-4">Name</th>
                  <th class="p-4">Email</th>
                  <th class="p-4">Role</th>
                  <th class="p-4">Service</th>
                  <th class="p-4">Created</th>
                  <th class="p-4 text-right">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                <tr v-for="item in filteredItems" :key="item.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                  <td class="p-4">
                    <div class="flex items-center gap-3">
                      <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center">
                        <span class="text-blue-600 dark:text-blue-300 font-medium text-sm">{{ item.name.charAt(0) }}</span>
                      </div>
                      <span class="font-medium text-gray-800 dark:text-white">{{ item.name }}</span>
                    </div>
                  </td>
                  <td class="p-4 text-gray-600 dark:text-gray-300">{{ item.email }}</td>
                  <td class="p-4">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium"
                      :class="{
                        'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300': item.role === 'super_admin',
                        'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300': item.role === 'agent',
                        'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300': item.role === 'user'
                      }">
                      {{ formatRole(item.role) }}
                    </span>
                  </td>
                  <td class="p-4 text-gray-600 dark:text-gray-300">
                    <span v-if="item.service" class="inline-flex items-center gap-1">
                      <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path>
                      </svg>
                      {{ item.service.name }}
                    </span>
                    <span v-else class="text-gray-400">N/A</span>
                  </td>
                  <td class="p-4 text-gray-500 dark:text-gray-400 text-sm">{{ formatDate(item.created_at) }}</td>
                  <td class="p-4 text-right">
                    <div class="flex justify-end gap-2">
                      <button @click="editItem(item)" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                      </button>
                      <button @click="deleteItem(item)" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500 hover:text-red-600 dark:hover:text-red-400 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="filteredItems.length === 0" class="flex flex-col items-center justify-center py-12">
          <div class="w-24 h-24 mb-4 text-gray-300 dark:text-gray-600">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
          </div>
          <h3 class="text-lg font-medium text-gray-500 dark:text-gray-400 mb-1">No users found</h3>
          <p class="text-gray-400 dark:text-gray-500 text-sm">Try adjusting your search or create a new user</p>
        </div>

        <!-- Modal: Create/Edit User -->
        <transition name="modal">
          <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50 backdrop-blur-sm">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
              <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                <div class="flex items-center justify-between">
                  <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ isEditing ? 'Edit User' : 'Add New User' }}</h2>
                  <button @click="closeModal" class="p-1 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                  </button>
                </div>
              </div>

              <form @submit.prevent="saveUser" class="p-6 space-y-5">
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Full Name</label>
                  <input v-model="userData.name" type="text" required class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email Address</label>
                  <input v-model="userData.email" type="email" required class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                </div>

                <div v-if="!isEditing">
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password</label>
                  <input v-model="userData.password" type="password" required class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Role</label>
                  <select v-model="userData.role" required class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all appearance-none">
                    <option value="super_admin">Super Admin</option>
                    <option value="agent">Agent</option>
                    <option value="user">User</option>
                  </select>
                </div>

                <div v-if="userData.role === 'agent'">
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Assigned Service</label>
                  <select v-model="userData.service_id" class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all appearance-none">
                    <option v-for="service in services" :key="service.id" :value="service.id">{{ service.name }}</option>
                  </select>
                </div>

                <div class="flex justify-end gap-3 pt-4">
                  <button @click="closeModal" type="button" class="px-4 py-2.5 rounded-lg border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Cancel
                  </button>
                  <button type="submit" class="px-4 py-2.5 rounded-lg bg-gradient-to-r from-blue-600 to-blue-500 text-white hover:from-blue-700 hover:to-blue-600 shadow-md transition-all">
                    {{ isEditing ? 'Update User' : 'Create User' }}
                  </button>
                </div>
              </form>
            </div>
          </div>
        </transition>
      </div>
    </AdminLayout>
  </template>

  <script setup>
  import { ref, computed, onMounted, watch } from 'vue';
  import AdminLayout from '../Components/AdminLayout.vue';
  import { Inertia } from '@inertiajs/inertia';
  import { usePage } from '@inertiajs/vue3';
  import Swal from 'sweetalert2';

  const searchTerm = ref('');
  const items = ref([]);
  const services = ref([]);
  const showModal = ref(false);
  const isEditing = ref(false);
  const userData = ref({
    name: '',
    email: '',
    password: '',
    role: 'user',
    service_id: null
  });

  const { users, services: serviceList } = usePage().props;

  onMounted(() => {
    items.value = users;
    services.value = serviceList;
  });

  // Watch role change to reset service_id if not an agent
  watch(() => userData.value.role, (newRole) => {
    if (newRole !== 'agent') userData.value.service_id = null;
  });

  // Format role names for display
  const formatRole = (role) => {
    const roles = {
      'super_admin': 'Super Admin',
      'agent': 'Agent',
      'user': 'User'
    };
    return roles[role] || role;
  };

  // Filter users
  const filteredItems = computed(() =>
    items.value.filter(item =>
      item.name.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
      item.email.toLowerCase().includes(searchTerm.value.toLowerCase())
    )
  );

  // Format date properly
  const formatDate = (date) => {
    if (!date) return 'N/A';
    return new Date(date).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric'
    });
  };

  // Open modal for new user
  const openCreateModal = () => {
    isEditing.value = false;
    showModal.value = true;
    userData.value = { name: '', email: '', password: '', role: 'user', service_id: null };
  };

  // Open modal for edit
  const editItem = (item) => {
    isEditing.value = true;
    showModal.value = true;
    userData.value = { ...item, password: '' };
  };

  // Save user (Create/Edit)
  const saveUser = async () => {
    try {
      if (isEditing.value) {
        await Inertia.put(`/users/${userData.value.id}`, userData.value);
        showSuccessToast('User updated successfully');
      } else {
        await Inertia.post('/users', userData.value);
        showSuccessToast('User created successfully');
      }
      closeModal();
    } catch (error) {
      showErrorToast('An error occurred. Please try again.');
    }
  };

  // Delete user
  const deleteItem = async (item) => {
    const result = await Swal.fire({
      title: 'Delete User?',
      text: `Are you sure you want to delete ${item.name}? This action cannot be undone.`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Delete',
      cancelButtonText: 'Cancel',
      confirmButtonColor: '#ef4444',
      reverseButtons: true,
      customClass: {
        container: 'z-[10000]'
      }
    });

    if (result.isConfirmed) {
      try {
        await Inertia.delete(`/users/${item.id}`);
        showSuccessToast('User deleted successfully');
      } catch (error) {
        showErrorToast('Failed to delete user');
      }
    }
  };

  // Show success toast
  const showSuccessToast = (message) => {
    Swal.fire({
      title: 'Success!',
      text: message,
      icon: 'success',
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true,
      background: '#f0fdf4',
      iconColor: '#10b981'
    });
  };

  // Show error toast
  const showErrorToast = (message) => {
    Swal.fire({
      title: 'Error!',
      text: message,
      icon: 'error',
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true,
      background: '#fef2f2',
      iconColor: '#ef4444'
    });
  };

  const closeModal = () => { showModal.value = false; };
  </script>

  <style>
  .modal-enter-active,
  .modal-leave-active {
    transition: all 0.3s ease;
  }

  .modal-enter-from,
  .modal-leave-to {
    opacity: 0;
    transform: scale(0.95);
  }
  </style>