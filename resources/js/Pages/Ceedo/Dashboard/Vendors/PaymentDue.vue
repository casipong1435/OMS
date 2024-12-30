<script setup>
import Layout from '../../Layout.vue';
import { Head, usePage, router } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

const { businessPayments } = usePage().props;

// Function to navigate back
function goBack() {
  window.history.back();
}

const getFullName = (first_name, middle_name, last_name) => {
    return first_name + " " + (middle_name != null ? middle_name + " " : "") + last_name;
};

function goToRoute(id){
  window.location.href = route('ceedo.vendorProfile', id);
}

onMounted(()=>{

  timers.value = usePage().props.businessPayments.map((business) => ({
    ...business,
    remainingTime: 0, // Initial time in seconds
    timerActive: false, // Timer status
  }));
});

const timers = ref([]);

// Start the timer for the selected row
function startTimer(index) {
  const row = timers.value[index];
  if (row.timerActive) return;

  row.remainingTime = 5 * 60; // 5 minutes in seconds
  row.timerActive = true;

  const timer = setInterval(() => {
    if (row.remainingTime > 0) {
      row.remainingTime--;
    } else {
      clearInterval(timer);
      row.timerActive = false;
      alert(`Timer for ${row.business_name} has ended!`);
    }
  }, 1000); // Update every second
}

// Format time in MM:SS
function formatTime(seconds) {
  const minutes = Math.floor(seconds / 60);
  const secs = seconds % 60;
  return `${minutes.toString().padStart(2, "0")}:${secs.toString().padStart(2, "0")}`;
}

const SendPaymentReminder = (index, business) => {

  router.post(route('ceedo.SendPaymentReminder'), 
  {
    profile_id: business.profile_id,
    payment: business.payments[0]
  },
  {
    onSuccess: (page) => {
      startTimer(index);
      Swal.fire({
        toast: true,
        icon: 'success',
        position: 'top-end',
        showConfirmButton: true,
        title: page.props.flash.success,
      });
    }
  });
};

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
        <div class="w-full overflow-x-auto">
          <div class="mb-2 p-2">
          </div>

          <table class="w-full whitespace-no-wrap">
            <thead>
              <tr
                class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800">
                <th class="px-4 py-3">ID</th>
                <th class="px-4 py-3">Owner</th>
                <th class="px-4 py-3">Business Name</th>
                <th class="px-4 py-3">Business Status</th>
                <th class="px-4 py-3">Due Date</th>
                <th class="px-4 py-3">Amount Due</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3"></th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800">
              <tr v-for="(business, index) in timers" :key="business.business_id"
                class="text-gray-700 dark:text-gray-400">
                <td class="px-4 py-3">{{ index + 1 }}</td>
                <td class="px-4 py-3 text-blue-600 cursor-pointer" @click="goToRoute(business.profile_id)">{{ getFullName(business.first_name, business.middle_name, business.last_name) }}</td>
                <td class="px-4 py-3">{{ business.business_name }}</td>
                <td class="px-4 py-3" :class="{'text-red-700': business.status == 3}">{{ business.status == 3 ? 'CLOSED' : 'ACTIVE' }}</td>
                <td class="px-4 py-3">
                  <ul>
                    <li v-for="(payment, pIndex) in business.payments" :key="pIndex">
                      {{ new Date(payment.due_date).toLocaleDateString() }}
                    </li>
                  </ul>
                </td>
                <td class="px-4 py-3">
                  <ul>
                    <li v-for="(payment, pIndex) in business.payments" :key="pIndex">
                       {{'₱' + payment.amount }}
                    </li>
                  </ul>
                </td>
                <td class="px-4 py-3">
                  <ul>
                    <li v-for="(payment, pIndex) in business.payments" :key="pIndex">
                      <span class="badge" :class="{'badge-error' : payment.status != 'Not Yet'}">{{ payment.status }}</span>
                    </li>
                  </ul>
                </td>
                <td><button type="button" class="btn btn-primary" @click="SendPaymentReminder(index, business)" :class="{'disabled pointer-events-none opacity-70 bg-gray-300': business.timerActive}">
                    <span v-if="business.timerActive">{{ formatTime(business.remainingTime) }}</span>
                    <span v-else>Notify</span>
                  </button></td>
              </tr>
              <tr v-if="businessPayments.length <= 0">
                <td  colspan="5" class="px-4 py-3 text-center">No Unpaid Payments!</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </Layout>
</template>
