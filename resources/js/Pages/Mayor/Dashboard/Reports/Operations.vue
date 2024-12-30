<script setup>
import Layout from '../../Layout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { watch, ref } from 'vue';
import axios from 'axios';

defineProps({
  areas: Array
});

const dropdownCategoryOpen = ref(false);
const selectedCategoryItems = ref([]);
const isAllCategorySelected = ref(false);
const vendors = ref([]);
const result = ref('');
const isFiltered = ref(false);

const toggleCategoryDropdown = () => {
  dropdownCategoryOpen.value = !dropdownCategoryOpen.value;
};

const handleSelectAllCategory = () => {
  if (isAllCategorySelected.value) {
    selectedCategoryItems.value = [];
  } else {
    selectedCategoryItems.value = usePage().props.areas.map((area) => area.id);
    console.log(selectedCategoryItems.value);
  }
  isAllCategorySelected.value = !isAllCategorySelected.value;

};

const getFullName = (first_name, middle_name, last_name) => {
  return first_name + " " + (middle_name != null ? middle_name + " " : "") + last_name;
};

function filterOperation(){
  axios.get(route('getOperationsReport'),{
    params: {
      category: JSON.stringify(selectedCategoryItems.value)
    }
  }).then(response => {
      vendors.value = response.data.vendors;
      isFiltered.value = true;
      if(vendors.value.length <= 0){
        result.value = 'No Data Found!'
      }
  }).catch(error => {
    // Handle error
    alert('There was an error fetching the data:', error);
  });
}

const formatDate = (date) => {
  date = new Date();
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are 0-based
  const day = String(date.getDate()).padStart(2, '0');
  return `${year}-${month}-${day}`;
};

</script>

<template>

  <Head title="Dashboard" />
  <Layout>
    <div class="container px-6 mx-auto grid">
      <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
        Operations Report
      </h2>

      <div class="flex justify-between items-center flex-col md:flex-row space-x-4 mt-4 sm:mt-0 mb-4">
        <span>
          <a target="_blank" as="button" :href="vendors <= 0 ? '' : route('mayor.operationalReport', JSON.stringify(selectedCategoryItems))" class="text-red-700"
            :class="{ 'disabled opacity-70 pointer-events-none': vendors <= 0 }">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
              stroke="currentColor" class="size-6">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
            </svg>
          </a>
        </span>
        <div class="flex justify-between items-center gap-2 flex-col md:flex-row">
          <button class="btn" @click="filterOperation()">Filter</button>
          <div class="relative col-span-1" style="width: 280px;">
            <!-- Trigger Button -->
            <button @click="toggleCategoryDropdown"
              class="w-full px-4 py-2 text-left border border-gray-300 rounded-md shadow-sm bg-white focus:outline-none">
              <span class="flex items-center justify-center text-sm">
                <span>Select Area </span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                  stroke="currentColor" class="size-4">
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="M8.25 15 12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                </svg>
              </span>
            </button>

            <!-- Dropdown -->
            <div v-if="dropdownCategoryOpen"
              class="absolute z-10 w-full mt-2 bg-white border border-gray-300 rounded-md shadow-lg">
              <div class="p-2 max-h-40 overflow-y-auto">
                <label
                  class="flex items-center space-x-2 cursor-pointer" for="all_area">
                  <input type="checkbox" id="all_area" :checked="isAllCategorySelected" @change="handleSelectAllCategory" 
                    class="text-blue-600 rounded focus:ring-2 focus:ring-blue-500" />
                  <span class="text-gray-700">All</span>
                </label>
                <label v-for="area in $page.props.areas" :key="area.id"
                  class="flex items-center space-x-2 cursor-pointer" :for="area.id">
                  <input type="checkbox" :id="area.id" :value="area.id" v-model="selectedCategoryItems"
                    class="text-blue-600 rounded focus:ring-2 focus:ring-blue-500" />
                  <span class="text-gray-700">{{ area.name }}</span>
                </label>


              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- CTA -->
      <!-- Responsive Table -->
      <div class="overflow-x-auto bg-white shadow-md rounded-lg">
        <table class="w-full text-left">
          <thead>
            <tr class="bg-gray-200">
              <th class="px-4 py-3">ID</th>
              <th class="px-4 py-3">Name</th>
              <th class="px-4 py-3">Business</th>
              <th class="px-4 py-3">Area</th>
              <th class="px-4 py-3">Section</th>
              <th class="px-4 py-3">Stall #</th>
              <th class="px-4 py-3">Date Approved</th>
              <th class="px-4 py-3">Status</th>
              <th class="px-4 py-3">Remarks</th>
            </tr>
          </thead>
          <tbody v-if="isFiltered">
            <tr v-for="vendor in vendors" :key="vendor.id">
              <td class="px-4 py-3 text-sm">
                {{ vendor.id }}
              </td>
              <td class="px-4 py-3">
                {{ getFullName(vendor.profile.first_name, vendor.profile.middle_name_name, vendor.profile.last_name) }}
              </td>
              <td class="px-4 py-3 text-sm">
                <span class="px-2 py-1 ">
                  {{ vendor.kind_of_business }}
                </span>
              </td>
              <td class="px-4 py-3 text-sm">
                <span class="px-2 py-1 ">
                  {{ vendor.establishment_unit.establishment.area.name }}
                </span>
              </td>
              <td class="px-4 py-3 text-sm">
                <span class="px-2 py-1 ">
                  {{ vendor.establishment_unit.establishment.name }}
                </span>
              </td>
              <td class="px-4 py-3 text-sm">
                <span class="px-2 py-1 ">
                  {{ vendor.establishment_unit.id }}
                </span>
              </td>
              <td class="px-4 py-3 text-sm">
                <span class="px-2 py-1 ">
                  {{ formatDate(vendor.date_approved) }}
                </span>
              </td>
              <td :class="{ 'text-green-700': vendor.status == 1, 'text-red-700': vendor.status == 0 }">{{ vendor.status
                == 1 ? 'Active' : 'Closed' }}</td>
              <td>{{ vendor.remarks }}</td>
            </tr>
            <tr v-if="vendors.length <= 0" class="text-center">
              <td colspan="9" class="p-2">{{ result }}</td>
            </tr>
          </tbody>
          <tbody v-else>
            <tr>
              <td colspan="9" class="px-4 py-2 text-center">Filter Data First!</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </Layout>
</template>