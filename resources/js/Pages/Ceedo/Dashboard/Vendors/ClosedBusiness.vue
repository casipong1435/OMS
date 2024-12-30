<script setup>
import Layout from '../../Layout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import axios from 'axios';

defineProps({
  closed_business: Array
});

const searchInput = ref(null);

const getFullName = (first_name, middle_name, last_name) => {
    return first_name + " " + (middle_name != null ? middle_name + " " : "") + last_name;
}

function filtersearchInput() {
    router.get(route('ceedo.closed_business'),
        { searchInput: searchInput.value },
        {
            preserveState: true,
            replace: true
        })
}

watch(searchInput, (value) => {
    if (value == "") {
        router.get(route('ceedo.closed_business'),
            { searchInput: value },
            {
                preserveState: true,
                replace: true
            })
    }
});

const formatDate = (date) => {
    date = new Date();
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are 0-based
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
};

function goToRoute(id){
  window.location.href = route('ceedo.vendorProfile', id);
}

</script>

<template>

  <Head title="Vendors" />
  <Layout>
    <div class="container px-6 mx-auto grid">
      <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
        Closed Business
      </h2>
      <!-- CTA -->
      <div class="">
        <div class="breadcrumbs text-sm">
          <ul>
            <li>
              <a>Closed Business</a>
            </li>
            <li></li>
          </ul>
        </div>
      </div>

      <div class="w-full overflow-hidden rounded-lg shadow-xs">
        <div class="w-full overflow-x-auto">
          <div class="mb-2 p-2 ">

            <div class="block sm:flex sm:justify-end sm:items-center gap-3">

              <div class="relative w-full max-w-xl focus-within:text-orange-500 overflow-x-auto mt-2 md:mt-0">
                <div class="absolute inset-y-0 flex items-center cursor-pointer text-white bg-primary rounded-none w-14 flex justify-center items-center" @click="filtersearchInput">
                  <svg class="w-4 h-4" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                      d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                      clip-rule="evenodd"></path>
                  </svg>
                </div>
                <input v-model="searchInput"
                  class="w-full pl-20 pr-2 text-sm text-gray-700 focus:border-blue-600 rounded focus:rounded outline-none"
                  type="text" placeholder="Search User" aria-label="Search" />
              </div>
            </div>
          </div>
          <table class="w-full whitespace-no-wrap">
            <thead>
              <tr
                class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800">
                <th class="px-4 py-3">ID</th>
                <th class="px-4 py-3">Name</th>
                <th class="px-4 py-3">Business Name</th>
                <th class="px-4 py-3">Kind of Business</th>
                <th class="px-4 py-3">Area</th>
                <th class="px-4 py-3">Section</th>
                <th class="px-4 py-3">Stall #</th>
                <th class="px-4 py-3">Date Closed</th>
                <th class="px-4 py-3">Reason</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800">
              <tr v-for="business in closed_business" :key="business.id" class="text-gray-700 dark:text-gray-400 hover:bg-gray-100 cursor-pointer" @click="goToRoute(business.profile.id)">
                <td class="px-4 py-3 text-sm">
                  <span class=" font-semibold rounded-full dark:bg-green-700 dark:text-green-100">
                    {{ business.id }}
                  </span>
                </td>
                <td class="px-4 py-3">
                  <div class="flex items-center text-sm">
                    <div class="relative hidden w-8 h-8 mr-3 rounded-full md:block">
                      <img v-if="business.profile.image" class="object-cover w-8 h-8 rounded-full" :src="'/images/clients/' + business.profile.image" alt=""
                      aria-hidden="true" />
                    <img v-else class="object-cover w-8 h-8 rounded-full" src="/images/avatars/avatar.png" alt=""
                      aria-hidden="true" />
                      <div class="absolute inset-0 rounded-full shadow-inner" aria-hidden="true"></div>
                    </div>
                    <div>
                      <p class="font-semibold">{{ getFullName(business.profile.first_name, business.profile.middle_name_name, business.profile.last_name) }}</p>
                    </div>
                  </div>
                </td>
                <td class="px-4 py-3 text-sm">
                  <span class="px-2 py-1 font-semibold rounded-full dark:bg-green-700 dark:text-green-100">
                    {{ business.name }}
                  </span>
                </td>
                <td class="px-4 py-3 text-sm">
                  <span class="px-2 py-1 font-semibold rounded-full dark:bg-green-700 dark:text-green-100">
                    {{ business.kind_of_business }}
                  </span>
                </td>
                <td class="px-4 py-3 text-sm">
                  <span class="px-2 py-1 font-semibold rounded-full dark:bg-green-700 dark:text-green-100">
                    {{ business.kind_of_business }}
                  </span>
                </td>
                <td class="px-4 py-3 text-sm">
                  <span class="px-2 py-1 font-semibold rounded-full dark:bg-green-700 dark:text-green-100">
                    {{ business.establishment_unit.establishment.area.name }}
                  </span>
                </td>
                <td class="px-4 py-3 text-sm">
                  <span class="px-2 py-1 font-semibold rounded-full dark:bg-green-700 dark:text-green-100">
                    {{ business.establishment_unit.establishment.name }}
                  </span>
                </td>
                <td class="px-4 py-3 text-sm">
                  <span class="px-2 py-1 font-semibold rounded-full dark:bg-green-700 dark:text-green-100">
                    {{ business.establishment_unit.id }}
                  </span>
                </td>
                <td class="px-4 py-3 text-sm">
                  <span class="px-2 py-1 font-semibold rounded-full dark:bg-green-700 dark:text-green-100">
                    {{ formatDate(business.date_closed) }}
                  </span>
                </td>
                <td class="px-4 py-3 text-sm">
                  <span class="px-2 py-1 font-semibold rounded-full dark:bg-green-700 dark:text-green-100">
                    {{ formatDate(business.remarks) }}
                  </span>
                </td>
              </tr>
              <tr v-if="closed_business.length <= 0" class="text-center">
                <td colspan="9" class="p-2">No Closed Business!</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </Layout>
</template>