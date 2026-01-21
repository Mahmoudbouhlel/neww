<template>
    <AdminLayout>
      <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-200 dark:from-gray-950 dark:to-gray-900 p-8">
        <!-- Header Section -->
        <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 mb-8">
          <div>
            <h1 class="text-4xl font-extrabold text-gray-900 dark:text-gray-100 tracking-tight">Appointment Dashboard</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2 text-lg">Effortlessly manage and track appointments</p>
          </div>
          <div class="flex gap-4">
            <button
              @click="openCreateModal"
              class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-medium transition-all duration-200 shadow-md hover:shadow-lg"
            >
              <PlusIcon class="w-5 h-5" />
              New Appointment
            </button>
          </div>
        </header>

        <!-- Filters Section -->
        <div class="mb-6 flex flex-col sm:flex-row gap-4">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search by personnel, service, or task..."
            class="w-full sm:w-80 px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 outline-none transition-all"
          />
          <select
            v-model="statusFilter"
            class="px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 outline-none"
          >
            <option value="">All Statuses</option>
            <option value="pending">Pending</option>
            <option value="in_progress">In Progress</option>
            <option value="completed">Completed</option>
            <option value="canceled">Canceled</option>
          </select>
        </div>

        <!-- Appointment Table -->
        <div class="overflow-hidden rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
          <div class="overflow-x-auto">
            <table class="w-full min-w-max">
              <thead class="bg-gray-100 dark:bg-gray-800/50">
                <tr class="text-left text-gray-600 dark:text-gray-300 text-sm font-semibold">
                  <th class="p-5">Personnel</th>
                  <th class="p-5">Service</th>
                  <th class="p-5">Task</th>
                  <th class="p-5">Status</th>
                  <th class="p-5">Requested</th>
                  <th class="p-5">Processed</th>
                  <th class="p-5">Completed</th>
                  <th class="p-5">Duration</th>
                  <th class="p-5 text-right">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <tr
                  v-for="appointment in filteredAppointments"
                  :key="appointment.id"
                  class="hover:bg-gray-50 dark:hover:bg-gray-700/20 transition-colors duration-150"
                >
                  <td class="p-5 font-medium text-gray-900 dark:text-gray-100">
                    {{ appointment?.personnel?.first_name || "N/A" }} {{ appointment?.personnel?.last_name || "N/A" }}
                  </td>
                  <td class="p-5 text-gray-600 dark:text-gray-300">
                    {{ appointment?.service?.name || "N/A" }}
                  </td>
                  <td class="p-5 text-gray-600 dark:text-gray-300">
                    {{ appointment?.task?.name || "N/A" }}
                  </td>
                  <td class="p-5">
                    <span
                      :class="statusBadgeClass(appointment?.status)"
                      class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium capitalize"
                    >
                      {{ appointment?.status || "N/A" }}
                    </span>
                  </td>
                  <td class="p-5 text-gray-500 dark:text-gray-400 whitespace-nowrap">
                    {{ formatDate(appointment?.requested_at) }}
                  </td>
                  <td class="p-5 text-gray-500 dark:text-gray-400 whitespace-nowrap">
                    {{ formatDate(appointment?.processed_at) }}
                  </td>
                  <td class="p-5 text-gray-500 dark:text-gray-400 whitespace-nowrap">
                    {{ formatDate(appointment?.completed_at) }}
                  </td>
                  <td class="p-5 text-gray-500 dark:text-gray-400">
                    {{ calculateDuration(appointment?.processed_at, appointment?.completed_at) }}
                  </td>
                  <td class="p-5 flex justify-end gap-3">
                    <!-- Start Button -->
                    <button
                      v-if="appointment?.status === 'pending'"
                      @click="confirmStatusUpdate(appointment, 'in_progress')"
                      class="flex items-center gap-2 text-sm bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition-all duration-200 shadow-sm hover:shadow"
                    >
                      <PlayIcon class="w-4 h-4" />
                      Start
                    </button>

                    <!-- Complete Button -->
                    <button
                      v-if="appointment?.status === 'in_progress'"
                      @click="confirmStatusUpdate(appointment, 'completed')"
                      class="flex items-center gap-2 text-sm bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-all duration-200 shadow-sm hover:shadow"
                    >
                      <CheckIcon class="w-4 h-4" />
                      Complete
                    </button>

                    <!-- Cancel Button -->
                    <button
                      v-if="appointment?.status === 'pending' || appointment?.status === 'in_progress'"
                      @click="confirmStatusUpdate(appointment, 'canceled')"
                      class="flex items-center gap-2 text-sm bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-all duration-200 shadow-sm hover:shadow"
                    >
                      <XMarkIcon class="w-4 h-4" />
                      Cancel
                    </button>

                    <!-- Delete Button -->
                    <button
                      v-if="appointment?.status === 'pending' || appointment?.status === 'canceled'"
                      @click="confirmDelete(appointment.id)"
                      class="flex items-center gap-2 text-sm bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-all duration-200 shadow-sm hover:shadow"
                    >
                      <TrashIcon class="w-4 h-4" />
                      Delete
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
            <div v-if="filteredAppointments.length === 0" class="p-10 text-center text-gray-500 dark:text-gray-400 text-lg">
              No appointments match your criteria.
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div class="mt-8 flex flex-col sm:flex-row items-center justify-between gap-6">
          <div class="text-sm text-gray-600 dark:text-gray-400">
            Showing {{ filteredAppointments.length }} of {{ appointments?.total || 0 }} appointments
          </div>
          <div class="flex gap-3">
            <button
              v-if="appointments?.prev_page_url"
              @click="changePage(appointments.current_page - 1)"
              class="flex items-center gap-2 px-5 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-200"
            >
              <ChevronLeftIcon class="w-5 h-5" />
              Previous
            </button>
            <button
              v-if="appointments?.next_page_url"
              @click="changePage(appointments.current_page + 1)"
              class="flex items-center gap-2 px-5 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-200"
            >
              Next
              <ChevronRightIcon class="w-5 h-5" />
            </button>
          </div>
        </div>
      </div>
    </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { usePage } from "@inertiajs/vue3";
import { Inertia } from "@inertiajs/inertia";
import AdminLayout from "../Components/AdminLayout.vue";
import {
  PlayIcon,
  CheckIcon,
  XMarkIcon,
  TrashIcon,
  ChevronLeftIcon,
  ChevronRightIcon,
  PlusIcon
} from "@heroicons/vue/24/outline";
import Swal from 'sweetalert2';

const appointments = usePage().props?.appointments || { data: [] };
const searchQuery = ref('');
const statusFilter = ref('');

const filteredAppointments = computed(() => {
  let result = appointments.data;

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    result = result.filter(appointment =>
      `${appointment?.personnel?.first_name || ''} ${appointment?.personnel?.last_name || ''}`.toLowerCase().includes(query) ||
      appointment?.service?.name?.toLowerCase().includes(query) ||
      appointment?.task?.name?.toLowerCase().includes(query)
    );
  }

  if (statusFilter.value) {
    result = result.filter(appointment => appointment?.status === statusFilter.value);
  }

  return result;
});

const formatDate = (date) => {
  if (!date) return "N/A";
  return new Date(date).toLocaleString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

const calculateDuration = (start, end) => {
  if (!start || !end) return "N/A";
  const startDate = new Date(start);
  const endDate = new Date(end);
  const diffMs = endDate - startDate;
  const hours = Math.floor(diffMs / 3600000);
  const minutes = Math.floor((diffMs % 3600000) / 60000);
  return hours > 0 ? `${hours}h ${minutes}m` : `${minutes}m`;
};

const statusBadgeClass = (status) => {
  const base = "inline-flex items-center px-3 py-1 rounded-full text-sm font-medium capitalize";
  switch (status) {
    case "pending":
      return `${base} bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300`;
    case "in_progress":
      return `${base} bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-300`;
    case "completed":
      return `${base} bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300`;
    case "canceled":
      return `${base} bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300`;
    default:
      return `${base} bg-gray-100 text-gray-800 dark:bg-gray-900/40 dark:text-gray-300`;
  }
};

const confirmStatusUpdate = (appointment, status) => {
  const actionMap = {
    'in_progress': 'start',
    'completed': 'complete',
    'canceled': 'cancel'
  };

  const action = actionMap[status] || 'update';
  const actionText = action.charAt(0).toUpperCase() + action.slice(1);

  Swal.fire({
    title: `Confirm ${actionText}`,
    text: `Are you sure you want to ${action} this appointment?`,
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#4F46E5',
    cancelButtonColor: '#EF4444',
    confirmButtonText: `Yes, ${action} it!`,
    cancelButtonText: 'Cancel',
    reverseButtons: true,
    customClass: {
      popup: 'rounded-2xl',
      confirmButton: 'rounded-lg',
      cancelButton: 'rounded-lg'
    }
  }).then((result) => {
    if (result.isConfirmed) {
      updateStatus(appointment, status);
    }
  });
};

const updateStatus = (appointment, status) => {
  Inertia.put(route("appointments.update", appointment.id), { status }, {
    onSuccess: () => {
      Swal.fire({
        title: 'Success!',
        text: `Appointment has been ${status === 'in_progress' ? 'started' : status === 'completed' ? 'completed' : 'canceled'}.`,
        icon: 'success',
        timer: 2000,
        showConfirmButton: false,
        customClass: {
          popup: 'rounded-2xl'
        }
      });
    },
    onError: () => {
      Swal.fire({
        title: 'Error!',
        text: 'Failed to update the appointment.',
        icon: 'error',
        customClass: {
          popup: 'rounded-2xl'
        }
      });
    }
  });
};

const confirmDelete = (id) => {
  Swal.fire({
    title: 'Delete Appointment?',
    text: "This action cannot be undone!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#4F46E5',
    cancelButtonColor: '#EF4444',
    confirmButtonText: 'Yes, delete it!',
    cancelButtonText: 'Cancel',
    reverseButtons: true,
    customClass: {
      popup: 'rounded-2xl',
      confirmButton: 'rounded-lg',
      cancelButton: 'rounded-lg'
    }
  }).then((result) => {
    if (result.isConfirmed) {
      deleteAppointment(id);
    }
  });
};

const deleteAppointment = (id) => {
  Inertia.delete(route("appointments.destroy", id), {
    onSuccess: () => {
      Swal.fire({
        title: 'Deleted!',
        text: 'The appointment has been removed.',
        icon: 'success',
        timer: 2000,
        showConfirmButton: false,
        customClass: {
          popup: 'rounded-2xl'
        }
      });
    },
    onError: () => {
      Swal.fire({
        title: 'Error!',
        text: 'Failed to delete the appointment.',
        icon: 'error',
        customClass: {
          popup: 'rounded-2xl'
        }
      });
    }
  });
};

const changePage = (page) => {
  Inertia.get(route("appointments.index", { page, search: searchQuery.value, status: statusFilter.value }));
};

const openCreateModal = () => {
  // Placeholder for modal logic
  Swal.fire({
    title: 'Create New Appointment',
    text: 'This feature is coming soon!',
    icon: 'info',
    customClass: {
      popup: 'rounded-2xl'
    }
  });
};
</script>

<style scoped>
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