<script setup>
import Layout from '../../Layout.vue';
import { Head, usePage, useForm, router } from '@inertiajs/vue3';
import { onMounted, ref, computed, reactive } from 'vue';

defineProps({
  businesses: Array,
  requests: Array
});

const activeTab = ref(0);
const fullscreenImage = ref(null);
const isResponseModalOpen = ref(false);

function openImage(image) {
  fullscreenImage.value = image;
}

function closeImage() {
  fullscreenImage.value = null;
}

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

function goToRoute(id, business_id) {
  window.location.href = route('ceedo.vendorProfile', {id:id, business_id:business_id});
}

const form = useForm({
  mobile_numbers: []
});

onMounted(() => {
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
      if (page.props.flash.success) {
        startCountdown();
        Swal.fire({
          toast: true,
          icon: 'success',
          position: 'top-end',
          showConfirmButton: true,
          title: page.props.flash.success,
        });
      } else {
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

const permitRequest = reactive({
  status: null,
  business_id: null,
  reason: null,
  id: null
});

function openResponseModal(request, status) {
  isResponseModalOpen.value = true;
  permitRequest.business_id = request.business.id;
  permitRequest.id = request.id;
  permitRequest.status = status;
}

function closeResponseModal() {
  isResponseModalOpen.value = false;
  permitRequest.business_id = null;
  permitRequest.id = null;
  permitRequest.status = null;
  permitRequest.reason = null;
}

const error_msg = ref(null);

function respondRequest() {
  router.put(route('ceedo.respondPermitUpdate'),
    {
      permitRequest
    },
    {
      onSuccess: (page) => {
        if (page.props.flash.success) {
          isResponseModalOpen.value = false;
          Swal.fire({
            toast: true,
            icon: 'success',
            position: 'top-end',
            showConfirmButton: true,
            title: page.props.flash.success,
          });
        } else {
          error_msg.value = page.props.flash.error;
        }
      }
    });
}

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

      <div class="tabs my-4">
        <div class="tab tab-bordered px-6" :class="{ 'tab-active': activeTab == 0 }" @click="activeTab = 0">
          Expired Permits
        </div>
        <div class="tab tab-bordered px-6" :class="{ 'tab-active': activeTab == 1 }" @click="activeTab = 1">
          Updates Approval
        </div>
      </div>

      <div v-if="activeTab == 0" class="w-full overflow-hidden rounded-lg shadow-xs">
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
                  <button v-if="!isCounting" type="button" class="btn btn-primary" @click="sendPermitNotice"
                    :class="{ 'disabled pointer-events-none opacity-80': form.processing || $page.props.businesses.length <= 0 }">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                      stroke="currentColor" class="size-6">
                      <path stroke-linecap="round" stroke-linejoin="round"
                        d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0M3.124 7.5A8.969 8.969 0 0 1 5.292 3m13.416 0a8.969 8.969 0 0 1 2.168 4.5" />
                    </svg>

                    <span class="mx-1">Remind All</span>
                    <span v-if="form.processing"
                      class="spinner-xs ml-2 spinner-circle [--spinner-color:var(--gray-8)]"></span>
                  </button>
                  <button v-else type="button" class="btn disabled pointer-events-none bg-gray-300">
                    Send Again After {{ minutes }}:{{ seconds < 10 ? '0' : '' }}{{ seconds }} </button>
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800">
              <tr v-for="(business, index) in timers" :key="business.index" class="text-gray-700 dark:text-gray-400">
                <td class="px-4 py-3">{{ business.id }}</td>
                <td class="px-4 py-4 text-blue-700 cursor-pointer" @click="goToRoute(business.profile_id, business.id)">{{
                  getFullName(business.profile.first_name, business.profile.middle_name, business.profile.last_name) }}
                </td>
                <td class="px-4 py-3">{{ business.name }}</td>
                <td class="px-4 py-3">{{ business.permit_number }}</td>
                <td class="px-4 py-3">{{ business.permit_expiration_date }}</td>
                <td class="px-4 py-3"><span class="badge badge-error">Expired</span></td>
                <td class="px-4 py-3">
                  <button type="button" class="btn btn-error"
                    @click="sendWarning(index, business.profile.user.mobile_number)"
                    :class="{ 'disabled pointer-events-none opacity-70 bg-gray-300': business.timerActive }">
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

      <div v-else class="w-full overflow-hidden rounded-lg shadow-xs">
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
                <th class="px-4 py-3">Image</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Action</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800">
              <tr v-for="request in usePage().props.requests" :key="request.id">
                <td class="px-4 py-3">{{ request.id }}</td>
                <td class="px-4 py-3">{{
                  getFullName(request.business.profile.first_name, request.business.profile.middle_name,
                    request.business.profile.last_name) }}
                </td>
                <td class="px-4 py-3">{{ request.business.name }}</td>
                <td class="px-4 py-3">{{ request.permit_number }}</td>
                <td class="px-4 py-3">{{ request.expiration_date }}</td>
                <td class="px-4 py-3">
                  <span class="text-purple-600 cursor-pointer" @click="openImage(request.image)">View Image</span>
                </td>
                <td class="px-4 py-3"><span class="badge badge-warning">Pending</span></td>
                <td class="px-4 py-3">
                  <button type="button" class="text-green-600" @click="openResponseModal(request, 1)">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                      stroke="currentColor" class="size-6">
                      <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                  </button>
                  <button type="button" class="text-red-600" @click="openResponseModal(request, 2)">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                      stroke="currentColor" class="size-6">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                  </button>
                </td>
              </tr>
              <tr v-if="requests.length <= 0">
                <td colspan="7" class="px-4 py-3 text-center">No Data Found!</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Fullscreen Modal -->
    <div v-if="fullscreenImage" class="fixed inset-0 z-50 bg-black bg-opacity-80 flex items-center justify-center">
      <button class="absolute top-5 right-5 text-white text-3xl" @click="closeImage">
        ✕
      </button>
      <img :src="'/images/business/' + fullscreenImage" alt="Fullscreen Image"
        class="max-w-full max-h-full rounded-lg shadow-lg" />
    </div>

    <!--Respond Modal-->
    <input class="modal-state" id="responseModal" type="checkbox" v-model="isResponseModalOpen" />
    <div class="modal">
      <label class="modal-overlay"></label>
      <div class="modal-content flex flex-col gap-5">
        <label for="responseModal" class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</label>
        <h2 class="text-xl">Respond Permit Update</h2>
        <div class="flex justify-center items-center flex-col gap-2"
          :class="{ 'text-success': permitRequest.status == 1, 'text-error': permitRequest.status == 2 }">
          <span>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
              stroke="currentColor" class="w-20 h-20">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
            </svg>
          </span>
          <span class="text-gray-700">Are you sure you want to {{ permitRequest.status == 1 ? 'approve' : 'decline' }} this
            business permit update?</span>
        </div>
        <div class="my-3" v-if="permitRequest.status == 2">
          <label for="reason">Reason:</label>
          <textarea id="reason" v-model="permitRequest.reason" class="w-full" required></textarea>
          <div v-if="error_msg != null" class="mt-2 text-red-600">{{ error_msg }}</div>
        </div>
        <div class="flex gap-3">
          <button class="btn btn-block"
            :class="{ 'btn-success': permitRequest.status == 1, 'btn-error': permitRequest.status == 2 }"
            @click="respondRequest()">Confirm</button>
          <button class="btn btn-block" @click="closeResponseModal()">Cancel</button>
        </div>
      </div>
    </div>

  </Layout>
</template>