<script setup>
import { Link, usePage, router } from '@inertiajs/vue3';
import { onMounted, computed, ref } from 'vue';
import dayjs from 'dayjs';
import relativeTime from 'dayjs/plugin/relativeTime';
import Navigation from './Navigation.vue';

let isSideMenuOpen = ref(false);


defineProps({
  isSideMenuOpen: Boolean
})

const { props } = usePage();

const auth_user = props.auth.user;
const notifications = props.auth.notifications;
const unreadNotifications = props.auth.unreadNotifications;

const viewAll = ref(false);

const itemsToDisplay = computed(() => {
  const allNotificationsArray = notifications ? Object.values(notifications) : [];
  return viewAll.value ? allNotificationsArray : allNotificationsArray.slice(0, 5);
});

dayjs.extend(relativeTime);
// Function to calculate "ago"
function timeAgo(createdAt) {
  return dayjs(createdAt).fromNow();
}

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

const markAsRead = (id) => {
  router.post(route('ceedo.markasReadNotification', id));
};

const markAsReadAll = () => {
  router.post(route('ceedo.markasReadAllNotification'));
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
      <Link :href="route('ceedo.dashboard')" aria-label="logo" class="ml-6 flex items-center space-x-2">
      <div aria-hidden="true" class="flex space-x-1">
        <div class="h-6 w-2 bg-orange-700"></div>
      </div>
      <span class="text-2xl font-bold text-gray-900 dark:text-white">OMS</span>
      </Link>
      <Navigation />
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
        <Link :href="route('ceedo.dashboard')" aria-label="logo" class="ml-6 flex items-center space-x-2">
        <div aria-hidden="true" class="flex space-x-1">
          <div class="h-6 w-2 bg-orange-700"></div>
        </div>
        <span class="text-2xl font-bold text-gray-900 dark:text-white">EEMMS</span>
        </Link>
        <Navigation />
      </div>
    </Transition>
  </aside>

  <div class="flex flex-col flex-1 w-full">
    <header class="z-10 py-4 bg-white shadow-md dark:bg-gray-800">
      <div class="flex items-center justify-between h-full px-6 mx-auto text-purple-600 dark:text-purple-300">
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

          <li class="relative">
            <button class="relative align-middle rounded-md focus:outline-none focus:shadow-outline-purple"
              @click="toggleNotificationsMenu" aria-label="Notifications" aria-haspopup="true">
              <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                <path
                  d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z">
                </path>
              </svg>
              <!-- Notification badge -->
              <span v-if="unreadNotifications.length > 0"
                class="dot dot-error h-3.5 w-3.5 absolute top-0 right-0 inline-block w-3 h-3 transform translate-x-1 -translate-y-1 text-white flex justify-center items-center"
                style="font-size: 10px;">{{ unreadNotifications.length }}</span>
            </button>
            <div v-if="isNotificationsMenuOpen">
              <ul x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" @click.self="closeNotificationsMenu"
                class="absolute right-0 overflow-y-auto  p-2 mt-2 space-y-2 text-gray-600 bg-white border border-gray-100 rounded-md shadow-md dark:text-gray-300 dark:border-gray-700 dark:bg-gray-700"
                style="width: 350px; height: 450px;">
                <div
                  class="block py-2 px-4 text-base font-medium text-start text-gray-700 bg-gray-50 dark:bg-gray-600 dark:text-gray-300 flex justify-between">
                  <span>Notifications</span>
                  <button type="button" class="text-blue-400" @click="markAsReadAll">Read All</button>
                </div>
                <div>

                  <li v-for="(notif, index) in itemsToDisplay" :key="index"
                    class="flex py-3 px-4 border-b bg-gray-100 dark:hover:bg-gray-600 dark:border-gray-600 text-start relative"
                    :class="{ 'bg-gray-50': notif.read_at }">
                    <div class="flex-shrink-0">
                      <div class="relative w-11 h-11 overflow-hidden bg-gray-100 rounded-full dark:bg-gray-600">
                      
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                          stroke="currentColor" class="absolute w-12 h-12 text-gray-400 -left-1">
                          <path stroke-linecap="round" stroke-linejoin="round"
                            d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0M3.124 7.5A8.969 8.969 0 0 1 5.292 3m13.416 0a8.969 8.969 0 0 1 2.168 4.5" />
                        </svg>

                      </div>

                    </div>
                    <div class="pl-3 w-full">
                      <div class="text-gray-500 font-normal text-sm mb-1.5 dark:text-gray-400">
                        <span class="font-semibold text-gray-900 dark:text-white">{{ notif.data.from
                          }}</span> {{ notif.data.message }}
                      </div>
                      <div class="text-xs font-medium text-primary-600 dark:text-primary-500">
                        {{ timeAgo(notif.created_at) }}
                      </div>
                    </div>
                    <button v-if="!notif.read_at" class="absolute bottom-1 right-1 text-sm" @click="markAsRead(notif.id)">Mark as Read</button>
                  </li>

                  <a v-if="itemsToDisplay.length <= 0 || itemsToDisplay.length == null"
                    class="flex py-3 px-4 border-b ">
                    <div class="text-center py-3 px-10">
                      No notifications available
                    </div>
                  </a>
                </div>
                <button v-if="itemsToDisplay.length >= 5" @click="viewAll = !viewAll" type="button"
                  class="block py-2 text-md font-medium text-center text-gray-900 bg-gray-50 hover:bg-gray-100 dark:bg-gray-600 dark:text-white dark:hover:underline">
                  <div class="inline-flex items-center">
                    <svg aria-hidden="true" class="mr-2 w-4 h-4 text-gray-500 dark:text-gray-400" fill="currentColor"
                      viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                      <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                      <path fill-rule="evenodd"
                        d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                        clip-rule="evenodd"></path>
                    </svg>
                    {{ viewAll ? 'Show Less' : 'Show All' }}
                  </div>
                </button>
              </ul>
            </div>
          </li>
          <!-- Profile menu -->
          <li class="relative">
            <button class="align-middle rounded-full focus:shadow-outline-purple focus:outline-none"
              @click="toggleProfileMenu" aria-label="Account" aria-haspopup="true">
              <img class="object-cover w-8 h-8 rounded-full" src="/images/avatars/avatar.png" alt=""
                aria-hidden="true" />
            </button>
            <div v-if="isProfileMenuOpen">
              <ul x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" @click.self="closeProfileMenu"
                class="absolute right-0 w-56 p-2 mt-2 space-y-2 text-gray-600 bg-white border border-gray-100 rounded-md shadow-md dark:border-gray-700 dark:text-gray-300 dark:bg-gray-700"
                aria-label="submenu">

                <li class="flex">
                  <Link
                    class="inline-flex items-center w-full px-2 py-1 text-sm font-semibold transition-colors duration-150 rounded-md hover:bg-gray-100 hover:text-gray-800 dark:hover:bg-gray-800 dark:hover:text-gray-200"
                    :href="route('logout')" method="post" as="button">
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