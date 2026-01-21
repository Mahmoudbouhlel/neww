<template>
    <AgentLayout>
        <div class="p-6 min-h-screen bg-gray-50">
            <div class="">
                <!-- Header Section (unchanged) -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Appointments</h1>
                        <p class="mt-2 text-gray-600">Manage all your scheduled appointments</p>
                    </div>

                    <div class="mt-4 md:mt-0">
                        <label class="sr-only">Filter by Status</label>
                        <select
                            v-model="filterStatus"
                            class="block w-full md:w-64 rounded-md border-0 py-2 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-green-600 sm:text-sm sm:leading-6"
                        >
                            <option value="all">All Appointments</option>
                            <option value="pending">Pending</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                            <option value="canceled">Canceled</option>
                        </select>
                    </div>
                </div>

                <!-- Appointments Table -->
                <div class="bg-white shadow-sm rounded-xl overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Personnel</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requested At</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Processed At</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completed At</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="appointment in filteredAppointments" :key="appointment.id" class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                <span class="text-gray-600 font-medium">
                                                    {{ appointment?.personnel?.first_name?.charAt(0) || '?' }}{{ appointment?.personnel?.last_name?.charAt(0) || '' }}
                                                </span>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ appointment?.personnel?.first_name || "N/A" }} {{ appointment?.personnel?.last_name || "N/A" }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ appointment?.personnel?.position || "N/A" }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 font-medium">{{ appointment?.task?.name || "N/A" }}</div>
                                        <div class="text-sm text-gray-500">{{ appointment?.task?.description || "No description" }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 rounded-full text-xs font-medium" :class="statusClass(appointment?.status)">
                                            {{ appointment?.status?.replace('_', ' ') || 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ formatDateTime(appointment?.requested_at) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ formatDateTime(appointment?.processed_at) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ formatDateTime(appointment?.completed_at) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            <button
                                                v-if="appointment.status === 'pending'"
                                                @click="updateStatus(appointment, 'in_progress')"
                                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition"
                                            >
                                                Start
                                            </button>
                                            <button
                                                v-if="appointment.status === 'in_progress'"
                                                @click="updateStatus(appointment, 'completed')"
                                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition"
                                            >
                                                Complete
                                            </button>
                                            <button
                                                v-if="appointment.status !== 'canceled' && appointment.status !== 'completed'"
                                                @click="updateStatus(appointment, 'canceled')"
                                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition"
                                            >
                                                Cancel
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="filteredAppointments.length === 0">
                                    <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                        No appointments found
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AgentLayout>
</template>
<script setup>
import { ref, computed, onMounted } from "vue";
import { Inertia } from "@inertiajs/inertia";
import { usePage } from "@inertiajs/vue3";
import AgentLayout from "../Components/AgentLayout.vue";


const page = usePage();
const filterStatus = ref("all");
const appointments = ref([...page.props.appointments]);

const formatDateTime = (date) => {
    if (!date) return "N/A";
    const dateObj = new Date(date);
    return dateObj.toLocaleDateString() + " " + dateObj.toLocaleTimeString();
};

const statusClass = (status) => ({
    "bg-yellow-100 text-yellow-800": status === "pending",
    "bg-blue-100 text-blue-800": status === "in_progress",
    "bg-green-100 text-green-800": status === "completed",
    "bg-red-100 text-red-800": status === "canceled",
});

const updateStatus = (appointment, status) => {
    Inertia.put(route("Agentappointments.update", appointment.id), { status }, {
        preserveScroll: true,
        onSuccess: () => console.log("Status updated"),
    });
};

const filteredAppointments = computed(() => {
    if (filterStatus.value === "all") return appointments.value;
    return appointments.value.filter((a) => a.status === filterStatus.value);
});


// ✅ Real-time listener for new appointments
onMounted(() => {
    window.Echo.private("appointments")
        .listen(".AppointmentCreated", (e) => {
            appointments.value.unshift(e.appointment);
            console.log("📬 New appointment received!");
            // Optionally show a toast here
        });
});
</script>
