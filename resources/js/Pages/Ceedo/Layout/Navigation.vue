<script setup>

import { Link,usePage } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

const {component} = usePage();

let isVendorsMenuOpen = ref(false);
let isAreasMenuOpen = ref(false);
let isReportsMenuOpen = ref(false);
let isHistoryMenu = ref(false);



 const isVendorsLinks = () => {

    if(component === 'Ceedo/Dashboard/Vendors/AllVendors' || component === 'Ceedo/Dashboard/Vendors/Application' || component === 'Ceedo/Dashboard/Vendors/Compliance' || component === 'Ceedo/Dashboard/Vendors/VendorProfile' || component === 'Ceedo/Dashboard/Vendors/Renewal' || component === 'Ceedo/Dashboard/Vendors/PaymentDue' || component === 'Ceedo/Dashboard/Vendors/ClosedBusiness' || component === 'Ceedo/Dashboard/Vendors/ApplicationInfo')
    {
        isVendorsMenuOpen.value = true;
        return true;
    }else{
        return false;
    }
};

const isAreasLinks = () => {
    if(component == 'Ceedo/Dashboard/Areas/Establishment' || component == 'Ceedo/Dashboard/Areas/Stall'){
        isAreasMenuOpen.value = true;
        return true;
    }else{
        return false;
    }
};

const isReportsLinks = () => {
    if(component == 'Ceedo/Dashboard/Reports/Compliance' || component == 'Ceedo/Dashboard/Reports/Operations' || component == 'Ceedo/Dashboard/Reports/Vendors'){
        isReportsMenuOpen.value = true;
        return true;
    }else{
        return false;
    }
};

const isHistoryLinks = () => {
    if(component == 'Ceedo/Dashboard/History/Activities'){
        isHistoryMenu.value = true;
        return true;
    }else{
        return false;
    }
};

</script>

<template>
    <ul class="mt-6">
        <li class="relative px-6 py-3">
            <span class="absolute inset-y-0 left-0 w-1  rounded-tr-lg rounded-br-lg"
                :class="{ 'bg-orange-600': $page.component.startsWith('Ceedo/Dashboard/Summary') }"
                aria-hidden="true"></span>
            <Link
                class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 dark:text-gray-100"
                :class="{ 'text-gray-800 disabled:opacity-50 pointer-events-none': $page.component.startsWith('Ceedo/Dashboard/Summary') }"
                :href="route('ceedo.dashboard')">
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

    <!-- Remaining menu items... -->
    <ul>
        <li class="relative px-6 py-3">
            <button
                class="inline-flex items-center justify-between w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
                :class="{ 'text-gray-800': isVendorsLinks() }"
                @click="isVendorsMenuOpen = !isVendorsMenuOpen" aria-haspopup="true">
                <span class="inline-flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                    </svg>
                    <span class="ml-4">Vendors</span>
                </span>
                <svg class="w-4 h-4" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                        clip-rule="evenodd"></path>
                </svg>
            </button>
            <div v-if="isVendorsMenuOpen">
                <ul class="p-2 mt-2 space-y-2 overflow-hidden text-sm font-medium text-gray-500 rounded-md shadow-inner bg-gray-50 dark:text-gray-400 dark:bg-gray-900"
                    aria-label="submenu">
                    <li class="px-2 py-1 transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200">
                        <Link class="w-full" :class="{'text-gray-800 disabled:opacity-50 pointer-events-none' : component === 'Ceedo/Dashboard/Vendors/AllVendors' || component === 'Ceedo/Dashboard/Vendors/VendorProfile'}" :href="route('ceedo.vendors')">All Vendors</Link>
                    </li>
                    <li class="px-2 py-1 transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200">
                        <Link class="w-full" :class="{'text-gray-800 disabled:opacity-50 pointer-events-none' : component === 'Ceedo/Dashboard/Vendors/Application' || component === 'Ceedo/Dashboard/Vendors/ApplicationInfo'}" :href="route('ceedo.applications')">
                            Applications
                        </Link>
                    </li>
                    <li class="px-2 py-1 transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200">
                        <Link class="w-full" :class="{'text-gray-800 disabled:opacity-50 pointer-events-none' : component === 'Ceedo/Dashboard/Vendors/Compliance' || component === 'Ceedo/Dashboard/Vendors/Renewal' || component === 'Ceedo/Dashboard/Vendors/PaymentDue'}" :href="route('ceedo.compliance')">
                            Compliance
                        </Link>
                    </li>
                    <li class="px-2 py-1 transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200">
                        <Link class="w-full" :class="{'text-gray-800 disabled:opacity-50 pointer-events-none' : component === 'Ceedo/Dashboard/Vendors/Compliance' || component === 'Ceedo/Dashboard/Vendors/ClosedBusiness' }" :href="route('ceedo.closed_business')">
                            Closed Business
                        </Link>
                    </li>
                </ul>
            </div>
        </li>
        <li class="relative px-6 py-3">
            <button
                class="inline-flex items-center justify-between w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
                :class="{ 'text-gray-800': isAreasLinks() }"
                @click="isAreasMenuOpen = !isAreasMenuOpen" aria-haspopup="true">
                <span class="inline-flex items-center">
                    <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                        <path
                            d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z">
                        </path>
                    </svg>
                    <span class="ml-4">Areas</span>
                </span>
                <svg class="w-4 h-4" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                        clip-rule="evenodd"></path>
                </svg>
            </button>
            <div v-if="isAreasMenuOpen">
                <ul class="p-2 mt-2 space-y-2 overflow-hidden text-sm font-medium text-gray-500 rounded-md shadow-inner bg-gray-50 dark:text-gray-400 dark:bg-gray-900"
                    aria-label="submenu">
                    <li class="px-2 py-1 transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200">
                        <Link class="w-full" :class="{'text-gray-800 disabled:opacity-50 pointer-events-none' : component === 'Ceedo/Dashboard/Areas/Establishment' || component == 'Ceedo/Dashboard/Areas/Stall'}" :href="route('ceedo.establishments')">Manage Establishment</Link>
                    </li>
                </ul>
            </div>
        </li>
        <li class="relative px-6 py-3">
            <button
                class="inline-flex items-center justify-between w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
                :class="{ 'text-gray-800': isReportsLinks() }"
                @click="isReportsMenuOpen = !isReportsMenuOpen" aria-haspopup="true">
                <span class="inline-flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                    </svg>

                    <span class="ml-4">Reports</span>
                </span>
                <svg class="w-4 h-4" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                        clip-rule="evenodd"></path>
                </svg>
            </button>
            <div v-if="isReportsMenuOpen">
                <ul class="p-2 mt-2 space-y-2 overflow-hidden text-sm font-medium text-gray-500 rounded-md shadow-inner bg-gray-50 dark:text-gray-400 dark:bg-gray-900"
                    aria-label="submenu">
                    <li class="px-2 py-1 transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200">
                        <Link class="w-full" :class="{'text-gray-800 disabled:opacity-50 pointer-events-none' : component === 'Ceedo/Dashboard/Reports/Operations'}" :href="route('ceedo.operations-report')">Operations Report</Link>
                    </li>
                    <li class="px-2 py-1 transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200">
                        <Link class="w-full" :class="{'text-gray-800 disabled:opacity-50 pointer-events-none' : component === 'Ceedo/Dashboard/Reports/Vendors'}" :href="route('ceedo.vendors-report')">
                            Financial Report
                        </Link>
                    </li>
                    <li class="px-2 py-1 transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200">
                        <Link class="w-full" :class="{'text-gray-800 disabled:opacity-50 pointer-events-none' : component === 'Ceedo/Dashboard/Reports/Compliance'}" :href="route('ceedo.compliance-report')">
                            Compliance Report
                        </Link>
                    </li>
                </ul>
            </div>
        </li>
        <li class="relative px-6 py-3">
            <button
                class="inline-flex items-center justify-between w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
                :class="{'text-gray-800':isHistoryLinks()}"
                @click="isHistoryMenu = !isHistoryMenu" aria-haspopup="true">
                <span class="inline-flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>


                    <span class="ml-4">History</span>
                </span>
                <svg class="w-4 h-4" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                        clip-rule="evenodd"></path>
                </svg>
            </button>
            <div v-if="isHistoryMenu">
                <ul class="p-2 mt-2 space-y-2 overflow-hidden text-sm font-medium text-gray-500 rounded-md shadow-inner bg-gray-50 dark:text-gray-400 dark:bg-gray-900"
                    aria-label="submenu">
                    <li class="px-2 py-1 transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200">
                        <Link class="w-full" :class="{'text-gray-800 disabled:opacity-50 pointer-events-none' : component === 'Ceedo/Dashboard/History/Activities'}" :href="route('ceedo.activities')">Activities</Link>
                    </li>
                </ul>
            </div>
        </li>
    </ul>
</template>