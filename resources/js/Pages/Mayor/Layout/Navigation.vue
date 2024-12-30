<script setup>

import { Link, usePage } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

const {component} = usePage();

let isReportsMenuOpen = ref(false);

const isReportsLinks = () => {
    if(component == 'Mayor/Dashboard/Reports/Compliance' || component == 'Mayor/Dashboard/Reports/Operations' || component == 'Mayor/Dashboard/Reports/Vendors'){
        isReportsMenuOpen.value = true;
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
                :class="{ 'bg-orange-600': $page.component.startsWith('Mayor/Dashboard/Summary') }"
                aria-hidden="true"></span>
            <Link
                class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 dark:text-gray-100"
                :class="{ 'text-gray-800 disabled:opacity-50 pointer-events-none': $page.component.startsWith('Mayor/Dashboard/Summary') }"
                :href="route('mayor.dashboard')">
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
                :class="{ 'bg-orange-600': $page.component.startsWith('Mayor/Dashboard/Sections') }"
                aria-hidden="true"></span>
            <Link
                class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 dark:text-gray-100"
                :class="{ 'text-gray-800 disabled:opacity-50 pointer-events-none': $page.component.startsWith('Mayor/Dashboard/BusinessOperation') || $page.component.startsWith('Mayor/Dashboard/VendorProfile') || $page.component.startsWith('Mayor/Dashboard/Sections') }"
                :href="route('mayor.business-operation')">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
            </svg>

            <span class="ml-4">Business Operations</span>
            </Link>
        </li>

        <li class="relative px-6 py-3">
            <span class="absolute inset-y-0 left-0 w-1  rounded-tr-lg rounded-br-lg"
                :class="{ 'bg-orange-600': $page.component.startsWith('Mayor/Dashboard/Compliance') }"
                aria-hidden="true"></span>
            <Link
                class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 dark:text-gray-100"
                :class="{ 'text-gray-800 disabled:opacity-50 pointer-events-none': $page.component.startsWith('Mayor/Dashboard/Compliance') }"
                :href="route('mayor.compliance')">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0M12 12.75h.008v.008H12v-.008Z" />
            </svg>

            <span class="ml-4">Document Compliance</span>
            </Link>
        </li>

        <li class="relative px-6 py-3">
            <span class="absolute inset-y-0 left-0 w-1  rounded-tr-lg rounded-br-lg"
                :class="{ 'bg-orange-600': $page.component.startsWith('Mayor/Dashboard/FinancialMonitoring') }"
                aria-hidden="true"></span>
            <Link
                class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 dark:text-gray-100"
                :class="{ 'text-gray-800 disabled:opacity-50 pointer-events-none': $page.component.startsWith('Mayor/Dashboard/FinancialMonitoring') }"
                :href="route('mayor.financial-monitoring')">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
            </svg>

            <span class="ml-4">Financial Monitoring</span>
            </Link>
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
                        <Link class="w-full" :class="{'text-gray-800 disabled:opacity-50 pointer-events-none' : component === 'Mayor/Dashboard/Reports/Operations'}" :href="route('mayor.operations-report')">Operations Report</Link>
                    </li>
                    <li class="px-2 py-1 transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200">
                        <Link class="w-full" :class="{'text-gray-800 disabled:opacity-50 pointer-events-none' : component === 'Mayor/Dashboard/Reports/Vendors'}" :href="route('mayor.vendors-report')">
                            Financial Report
                        </Link>
                    </li>
                    <li class="px-2 py-1 transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200">
                        <Link class="w-full" :class="{'text-gray-800 disabled:opacity-50 pointer-events-none' : component === 'Mayor/Dashboard/Reports/Compliance'}" :href="route('mayor.compliance-report')">
                            Compliance Report
                        </Link>
                    </li>
                </ul>
            </div>
        </li>
    </ul>

</template>