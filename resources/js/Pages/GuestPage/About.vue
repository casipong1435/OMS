<script setup>
import Layout from '.././GuestLayout/Layout.vue';
import About from '../GuestLayout/About.vue';
import { Head } from '@inertiajs/vue3';

</script>

<template>
    <Head title="About"/>
    <Layout>
        <main class="space-y-40 mb-40">
            <About/>
        </main>
    </Layout>
</template>
