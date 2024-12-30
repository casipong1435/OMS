<script setup>
import Container from './Container.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
  canLogin: {
    type: Boolean,
  },
  canRegister: {
    type: Boolean,
  },
});
</script>

<template>
  <div class="relative" id="home">
    <div aria-hidden="true" class="absolute inset-0 grid grid-cols-2 -space-x-52 opacity-40 dark:opacity-20">
      <div class="blur-[106px] h-56 bg-gradient-to-br from-green-700 to-purple-400 dark:from-blue-700"></div>
      <div class="blur-[106px] h-32 bg-gradient-to-r from-cyan-400 to-sky-300 dark:to-indigo-600"></div>
    </div>
    <Container>
      <div class="relative pt-20 ml-auto">
        <div class="lg:w-2/3 text-center mx-auto mb-10">
          <h1 class="text-gray-900 dark:text-white font-bold text-5xl md:text-6xl xl:text-7xl">Start your business in
            <span class="text-orange-500 dark:text-white">Tangub City.</span>
          </h1>
          <p class="mt-8 text-gray-700 dark:text-gray-300">Tangub City offers a great opportunity with a growing
            economy, supportive local government, and a thriving entrepreneurial community. With its strategic location
            and business-friendly environment, Tangub City is the perfect place to launch your venture and thrive.</p>
          <div class="mt-16 flex flex-wrap justify-center gap-y-4 gap-x-6">
            <Link :href="!$page.props.auth.user ? route('register') : route('services')"
              class="relative flex h-11 w-full items-center justify-center px-6 before:absolute before:inset-0 before:rounded-full before:bg-primary before:transition before:duration-300 hover:before:scale-105 active:duration-75 active:before:scale-95 sm:w-max">
            <span class="relative text-base font-semibold text-white">Get started</span>
            </Link>
            <Link :href="route('about')"
              class="relative flex h-11 w-full items-center justify-center px-6 before:absolute before:inset-0 before:rounded-full before:border before:border-transparent before:bg-primary/10 before:bg-gradient-to-b before:transition before:duration-300 hover:before:scale-105 active:duration-75 active:before:scale-95 dark:before:border-gray-700 dark:before:bg-gray-800 sm:w-max">
            <span class="relative text-base font-semibold text-primary dark:text-white">Learn more</span>
            </Link>
          </div>
        </div>
        <div data-aos="fade-up" data-aos-duration="500"
    data-aos-easing="ease-in-out"
          class="grid grid-cols-3 gap-10 py-8 px-10 mt-20 border-y border-gray-100 dark:border-gray-800 sm:flex justify-between">
          <div class="text-left col-span-3 md:col-span-1">
            <h6 class="text-lg font-semibold text-gray-700 dark:text-white">Small to Medium Businesses</h6>
            <p class="mt-2 text-gray-500">Vital contributors to the economy, offering personalized services and
              fostering community growth.</p>
          </div>
          <div class="text-left col-span-3 md:col-span-1" data-aos="fade-up" data-aos-duration="500"
          data-aos-easing="ease-in-out">
            <h6 class="text-lg font-semibold text-gray-700 dark:text-white">Starting Ground for Entreprenuership</h6>
            <p class="mt-2 text-gray-500">A perfect place to launch and grow your business with supportive resources and opportunities.</p>
          </div>
          <div class="text-left col-span-3 md:col-span-1" data-aos="fade-up" data-aos-duration="500"
          data-aos-easing="ease-in-out">
            <h6 class="text-lg font-semibold text-gray-700 dark:text-white">Thriving Hub for Innovation</h6>
            <p class="mt-2 text-gray-500">A dynamic environment where creativity and new ideas drive progress and business success.</p>
          </div>
        </div>
      </div>
    </Container>
  </div>
</template>