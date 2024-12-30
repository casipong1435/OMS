<script setup>
import Layout from '../Layout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

defineProps({
  paymentHistory: Array,
});

const searchQuery = ref('');
const filteredTransactions = ref([...usePage().props.paymentHistory]);

// Method to filter transactions based on the search query
const searchTransactions = () => {
  filteredTransactions.value = usePage().props.paymentHistory.filter(transaction =>
    transaction.business.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    getFullName(
      transaction.business.profile.first_name,
      transaction.business.profile.middle_name,
      transaction.business.profile.last_name
    )
      .toLowerCase()
      .includes(searchQuery.value.toLowerCase())
  );
};

const getFullName = (first_name, middle_name, last_name) => {
  return first_name + ' ' + (middle_name != null ? middle_name + ' ' : '') + last_name;
};

watch(searchQuery, (value) => {
  if(value === ''){
    filteredTransactions.value = usePage().props.paymentHistory;
  }
});
</script>

<template>
  <Head title="Dashboard" />
  <Layout>
    <div class="container mx-auto p-4">
      <!-- Card Container -->
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
                <th class="px-4 py-3">Remarks</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y">
              <tr v-for="transaction in filteredTransactions" :key="transaction.id">
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
                <td class="px4 py-3" :class="{'text-red-700':remark != 'Not Yet'}">
                  {{ transaction.remark }}
                </td>
              </tr>

              <tr v-if="filteredTransactions.length === 0">
                <td colspan="5" class="px-4 py-3 text-center text-gray-500">No Transactions Found</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </Layout>
</template>
