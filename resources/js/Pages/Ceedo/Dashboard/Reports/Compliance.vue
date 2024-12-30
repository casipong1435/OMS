<script setup>
import Layout from '../../Layout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

defineProps({
  businesses: Array
});

const getFullName = (first_name, middle_name, last_name) => {
  return (
    first_name +
    " " +
    (middle_name != null ? middle_name + " " : "") +
    last_name
  );
};

</script>

<template>

  <Head title="Dashboard" />
  <Layout>
    <div class="container mx-auto p-6">
      <!-- Date Filters -->
      <div class="flex flex-col sm:flex-row items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Compliance Reports</h1>

      </div>

      <!-- Responsive Table -->
      <div class="overflow-x-auto bg-white shadow-md rounded-lg">
        <table class="w-full text-left table-auto">
          <thead>
            <tr class="bg-gray-100 border-b">
              <th colspan="7" class="px-6 py-4 text-lg font-semibold text-gray-800 flex w-full">
                <span class="me-2">Expire Permits</span>
                <span>
                  <a target="_blank" :href="route('ceedo.complianceReport')" class="text-red-700" :class="{'disabled opacity-70 pointer-events-none': usePage().props.businesses.length <= 0}">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                      d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                  </svg>
                </a>
              </span>
              </th>
            </tr>
            <tr class="bg-gray-200">
              <th class="px-6 py-3 text-gray-700 font-medium">Owner's Name</th>
              <th class="px-6 py-3 text-gray-700 font-medium">Business</th>
              <th class="px-6 py-3 text-gray-700 font-medium">Plate</th>
              <th class="px-6 py-3 text-gray-700 font-medium">Kind of Business</th>
              <th class="px-6 py-3 text-gray-700 font-medium">Permit</th>
              <th class="px-6 py-3 text-gray-700 font-medium">Expiration Date</th>
              <th class="px-6 py-3 text-gray-700 font-medium">Remarks</th>
            </tr>
          </thead>
          <tbody>
            <tr class="border-b hover:bg-gray-100 transition duration-300"
              v-for="business in usePage().props.businesses" :key="business.id">
              <td class="px-6 py-4 text-gray-800">{{ getFullName(business.profile.first_name,
                business.profile.middle_name, business.profile.last_name) }}</td>
              <td class="px-6 py-4 text-gray-800">{{ business.name }}</td>
              <td class="px-6 py-4 text-gray-800">{{ business.plate }}</td>
              <td class="px-6 py-4 text-gray-800">{{ business.kind_of_business }}</td>
              <td class="px-6 py-4 text-gray-800">{{ business.permit_number }}</td>
              <td class="px-6 py-4 text-gray-800">{{ business.permit_expiration_date }}</td>
              <td class="px-6 py-4 text-gray-800"><span class="badge badge-error">Expired</span></td>
            </tr>
            <!-- Additional rows can be added here -->
          </tbody>
        </table>
      </div>
    </div>
  </Layout>
</template>