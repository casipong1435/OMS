<script setup>
import { ref, onMounted, watch } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import Layout from '../Layout.vue';
import Chart from 'chart.js/auto';

// Props from Laravel backend
const {
  totalBusinessOperations,
  totalVendorApplications,
  totalAreas,
  totalEstablishments,
  totalStallsAvailable,
  totalStallsOccupied,
  totalIncome,
  monthlyIncome,
  year_selected,
  yearlyIncome
} = usePage().props;

const years = ref([]);
const selectedYear = ref(year_selected);

const generateYears = () => {
  const currentYear = new Date().getFullYear();
  const availableYears = [];
  for (let year = 2024; year <= currentYear; year++) {
    availableYears.push(year);
  }
  years.value = availableYears;
};

// Chart instance
let chartInstance = null;

// Watcher for year selection
watch(selectedYear, (value) => {
  if (value) {
    router.get(route('mayor.dashboard'),
      { year_selected: value },
      {
        preserveState: false,
        replace: true,
      });
  }
});

// Chart rendering logic
const renderChart = (labels, data) => {
  const ctx = document.getElementById('monthly-income-chart').getContext('2d');

  if (chartInstance) {
    chartInstance.destroy();
  }

  chartInstance = new Chart(ctx, {
    type: 'line',
    data: {
      labels, // Month labels
      datasets: [
        {
          label: `Monthly Income (${selectedYear.value})`,
          data,
          borderColor: '#4CAF50',
          backgroundColor: 'rgba(76, 175, 80, 0.2)',
          tension: 0.3,
          fill: true,
          pointBackgroundColor: '#4CAF50',
          pointBorderColor: '#388E3C',
        },
      ],
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: 'top',
        },
      },
      scales: {
        x: {
          title: {
            display: true,
            text: 'Months',
          },
        },
        y: {
          title: {
            display: true,
            text: 'Income (₱)',
          },
          beginAtZero: true,
        },
      },
    },
  });
};

let chartLineInstance = null;
let chartPieInstance = null;

function getAreaNames() {
  return Object.values(yearlyIncome).map((area) => area.name);
}

function getAreaIncome() {
  return Object.values(yearlyIncome).map((area) => area.total_payments);
}

function getAreaColors() {
  const colors = ['#4CAF50', '#FF9800', '#2196F3', '#FF5722', '#9C27B0'];
  return colors.slice(0, getAreaNames().length); // Ensure color array is equal to area count
}

// Line Chart Rendering Logic
const renderLineChart = () => {
  const ctx = document.getElementById('yearly-income-chart').getContext('2d');

  if (chartLineInstance) {
    chartInstance.destroy();
  }

  chartLineInstance = new Chart(ctx, {
    type: 'line',
    data: {
      labels: getAreaNames(),
      datasets: [
        {
          label: `Area Income (${selectedYear.value})`,
          data: getAreaIncome(),
          borderColor: '#4CAF50',
          backgroundColor: 'rgba(76, 175, 80, 0.2)',
          tension: 0.3,
          fill: true,
          pointBackgroundColor: '#4CAF50',
          pointBorderColor: '#388E3C',
        },
      ],
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: 'top',
        },
      },
      scales: {
        x: {
          title: {
            display: true,
            text: 'Areas',
          },
        },
        y: {
          title: {
            display: true,
            text: 'Income (₱)',
          },
          beginAtZero: true,
        },
      },
    },
  });
};

// Pie Chart Rendering Logic
const renderPieChart = () => {
  const ctx = document.getElementById('income-pie-chart').getContext('2d');

  if (chartPieInstance) {
    chartPieInstance.destroy();
  }

  chartPieInstance = new Chart(ctx, {
    type: 'pie',
    data: {
      labels: getAreaNames(),
      datasets: [
        {
          label: `Income Distribution (${selectedYear.value})`,
          data: getAreaIncome(),
          backgroundColor: getAreaColors(),
        },
      ],
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: 'top',
        },
      },
    },
  });
};

// Load chart on mount and update on prop change
onMounted(() => {
  generateYears();
  renderChart(
    ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
    usePage().props.monthlyIncome
  );
  renderLineChart();
  renderPieChart();
});

watch(() => usePage().props.monthlyIncome, (newIncome) => {
  renderChart(
    ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
    newIncome
  );
});
</script>

<style scoped>
/* Optional: Adjust the height of the cards if needed */
.card {
  height: 200px;
}
</style>
<template>

  <Head title="Dashboard" />
  <Layout>
    <div class="container mx-auto p-6 space-y-8">
      <!-- Dashboard Header -->
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-semibold text-gray-800">Dashboard</h1>
        <div class="flex items-center space-x-4">
          <label for="year-select" class="text-lg font-medium text-gray-700">Select Year</label>
          <select id="year-select" v-model="selectedYear"
            class="bg-gray-100 text-gray-800 p-3 rounded-md shadow-md focus:outline-none">
            <option v-for="year in years" :key="year" :value="year">{{ year }}</option>
          </select>
        </div>
      </div>

      <!-- Dashboard Summary Card -->
      <div class="bg-white shadow-xl rounded-lg p-6 border border-gray-200">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Dashboard Summary</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <div class="bg-white p-4 border border-gray-200 rounded-lg flex items-center">
            <div class="w-12 h-12 bg-blue-600 text-white flex justify-center items-center rounded-full mr-4">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round"
                  d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0M12 12.75h.008v.008H12v-.008Z" />
              </svg>

            </div>
            <div>
              <h3 class="text-lg font-semibold text-gray-800">Total Business Operations</h3>
              <p class="text-2xl font-bold text-blue-600">{{ totalBusinessOperations }}</p>
            </div>
          </div>

          <div class="bg-white p-4 border border-gray-200 rounded-lg flex items-center">
            <div class="w-12 h-12 bg-green-600 text-white flex justify-center items-center rounded-full mr-4">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round"
                  d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
              </svg>

            </div>
            <div>
              <h3 class="text-lg font-semibold text-gray-800">Total Income</h3>
              <p class="text-2xl font-bold text-green-600">₱ {{ totalIncome }}</p>
              <span class="text-sm text-gray-500">As of January - December {{ selectedYear }}</span>
            </div>
          </div>

          <div class="bg-white p-4 border border-gray-200 rounded-lg flex items-center">
            <div class="w-12 h-12 bg-red-600 text-white flex justify-center items-center rounded-full mr-4">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round"
                  d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
              </svg>

            </div>
            <div>
              <h3 class="text-lg font-semibold text-gray-800">Total Vendor Applications</h3>
              <p class="text-2xl font-bold text-red-600">{{ totalVendorApplications }}</p>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
          <div class="bg-white p-4 border border-gray-200 rounded-lg flex items-center">
            <div class="w-12 h-12 bg-yellow-600 text-white flex justify-center items-center rounded-full mr-4">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                <path stroke-linecap="round" stroke-linejoin="round"
                  d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
              </svg>

            </div>
            <div>
              <h3 class="text-lg font-semibold text-gray-800">Total Areas Inside</h3>
              <p class="text-2xl font-bold text-yellow-600">{{ totalAreas }}</p>
            </div>
          </div>

          <div class="bg-white p-4 border border-gray-200 rounded-lg flex items-center">
            <div class="w-12 h-12 bg-green-600 text-white flex justify-center items-center rounded-full mr-4">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round"
                  d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
              </svg>
            </div>
            <div>
              <h3 class="text-lg font-semibold text-gray-800">Total Stalls Available</h3>
              <p class="text-2xl font-bold text-green-600">{{ totalStallsAvailable }}</p>
            </div>
          </div>

          <div class="bg-white p-4 border border-gray-200 rounded-lg flex items-center">
            <div class="w-12 h-12 bg-red-600 text-white flex justify-center items-center rounded-full mr-4">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round"
                  d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
              </svg>
            </div>
            <div>
              <h3 class="text-lg font-semibold text-gray-800">Total Stalls Occupied</h3>
              <p class="text-2xl font-bold text-red-600">{{ totalStallsOccupied }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Chart Section -->
      <div class="bg-white shadow-xl rounded-lg p-6 border border-gray-200 mt-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Monthly Income Chart</h2>
        <canvas id="monthly-income-chart"></canvas>
      </div>

      <div class="bg-white shadow-lg rounded-lg p-8">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Yearly Income Overview</h2>

        <!-- Charts Section (Side by side on large screens) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
          <!-- Line Chart -->
          <div class="w-full">
            <canvas id="yearly-income-chart" height="300"></canvas>
          </div>

          <!-- Pie Chart -->
          <div class="w-full">
            <canvas id="income-pie-chart" height="300"></canvas>
          </div>
        </div>

        <!-- Consolidated Area Income Card -->
        <div class="mt-8 p-6 bg-white rounded-lg shadow-lg">
          <div class="flex justify-between items-center gap-2">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Income per Area</h3>
            <span>Year: <b>{{ year_selected }}</b></span>
          </div>
          <div class="space-y-4">
            <div v-for="(areaName, index) in getAreaNames()" :key="index"
              class="flex justify-between items-center p-4 border-b" :style="{
                backgroundColor: getAreaColors()[index],
                color: getAreaColors()[index] ? '#fff' : '#000'  // Ensure the text is readable based on background
              }">
              <span class="text-lg text-gray-800">{{ areaName }}</span>
              <span class="text-lg font-semibold text-gray-900">₱{{ getAreaIncome()[index].toLocaleString() }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>
