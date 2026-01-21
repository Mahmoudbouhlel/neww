<template>
    <AgentLayout>
        <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 p-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Document Requests</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Manage document requests assigned to you</p>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="mb-6 p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ID</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input
                                v-model="filters.id"
                                type="text"
                                placeholder="Search by ID..."
                                class="pl-10 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm"
                            />
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Personnel</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input
                                v-model="filters.personnel"
                                type="text"
                                placeholder="Search by name..."
                                class="pl-10 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm"
                            />
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Template</label>
                        <select
                            v-model="filters.template"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm"
                        >
                            <option value="">All Templates</option>
                            <option v-for="template in uniqueTemplates" :key="template" :value="template">{{ template }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Service</label>
                        <select
                            v-model="filters.service"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm"
                        >
                            <option value="">All Services</option>
                            <option v-for="service in uniqueServices" :key="service" :value="service">{{ service }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                        <select
                            v-model="filters.status"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm"
                        >
                            <option value="all">All</option>
                            <option value="pending">Pending</option>
                            <option value="processing">In Progress</option>
                            <option value="completed">Completed</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end mt-4">
                    <button
                        @click="resetFilters"
                        class="px-4 py-2 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                    >
                        Reset Filters
                    </button>
                </div>
            </div>

            <!-- Loading -->
            <div v-if="loading" class="flex justify-center py-10">
                <div class="animate-spin rounded-full h-10 w-10 border-t-2 border-b-2 border-blue-500"></div>
            </div>

            <!-- Table -->
            <div v-else
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    ID
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    User
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Template
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Service
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Data
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Status
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Comment
                                </th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="req in filteredRequests" :key="req.id"
                                class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
                                    {{ req.id }}
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
                                    {{ req.personnel.first_name }} {{ req.personnel.last_name }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300">
                                    {{ req.template?.name || '—' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300">
                                    {{ req.service?.name || '—' }}
                                </td>
                                <td class="px-6 py-4">
                                    <pre
                                        class="text-xs text-gray-800 dark:text-white font-mono bg-gray-100 dark:bg-gray-700 p-2 rounded whitespace-pre-wrap">{{ prettyJson(req.data) }}</pre>
                                </td>
                                <td class="px-6 py-4">
                                    <span :class="statusBadgeClass(req.status)">{{ req.status }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300 truncate max-w-xs"
                                    :title="req.agent_comment">
                                    {{ req.agent_comment || '—' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex space-x-2 justify-end">
                                        <button @click="openModal(req)"
                                            :disabled="req.status === 'completed' || req.status === 'rejected'"
                                            class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
                                            </svg>
                                        </button>
                                        <button @click="viewData(req)"
                                            class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                </path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="filteredRequests.length === 0">
                                <td colspan="8" class="text-center text-sm text-gray-500 dark:text-gray-400 py-6">
                                    No matching document requests.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Status Update Modal -->
            <transition name="modal">
                <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto">
                    <div class="flex items-center justify-center min-h-screen px-4">
                        <div class="fixed inset-0 bg-black bg-opacity-50"></div>
                        <div
                            class="bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow-xl transform transition-all w-full max-w-md p-6 z-10">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Update Request</h2>
                            <div class="space-y-4">
                                <label class="block">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Status</span>
                                    <select v-model="newStatus"
                                        class="mt-1 block w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-white rounded-md shadow-sm focus:ring focus:ring-blue-500">
                                        <option value="pending">Pending</option>
                                        <option value="processing">In Progress</option>
                                        <option value="completed">Completed</option>
                                        <option value="rejected">Rejected</option>
                                    </select>
                                </label>
                                <label class="block">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Agent
                                        Comment</span>
                                    <textarea v-model="agentComment" rows="3"
                                        class="mt-1 block w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-white rounded-md shadow-sm focus:ring focus:ring-blue-500"></textarea>
                                </label>
                            </div>
                            <div class="mt-6 flex justify-end space-x-3">
                                <button @click="closeModal"
                                    class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-900 dark:text-white rounded-md hover:bg-gray-400 dark:hover:bg-gray-500">Cancel</button>
                                <button @click="submitUpdate" :disabled="updating"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                    {{ updating ? 'Updating...' : 'Update' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </transition>

            <!-- View Data Modal -->
            <transition name="modal">
                <div v-if="showDataModal" class="fixed inset-0 z-50 overflow-y-auto">
                    <div
                        class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                            <div class="absolute inset-0 bg-gray-500 dark:bg-gray-900 opacity-75"></div>
                        </div>
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen"
                            aria-hidden="true"></span>
                        <div
                            class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                            <div class="px-4 pt-5 pb-4 sm:p-6">
                                <div class="sm:flex sm:items-start">
                                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                                            Request Data</h3>
                                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                            <div v-if="selectedRequest?.data && Object.keys(selectedRequest.data).length"
                                                class="space-y-3">
                                                <div v-for="(value, key) in selectedRequest.data" :key="key"
                                                    class="grid grid-cols-3 gap-4">
                                                    <div
                                                        class="col-span-1 text-sm font-medium text-gray-700 dark:text-gray-300 capitalize">
                                                        {{ key }}</div>
                                                    <div
                                                        class="col-span-2 text-sm text-gray-900 dark:text-white break-words">
                                                        {{ value || '-' }}</div>
                                                </div>
                                            </div>
                                            <div v-else class="text-center text-gray-500 dark:text-gray-400 py-4">
                                                No data available
                                            </div>
                                        </div>
                                        <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                            <button @click="showDataModal = false" type="button"
                                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors duration-200">
                                                Close
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </transition>
        </div>
    </AgentLayout>
</template>

<script setup>
import AgentLayout from '@/Pages/Agent/Components/AgentLayout.vue';
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

const props = defineProps({ documentRequests: Array });

const filters = ref({
    id: '',
    personnel: '',
    template: '',
    service: '',
    status: 'all',
});

const loading = ref(false);
const updating = ref(false);
const showModal = ref(false);
const showDataModal = ref(false);
const selectedRequest = ref(null);
const newStatus = ref('processing');
const agentComment = ref('');

// Computed properties for unique filter options
const uniqueTemplates = computed(() => {
    const templates = new Set(props.documentRequests
        .map(req => req.template?.name)
        .filter(name => name));
    return [...templates].sort();
});

const uniqueServices = computed(() => {
    const services = new Set(props.documentRequests
        .map(req => req.service?.name)
        .filter(name => name));
    return [...services].sort();
});

// Client-side filtering
const filteredRequests = computed(() => {
    return props.documentRequests.filter(req => {
        // ID filter
        if (filters.value.id) {
            const idStr = req.id.toString().toLowerCase();
            const searchId = filters.value.id.toLowerCase();
            if (!idStr.includes(searchId)) return false;
        }

        // Personnel filter
        if (filters.value.personnel) {
            const searchTerm = filters.value.personnel.toLowerCase();
            const fullName = `${req.personnel.first_name} ${req.personnel.last_name}`.toLowerCase();
            if (!fullName.includes(searchTerm)) return false;
        }

        // Template filter
        if (filters.value.template) {
            if (req.template?.name !== filters.value.template) return false;
        }

        // Service filter
        if (filters.value.service) {
            if (req.service?.name !== filters.value.service) return false;
        }

        // Status filter
        if (filters.value.status !== 'all') {
            if (req.status !== filters.value.status) return false;
        }

        return true;
    });
});

const prettyJson = (data) => {
    try {
        return JSON.stringify(JSON.parse(data), null, 2);
    } catch {
        return data;
    }
};

const statusBadgeClass = (status) => {
    const base = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ring-1 ring-inset capitalize';
    return {
        pending: `${base} bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 ring-yellow-300`,
        processing: `${base} bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 ring-blue-300`,
        completed: `${base} bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 ring-green-300`,
        rejected: `${base} bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 ring-red-300`,
    }[status] || `${base} bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 ring-gray-300`;
};

const openModal = (req) => {
    if (req.status === 'completed' || req.status === 'rejected') return;
    selectedRequest.value = req;
    newStatus.value = req.status;
    agentComment.value = req.agent_comment || '';
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    selectedRequest.value = null;
    agentComment.value = '';
};

const submitUpdate = async () => {
    if (!selectedRequest.value) return;
    updating.value = true;

    try {
        await router.put(`/agent/document-requests/${selectedRequest.value.id}`, {
            status: newStatus.value,
            agent_comment: agentComment.value,
        }, {
            preserveScroll: true,
            onSuccess: () => {
                Swal.fire({
                    icon: 'success',
                    title: 'Updated',
                    text: 'Request updated successfully.',
                    timer: 2000,
                    showConfirmButton: false,
                });
                closeModal();
                loading.value = true;
                router.reload({
                    only: ['documentRequests'],
                    onFinish: () => loading.value = false,
                });
            },
            onError: () => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to update request.' });
            },
            onFinish: () => updating.value = false,
        });
    } catch (e) {
        updating.value = false;
        Swal.fire({ icon: 'error', title: 'Error', text: 'Unexpected error occurred.' });
    }
};

const viewData = (req) => {
    selectedRequest.value = req;
    showDataModal.value = true;
};

const resetFilters = () => {
    filters.value = {
        id: '',
        personnel: '',
        template: '',
        service: '',
        status: 'all',
    };
};
</script>