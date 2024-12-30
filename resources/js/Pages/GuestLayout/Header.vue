<script setup>
import Container from './Container.vue';
import { ref, onMounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const { component } = usePage();

const isToggled = ref(false);
const isProfileMenuOpen = ref(false);

function toggleNavlinks() {
  isToggled.value = !isToggled.value;
}

function toggleProfileMenu() {
  isProfileMenuOpen.value = !isProfileMenuOpen.value;
}

const closeNavlinks = () => {
  isToggled.value = false;
};

onMounted(() => {
  const links = document.querySelectorAll("#navlinks ul li a");
  links.forEach((link) => {
    link.addEventListener("click", closeNavlinks);
  });
});

function getRoute(){
  switch (usePage().props.auth.user.role){
    case 'user':
      return route('user.dashboard');
    case 'admin':
      return route('admin.dashboard');
    case 'ceedo':
      return route('ceedo.dashboard');
    case 'treasurer':
      return route('treasurer.dashboard');
    case 'mayor':
      return route('mayor.dashboard');
  }
}

</script>

<template>
  <header>
    <nav class="z-10 w-full border-b border-black/5 dark:border-white/5 lg:border-transparent">
      <Container>
        <div class="relative flex flex-wrap items-center justify-between gap-6 py-3 md:gap-0 md:py-4">
          <div class="relative z-20 flex w-full justify-between md:px-0 lg:w-max">
            <Link :href="route('welcome')" aria-label="logo" class="flex items-center space-x-2">
            <div aria-hidden="true" class="flex space-x-1">
              <div class="h-6 w-2 bg-orange-700"></div>
            </div>
            <span class="text-2xl font-bold text-gray-900 dark:text-white">OMS</span>
            </Link>

            <div class="relative flex max-h-10 items-center lg:hidden">
              <button aria-label="humburger" id="hamburger" @click="toggleNavlinks" :class="{ toggled: isToggled }"
                class="relative -mr-6 p-6">
                <div aria-hidden="true"
                  class="m-auto h-0.5 w-5 rounded bg-sky-900 transition duration-300 dark:bg-gray-300"
                  :class="{ 'rotate-45 translate-y-1.5': isToggled }"></div>
                <div aria-hidden="true"
                  class="m-auto mt-2 h-0.5 w-5 rounded bg-sky-900 transition duration-300 dark:bg-gray-300"
                  :class="{ '-rotate-45 -translate-y-1.5': isToggled }"></div>
              </button>
            </div>
          </div>

          <div id="navLayer" aria-hidden="true"
            class="fixed inset-0 z-10 h-screen w-screen origin-bottom scale-y-0 bg-white/70 backdrop-blur-2xl transition duration-500 dark:bg-gray-900/70 lg:hidden"
            :class="{ 'scale-y-100': isToggled }"></div>

          <div id="navlinks"
            class="invisible absolute top-full left-0 z-20 w-full origin-top-right translate-y-1 scale-90 flex-col flex-wrap justify-end gap-6 rounded-3xl border border-gray-100 bg-white p-8 opacity-0 shadow-2xl shadow-gray-600/10 transition-all duration-300 dark:border-gray-700 dark:bg-gray-800 dark:shadow-none lg:visible lg:relative lg:flex lg:w-7/12 lg:translate-y-0 lg:scale-100 lg:flex-row lg:items-center lg:gap-0 lg:border-none lg:bg-transparent lg:p-0 lg:opacity-100 lg:shadow-none"
            :class="{ '!visible !scale-100 !opacity-100 !lg:translate-y-0': isToggled }">
            <div class="w-full text-gray-600 dark:text-gray-200 lg:w-auto lg:pr-4 lg:pt-0">
              <ul class="flex flex-col gap-6 tracking-wide lg:flex-row lg:gap-0 lg:text-sm">
                <li>
                  <Link :href="route('welcome')"
                    class="hover:text-primary block transition dark:hover:text-white md:px-4"
                    :class="{ 'text-primary disabled  pointer-events-none': component.startsWith('GuestPage/Welcome') }">
                  <span>Home</span>
                  </Link>
                </li>
                <li>
                  <Link :href="route('about')" class="hover:text-primary block transition dark:hover:text-white md:px-4"
                    :class="{ 'text-primary disabled  pointer-events-none': component.startsWith('GuestPage/About') }">
                  <span>About</span>
                  </Link>
                </li>
                <li>
                  <Link :href="route('services')"
                    class="hover:text-primary block transition dark:hover:text-white md:px-4"
                    :class="{ 'text-primary disabled  pointer-events-none': component.startsWith('GuestPage/Services') || component.startsWith('GuestPage/Establishment') || component.startsWith('GuestPage/Stall') }">
                  <span>Services</span>
                  </Link>
                </li>
                <li>
                  <Link :href="route('ordinance')"
                    class="hover:text-primary block transition dark:hover:text-white md:px-4"
                    :class="{ 'text-primary disabled  pointer-events-none': component.startsWith('GuestPage/Ordinance')}">
                  <span>Ordinances</span>
                  </Link>
                </li>
                <li v-if="!$page.props.auth.user">
                  <Link :href="route('login')"
                    class="flex gap-2 font-semibold text-gray-700 transition hover:text-primary dark:text-white dark:hover:text-white md:px-4">
                  <span>Login</span>
                  </Link>
                </li>
              </ul>
            </div>

            <div class="mt-12 lg:mt-0">
              <Link v-if="!$page.props.auth.user" :href="route('register')"
                class="relative flex h-9 w-full items-center justify-center px-4 before:absolute before:inset-0 before:rounded-full before:bg-primary before:transition before:duration-300 hover:before:scale-105 active:duration-75 active:before:scale-95 sm:w-max">
              <span class="relative text-sm font-semibold text-white">Register</span>
              </Link>
              <div v-else>
                <div class="relative">
                  <button class="align-middle rounded-full focus:shadow-outline-purple focus:outline-none"
                    @click="toggleProfileMenu" aria-label="Account" aria-haspopup="true">
                    <img v-if="usePage().props.auth.image" class="object-cover w-8 h-8 rounded-full" :src="'/images/clients/' + usePage().props.auth.image" alt=""
                      aria-hidden="true" />
                    <img v-else class="object-cover w-8 h-8 rounded-full" src="/images/avatars/avatar.png" alt=""
                      aria-hidden="true" />
                  </button>
                  <div v-if="isProfileMenuOpen">
                    <ul x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
                      x-transition:leave-end="opacity-0" @click.self="closeProfileMenu"
                      class="absolute right-0 w-56 p-2 mt-2 space-y-2 text-gray-600 bg-white border border-gray-100 rounded-md shadow-md dark:border-gray-700 dark:text-gray-300 dark:bg-gray-700"
                      aria-label="submenu">
                      <li class="flex">
                        <Link class="inline-flex items-center w-full px-2 py-1 text-sm font-semibold transition-colors duration-150 rounded-md hover:bg-gray-100 hover:text-gray-800 dark:hover:bg-gray-800 dark:hover:text-gray-200"
                          :href="getRoute()">
                          <svg class="w-4 h-4 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0 1 18 16.5h-2.25m-7.5 0h7.5m-7.5 0-1 3m8.5-3 1 3m0 0 .5 1.5m-.5-1.5h-9.5m0 0-.5 1.5M9 11.25v1.5M12 9v3.75m3-6v6" />
                          </svg>

                          <span>Dashboard</span>
                        </Link>
                      </li>
                      <li v-if="usePage().props.auth.user.role == 'user'" class="flex">
                        <a class="inline-flex items-center w-full px-2 py-1 text-sm font-semibold transition-colors duration-150 rounded-md hover:bg-gray-100 hover:text-gray-800 dark:hover:bg-gray-800 dark:hover:text-gray-200"
                          :href="route('user.profile')">
                          <svg class="w-4 h-4 mr-3" aria-hidden="true" fill="none" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                            <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                          </svg>
                          <span>Profile</span>
                        </a>
                      </li>
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
                </div>
              </div>
            </div>
          </div>
        </div>
      </Container>
    </nav>
  </header>
</template>
