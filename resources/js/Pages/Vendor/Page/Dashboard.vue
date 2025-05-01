<script setup>
import Dashboard from "../Dashboard.vue";
import { ref, computed, onMounted } from "vue";
import { usePage, Head } from "@inertiajs/vue3";

const { nextPayments, totalPayment, totalOverdue, expiredPermits, businesses, active_businesses } = usePage().props;

const payments = ref(nextPayments);

// Computed properties
const hasPayments = computed(() => payments.value.length > 0);
</script>

<template>

  <Head title="Vendor Dashboard" />
  <Dashboard>
    <div v-if="$page.props.auth.user.status == 0" class="container mx-auto p-6">
      <!-- Dashboard Header -->
      <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Vendor Dashboard</h1>
        <p class="text-gray-600">Overview of your business status and payments</p>
      </div>

      <!-- Dashboard Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">

        <!-- Active Business -->
        <div class="bg-white shadow-md rounded-lg p-4">
          <h3 class="text-lg font-semibold text-gray-700">Active Business</h3>
          <p class="text-2xl font-bold text-blue-500">{{ active_businesses ? active_businesses : 0 }}</p>
        </div>


        <!-- Total Payments -->
        <div class="bg-white shadow-md rounded-lg p-4">
          <h3 class="text-lg font-semibold text-gray-700">Total Payments</h3>
          <p class="text-2xl font-bold text-green-500">₱{{ totalPayment.toFixed(2) }}</p>

        </div>



        <!-- Due Payments -->
        <div class="bg-white shadow-md rounded-lg p-4">
          <h3 class="text-lg font-semibold text-gray-700">Due Payments</h3>
          <p class="text-2xl font-bold text-red-500">{{ totalOverdue }}</p>
        </div>

        
      </div>

      <!-- Incoming Payments -->
      <div class="bg-white shadow-md rounded-lg p-4">
        <h3 class="text-lg font-semibold text-gray-700">Incoming Payments</h3>

        <!-- Check if there are any payments -->
        <div v-if="hasPayments">
          <div v-for="payment in payments" :key="payment.business_id" class="mb-4 border-b pb-4">
            <strong>Business Name:</strong> {{ payment.business_name }} <br />
            <strong>Total Amount:</strong> ₱{{ payment.amount }} <br />
            <strong>Due Date:</strong> {{ new Date(payment.due_date).toLocaleDateString() }} <br />
            <strong>Daily Rate:</strong> ₱{{ payment.rate }} <br />
            <strong>Penalty:</strong> ₱{{ payment.penalty }} <br />
            <strong>No. of Days:</strong> {{ payment.days }} <br />
            <strong>Status:</strong> {{ payment.status }} <br />
          </div>
        </div>

        <div v-else>
          <p>No upcoming payments.</p>
        </div>
      </div>

      <!-- Notice for Business Permit -->
      <div v-if="expiredPermits.length > 0"
        class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-lg">
        <div v-for="(expiredPermit, index) in expiredPermits" :key="index">
          <h3 class="text-lg font-semibold">Notice</h3>
          <p>The business permit of {{ expiredPermit }} has been expired. Please update it to avoid penalties.</p>
          <br />
        </div>
        <a :href="route('user.businessList')"
          class="mt-2 px-4 py-2 bg-yellow-500 text-white font-semibold rounded-lg shadow-md hover:bg-yellow-600">
          Update Permit
        </a>
      </div>

      <div v-if="businesses.length > 0" class="mt-4">
        <div v-for="(business, index) in businesses" :key="index" class="mt-4">
          <div v-if="business.status == 3" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg">
            <h3 class="text-lg font-semibold">Notice</h3>
            <p>Your business {{ business.name }} has been closed by CEEDO due to {{ business.remarks ? business.remarks : 'no reason' }}. You can contact
              CEEDO for more details.</p>
            <br />
            <a :href="route('user.businessList')"
              class="mt-2 px-4 py-2 bg-red-500 text-white font-semibold rounded-lg shadow-md hover:bg-red-600">
              Check Business
            </a>
          </div>

        </div>

      </div>
    </div>
    <div v-else class="px-5 text-center text-3xl flex justify-center items-center w-full" style="height: 300px;">
      <div>
        <strong>Notice: </strong>
        <span>Your Account was restricted by CEEDO. Contact the admin to settle!</span>
      </div>
    </div>
  </Dashboard>
</template>
