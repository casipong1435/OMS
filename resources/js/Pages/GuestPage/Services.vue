<script setup>
import Layout from '.././GuestLayout/Layout.vue';
import Services from '.././GuestLayout/Services.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>

    <Head title="Services" />
    <Layout>
        <main class="space-y-40 mb-40">
            <Services />
        </main>
    </Layout>
</template>
