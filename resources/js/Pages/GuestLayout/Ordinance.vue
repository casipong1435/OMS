<script setup>
import Container from './Container.vue';

defineProps({
    ordinances: Array
});

</script>

<template>
    <div class="relative" id="services">
        <div aria-hidden="true" class="absolute inset-0 grid grid-cols-2 -space-x-52 opacity-40 dark:opacity-20">
            <div class="blur-[106px] h-56 bg-gradient-to-br from-green-700 to-purple-400 dark:from-blue-700"></div>
            <div class="blur-[106px] h-32 bg-gradient-to-r from-cyan-400 to-sky-300 dark:to-indigo-600"></div>
        </div>
        <Container>
            <div class="relative  ml-auto">
                <header class="text-center my-8">
                    <h1 class="text-4xl font-bold text-gray-800">Tangub City Ordinances</h1>
                    <p class="text-gray-600 mt-2">Explore the latest city ordinances and download them for reference.
                    </p>
                </header>

                <!-- Ordinance Cards Section -->
                <div class="grid gap-6 md:grid-cols-2">
                    <!-- Ordinance 1 -->
                    <div v-for="ordinance in $page.props.ordinances" :key="ordinance.id" class="bg-white shadow-lg rounded-lg p-6" >
                        <h2 class="text-2xl font-semibold text-gray-800">{{ ordinance.ordinance_number }}</h2>
                        <p class="text-gray-600 mt-2">{{ ordinance.description }}</p>
                        <a :href="'/Data/' + ordinance.filename" download
                            class="mt-4 inline-block bg-red-500 text-white px-4 py-2 rounded shadow hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-300">Download
                            PDF</a>
                    </div>
                </div>
            </div>
        </Container>
    </div>
</template>