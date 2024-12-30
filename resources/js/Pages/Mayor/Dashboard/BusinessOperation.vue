<script setup>
import Layout from '../Layout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
  areas: Array
});

function goBack(){
 window.history.back();
}

</script>

<template>

  <Head title="Dashboard" />
  <Layout>
    <div class="container mx-auto p-6">
      <div class="mb-4">
        <div class="breadcrumbs text-sm">
          <ul>
            <li>
              <a @click="goBack()" class="text-gray-800">
              Business Operations</a>
            </li>
            <li></li>
          </ul>
        </div>
      </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
      <div class="bg-white shadow-lg rounded-lg overflow-hidden" v-for="area in $page.props.areas" :key="area.id">
        <img class="w-full h-48 object-cover" :src="area.image ? '/images/Areas/Establishment/' + area.image : '/images/Areas/Establishment/vendor.jpg' " alt="Card Image" />
        <div class="p-6">
          <h2 class="text-xl font-semibold text-gray-800 mb-2">{{ area.name }}</h2>
          <div class="mb-2">
              <span class="me-2 font-bold">Vendors: </span>
              <span>{{ area.active_business_count }}</span>
          </div>
          
          <a :href="route('mayor.business-sections', area.id)" class="inline-block bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition duration-300">View Details</a>
        </div>
      </div>

    </div>
    </div>
  </Layout>
</template>