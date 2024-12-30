<script setup>

import { ref, onMounted, nextTick, watch, onBeforeUnmount } from 'vue';
import { router, usePage, Head } from '@inertiajs/vue3';
import Layout from '../Layout.vue';
import Chart from 'chart.js/auto'; // Import Chart.js

// Props
defineProps({
  totalOfficials: Number,
  totalNonOfficials: Number,
  totalUsersWithBusiness: Number,
  userGrowthData: Object,  // Data for the user growth chart
});

// Reactive variables
const years = ref([]);
const months = ref([
  "January", "February", "March", "April", "May", "June", 
  "July", "August", "September", "October", "November", "December"
]);

// Generate years dynamically from 2024 up to the current year
const generateYears = () => {
  const currentYear = new Date().getFullYear();
  let availableYears = [];
  for (let year = 2024; year <= currentYear; year++) {
    availableYears.push(year);
  }
  years.value = availableYears;
};


// Chart data
const chartRef = ref(null);  // Reference to the chart element
let chartInstance = null;  // Keep track of the chart instance

const generateChart = () => {
  // Destroy the previous chart instance if it exists
  if (chartInstance) {
    chartInstance.destroy();
  }

  // Make sure chart is created only once
  nextTick(() => {
    const growthData = usePage().props.userGrowthData;

    // Prepare the labels (months)
    const labels = months.value;

    // Prepare datasets by year
    const datasets = [];
    const years = Object.keys(growthData); // Get the years from the data

    years.forEach(year => {
  const data = months.value.map((month, index) => {
    // Get the growth data for this year and month
    // Access userGrowthData using year as the key
    return growthData[year] ? growthData[year][index] : 0;
  });

      datasets.push({
        label: `Growth for ${year}`,
        data: data,  // The user count per month for this year
        borderColor: getRandomColor(), // Random color for each year
        backgroundColor: 'rgba(76, 175, 80, 0.2)',
        fill: true,
        tension: 0.4,
      });
    });

    // Create a new chart instance
    chartInstance = new Chart(chartRef.value, {
      type: 'line',
      data: {
        labels: labels,  // The months
        datasets: datasets,  // Datasets for each year
      },
      options: {
        responsive: false,  // Disable responsive resizing
        plugins: {
          title: {
            display: true,
            text: `User Growth (Monthly) by Year`,
          },
          tooltip: {
            mode: 'index',
            intersect: false,
          },
        },
        scales: {
          x: {
            beginAtZero: true,
          },
          y: {
            beginAtZero: true,
          },
        },
      },
    });
  });
};

// Function to generate a random color for each dataset
const getRandomColor = () => {
  const letters = '0123456789ABCDEF';
  let color = '#';
  for (let i = 0; i < 6; i++) {
    color += letters[Math.floor(Math.random() * 16)];
  }
  return color;
};

// Cleanup before component is unmounted
onBeforeUnmount(() => {
  if (chartInstance) {
    chartInstance.destroy();
  }
});

onMounted(() => {
  generateYears();
  generateChart();  // Generate chart on mounted
});
</script>

<template>
  <Head title="Dashboard" />
  <Layout>
    <div class="container mx-auto p-6">
      <!-- Dashboard Header -->
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-semibold text-gray-800">Dashboard</h1>
      </div>

      <!-- Dashboard Summary -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <!-- Total Officials -->
        <div class="bg-white shadow-lg rounded-lg p-6 border border-gray-100">
          <h2 class="text-xl font-semibold text-gray-800">Total Officials</h2>
          <div class="text-4xl font-bold text-blue-600">{{ totalOfficials }}</div>
        </div>

        <!-- Total Non-Officials -->
        <div class="bg-white shadow-lg rounded-lg p-6 border border-gray-100">
          <h2 class="text-xl font-semibold text-gray-800">Total Non-Officials</h2>
          <div class="text-4xl font-bold text-red-600">{{ totalNonOfficials }}</div>
        </div>

        <!-- Total Users with Businesses -->
        <div class="bg-white shadow-lg rounded-lg p-6 border border-gray-100">
          <h2 class="text-xl font-semibold text-gray-800">Total Users with Businesses</h2>
          <div class="text-4xl font-bold text-green-600">{{ totalUsersWithBusiness }}</div>
        </div>
      </div>

      <!-- Chart Section -->
      <div class="bg-white shadow-lg rounded-lg p-6 border border-gray-100">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">User Growth by Month</h2>
        <canvas ref="chartRef" class="chart-container"></canvas> <!-- Reference to the chart -->
      </div>
    </div>
  </Layout>
</template>

<style scoped>
/* Optional: Adjust the height of the chart */
.chart-container {
  width: 100%;
  height: 300px; /* Set a fixed height */
}
</style>
