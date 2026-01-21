<template>
    <AgentLayout>
        <div class="p-6 min-h-screen bg-gradient-to-br from-gray-50 to-white dark:from-gray-900 dark:to-gray-800">
            <div class="space-y-8">

                <!-- Header -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Agent Dashboard</h1>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Overview of your current activity</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow px-5 py-3 border border-gray-200 dark:border-gray-700">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Assigned Service</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ service?.name || 'N/A' }}</p>
                    </div>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div v-for="(card, i) in overviewCards" :key="i"
                         class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm p-5 hover:shadow-md transition">
                        <div class="flex items-center gap-4">
                            <div :class="`p-3 rounded-xl bg-opacity-20 ${card.bg}`">
                                <i :class="`${card.icon} text-xl ${card.bg}`"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ card.title }}</p>
                                <p class="text-xl font-bold text-gray-900 dark:text-white">{{ card.count }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-4 border border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Appointments Status</h2>
                        <v-chart class="w-full h-80" :option="statusDistributionChart" autoresize />
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-4 border border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Daily Appointments (30 Days)</h2>
                        <v-chart class="w-full h-80" :option="dailyTrendsChart" autoresize />
                    </div>
                    <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow p-4 border border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Task Distribution</h2>
                        <v-chart class="w-full h-80" :option="taskDistributionChart" autoresize />
                    </div>
                </div>

                <!-- Appointment Table -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Appointments Queue</h2>
                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ appointments.length }} pending</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Personnel</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Task</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Requested At</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                <tr v-for="appointment in appointments" :key="appointment.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="h-10 w-10 bg-blue-100 dark:bg-blue-800 text-blue-700 dark:text-blue-300 rounded-full flex items-center justify-center font-semibold">
                                                {{ getInitials(appointment.personnel) }}
                                            </div>
                                            <div class="ml-4">
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ formatName(appointment.personnel) }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ appointment?.personnel?.position || 'N/A' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ appointment?.task?.name || 'N/A' }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-1">{{ appointment?.task?.description || 'No description' }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold" :class="statusClass(appointment.status)">
                                            {{ formatStatus(appointment.status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                        {{ formatDateTime(appointment?.requested_at) }}
                                    </td>

                                </tr>
                                <tr v-if="appointments.length === 0">
                                    <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                        <i class="fas fa-calendar-check text-2xl mb-2"></i>
                                        <p>No appointments in queue</p>
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
import { ref, computed } from "vue";
import { Inertia } from "@inertiajs/inertia";
import { usePage } from "@inertiajs/vue3";
import AgentLayout from "./Components/AgentLayout.vue";

// ECharts setup
import { use } from "echarts/core";
import { PieChart, BarChart, LineChart } from "echarts/charts";
import { TooltipComponent, LegendComponent, GridComponent } from "echarts/components";
import { CanvasRenderer } from "echarts/renderers";
import VChart from "vue-echarts";

use([PieChart, BarChart, LineChart, TooltipComponent, LegendComponent, GridComponent, CanvasRenderer]);

const { service, appointments, stats, chartData } = usePage().props;

// Overview Cards
const overviewCards = computed(() => [
    {
        title: "Total Tasks",
        count: stats.totalTasks,
        icon: "fas fa-tasks",
        bg: "text-blue-600"
    },
    {
        title: "Pending Appointments",
        count: stats.pendingAppointments,
        icon: "fas fa-clock",
        bg: "text-yellow-600"
    },
    {
        title: "In Progress",
        count: stats.inProgressAppointments,
        icon: "fas fa-spinner",
        bg: "text-indigo-600"
    },
    {
        title: "Completed",
        count: stats.completedAppointments,
        icon: "fas fa-check-circle",
        bg: "text-green-600"
    }
]);

// Status Distribution Pie Chart
const statusDistributionChart = computed(() => ({
    tooltip: {
        trigger: 'item',
        formatter: '{a} <br/>{b}: {c} ({d}%)'
    },
    legend: {
        orient: 'vertical',
        right: 10,
        top: 'center',
        textStyle: {
            color: usePage().props.darkMode ? '#E5E7EB' : '#6B7280'
        }
    },
    series: [
        {
            name: 'Appointments',
            type: 'pie',
            radius: ['50%', '70%'],
            avoidLabelOverlap: false,
            itemStyle: {
                borderRadius: 10,
                borderColor: usePage().props.darkMode ? '#1F2937' : '#FFFFFF',
                borderWidth: 2
            },
            label: {
                show: false,
                position: 'center'
            },
            emphasis: {
                label: {
                    show: true,
                    fontSize: '18',
                    fontWeight: 'bold',
                    color: usePage().props.darkMode ? '#F3F4F6' : '#111827'
                }
            },
            labelLine: {
                show: false
            },
            data: [
                { value: chartData.statusDistribution.pending, name: 'Pending', itemStyle: { color: '#F59E0B' } },
                { value: chartData.statusDistribution.in_progress, name: 'In Progress', itemStyle: { color: '#6366F1' } },
                { value: chartData.statusDistribution.completed, name: 'Completed', itemStyle: { color: '#10B981' } }
            ]
        }
    ]
}));

// Daily Trends Line Chart
const dailyTrendsChart = computed(() => ({
    tooltip: {
        trigger: 'axis',
        axisPointer: {
            type: 'shadow'
        }
    },
    legend: {
        data: ['Pending', 'In Progress', 'Completed', 'Total'],
        textStyle: {
            color: usePage().props.darkMode ? '#E5E7EB' : '#6B7280'
        }
    },
    grid: {
        left: '3%',
        right: '4%',
        bottom: '3%',
        containLabel: true
    },
    xAxis: {
        type: 'category',
        data: chartData.dailyTrends.map(item => item.date),
        axisLabel: {
            color: usePage().props.darkMode ? '#9CA3AF' : '#6B7280'
        }
    },
    yAxis: {
        type: 'value',
        axisLabel: {
            color: usePage().props.darkMode ? '#9CA3AF' : '#6B7280'
        }
    },
    series: [
        {
            name: 'Pending',
            type: 'line',
            stack: 'Total',
            data: chartData.dailyTrends.map(item => item.pending),
            itemStyle: { color: '#F59E0B' },
            smooth: true
        },
        {
            name: 'In Progress',
            type: 'line',
            stack: 'Total',
            data: chartData.dailyTrends.map(item => item.in_progress),
            itemStyle: { color: '#6366F1' },
            smooth: true
        },
        {
            name: 'Completed',
            type: 'line',
            stack: 'Total',
            data: chartData.dailyTrends.map(item => item.completed),
            itemStyle: { color: '#10B981' },
            smooth: true
        },
        {
            name: 'Total',
            type: 'line',
            data: chartData.dailyTrends.map(item => item.total),
            itemStyle: { color: '#EF4444' },
            smooth: true,
            lineStyle: {
                width: 3,
                type: 'dotted'
            }
        }
    ]
}));

// Task Distribution Bar Chart
const taskDistributionChart = computed(() => {
    const taskNames = Object.keys(chartData.taskDistribution);
    const taskValues = Object.values(chartData.taskDistribution);

    return {
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'shadow'
            }
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            containLabel: true
        },
        xAxis: {
            type: 'value',
            axisLabel: {
                color: usePage().props.darkMode ? '#9CA3AF' : '#6B7280'
            }
        },
        yAxis: {
            type: 'category',
            data: taskNames,
            axisLabel: {
                color: usePage().props.darkMode ? '#9CA3AF' : '#6B7280'
            }
        },
        series: [
            {
                name: 'Appointments',
                type: 'bar',
                data: taskValues,
                itemStyle: {
                    color: function(params) {
                        const colors = ['#3B82F6', '#10B981', '#F59E0B', '#6366F1', '#EC4899', '#8B5CF6'];
                        return colors[params.dataIndex % colors.length];
                    },
                    borderRadius: [0, 4, 4, 0]
                },
                label: {
                    show: true,
                    position: 'right',
                    color: usePage().props.darkMode ? '#F3F4F6' : '#111827'
                }
            }
        ]
    };
});

// Utility Functions
const formatDateTime = (date) => date ? new Date(date).toLocaleString() : "N/A";
const formatStatus = (status) => status ? status.replace('_', ' ') : 'N/A';
const formatName = (personnel) => {
    if (!personnel) return "N/A";
    return `${personnel?.first_name || ''} ${personnel?.last_name || ''}`.trim() || "N/A";
};
const getInitials = (personnel) => {
    if (!personnel) return '?';
    return `${personnel?.first_name?.charAt(0) || ''}${personnel?.last_name?.charAt(0) || ''}` || '?';
};

const statusClass = (status) => ({
    "bg-yellow-100/50 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-200": status === "pending",
    "bg-blue-100/50 text-blue-800 dark:bg-blue-900/20 dark:text-blue-200": status === "in_progress",
    "bg-green-100/50 text-green-800 dark:bg-green-900/20 dark:text-green-200": status === "completed",
    "bg-red-100/50 text-red-800 dark:bg-red-900/20 dark:text-red-200": status === "canceled",
});

// Update Appointment Status
const updateStatus = (appointment, status) => {
    Inertia.put(route("agent.appointments.update", appointment.id), { status }, {
        preserveScroll: true,
        onSuccess: () => {
            // Optional: Add toast notification
            console.log("Status updated successfully");
        },
    });
};
</script>

