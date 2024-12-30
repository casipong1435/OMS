<script setup>
import Aside from './Layout/Aside.vue';
import { ref } from 'vue';
let isSideMenuOpen = ref(false);
</script>

<template>
    <div class="flex h-screen bg-gray-50 dark:bg-gray-900"
    :class="{ 'overflow-hidden': isSideMenuOpen }">
        <Aside :isSideMenuOpen="isSideMenuOpen">
            <main class="h-full overflow-y-auto">
                <slot/>
            </main>
        </Aside>
    </div>
</template>