<script setup>
import Layout from '../Layout.vue';
import { Head, usePage } from '@inertiajs/vue3';

defineProps({
  businesses: Array
});

function goBack(){
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

function goToRoute(id){
  window.location.href = route('mayor.vendorProfile', id);
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
              </tr>
            </thead>
            <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800">
              <tr v-for="business in usePage().props.businesses" :key="business.index" class="text-gray-700 dark:text-gray-400">
                <td class="px-4 py-3">{{ business.id }}</td>
                <td class="px-4 py-4 text-blue-700 cursor-pointer" @click="goToRoute(business.profile_id)">{{ getFullName(business.profile.first_name, business.profile.middle_name, business.profile.last_name) }}</td>
                <td class="px-4 py-3">{{ business.name }}</td>
                <td class="px-4 py-3">{{ business.permit_number }}</td>
                <td class="px-4 py-3">{{ business.permit_expiration_date }}</td>
                <td class="px-4 py-3"><span class="badge badge-error">Expired</span></td>
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