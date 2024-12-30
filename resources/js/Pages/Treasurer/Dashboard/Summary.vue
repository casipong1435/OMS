<script setup>
import { router, usePage } from '@inertiajs/vue3';
import { ref, onMounted, watch } from 'vue';
import Layout from '../Layout.vue';
import Chart from 'chart.js/auto';

// Props for total income and active businesses
defineProps({
  totalIncome: String,
  activeBusinesses: Number,
  monthlyIncome: Array // New prop for monthly income data
});

const { yearlyIncome, year_selected } = usePage().props;

// Data and methods for year selection
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
    router.get(route('treasurer.dashboard'),
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

<template>
  <Layout>
    <div class="container mx-auto p-6">
      <!-- Dashboard Header -->
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-semibold text-gray-800">Dashboard</h1>
        <div>
          <label for="year-select" class="text-lg font-medium mr-2">Select Year</label>
          <select id="year-select" v-model="selectedYear"
            class="bg-gray-200 text-gray-800 p-2 rounded-md">
            <option v-for="year in years" :key="year" :value="year">{{ year }}</option>
          </select>
        </div>
      </div>

      <!-- Summary Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
        <!-- Total Active Businesses -->
        <div class="bg-white shadow-lg rounded-lg p-6 border border-gray-100">
          <h2 class="text-xl font-semibold text-gray-800 mb-4">Total Active Businesses</h2>
          <div class="text-4xl font-bold text-green-600">{{ activeBusinesses }}</div>
        </div>

        <!-- Total Income -->
        <div class="bg-white shadow-lg rounded-lg p-6 border border-gray-100">
          <h2 class="text-xl font-semibold text-gray-800">Total Income</h2>
          <span class="text-gray-400 text-sm">As of January - December {{ year_selected }}</span>
          <div class="text-4xl font-bold text-green-600">₱ {{ totalIncome }}</div>
        </div>
      </div>

      <!-- Chart Section -->
      <div class="bg-white shadow-lg rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Monthly Income Overview</h2>
        <canvas id="monthly-income-chart" width="800" height="400"></canvas>
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
            <div
              v-for="(areaName, index) in getAreaNames()"
              :key="index"
              class="flex justify-between items-center p-4 border-b"
              :style="{
                backgroundColor: getAreaColors()[index],
                color: getAreaColors()[index] ? '#fff' : '#000'  // Ensure the text is readable based on background
              }"
            >
              <span class="text-lg text-gray-800">{{ areaName }}</span>
              <span class="text-lg font-semibold text-gray-900">₱{{ getAreaIncome()[index].toLocaleString() }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>

<style scoped>
/* Add custom styles if needed */
</style>
