<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { onMounted, computed, ref } from 'vue';
import Navigation from './Navigation.vue';

let isPagesMenuOpen = ref(false);
let isSideMenuOpen = ref(false);


defineProps({
  isSideMenuOpen: Boolean
})


onMounted(() => {
  usePage().props.isSideMenuOpen = false;
  isSideMenuOpen.value = usePage().props.isSideMenuOpen;
});

const closeSideMenu = () => {
  isSideMenuOpen.value = false
};

const getThemeFromLocalStorage = () => {
  // if user already changed the theme, use it
  if (window.localStorage.getItem('dark')) {
    return JSON.parse(window.localStorage.getItem('dark'))
  }

  // else return their preferences
  return (
    !!window.matchMedia &&
    window.matchMedia('(prefers-color-scheme: dark)').matches
  )
}

const setThemeToLocalStorage = (value) => {
  window.localStorage.setItem('dark', value)
}

let dark = getThemeFromLocalStorage();

const toggleTheme = () => {
  dark = !dark
  setThemeToLocalStorage(dark)
};

const toggleSideMenu = () => {
  isSideMenuOpen.value = !isSideMenuOpen.value;
}

let isNotificationsMenuOpen = ref(false);

const toggleNotificationsMenu = () => {
  isNotificationsMenuOpen.value = !isNotificationsMenuOpen.value
};

const closeNotificationsMenu = () => {
  isNotificationsMenuOpen.value = false
};

let isProfileMenuOpen = ref(false);

const toggleProfileMenu = () => {
  isProfileMenuOpen.value = !isProfileMenuOpen.value
}
const closeProfileMenu = () => {
  isProfileMenuOpen.value = false
};

</script>

<style>
.slide-enter-active,
.slide-leave-active {
  transition: opacity 0.15s ease-in-out, transform 0.15s ease-in-out;
}

.slide-enter-from,
.slide-leave-to {
  opacity: 0;
  transform: translateX(-20px);
}
</style>

<template>
  <aside class="z-20 hidden w-64 overflow-y-auto bg-white dark:bg-gray-800 md:block flex-shrink-0">
    <div class="py-4 text-gray-500 dark:text-gray-400">
      <Link :href="route('admin.dashboard')" aria-label="logo" class="ml-6 flex items-center space-x-2">
        <div aria-hidden="true" class="flex space-x-1">
          <div class="h-6 w-2 bg-orange-700"></div>
        </div>
        <span class="text-2xl font-bold text-gray-900 dark:text-white">OMS</span>
      </Link>
      <Navigation/>
    </div>
  </aside>

  <!-- Backdrop -->
  <Transition enter-active-class="transition ease-in-out duration-150" enter-from-class="opacity-0"
    enter-to-class="opacity-100" leave-active-class="transition ease-in-out duration-150" leave-from-class="opacity-100"
    leave-to-class="opacity-0">
    <div v-show="isSideMenuOpen"
      class="fixed inset-0 z-10 flex items-end bg-black bg-opacity-50 sm:items-center sm:justify-center"></div>
  </Transition>

  <!-- Mobile sidebar -->
  <aside class="fixed inset-y-0 z-20 flex-shrink-0 w-64 mt-16 overflow-y-auto bg-white dark:bg-gray-800 md:hidden"
    v-show="isSideMenuOpen" @click.self="closeSideMenu">
    <Transition enter-active-class="transition ease-in-out duration-150"
      enter-from-class="opacity-0 transform -translate-x-20" enter-to-class="opacity-100"
      leave-active-class="transition ease-in-out duration-150" leave-from-class="opacity-100"
      leave-to-class="opacity-0 transform -translate-x-20">
      <div v-show="isSideMenuOpen" class="py-4 text-gray-500 dark:text-gray-400">
        <Link :href="route('admin.dashboard')" aria-label="logo" class="ml-6 flex items-center space-x-2">
          <div aria-hidden="true" class="flex space-x-1">
            <div class="h-6 w-2 bg-orange-700"></div>
          </div>
          <span class="text-2xl font-bold text-gray-900 dark:text-white">OMS</span>
        </Link>
        <Navigation/>
      </div>
    </Transition>
  </aside>

  <div class="flex flex-col flex-1 w-full">
    <header class="z-10 py-4 bg-white shadow-md dark:bg-gray-800">
      <div class=" flex items-center justify-between h-full px-6 mx-auto text-purple-600 dark:text-purple-300">
        <!-- Mobile hamburger -->
        <button class="p-1 mr-5 -ml-1 rounded-md md:hidden focus:outline-none focus:shadow-outline-purple"
          @click="toggleSideMenu" aria-label="Menu">
          <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd"
              d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
              clip-rule="evenodd"></path>
          </svg>
        </button>
        <!-- Search input -->
        <div class="flex justify-center flex-1 lg:mr-32">
          
        </div>
        <ul class="flex items-center flex-shrink-0 space-x-6">
          <!-- Theme toggler -->

          <!-- Profile menu -->
          <li class="relative">
            <button class="align-middle rounded-full focus:shadow-outline-purple focus:outline-none"
              @click="toggleProfileMenu" aria-label="Account" aria-haspopup="true">
              <img class="object-cover w-8 h-8 rounded-full"
                src="/images/avatars/avatar.png"
                alt="" aria-hidden="true" />
            </button>
            <div v-if="isProfileMenuOpen">
              <ul @click.self="closeProfileMenu"
                class="absolute right-0 w-56 p-2 mt-2 space-y-2 text-gray-600 bg-white border border-gray-100 rounded-md shadow-md dark:border-gray-700 dark:text-gray-300 dark:bg-gray-700"
                aria-label="submenu">
                <li class="flex">
                  <Link class="inline-flex items-center w-full px-2 py-1 text-sm font-semibold transition-colors duration-150 rounded-md hover:bg-gray-100 hover:text-gray-800 dark:hover:bg-gray-800 dark:hover:text-gray-200"
                    :href="route('logout')" method="post"
                    as="button">
                    <svg class="w-4 h-4 mr-3" aria-hidden="true" fill="none" stroke-linecap="round"
                      stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                      <path
                        d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1">
                      </path>
                    </svg>
                    <span>Log out</span>
                  </Link>
                </li>
              </ul>
            </div>
          </li>
        </ul>
      </div>
    </header>
    <slot />
  </div>
</template>