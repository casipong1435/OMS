<script setup>
import Layout from '../GuestLayout/Layout.vue';
import Stall from '../GuestLayout/Stall.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    errors: Object
});
</script>

<template>

    <Head title="Stalls" />
    <Layout>
        <main class="space-y-40 mb-40">
            <Stall :errors="errors"/>
        </main>
    </Layout>
</template>
