<script setup>
import Layout from '../../Layout.vue';
import { Head, usePage, useForm, router } from '@inertiajs/vue3';
import { onMounted, ref, computed } from 'vue';

defineProps({
  businesses: Array
});

function goBack() {
  window.history.back();
}

const getFullName = (first_name, middle_name, last_name) => {
  return (
    first_name +
    " " +
    (middle_name != null ? middle_name + " " : "") +
    last_name
  );
};

function goToRoute(id) {
  window.location.href = route('ceedo.vendorProfile', id);
}

const form = useForm({
  mobile_numbers: []
});

onMounted(()=>{
  form.mobile_numbers = usePage().props.businesses.map((business) => business.profile.user.mobile_number);

  timers.value = usePage().props.businesses.map((business) => ({
    ...business,
    remainingTime: 0, // Initial time in seconds
    timerActive: false, // Timer status
  }));
});

const isCounting = ref(false); // Tracks if the countdown is active
const timeLeft = ref(300); // Countdown duration in seconds (5 minutes)

// Compute minutes and seconds from timeLeft
const minutes = computed(() => Math.floor(timeLeft.value / 60));
const seconds = computed(() => timeLeft.value % 60);

let intervalId;

function startCountdown() {
  isCounting.value = true; // Hide the button
  timeLeft.value = 300; // Set count time to 5 minutes (300 seconds)
  
  intervalId = setInterval(() => {
    if (timeLeft.value > 0) {
      timeLeft.value--;
    } else {
      clearInterval(intervalId); // Stop count
      isCounting.value = false; // Show the button again
    }
  }, 1000);
}

const sendPermitNotice = () => {
  //startCountdown();
  
  form.post(route('ceedo.SendPermitUpdateNotice'), {
    onSuccess: (page) => {
      if(page.props.flash.success){
          startCountdown();
          Swal.fire({
          toast: true,
          icon: 'success',
          position: 'top-end',
          showConfirmButton: true,
          title: page.props.flash.success,
        });
      }else{
        Swal.fire({
          toast: true,
          icon: 'error',
          position: 'top-end',
          showConfirmButton: true,
          title: page.props.flash.error,
        });
      }
    }
  });
  
};

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

const sendWarning = (index, mobile_number) => {

  router.post(route('ceedo.sendWarning'), 
  {
    mobile_number: mobile_number
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

  <Head title="Dashboard" />
  <Layout>
    <div class="container px-6 mx-auto grid">
      <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
        Vendors With Expired Business Permits
      </h2>
      <!-- CTA -->
      <div class="">
        <div class="breadcrumbs text-sm">
          <ul>
            <li>
              <a @click="goBack()">Compliance</a>
            </li>
            <li>Renewal</li>
          </ul>
        </div>
      </div>

      <div class="w-full overflow-hidden rounded-lg shadow-xs">
        <div class="w-full overflow-x-auto">
          <div class="mb-2 p-2 ">
          </div>
          <table class="w-full whitespace-no-wrap">
            <thead>
              <tr
                class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800">
                <th class="px-4 py-3">ID</th>
                <th class="px-4 py-3">Name</th>
                <th class="px-4 py-3">Business Name</th>
                <th class="px-4 py-3">Permit Number</th>
                <th class="px-4 py-3">Expiration Date</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">
                  <button v-if="!isCounting" type="button" class="btn btn-primary" @click="sendPermitNotice" :class="{'disabled pointer-events-none opacity-80': form.processing}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                      stroke="currentColor" class="size-6">
                      <path stroke-linecap="round" stroke-linejoin="round"
                        d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0M3.124 7.5A8.969 8.969 0 0 1 5.292 3m13.416 0a8.969 8.969 0 0 1 2.168 4.5" />
                    </svg>

                    <span class="mx-1">Remind All</span>
                    <span v-if="form.processing" class="spinner-xs ml-2 spinner-circle [--spinner-color:var(--gray-8)]"></span>
                  </button>
                  <button v-else type="button" class="btn disabled pointer-events-none bg-gray-300">
                    Send Again After {{ minutes }}:{{ seconds < 10 ? '0' : '' }}{{ seconds }}
                  </button>
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800">
              <tr v-for="(business, index) in timers" :key="business.index"
                class="text-gray-700 dark:text-gray-400">
                <td class="px-4 py-3">{{ business.id }}</td>
                <td class="px-4 py-4 text-blue-700 cursor-pointer" @click="goToRoute(business.profile_id)">{{
                  getFullName(business.profile.first_name, business.profile.middle_name, business.profile.last_name) }}
                </td>
                <td class="px-4 py-3">{{ business.name }}</td>
                <td class="px-4 py-3">{{ business.permit_number }}</td>
                <td class="px-4 py-3">{{ business.permit_expiration_date }}</td>
                <td class="px-4 py-3"><span class="badge badge-error">Expired</span></td>
                <td class="px-4 py-3">
                  <button type="button" class="btn btn-error" @click="sendWarning(index, business.profile.user.mobile_number)" :class="{'disabled pointer-events-none opacity-70 bg-gray-300': business.timerActive}">
                    <span v-if="business.timerActive">{{ formatTime(business.remainingTime) }}</span>
                    <span v-else>Send Warning</span>
                  </button>
              </td>
              </tr>
              <tr v-if="usePage().props.businesses.length <= 0">
                <td colspan="7" class="px-4 py-3 text-center">No Data Found!</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </Layout>
</template>