<script setup>
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    status: {
        type: String,
    },
    errors: Object
});

const form = useForm({
    username: '',
    password: '',
    remember: false,
});

let isShowPassword = ref(false);

const submitForm = () => {
    form.post(route('official.auth.post'), {
        onFinish: () => form.reset('password'),
    });
};

const handleShowPassword = () => {
    isShowPassword.value = !isShowPassword.value;
};
</script>
<template>

    <Head title="Login" />

    <section class="bg-white">
        <div class="lg:grid lg:min-h-screen lg:grid-cols-12">
            <section class="relative flex h-32 items-end bg-green-700 lg:col-span-5 lg:h-full xl:col-span-6">
                <img alt="" src="/images/authadmin.svg" class="absolute inset-0 h-full w-full object-center opacity-80" />

                <div class="hidden lg:relative lg:block lg:p-12">

                    <h2 class="mt-6 text-2xl font-bold text-white sm:text-3xl md:text-4xl">
                        Official Authentication
                    </h2>

                    <p class="mt-4 leading-relaxed text-white/90">
                        Tangub City's Economic Enterprise Development Online Monitoring System
                    </p>
                </div>
            </section>

            <main
                class="flex items-center justify-center px-8 py-8 sm:px-12 lg:col-span-7 lg:px-16 lg:py-12 xl:col-span-6">
                <div class="max-w-full lg:max-w-3xl">

                    <div class="text-4xl">
                        <h1>Login</h1>
                    </div>

                    <form @submit.prevent="submitForm" class="mt-8 grid grid-cols-12 gap-6">

                        <div class="col-span-12">
                            <label for="username" class="block text-sm font-medium text-gray-700"> Username
                            </label>

                            <input type="text" id="username" v-model="form.username"
                                class="mt-1 w-full rounded-md border-gray-200 bg-white text-sm text-gray-700 shadow-sm"
                                 required />
                            <InputError class="mt-2 text-white" :message="errors.username" />

                        </div>
                        
                        <div class="col-span-12">
                            <label for="password" class="block text-sm font-medium text-gray-700"> Password </label>

                            <input :type="isShowPassword ? 'text' : 'password' " id="password" v-model="form.password"
                                class="mt-1 w-full rounded-md border-gray-200 bg-white text-sm text-gray-700 shadow-sm" required />
                                <InputError class="mt-2 text-white" :message="errors.password" />
                        </div>
                        <div class="col-span-12">
                            <div class="flex justify-start items-center">
                                <input type="checkbox" id="show_password" @change="handleShowPassword">
                                <label for="show_password" class="ml-2">Show Password</label>
                            </div>
                            
                        </div>
                        
                        <div class="col-span-12 sm:flex sm:items-center sm:gap-4">
                            <button :disabled="form.processing"
                                class="inline-block shrink-0 w-full rounded-md border bg-green-700 px-12 py-3 text-sm font-medium text-white transition focus:outline-none focus:ring active:text-gray-300"
                                :class="{'disabled opacity-25 pointer-events-none' : form.processing}">
                                Login <span v-if="form.processing" class="spinner-xl ml-2 spinner-circle [--spinner-color:var(--gray-8)]   "></span>
                            </button>

                        </div>
                    </form> 
                </div>
            </main>
        </div>
    </section>


</template>
