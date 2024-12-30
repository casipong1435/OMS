<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
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

const submit = () => {
    form.post(route('login'), {
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
                <img alt="" src="images/loginart.svg" class="absolute inset-0 h-full w-full object-center opacity-80" />

                <div class="hidden lg:relative lg:block lg:p-12">

                    <h2 class="mt-6 text-2xl font-bold text-white sm:text-3xl md:text-4xl">
                        Build your business here!
                    </h2>

                    <p class="mt-4 leading-relaxed text-white/90">
                        Tangub City's Economic Enterprise Development Online Monitoring System
                    </p>
                </div>
            </section>

            <main
                class="flex items-center justify-center px-8 py-8 sm:px-12 lg:col-span-7 lg:px-16 lg:py-12 xl:col-span-6">
                <div class="max-w-xl lg:max-w-3xl">

                    <div class="text-4xl">
                        <h1>Login</h1>
                    </div>

                    <form @submit.prevent="submit" class="mt-8 grid grid-cols-6 gap-6" method="POST">

                        <div class="col-span-6">
                            <label for="username" class="block text-sm font-medium text-gray-700"> Username
                            </label>

                            <input type="text" id="username" name="username" v-model="form.username"
                                class="mt-1 w-full rounded-md border-gray-200 bg-white text-sm text-gray-700 shadow-sm"
                                required />
                                <InputError class="mt-2 " :message="errors.username" />
                            <!--
                            <input type="text" id="username" name="username"
                                class="mt-1 w-full rounded-md border-gray-200 bg-white text-sm text-gray-700 shadow-sm"
                                pattern="^09\d{9}$" maxlength="11" placeholder="ex. (09XXXXXXXXX)" required />
                            -->
                        </div>

                        <div class="col-span-6">
                            <label for="password" class="block text-sm font-medium text-gray-700"> Password </label>

                            <input :type="isShowPassword ? 'text' : 'password' " id="password" name="password" v-model="form.password"
                                class="mt-1 w-full rounded-md border-gray-200 bg-white text-sm text-gray-700 shadow-sm" />
                                <InputError class="mt-2 " :message="errors.password" />
                        </div>
                        <div class="col-span-6">
                            <div class="flex justify-start items-center">
                                <input type="checkbox" id="show_password" @change="handleShowPassword">
                                <label for="show_password" class="ml-2">Show Password</label>
                            </div>
                        </div>

                        <div class="col-span-6 sm:flex sm:items-center sm:gap-4">
                            <button :disabled="form.processing" type="submit"
                                class="inline-block shrink-0 rounded-md border bg-green-700 px-12 py-3 text-sm font-medium text-white transition focus:outline-none focus:ring active:text-gray-300"
                                :class="{'disabled opacity-25 pointer-events-none' : form.processing}">
                                Login <span v-if="form.processing" class="spinner-xl ml-2 spinner-circle [--spinner-color:var(--gray-8)]   "></span>
                            </button>

                            <p class="mt-4 text-sm text-gray-500 sm:mt-0">
                                Don't have an account?
                                <Link :href="route('register')" class="text-gray-700 underline">Register</Link>.
                            </p>
                        </div>
                        <div class="col-span-6 text-center text-purple-600">
                            <Link class="mt-3" :href="route('password.request')">Forgot Password</Link>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </section>


</template>
