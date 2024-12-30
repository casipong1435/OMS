<script setup>
import Layout from '../Layout.vue';
import { Head, usePage, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

defineProps({
  businessPayments: Array,
  paymentHistory: Array
});

// Function to navigate back
function goBack() {
  window.history.back();
}

const getFullName = (first_name, middle_name, last_name) => {
  return first_name + " " + (middle_name != null ? middle_name + " " : "") + last_name;
};

// Function to toggle visibility of payments for each business
const togglePayments = (businessId) => {
  // Find the business by id and toggle its payment visibility
  const business = usePage().props.businessPayments.find(b => b.business_id === businessId);
  if (business) {
    business.showPayments = !business.showPayments;
  }
};





const searchQuery = ref('');
const filteredPayments = ref([...usePage().props.businessPayments]);

// Method to filter transactions based on the search query
const searchTransactions = () => {
  filteredPayments.value = usePage().props.businessPayments.filter(payment =>
    payment.business_name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    getFullName(
      payment.first_name,
      payment.middle_name,
      payment.last_name
    )
      .toLowerCase()
      .includes(searchQuery.value.toLowerCase())
  );
};

watch(searchQuery, (value) => {
  if(value === ''){
    filteredPayments.value = usePage().props.businessPayments;
  }
});

const searchQueryLog = ref('');
const filteredTransactionsLog = ref([...usePage().props.paymentHistory]);

// Method to filter transactions based on the search query
const searchTransactionsLog = () => {
  filteredTransactionsLog.value = usePage().props.paymentHistory.filter(transaction =>
    transaction.business.name.toLowerCase().includes(searchQueryLog.value.toLowerCase()) ||
    getFullName(
      transaction.business.profile.first_name,
      transaction.business.profile.middle_name,
      transaction.business.profile.last_name
    )
      .toLowerCase()
      .includes(searchQueryLog.value.toLowerCase())
  );
};


watch(searchQueryLog, (value) => {
  if(value === ''){
    filteredTransactionsLog.value = usePage().props.paymentHistory;
  }
});

</script>

<template>

  <Head title="Vendors With Unpaid Payments" />
  <Layout>
    <div class="container px-6 mx-auto grid">
      <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
        Vendors With Unpaid Payments
      </h2>

      <!-- Breadcrumbs -->
      <div class="breadcrumbs text-sm">
        <ul>
          <li>
            <a @click="goBack()">Compliance</a>
          </li>
          <li>Payment Dues</li>
        </ul>
      </div>

      <!-- Table -->
      <div class="w-full overflow-hidden rounded-lg shadow-xs">
        <div class="flex justify-end p-4">
          <input
            type="text"
            placeholder="Search..."
            v-model="searchQuery"
            class="border rounded-md px-3 py-2 text-gray-700"
          />
          <button
            @click="searchTransactions"
            class="ml-2 px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600"
          >
            Search
          </button>
        </div>
        <div class="w-full overflow-x-auto">
          <div class="mb-2 p-2"></div>

          <div v-for="(business, index) in filteredPayments" :key="business.business_id" class="overflow-x-auto mb-4">
            <!-- Table for Each Business -->
            <table
              class="min-w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg shadow-md">

              <!-- Table Header (Appears once) -->
              <thead class="bg-gray-50 dark:bg-gray-700">
                <tr
                  class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700">
                  <th class="px-4 py-3">ID</th>
                  <th class="px-4 py-3">Owner</th>
                  <th class="px-4 py-3">Business Name</th>
                  <th class="px-4 py-3">Business Status</th>
                  <th class="px-4 py-3">Business Plate</th>
                  <th class="px-4 py-3">Unpaid Payments</th>
                </tr>
              </thead>

              <!-- Table Content (Business Data) -->
              <tbody>
                <tr @click="togglePayments(business.business_id)"
                  class="cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                  <td class="px-4 py-3">{{ index + 1 }}</td>
                  <td class="px-4 py-3">{{ getFullName(business.first_name, business.middle_name, business.last_name) }}
                  </td>
                  <td class="px-4 py-3">{{ business.business_name }}</td>
                  <td class="px-4 py-3" :class="{'text-red-700': business.status == 3}">{{ business.status == 3 ? 'CLOSED' : 'ACTIVE' }}</td>
                  <td class="px-4 py-3">{{ business.plate }}</td>
                  <td class="px-4 py-3">{{ business.payments.length }}</td>
                </tr>
              </tbody>

              <!-- Show Payments Details if business.showPayments is true -->
              <tbody v-if="business.showPayments">
                <!-- Payment Table Header -->
                <tr
                  class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase bg-gray-100 dark:bg-gray-600">
                  <th class="px-4 py-3">#</th>
                  <th class="px-4 py-3">Amount</th>
                  <th class="px-4 py-3">Due Date</th>
                  <th class="px-4 py-3" colspan="2">Status</th>
        
                </tr>

                <!-- Payment Data -->
                <tr v-for="(payment, pIndex) in business.payments" :key="pIndex"
                  class="text-left hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                  <td class="px-4 py-3">{{ pIndex + 1 }}</td>
                  <td class="px-4 py-3">{{ '₱' + payment.amount }}</td>
                  <td class="px-4 py-3">{{ new Date(payment.due_date).toLocaleDateString() }}</td>
                  <td class="px-4 py-3" colspan="2">
                    <span class="badge" :class="{ 'badge-error': payment.status !== 'Not Yet' }">{{ payment.status
                      }}</span>
                  </td>
                  
                </tr>

                <!-- If no payments available -->
                <tr v-if="business.payments.length === 0">
                  <td colspan="5" class="px-4 py-3 text-center text-gray-500">No Unpaid Payments!</td>
                </tr>
              </tbody>

            </table>
          </div>
          <div v-if="filteredPayments.length <= 0">
            <div class="text-center text-2xl">No Data Found</div>
          </div>
        </div>
      </div>
    </div>

    <div class="bg-white shadow-md rounded-md overflow-hidden">
        <!-- Table Header -->
        <div class="p-4 bg-gray-100 border-b">
          <h2 class="text-lg font-semibold text-gray-800">Transaction Logs</h2>
        </div>

        <!-- Search Section -->
        <div class="flex justify-end p-4">
          <input
            type="text"
            placeholder="Search by Business Name or Vendor's Name..."
            v-model="searchQueryLog"
            class="border rounded-md px-3 py-2 text-gray-700"
          />
          <button
            @click="searchTransactionsLog"
            class="ml-2 px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600"
          >
            Search
          </button>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
          <table class="min-w-full table-auto">
            <thead>
              <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase bg-gray-50 border-b">
                <th class="px-4 py-3">Business Name</th>
                <th class="px-4 py-3">Vendor's Name</th>
                <th class="px-4 py-3">Due Date</th>
                <th class="px-4 py-3">Amount</th>
                <th class="px-4 py-3">Paid At</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y">
              <tr v-for="transaction in filteredTransactionsLog" :key="transaction.id">
                <td class="px-4 py-3">{{ transaction.business.name }}</td>
                <td class="px-4 py-3">
                  {{
                    getFullName(
                      transaction.business.profile.first_name,
                      transaction.business.profile.middle_name,
                      transaction.business.profile.last_name
                    )
                  }}
                </td>
                <td class="px-4 py-3">{{ transaction.due_date }}</td>
                <td class="px-4 py-3 text-green-500 font-bold">{{ '₱' + Number(transaction.amount) }}</td>
                <td class="px-4 py-3">{{ transaction.paid_at }}</td>
              </tr>

              <tr v-if="filteredTransactionsLog.length === 0">
                <td colspan="5" class="px-4 py-3 text-center text-gray-500">No Transactions Found</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
  </Layout>
</template>
