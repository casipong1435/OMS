<script setup>

import { router } from '@inertiajs/vue3';

defineProps({
    data: {
        type: Object,
        required: true,
    },
});

const updatePageNumber = (link) => {
    let pageNumber = link.url.split("=")[1];

    router.visit("/admin/beneficiaries/senior?page=" + pageNumber, {
        preserveScroll: true
    });


}

</script>

<template>
    <nav class="flex flex-row items-center justify-between  md:items-center space-y-3 md:space-y-0 p-4"
        aria-label="Pagination">
        <span class="text-sm font-normal text-gray-500 dark:text-gray-400 mr-2">
            Showing
            <span class="text-gray-900 dark:text-white">{{ data.to }}</span>
            of
            <span class="text-gray-900 dark:text-white">{{ data.total }}</span>
            Results
        </span>
        <div>
            <button v-for="(link, index) in data.links" :key="link.label" @click="updatePageNumber(link)"
                class="relative inline-flex items-center px-4 py-2 border text-sm font-medium"
                :disabled="link.active || !link.url"
                :class="{ 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600': link.active, 'bg-white border-gray-300 text-gray-500 hover:bg-indigo-50': !link.active && link.url }">
                <span v-if="index == 0">
                    <span>&laquo;</span>
                </span>
                <span v-else-if="index == (data.links.length - 1)">
                    <span>&raquo;</span>
                </span>
                <span v-else-if="index > 2">
                    <span>...</span>
                </span>
                <span v-else>
                    <span v-html="link.label"></span>
                </span>
            </button>
        </div>
    </nav>
</template>