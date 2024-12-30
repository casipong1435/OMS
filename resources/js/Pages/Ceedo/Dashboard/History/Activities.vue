<script setup>
import Layout from '../../Layout.vue';
import { Head, usePage, router } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

//const { activities } = usePage().props;

const searchQuery = ref('');
const currentPage = ref(1);
const perPage = 5;
const isDeleteOpen = ref(false);

// Computed to filter activities based on searchQuery
const filteredActivities = computed(() => {
  if (searchQuery.value === '') return usePage().props.activities.data;
  return usePage().props.activities.data.filter(activity =>
    activity.details.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    activity.activity.toLowerCase().includes(searchQuery.value.toLowerCase())
  );
});

// Paginated activities
const paginatedActivities = computed(() => {
  const start = (currentPage.value - 1) * perPage;
  const end = start + perPage;
  return filteredActivities.value.slice(start, end);
});

// Total pages
const totalPages = computed(() => Math.ceil(filteredActivities.value.length / perPage));

// Method to go to the next page
const nextPage = () => {
  if (currentPage.value < totalPages.value) currentPage.value++;
};

// Method to go to the previous page
const prevPage = () => {
  if (currentPage.value > 1) currentPage.value--;
};

// Watch searchQuery to reset pagination
watch(searchQuery, () => {
  currentPage.value = 1;
});

const deleteAllActivities = () => {
  router.delete(route('ceedo.deleteActivities'), 
  {
    onSuccess: (page) => {
      isDeleteOpen.value = false;
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

  <Head title="System Logs" />
  <Layout>
    <div class="container mx-auto p-6 bg-white rounded-lg shadow-lg">
      <h2 class="text-2xl font-semibold text-gray-800 mb-4">System Logs</h2>

      <!-- Search Bar -->
      <div class="flex justify-between items-center mb-4">
        <div>
          <button type="button" class="btn btn-error"
            :class="{ 'disabled opacity-75 pointer-events-none': usePage().props.activities.data.length <= 0 }"
            @click="isDeleteOpen = true">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
              stroke="currentColor" class="w-6 h-6">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
            </svg>
            <span class="ml-2">Delete All</span>
          </button>
        </div>
        <div>
          <input type="text" placeholder="Search logs..." v-model="searchQuery"
            class="p-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none" />
        </div>
      </div>

      <!-- Table -->
      <div class="overflow-x-auto">
        <table class="table-auto w-full border-collapse border border-gray-300 text-left">
          <thead>
            <tr class="bg-gray-200 text-gray-700">
              <th class="p-3 border border-gray-300">Date & Time</th>
              <th class="p-3 border border-gray-300">Activity</th>
              <th class="p-3 border border-gray-300">Details</th>
            </tr>
          </thead>
          <tbody>
            <tr class="hover:bg-gray-100" v-for="activity in paginatedActivities" :key="activity.id">
              <td class="p-3 border border-gray-300">{{ new Date(activity.created_at).toLocaleString() }}</td>
              <td class="p-3 border border-gray-300">{{ activity.activity }}</td>
              <td class="p-3 border border-gray-300">{{ activity.details }}</td>
            </tr>
            <tr v-if="paginatedActivities.length === 0">
              <td colspan="3" class="text-center p-3 border border-gray-300">
                No Activities Found
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="flex justify-between items-center mt-4">
        <div class="text-sm text-gray-600">
          {{ 'Showing ' + ((currentPage - 1) * perPage + 1) + ' to ' + Math.min(currentPage * perPage,
            filteredActivities.length) + ' of ' + filteredActivities.length + ' entries' }}
        </div>
        <div class="flex space-x-2">
          <button @click="prevPage" :disabled="currentPage === 1"
            class="px-3 py-1 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 disabled:opacity-50">
            Previous
          </button>
          <button @click="nextPage" :disabled="currentPage === totalPages"
            class="px-3 py-1 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 disabled:opacity-50">
            Next
          </button>
        </div>
      </div>
    </div>

    <input class="modal-state" id="deleteActivity" type="checkbox" v-model="isDeleteOpen" />
    <div class="modal" style="z-index: 9999;">
      <label class="modal-overlay"></label>
      <div class="modal-content flex flex-col gap-5">
        <label for="deleteActivity" class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</label>
        <div class="my-2 flex flex-col justify-center items-center p-2">
          <span class="text-red-700 mb-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
              stroke="currentColor" class="w-14 h-14">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
            </svg>
          </span>
          <span class="text-lg mb-2">Are you sure you want to delete all the activities?</span>
        </div>
        <div class="flex gap-3">
          <button class="btn btn-error btn-block" @click="deleteAllActivities">Confirm</button>
          <button type="button" class="btn btn-block" @click="isDeleteOpen = false">Cancel</button>
        </div>
      </div>
    </div>


  </Layout>
</template>
