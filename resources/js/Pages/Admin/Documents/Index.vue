<template>
    <AdminLayout>
      <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-200 dark:from-gray-950 dark:to-gray-900 p-8">
        <!-- Header Section -->
        <header class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 mb-10">
          <div>
            <h1 class="text-4xl font-extrabold text-gray-900 dark:text-gray-100 tracking-tight">Document Dashboard</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2 text-lg">Create and manage document templates</p>
          </div>
          <Button
            @click="openCreateModal"
            class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-6 py-3 rounded-xl transition-all duration-200 shadow-md hover:shadow-lg"
          >
            <PlusIcon class="w-5 h-5" />
            New Document
          </Button>
        </header>

        <!-- Search and Filter Section -->
        <div class="flex flex-col md:flex-row gap-4 mb-8">
          <div class="relative flex-grow">
            <MagnifyingGlassIcon class="absolute left-4 top-3.5 h-5 w-5 text-gray-400 dark:text-gray-500" />
            <input
              v-model="searchTerm"
              type="text"
              placeholder="Search by document or service..."
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

        <!-- Document Table -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
              <thead class="bg-gray-100 dark:bg-gray-800/50">
                <tr>
                  <th scope="col" class="px-6 py-4 text-left text-sm font-semibold text-gray-600 dark:text-gray-300">Document Name</th>
                  <th scope="col" class="px-6 py-4 text-left text-sm font-semibold text-gray-600 dark:text-gray-300">Service</th>
                  <th scope="col" class="px-6 py-4 text-left text-sm font-semibold text-gray-600 dark:text-gray-300">Fields</th>
                  <th scope="col" class="px-6 py-4 text-right text-sm font-semibold text-gray-600 dark:text-gray-300">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <tr v-for="doc in filteredDocuments" :key="doc.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/20 transition-all duration-150">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ doc.name }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900 dark:text-gray-100">{{ doc.service?.name || 'N/A' }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-200">
                      {{ JSON.parse(doc.fields).length }} fields
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <div class="flex space-x-3 justify-end">
                      <Button
                        @click="openEditModal(doc)"
                        class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 transition-all duration-200"
                      >
                        <PencilSquareIcon class="w-5 h-5" />
                      </Button>
                      <Button
                        @click="deleteDocument(doc.id)"
                        class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 transition-all duration-200"
                      >
                        <TrashIcon class="w-5 h-5" />
                      </Button>
                    </div>
                  </td>
                </tr>
                <tr v-if="filteredDocuments.length === 0">
                  <td colspan="4" class="px-6 py-6 text-center text-lg text-gray-500 dark:text-gray-400">
                    No documents found. Try adjusting your search or create a new document.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Document Modal -->
        <transition name="modal">
          <div v-if="showDocumentModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" @click="closeModal"></div>
            <div class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-2xl transition-all transform overflow-hidden">
              <div class="absolute top-0 right-0 pt-4 pr-4">
                <button @click="closeModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                  <XMarkIcon class="h-6 w-6" />
                </button>
              </div>
              <div class="p-8">
                <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-3">
                  {{ form.id ? 'Edit Document' : 'Create New Document' }}
                </h3>
                <p class="text-gray-500 dark:text-gray-400 mb-6">
                  {{ form.id ? 'Update document details' : 'Add details for your new document' }}
                </p>
                <form @submit.prevent="saveDocument" class="space-y-5">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Document Name</label>
                    <input
                      v-model="form.name"
                      type="text"
                      required
                      class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all shadow-sm"
                    />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Service</label>
                    <select
                      v-model="form.service_id"
                      required
                      class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all appearance-none"
                    >
                      <option v-for="service in services" :value="service.id" :key="service.id">{{ service.name }}</option>
                    </select>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Form Fields</label>
                    <div v-for="(field, index) in form.fields" :key="index" class="flex items-center gap-3 mb-3">
                      <input
                        v-model="field.label"
                        placeholder="Field label"
                        required
                        class="flex-1 px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all shadow-sm"
                      />
                      <select
                        v-model="field.type"
                        class="w-32 px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all appearance-none"
                      >
                        <option value="text">Text</option>
                        <option value="date">Date</option>
                        <option value="number">Number</option>
                      </select>
                      <Button
                        type="button"
                        @click="removeField(index)"
                        class="text-red-600 hover:text-red-800 p-2 transition-all duration-200"
                      >
                        <XMarkIcon class="w-5 h-5" />
                      </Button>
                    </div>
                    <Button
                      type="button"
                      @click="addField"
                      class="flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-all duration-200 shadow-sm"
                    >
                      <PlusIcon class="w-4 h-4" />
                      Add Field
                    </Button>
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
                      {{ form.id ? 'Update Document' : 'Create Document' }}
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
  import { ref, computed } from 'vue';
  import { router } from '@inertiajs/vue3';
  import AdminLayout from '../Components/AdminLayout.vue';
  import Swal from 'sweetalert2';
  import {
    MagnifyingGlassIcon,
    PencilSquareIcon,
    TrashIcon,
    PlusIcon,
    XMarkIcon
  } from '@heroicons/vue/24/outline';

  const props = defineProps({
    documents: Array,
    services: Array,
  });

  const searchTerm = ref('');
  const serviceFilter = ref('');
  const showDocumentModal = ref(false);
  const form = ref({
    id: null,
    name: '',
    service_id: '',
    fields: [{ label: '', type: 'text', required: false }]
  });

  const filteredDocuments = computed(() => {
    return props.documents.filter(doc => {
      const matchesSearch = (
        doc.name.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
        (doc.service?.name || '').toLowerCase().includes(searchTerm.value.toLowerCase())
      );
      const matchesService = serviceFilter.value ? doc.service_id === serviceFilter.value : true;
      return matchesSearch && matchesService;
    });
  });

  const openCreateModal = () => {
    form.value = { id: null, name: '', service_id: '', fields: [{ label: '', type: 'text', required: false }] };
    showDocumentModal.value = true;
  };

  const openEditModal = (doc) => {
    form.value = {
      id: doc.id,
      name: doc.name,
      service_id: doc.service_id,
      fields: JSON.parse(doc.fields)
    };
    showDocumentModal.value = true;
  };

  const closeModal = () => {
    showDocumentModal.value = false;
  };

  const addField = () => {
    form.value.fields.push({ label: '', type: 'text', required: false });
  };

  const removeField = (index) => {
    form.value.fields.splice(index, 1);
  };

  const saveDocument = () => {
    const action = form.value.id ? router.put : router.post;
    const routeName = form.value.id ? 'documents.update' : 'documents.store';
    const params = form.value.id ? [routeName, form.value.id] : [routeName];

    action(route(...params), form.value, {
      onSuccess: () => {
        showDocumentModal.value = false;
        Swal.fire({
          icon: 'success',
          title: form.value.id ? 'Document Updated' : 'Document Created',
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
          title: form.value.id ? 'Failed to update document' : 'Failed to create document',
          customClass: {
            popup: 'rounded-xl bg-white dark:bg-gray-800',
            title: 'text-gray-900 dark:text-gray-100'
          }
        });
      }
    });
  };

  const deleteDocument = (id) => {
    Swal.fire({
      title: 'Delete Document?',
      text: 'This action cannot be undone.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#4F46E5',
      cancelButtonColor: '#EF4444',
      confirmButtonText: 'Delete',
      cancelButtonText: 'Cancel',
      reverseButtons: true,
      customClass: {
        popup: 'rounded-xl bg-white dark:bg-gray-800',
        title: 'text-gray-900 dark:text-gray-100',
        confirmButton: 'rounded-lg',
        cancelButton: 'rounded-lg'
      }
    }).then((result) => {
      if (result.isConfirmed) {
        router.delete(route('documents.destroy', id), {
          onSuccess: () => {
            Swal.fire({
              icon: 'success',
              title: 'Document Deleted',
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
              title: 'Failed to delete document',
              customClass: {
                popup: 'rounded-xl bg-white dark:bg-gray-800',
                title: 'text-gray-900 dark:text-gray-100'
              }
            });
          }
        });
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
