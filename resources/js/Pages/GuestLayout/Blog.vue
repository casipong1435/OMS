<script setup>
import Container from './Container.vue';
import { Link } from '@inertiajs/vue3';

</script>


<template>
  <div id="blog">
    <Container data-aos="fade-left" data-aos-duration="500"
    data-aos-easing="ease-in-out">
      <div class="mb-12 space-y-2 text-center">
        <h2 class="text-3xl font-bold text-gray-800 md:text-4xl dark:text-white">Featured Areas</h2>
        <p class="lg:mx-auto lg:w-6/12 text-gray-600 dark:text-gray-300">
          Tangub City offers diverse areas ideal for small to medium businesses, from high-traffic commercial zones to
          quieter spots for specialized services. Find the perfect location to launch and grow your business.
        </p>
      </div>
      <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
        <div
          class="group p-6 sm:p-8 rounded-3xl bg-white border border-gray-100 dark:shadow-none dark:border-gray-700 dark:bg-gray-800 bg-opacity-50 shadow-2xl shadow-gray-600/10" v-for="area in $page.props.featured_areas" :key="area.id">
          <div class="relative overflow-hidden rounded-xl">
            <img v-if="area.image"
              :src="'/images/Areas/Establishment/' + area.image"
              alt="art cover" loading="lazy" width="1000" height="667"
              class="h-64 w-full object-cover object-top transition duration-500 group-hover:scale-105" />
              <img v-else
              src="/images/Areas/Establishment/vendor.jpg"
              alt="art cover" loading="lazy" width="1000" height="667"
              class="h-64 w-full object-cover object-top transition duration-500 group-hover:scale-105" />
          </div>
          <div class="mt-6 relative">
            <h3 class="text-2xl font-semibold text-gray-800 dark:text-white">
              {{ area.name }}
            </h3>
            <p class="mt-6 mb-8 text-gray-600 dark:text-gray-300">
              {{ area.description }}
            </p>
            <a class="inline-block" :href="route('guest.establishment', area.id)">
              <span class="text-info dark:text-blue-300 hover:text-blue-600">Explore</span>
            </a>
          </div>

        </div>
        

        <div v-if="$page.props.area_count > 3" class="md:col-span-2 lg:col-span-3 text-center mt-4">
          <Link as="button" :href="route('services')" class="btn btn-solid-primary font-bold p-6 text-md">Show More
          </Link>
        </div>
      </div>
    </Container>
  </div>
</template>