<script setup>
import Layout from '../Layout.vue';
import { Head, usePage, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

defineProps({
  businessPayments: Array
});

const isOpenPayModal = ref(false);
// Function to navigate back
function goBack() {
  window.history.back();
}

const getFullName = (first_name, middle_name, last_name) => {
  return first_name + " " + (middle_name != null ? middle_name + " " : "") + last_name;
};


const form = useForm({
  vendor: null,
  business_id: null,
  name: null,
  amount: null,
  due_date:null
});

function openPayModal(payment, business){
  isOpenPayModal.value = true;
  form.vendor = getFullName(business.first_name, business.middle_name, business.last_name)
  form.business_id = business.business_id;
  form.name = business.business_name;
  form.amount = payment.amount;
  form.due_date = payment.due_date;
}

function pay(){
  form.post(route('treasurer.pay'), 
    {
        onSuccess: (page) => {
        Swal.fire({
          toast: true,
          icon: 'success',
          position: 'top-end',
          showConfirmButton: true,
          title: page.props.flash.success,
        });
        isOpenPayModal.value = false;
      }
    });
}

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

function gotoPaymentPortal(id){
  window.location.href = route('treasurer.payment_portal', id);
}

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

          <div class="overflow-x-auto mb-4">
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
                  <th class="px-4 py-3">Business Plate</th>
                  <th class="px-4 py-3">Unpaid Payments</th>
                  <th>Status</th>
                </tr>
              </thead>

              <!-- Table Content (Business Data) -->
              <tbody>
                <tr v-for="(business, index) in filteredPayments" :key="business.business_id"
                  class="cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors" @click="gotoPaymentPortal(business.business_id)">
                  <td class="px-4 py-3">{{ index + 1 }}</td>
                  <td class="px-4 py-3">{{ getFullName(business.first_name, business.middle_name, business.last_name) }}
                  </td>
                  <td class="px-4 py-3">{{ business.business_name }}</td>
                  <td class="px-4 py-3">{{ business.plate }}</td>
                  <td class="px-4 py-3">{{ business.payments.length }}</td>
                  <td class="px-4 py-3" :class="{'text-red-700': business.business_status != 1}">{{ business.business_status != 1 ? 'Closed' : 'Active' }}</td>
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

    <input class="modal-state" id="isOpenPayModal" type="checkbox" v-model="isOpenPayModal" />
    <div class="modal">
      <label class="modal-overlay" for="isOpenPayModal"></label>
      <div class="modal-content flex flex-col gap-5">
        <label for="isOpenPayModal" class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</label>
        <h2 class="text-xl mb-2">Payment Information</h2>
        <div class="px-3">
          <div class="flex justify-between items-center gap-3 mb-2">
            <span class="font-bold">Vendor:</span>
            <span>{{ form.vendor }}</span>
          </div>

          <div class="flex justify-between items-center gap-3 mb-2">
            <span class="font-bold">Business Name:</span>
            <span>{{ form.name }}</span>
          </div>

          <div class="flex justify-between items-center gap-3 mb-2">
            <span class="font-bold">Amount:</span>
            <span>{{ '₱'+form.amount }}</span>
          </div>

          <div class="flex justify-between items-center gap-3 mb-2">
            <span class="font-bold">Due Date:</span>
            <span>{{ new Date(form.due_date).toLocaleDateString() }}</span>
          </div>
        </div>
        <div class="flex gap-3">
          <button class="btn btn-primary btn-block" @click="pay()">Pay Now</button>
          <button class="btn btn-block" @click="isOpenPayModal = false">Cancel</button>
        </div>
      </div>
    </div>
  </Layout>
</template>
