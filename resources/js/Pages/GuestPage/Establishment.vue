<script setup>
import Layout from '../GuestLayout/Layout.vue';
import Establishment from '../GuestLayout/Establishment.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>

    <Head title="Establishment" />
    <Layout>
        <main class="space-y-40 mb-40">
            <Establishment />
        </main>
    </Layout>
</template>
