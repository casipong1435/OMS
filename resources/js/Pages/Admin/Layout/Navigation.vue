<script setup>

import { Link } from '@inertiajs/vue3';

</script>

<template>
  <ul class="mt-6">
    <li class="relative px-6 py-3">
      <span class="absolute inset-y-0 left-0 w-1  rounded-tr-lg rounded-br-lg"
        :class="{ 'bg-orange-600': $page.component.startsWith('Admin/Dashboard/AccountManagement/Summary') }"
        aria-hidden="true"></span>
      <Link
        class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 dark:text-gray-100"
        :class="{ 'text-gray-800 disabled:opacity-50 pointer-events-none': $page.component.startsWith('Admin/Dashboard/AccountManagement/Summary') }"
        :href="route('admin.dashboard')">
      <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round"
        stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
        <path
          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
        </path>
      </svg>
      <span class="ml-4">Dashboard</span>
      </Link>
    </li>
  </ul>
  <div>
    <div class="text-sm text-gray-400 py-2 px-4 m-1 text-center border-b-2 border-t-2">Account Management</div>
  </div>
  <!-- Remaining menu items... -->
  <ul class="pl-2">
    <li class="relative px-6 py-3">
      <span class="absolute inset-y-0 left-0 w-1  rounded-tr-lg rounded-br-lg"
        :class="{ 'bg-orange-600': $page.component.startsWith('Admin/Dashboard/AccountManagement/Officials') }"
        aria-hidden="true"></span>
      <Link
        class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
        :class="{ 'text-gray-800 disabled:opacity-50 pointer-events-none': $page.component.startsWith('Admin/Dashboard/AccountManagement/Officials') }"
        :href="route('admin.officials')">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
        class="size-6">
        <path stroke-linecap="round" stroke-linejoin="round"
          d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
      </svg>

      <span class="ml-4">Officials</span>
      </Link>
    </li>
    <li class="relative px-6 py-3">
      <span class="absolute inset-y-0 left-0 w-1  rounded-tr-lg rounded-br-lg"
        :class="{ 'bg-orange-600': $page.component.startsWith('Admin/Dashboard/AccountManagement/Users') }"
        aria-hidden="true"></span>
      <Link
        class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
        :class="{ 'text-gray-800 disabled:opacity-50 pointer-events-none': $page.component.startsWith('Admin/Dashboard/AccountManagement/Users') }"
        :href="route('admin.users')">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
        class="size-6">
        <path stroke-linecap="round" stroke-linejoin="round"
          d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
      </svg>


      <span class="ml-4">Users</span>
      </Link>
    </li>
  </ul>
</template>