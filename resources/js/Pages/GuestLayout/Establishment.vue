<script setup>
import Container from './Container.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    establishments: Array
});

function openDrawer(data) {

}

</script>

<template>
    <div class="relative" id="services">
        <div aria-hidden="true" class="absolute inset-0 grid grid-cols-2 -space-x-52 opacity-40 dark:opacity-20">
            <div class="blur-[106px] h-56 bg-gradient-to-br from-green-700 to-purple-400 dark:from-blue-700"></div>
            <div class="blur-[106px] h-32 bg-gradient-to-r from-cyan-400 to-sky-300 dark:to-indigo-600"></div>
        </div>
        <Container>
            <div class="relative ml-auto">
                <div class="text-2xl mb-10 text-center">{{ $page.props.name }}</div>
                <div class="mb-2">
                    <div class="breadcrumbs text-sm">
                        <ul>
                            <li>
                                <Link :href="route('services')" class="text-gray-800"
                                    :class="{ 'text-gray-400 disabled:opacity-50 pointer-events-none': $page.component.startsWith('GuestPage/Services') }">
                                Area</Link>
                            </li>
                            <li>{{ $page.props.name }}</li>
                        </ul>
                    </div>
                </div>
                <!-- CTA -->
                <hr>
                <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3 mt-5">
                    <div class="max-w-sm bg-gray-200 col-span-2 md:col-span-1 sm:col-span-1 rounded overflow-hidden hover:shadow-lg hover:shadow-blue-200 relative"
                        v-for="establishment in $page.props.establishments" :key="establishment.id">
                        <div class="px-6 py-4">
                            <div class="font-bold text-xl mb-2">{{ establishment.name }}</div>
                            <div class="text-justify mb-2">{{ establishment.description }}</div>
                        </div>

                        <div class="px-6 pt-4 pb-2 flex justify-between gap-x-5 items-center mb-2 ">
                            <Link class="btn btn-primary" :href="route('guest.establishment_unit', establishment.id)">
                            Details</Link>
                            <div class="flex justify-between items-center gap-4">
                                <div class="flex justify-center items-center">
                                    <span class="inline text-sm font-semibold text-gray-700 mr-2"><svg
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                                        </svg>
                                    </span>
                                    <span class="text-sm text-gray-700">{{ establishment.establishment_units.length
                                        }}</span>
                                </div>
                                <div class="flex justify-between items-center gap-2">
                                    <div class="flex flex-row items-center">
                                        <span class="inline text-sm font-semibold text-gray-700 mr-2"><svg
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                            </svg>
                                        </span>
                                        <span class="text-sm text-gray-700">{{ establishment.rate }} /day</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-if="$page.props.establishments.length <= 0" class="col-span-12 text-center text-xl">
                        No Section Added Yet!
                    </div>
                </div>
            </div>
        </Container>
    </div>
</template>