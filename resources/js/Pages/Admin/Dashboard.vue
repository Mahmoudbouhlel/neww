<template>
    <AdminLayout>
      <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 p-6 md:p-10">
        <!-- **Overview Cards** - Enhanced Glassmorphism with Micro-Interactions -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
          <div
            v-for="(card, index) in overviewCards"
            :key="index"
            class="relative overflow-hidden rounded-3xl p-6 backdrop-blur-2xl transition-all duration-700 hover:scale-105 hover:shadow-2xl hover:-translate-y-1 cursor-pointer"
            :style="`background: linear-gradient(145deg, ${card.bgFrom} 0%, ${card.bgTo} 100%);`"
          >
            <!-- Animated Glow Effect -->
            <div class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent opacity-0 group-hover:opacity-50 rounded-3xl transition-opacity duration-500"></div>
            <div class="absolute inset-0 bg-white/5 animate-subtle-pulse rounded-3xl"></div>

            <div class="relative z-10 flex items-center gap-5 text-white">
              <!-- Animated Icon -->
              <div class="p-4 rounded-2xl bg-white/15 backdrop-blur-lg shadow-lg transform transition-transform group-hover:scale-110">
                <i :class="card.icon + ' text-3xl'"></i>
              </div>
              <!-- Text Content with Micro-Animation -->
              <div class="transform transition-transform group-hover:translate-x-2">
                <p class="text-sm font-medium tracking-wider uppercase opacity-85">{{ card.title }}</p>
                <p class="text-5xl font-black tracking-tight">{{ card.count }}</p>
              </div>
            </div>
          </div>
        </div>

      <!-- **Service Cards Section** - Futuristic Interactive Cards with Toggle -->
<div class="mb-16">
  <div class="flex items-center justify-between mb-10">
    <h2 class="text-5xl font-extrabold text-gray-900 dark:text-gray-100 flex items-center gap-6 animate-fade-in">
      <span class="bg-gradient-to-r from-blue-600 to-indigo-700 bg-clip-text text-transparent">Services</span>
      <span class="text-sm bg-blue-200/50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 px-5 py-2 rounded-full font-semibold shadow-md">
        {{ services.length }} services
      </span>
    </h2>
    <button
      @click="toggleServices"
      class="relative px-6 py-3 rounded-full bg-gradient-to-r from-blue-600 to-indigo-700 text-white font-semibold text-sm shadow-lg hover:shadow-[0_0_15px_rgba(59,130,246,0.5)] transition-all duration-300 hover:scale-105 flex items-center gap-2"
    >
      <i :class="showServices ? 'fas fa-chevron-up' : 'fas fa-chevron-down'" class="text-sm"></i>
      {{ showServices ? 'Hide Services' : 'Show Services' }}
      <div class="absolute inset-0 bg-white/10 rounded-full opacity-0 group-hover:opacity-30 transition-opacity duration-300 animate-ripple"></div>
    </button>
  </div>

  <transition name="slide-fade">
    <div v-show="showServices" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
      <div
        v-for="(service, index) in services"
        :key="index"
        class="group relative bg-white/70 dark:bg-gray-800/70 rounded-3xl shadow-2xl hover:shadow-[0_10px_30px_rgba(59,130,246,0.3)] transition-all duration-700 hover:-translate-y-3 hover:scale-105 border border-gray-100/50 dark:border-gray-700/50 overflow-hidden backdrop-blur-xl"
        style="transform: perspective(1000px);"
        @mousemove="handleMouseMove"
        @mouseleave="handleMouseLeave"
      >
        <!-- Animated Border with Glow -->
        <div class="absolute inset-0 border-2 border-transparent group-hover:border-blue-500/70 rounded-3xl transition-all duration-500 shadow-[0_0_20px_rgba(59,130,246,0.2)]"></div>
        <!-- Ripple Effect -->
        <div class="absolute inset-0 bg-blue-500/10 opacity-0 group-hover:opacity-30 rounded-3xl transition-opacity duration-300 animate-ripple"></div>

        <!-- Service Header -->
        <div class="bg-gradient-to-r from-blue-100/50 to-indigo-100/50 dark:from-gray-800/50 dark:to-gray-700/50 p-7 rounded-t-3xl">
          <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 flex items-center gap-4">
            <span class="w-4 h-4 rounded-full bg-blue-600 animate-pulse group-hover:scale-125 transition-transform duration-300"></span>
            {{ service.name }}
          </h3>
        </div>

        <!-- Service Stats -->
        <div class="p-7">
          <div class="space-y-6">
            <div class="flex justify-between items-center text-sm font-semibold transition-all duration-300 group-hover:translate-x-1">
              <span class="text-gray-600 dark:text-gray-400 flex items-center gap-2">
                <i class="fas fa-tasks text-blue-500 group-hover:animate-bounce"></i> Total Tasks
              </span>
              <span class="text-gray-900 dark:text-gray-100 font-extrabold">{{ service.tasks_count }}</span>
            </div>
            <div class="flex justify-between items-center text-sm font-semibold transition-all duration-300 group-hover:translate-x-1">
              <span class="text-green-600 dark:text-green-400 flex items-center gap-2">
                <i class="fas fa-check-circle text-green-500 group-hover:animate-bounce"></i> Completed
              </span>
              <span class="text-gray-900 dark:text-gray-100 font-extrabold">{{ service.tasks_completed }}</span>
            </div>
            <div class="flex justify-between items-center text-sm font-semibold transition-all duration-300 group-hover:translate-x-1">
              <span class="text-yellow-600 dark:text-yellow-400 flex items-center gap-2">
                <i class="fas fa-clock text-yellow-500 group-hover:animate-bounce"></i> Pending
              </span>
              <span class="text-gray-900 dark:text-gray-100 font-extrabold">{{ service.tasks_pending }}</span>
            </div>
          </div>

          <!-- Animated Progress Bar with Glow -->
          <div class="mt-8">
            <div class="h-3 bg-gray-200/50 dark:bg-gray-600/50 rounded-full overflow-hidden relative">
              <div
                class="h-full bg-gradient-to-r from-blue-600 to-indigo-700 rounded-full transition-all duration-1000 ease-out animate-progress-fill"
                :style="`width: ${calculateCompletionPercentage(service)}%`"
              ></div>
              <div class="absolute inset-0 bg-blue-500/20 rounded-full group-hover:shadow-[0_0_10px_rgba(59,130,246,0.5)] transition-shadow duration-300"></div>
            </div>
            <p class="text-xs text-right mt-3 text-gray-500 dark:text-gray-400 font-semibold">
              {{ calculateCompletionPercentage(service) }}% complete
            </p>
          </div>
        </div>
      </div>
    </div>
  </transition>
</div>

        <!-- **Charts Section** - Interactive and Animated Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-12">
          <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl overflow-hidden border border-gray-100 dark:border-gray-700 transform transition-all hover:scale-[1.02]">
            <div class="p-6 border-b border-gray-100 dark:border-gray-700">
              <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Task Completion</h3>
            </div>
            <div class="p-5 h-80">
              <VChart :option="taskCompletionChart" class="h-full w-full" />
            </div>
          </div>

          <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl overflow-hidden border border-gray-100 dark:border-gray-700 transform transition-all hover:scale-[1.02]">
            <div class="p-6 border-b border-gray-100 dark:border-gray-700">
              <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Appointments</h3>
            </div>
            <div class="p-5 h-80">
              <VChart :option="appointmentChart" class="h-full w-full" />
            </div>
          </div>

          <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl overflow-hidden border border-gray-100 dark:border-gray-700 transform transition-all hover:scale-[1.02]">
            <div class="p-6 border-b border-gray-100 dark:border-gray-700">
              <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Agents by Dept</h3>
            </div>
            <div class="p-5 h-80">
              <VChart :option="agentsChart" class="h-full w-full" />
            </div>
          </div>
        </div>

        <!-- **Recent Appointments Table** - Enhanced Data Table with Animations -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl overflow-hidden border border-gray-100 dark:border-gray-700 mb-12">
          <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Recent Appointments</h3>
            <!-- Animated Filter Buttons -->
            <div class="inline-flex rounded-xl bg-gray-100 dark:bg-gray-700 p-1 gap-1">
              <button
                v-for="filter in filters"
                :key="filter.value"
                @click="filterAppointments(filter.value)"
                :class="{
                  'bg-blue-600 text-white shadow-lg': activeFilter === filter.value,
                  'text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600': activeFilter !== filter.value
                }"
                class="px-5 py-2 text-sm font-semibold rounded-xl transition-all duration-300 hover:scale-105"
              >
                {{ filter.label }}
              </button>
            </div>
          </div>

          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
              <thead class="bg-gray-50 dark:bg-gray-700 sticky top-0 z-10">
                <tr>
                  <th
                    v-for="header in tableHeaders"
                    :key="header.key"
                    scope="col"
                    class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider"
                  >
                    {{ header.label }}
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                <tr
                  v-for="appointment in filteredAppointments"
                  :key="appointment.id"
                  class="group hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-300 transform hover:-translate-y-0.5"
                >
                  <td class="px-6 py-5 whitespace-nowrap">
                    <div class="flex items-center gap-4">
                      <div class="flex-shrink-0 h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center transform transition-transform group-hover:scale-110">
                        <span class="text-blue-700 dark:text-blue-300 font-bold text-lg">
                          {{ getInitials(appointment?.personnel?.first_name, appointment?.personnel?.last_name) }}
                        </span>
                      </div>
                      <div>
                        <div class="text-sm font-bold text-gray-900 dark:text-gray-100">
                          {{ appointment?.personnel?.first_name || "N/A" }} {{ appointment?.personnel?.last_name || "N/A" }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                          {{ appointment?.personnel?.department || "N/A" }}
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-5 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                    {{ appointment?.service?.name || "N/A" }}
                  </td>
                  <td class="px-6 py-5 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                    {{ appointment?.task?.name || "N/A" }}
                  </td>
                  <td class="px-6 py-5 whitespace-nowrap">
                    <span
                      :class="statusClasses(appointment?.status).base"
                      class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-bold transform transition-transform group-hover:scale-105"
                    >
                      <span :class="statusClasses(appointment?.status).dot" class="w-2.5 h-2.5 rounded-full mr-2 animate-pulse"></span>
                      {{ formatStatus(appointment?.status) || "N/A" }}
                    </span>
                  </td>
                  <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                    {{ formatDateTime(appointment?.requested_at) }}
                  </td>
                </tr>
                <tr v-if="filteredAppointments.length === 0">
                  <td
                    :colspan="tableHeaders.length"
                    class="px-6 py-5 text-center text-sm text-gray-500 dark:text-gray-400"
                  >
                    No appointments found
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- **Recent Document Requests Table** - Enhanced Data Table with Animations -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl overflow-hidden border border-gray-100 dark:border-gray-700">
          <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Recent Document Requests</h3>
            <!-- Animated Filter Buttons -->
            <div class="inline-flex rounded-xl bg-gray-100 dark:bg-gray-700 p-1 gap-1">
              <button
                v-for="filter in documentFilters"
                :key="filter.value"
                @click="filterDocumentRequests(filter.value)"
                :class="{
                  'bg-blue-600 text-white shadow-lg': activeDocumentFilter === filter.value,
                  'text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600': activeDocumentFilter !== filter.value
                }"
                class="px-5 py-2 text-sm font-semibold rounded-xl transition-all duration-300 hover:scale-105"
              >
                {{ filter.label }}
              </button>
            </div>
          </div>

          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
              <thead class="bg-gray-50 dark:bg-gray-700 sticky top-0 z-10">
                <tr>
                  <th
                    v-for="header in documentTableHeaders"
                    :key="header.key"
                    scope="col"
                    class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider"
                  >
                    {{ header.label }}
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                <tr
                  v-for="request in filteredDocumentRequests"
                  :key="request.id"
                  class="group hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-300 transform hover:-translate-y-0.5"
                >
                  <td class="px-6 py-5 whitespace-nowrap">
                    <div class="flex items-center gap-4">
                      <div class="flex-shrink-0 h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center transform transition-transform group-hover:scale-110">
                        <span class="text-blue-700 dark:text-blue-300 font-bold text-lg">
                          {{ getInitials(request?.user?.first_name, request?.user?.last_name) }}
                        </span>
                      </div>
                      <div>
                        <div class="text-sm font-bold text-gray-900 dark:text-gray-100">
                            {{ request.personnel?.first_name }} {{ request.personnel?.last_name }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                          {{ request?.user?.position_name|| "N/A" }}
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-5 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                    {{ request?.service?.name || "N/A" }}
                  </td>
                  <td class="px-6 py-5 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                    {{ request?.template?.name || "N/A" }}
                  </td>
                  <td class="px-6 py-5 whitespace-nowrap">
                    <span
                      :class="statusClasses(request?.status).base"
                      class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-bold transform transition-transform group-hover:scale-105"
                    >
                      <span :class="statusClasses(request?.status).dot" class="w-2.5 h-2.5 rounded-full mr-2 animate-pulse"></span>
                      {{ formatStatus(request?.status) || "N/A" }}
                    </span>
                  </td>
                  <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                    {{ formatDateTime(request?.created_at) }}
                  </td>
                </tr>
                <tr v-if="filteredDocumentRequests.length === 0">
                  <td
                    :colspan="documentTableHeaders.length"
                    class="px-6 py-5 text-center text-sm text-gray-500 dark:text-gray-400"
                  >
                    No document requests found
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </AdminLayout>
  </template>

  <script setup>
  import { ref, computed } from "vue";
  import { usePage } from "@inertiajs/vue3";
  import AdminLayout from "./Components/AdminLayout.vue";
  import { use } from "echarts/core";
  import { PieChart, BarChart, LineChart } from "echarts/charts";
  import { TooltipComponent, LegendComponent, GridComponent } from "echarts/components";
  import { CanvasRenderer } from "echarts/renderers";
  import VChart from "vue-echarts";

  use([PieChart, BarChart, LineChart, TooltipComponent, LegendComponent, GridComponent, CanvasRenderer]);

  // Data initialization
  const totalTasks = ref(usePage().props.totalTasks || 0);
  const totalAgents = ref(usePage().props.totalAgents || 0);
  const pendingAppointments = ref(usePage().props.pendingAppointments || 0);
  const completedAppointments = ref(usePage().props.completedAppointments || 0);
  const totalDocumentRequests = ref(usePage().props.totalDocumentRequests || 0);
  const pendingDocumentRequests = ref(usePage().props.pendingDocumentRequests || 0);
  const completedDocumentRequests = ref(usePage().props.completedDocumentRequests || 0);
  const recentAppointments = ref(usePage().props.recentAppointments || []);
  const recentDocumentRequests = ref(usePage().props.recentDocumentRequests || []);
  const services = ref(usePage().props.services || []);
  const activeFilter = ref("all");
  const activeDocumentFilter = ref("all");
  // Toggle state for services section
const showServices = ref(false);

// Toggle method
const toggleServices = () => {
  showServices.value = !showServices.value;
};



  // Overview cards with vibrant gradients
  const overviewCards = ref([
    {
      title: "Total Tasks",
      count: totalTasks,
      icon: "fas fa-tasks",
      bgFrom: "#3b82f6",
      bgTo: "#8b5cf6",
    },
    {
      title: "Total Agents",
      count: totalAgents,
      icon: "fas fa-users",
      bgFrom: "#10b981",
      bgTo: "#3b82f6",
    },
    {
      title: "Pending Appointments",
      count: pendingAppointments,
      icon: "fas fa-clock",
      bgFrom: "#f59e0b",
      bgTo: "#ef4444",
    },
    {
      title: "Completed Appointments",
      count: completedAppointments,
      icon: "fas fa-check-circle",
      bgFrom: "#8b5cf6",
      bgTo: "#ec4899",
    },
    {
      title: "Total Doc Requests",
      count: totalDocumentRequests,
      icon: "fas fa-file-alt",
      bgFrom: "#6b7280",
      bgTo: "#3b82f6",
    },
    {
      title: "Pending Doc Requests",
      count: pendingDocumentRequests,
      icon: "fas fa-hourglass-half",
      bgFrom: "#f59e0b",
      bgTo: "#d97706",
    },
    {
      title: "Completed Doc Requests",
      count: completedDocumentRequests,
      icon: "fas fa-file-check",
      bgFrom: "#10b981",
      bgTo: "#059669",
    },
  ]);

  // Table configuration for appointments
  const tableHeaders = ref([
    { key: "personnel", label: "Personnel" },
    { key: "service", label: "Service" },
    { key: "task", label: "Task" },
    { key: "status", label: "Status" },
    { key: "requested_at", label: "Requested At" },
  ]);

  // Table configuration for document requests
  const documentTableHeaders = ref([
    { key: "user", label: "User" },
    { key: "service", label: "Service" },
    { key: "template", label: "Document Template" },
    { key: "status", label: "Status" },
    { key: "created_at", label: "Requested At" },
  ]);

  // Filter configuration for appointments
  const filters = ref([
    { value: "all", label: "All" },
    { value: "pending", label: "Pending" },
    { value: "in_progress", label: "In Progress" },
    { value: "completed", label: "Completed" },
  ]);

  // Filter configuration for document requests
  const documentFilters = ref([
    { value: "all", label: "All" },
    { value: "pending", label: "Pending" },
    { value: "completed", label: "Completed" },
  ]);

  // Charts configuration with enhanced animations
  const taskCompletionChart = computed(() => ({
    tooltip: { trigger: "item" },
    legend: {
      orient: "vertical",
      right: 10,
      top: "center",
      textStyle: { color: "#9ca3af", fontSize: 12 },
    },
    series: [
      {
        name: "Task Status",
        type: "pie",
        radius: ["50%", "70%"],
        avoidLabelOverlap: false,
        itemStyle: {
          borderRadius: 10,
          borderColor: "#fff",
          borderWidth: 3,
        },
        label: { show: false },
        emphasis: {
          scale: true,
          scaleSize: 15,
          label: { show: true, fontSize: 16, fontWeight: "bold" },
        },
        data: [
          { value: usePage().props.completedAppointments, name: "Completed", itemStyle: { color: "#10b981" } },
          { value: usePage().props.pendingAppointments, name: "Pending", itemStyle: { color: "#f59e0b" } },
        ],
      },
    ],
  }));

  const appointmentChart = computed(() => ({
    tooltip: { trigger: "axis" },
    grid: { left: "3%", right: "3%", bottom: "3%", containLabel: true },
    xAxis: {
      type: "category",
      data: ["Total", "Pending", "Completed"],
      axisLine: { lineStyle: { color: "#d1d5db" } },
      axisLabel: { color: "#6b7280", fontSize: 12 },
    },
    yAxis: {
      type: "value",
      axisLine: { lineStyle: { color: "#d1d5db" } },
      axisLabel: { color: "#6b7280", fontSize: 12 },
      splitLine: { lineStyle: { color: "#e5e7eb", opacity: 0.2 } },
    },
    series: [
      {
        data: [
          { value: usePage().props.totalAppointments, itemStyle: { color: "#3b82f6" } },
          { value: usePage().props.pendingAppointments, itemStyle: { color: "#f59e0b" } },
          { value: usePage().props.completedAppointments, itemStyle: { color: "#10b981" } },
        ],
        type: "bar",
        barWidth: "30%",
        itemStyle: {
          borderRadius: [8, 8, 0, 0],
          shadowColor: "rgba(0, 0, 0, 0.2)",
          shadowBlur: 8,
        },
        animationEasing: "elasticOut",
        animationDuration: 1200,
      },
    ],
  }));

  const agentsChart = computed(() => ({
    tooltip: { trigger: "item" },
    series: [
      {
        name: "Agents",
        type: "pie",
        radius: ["40%", "65%"],
        data: usePage().props.departments?.map((dept) => ({
          value: dept.agent_count,
          name: dept.name,
          itemStyle: { color: dept.color || "#3b82f6" },
        })) || [],
        itemStyle: {
          borderRadius: 8,
          borderColor: "#fff",
          borderWidth: 3,
        },
        emphasis: {
          scale: true,
          scaleSize: 12,
          label: { show: true, fontSize: 16, fontWeight: "bold" },
        },
        animationType: "expansion",
        animationEasing: "cubicOut",
      },
    ],
  }));

  // Computed properties
  const filteredAppointments = computed(() => {
    if (activeFilter.value === "all") return recentAppointments.value;
    return recentAppointments.value.filter((app) => app.status === activeFilter.value);
  });

  const filteredDocumentRequests = computed(() => {
    if (activeDocumentFilter.value === "all") return recentDocumentRequests.value;
    return recentDocumentRequests.value.filter((req) => req.status === activeDocumentFilter.value);
  });

  // Methods
  const filterAppointments = (status) => {
    activeFilter.value = status;
  };

  const filterDocumentRequests = (status) => {
    activeDocumentFilter.value = status;
  };

  const formatDateTime = (date) => {
    if (!date) return "N/A";
    return new Date(date).toLocaleString("en-US", {
      month: "short",
      day: "numeric",
      hour: "2-digit",
      minute: "2-digit",
    });
  };

  const formatStatus = (status) => {
    const statusMap = {
      pending: "Pending",
      in_progress: "In Progress",
      completed: "Completed",
      canceled: "Canceled",
    };
    return statusMap[status] || status;
  };

  const calculateCompletionPercentage = (service) => {
    if (!service.tasks_count || isNaN(service.tasks_count) || isNaN(service.tasks_completed)) {
      return 0;
    }
    return Math.round((service.tasks_completed / service.tasks_count) * 100);
  };
  const handleMouseMove = (e) => {
  const card = e.currentTarget;
  const rect = card.getBoundingClientRect();
  const x = e.clientX - rect.left;
  const y = e.clientY - rect.top;
  const centerX = rect.width / 2;
  const centerY = rect.height / 2;
  const rotateX = (y - centerY) / 50;
  const rotateY = (centerX - x) / 50;
  card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
};

const handleMouseLeave = (e) => {
  const card = e.currentTarget;
  card.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg)';
};
  const statusClasses = (status) => {
    const classes = {
      pending: {
        base: "bg-yellow-100 dark:bg-yellow-900/50 text-yellow-700 dark:text-yellow-300",
        dot: "bg-yellow-500",
      },
      in_progress: {
        base: "bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300",
        dot: "bg-blue-500",
      },
      completed: {
        base: "bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-300",
        dot: "bg-green-500",
      },
      canceled: {
        base: "bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-300",
        dot: "bg-red-500",
      },
    };
    return (
      classes[status] || {
        base: "bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300",
        dot: "bg-gray-500",
      }
    );
  };

  const getInitials = (firstName, lastName) => {
    if (!firstName && !lastName) return "NA";
    return `${firstName?.charAt(0) || ""}${lastName?.charAt(0) || ""}`.toUpperCase();
  };
  </script>

  <style>
  /* Custom Scrollbar */
  .dark ::-webkit-scrollbar {
    width: 8px;
    height: 8px;
  }

  .dark ::-webkit-scrollbar-track {
    background: #1f2937;
    border-radius: 4px;
  }

  .dark ::-webkit-scrollbar-thumb {
    background: #6b7280;
    border-radius: 4px;
  }

  .dark ::-webkit-scrollbar-thumb:hover {
    background: #9ca3af;
  }

  /* Global Typography */
  body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
  }

  /* Subtle Pulse Animation */
  @keyframes subtle-pulse {
    0% { opacity: 0.2; }
    50% { opacity: 0.4; }
    100% { opacity: 0.2; }
  }

  .animate-subtle-pulse {
    animation: subtle-pulse 3s infinite ease-in-out;
  }

  /* Hover Glow Effect */
  .group:hover .group-hover\:opacity-50 {
    opacity: 0.5;
  }
  </style>