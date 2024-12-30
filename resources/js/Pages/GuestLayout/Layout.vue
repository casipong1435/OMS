<script setup>
import Header from './Header.vue';
</script>

<template>
    <div class="bg-white dark:bg-gray-900">
        <Header />
        <slot />
    </div>
</template>