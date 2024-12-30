<script setup>

import { Link, usePage } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';


</script>

<template>
    <ul class="mt-6">
        <li class="relative px-6 py-3">
            <span class="absolute inset-y-0 left-0 w-1  rounded-tr-lg rounded-br-lg"
                :class="{ 'bg-orange-600': $page.component.startsWith('Treasurer/Dashboard/Summary') }"
                aria-hidden="true"></span>
            <Link
                class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 dark:text-gray-100"
                :class="{ 'text-gray-800 disabled:opacity-50 pointer-events-none': $page.component.startsWith('Treasurer/Dashboard/Summary') }"
                :href="route('treasurer.dashboard')">
            <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round"
                stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                <path
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                </path>
            </svg>
            <span class="ml-4">Dashboard</span>
            </Link>
        </li>

        <li class="relative px-6 py-3">
            <span class="absolute inset-y-0 left-0 w-1  rounded-tr-lg rounded-br-lg"
                :class="{ 'bg-orange-600': $page.component.startsWith('Treasurer/Dashboard/PaymentDue') || $page.component.startsWith('Treasurer/Dashboard/PaymentPortal') }"
                aria-hidden="true"></span>
            <Link
                class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 dark:text-gray-100"
                :class="{ 'text-gray-800 disabled:opacity-50 pointer-events-none': $page.component.startsWith('Treasurer/Dashboard/PaymentDue') || $page.component.startsWith('Treasurer/Dashboard/PaymentPortal') }"
                :href="route('treasurer.payment-due')">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
            </svg>

            <span class="ml-4">Due Payments</span>
            </Link>
        </li>

        <li class="relative px-6 py-3">
            <span class="absolute inset-y-0 left-0 w-1  rounded-tr-lg rounded-br-lg"
                :class="{ 'bg-orange-600': $page.component.startsWith('Treasurer/Dashboard/Transaction') }"
                aria-hidden="true"></span>
            <Link
                class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 dark:text-gray-100"
                :class="{ 'text-gray-800 disabled:opacity-50 pointer-events-none': $page.component.startsWith('Treasurer/Dashboard/Transaction') }"
                :href="route('treasurer.transaction')">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="m9 14.25 6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0c1.1.128 1.907 1.077 1.907 2.185ZM9.75 9h.008v.008H9.75V9Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm4.125 4.5h.008v.008h-.008V13.5Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
            </svg>

            <span class="ml-4">Transactions</span>
            </Link>
        </li>

        <li class="relative px-6 py-3">
            <span class="absolute inset-y-0 left-0 w-1  rounded-tr-lg rounded-br-lg"
                :class="{ 'bg-orange-600': $page.component.startsWith('Treasurer/Dashboard/Report') }"
                aria-hidden="true"></span>
            <Link
                class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 dark:text-gray-100"
                :class="{ 'text-gray-800 disabled:opacity-50 pointer-events-none': $page.component.startsWith('Treasurer/Dashboard/Report') }"
                :href="route('treasurer.report')">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
            </svg>

            <span class="ml-4">Report</span>
            </Link>
        </li>
    </ul>
</template>