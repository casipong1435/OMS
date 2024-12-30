<script setup>
import Layout from '.././GuestLayout/Layout.vue';
import Hero from '../GuestLayout/Hero.vue';
import Blog from '../GuestLayout/Blog.vue';
import Features from '../GuestLayout/Features.vue';

</script>

<template>
    <Layout>
        <main class="space-y-40 mb-40">
            <Hero />
            <Blog />
            <Features />
        </main>
    </Layout>
</template>
