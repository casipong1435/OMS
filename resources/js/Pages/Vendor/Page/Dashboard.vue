<script setup>
import Dashboard from "../Dashboard.vue";
import { Head, usePage } from "@inertiajs/vue3";
import { ref, computed, onMounted } from "vue";

defineProps({
  isPermitNotExpired: Boolean,
  totalAmount: Number,
  totalMonths: Number,
});

const { business_status, isPermitNotExpired: permitNotExpired } = usePage().props;
const payments = ref([]);

// Initialize payments on component mount
onMounted(() => {
  payments.value = usePage().props.payments || [];
});

// Computed properties
const nextPayment = computed(() => payments.value.length ? payments.value[0] : null);
const overduePayments = computed(() => payments.value.length ? payments.value.filter(payment => payment.status !== 'Not Yet') : []);
</script>

<template>
  <Head title="Vendor Dashboard" />
  <Dashboard>
    <div class="container mx-auto p-6">
      <!-- Dashboard Header -->
      <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Vendor Dashboard</h1>
        <p class="text-gray-600">Overview of your business status and payments</p>
      </div>

      <!-- Dashboard Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- Total Payments -->
        <div class="bg-white shadow-md rounded-lg p-4">
          <h3 class="text-lg font-semibold text-gray-700">Total Payments</h3>
          <p class="text-2xl font-bold text-green-500">₱{{ totalAmount }}</p>
          <p>Total Months: {{ totalMonths }}</p>
        </div>

        <!-- Incoming Payments -->
        <div class="bg-white shadow-md rounded-lg p-4">
          <h3 class="text-lg font-semibold text-gray-700">Incoming Payments</h3>
          <strong>Total Amount:</strong> ₱{{ nextPayment?.amount || 'N/A' }} <br>
          <strong>Due Date:</strong> {{ nextPayment?.due_date ? new Date(nextPayment.due_date).toLocaleDateString() : 'N/A' }} <br>
          <span>Daily Rate:</span> ₱{{ nextPayment?.rate || 'N/A' }} <br>
          <span>No. of Days:</span> {{ nextPayment?.days || 'N/A' }}
        </div>

        <!-- Due Payments -->
        <div class="bg-white shadow-md rounded-lg p-4">
          <h3 class="text-lg font-semibold text-gray-700">Due Payments</h3>
          <p class="text-2xl font-bold text-red-500">{{ overduePayments.length }}</p>
        </div>
      </div>

      <!-- Notice for Business Permit -->
      <div v-if="business_status == 1 && !permitNotExpired" class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-lg">
        <h3 class="text-lg font-semibold">Notice</h3>
        <p>Your business permit is expired. Please update it to avoid penalties.</p>
        <br />
        <a
          :href="route('user.business')"
          class="mt-2 px-4 py-2 bg-yellow-500 text-white font-semibold rounded-lg shadow-md hover:bg-yellow-600"
        >
          Update Permit
        </a>
      </div>

      <div v-if="business_status == 3 && !permitNotExpired" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg">
        <h3 class="text-lg font-semibold">Notice</h3>
        <p>Your business has been closed by CEEDO. You can contact CEEDO for more details.</p>
        <br />
        <a
          :href="route('user.business')"
          class="mt-2 px-4 py-2 bg-red-500 text-white font-semibold rounded-lg shadow-md hover:bg-red-600"
        >
          Check Business
        </a>
      </div>
    </div>
  </Dashboard>
</template>
