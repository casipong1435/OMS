<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm, router, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';


defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    errors: Object,
});

const form = useForm({
    first_name: '',
    middle_name: '',
    last_name: '',
    date_of_birth: '',
    sex: '',
    mobile_number: '',
    username: '',
    password: '',
    password_confirmation: '',
    remember: false,
});

let isShowPassword = ref(false);

const submitRegistration = () => {
    form.post(route('register'), {
        onSuccess: () => form.reset(),
    });
};

const handleShowPassword = () => {
    isShowPassword.value = !isShowPassword.value;
};

const removeAlert = () => {
    usePage().props.flash.success = null;
};

const validateNumber = (event) => {
    // Remove any non-numeric characters from the input
    let value = event.target.value.replace(/\D/g, '');

    if (value.charAt(0) !== '9') {
        value = ''; // Clear the input if the first digit is not 9
    }
    form.mobile_number = value.slice(0, 10);
};

</script>
<template>

    <Head title="Register" />

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

                    <div class="text-4xl mb-2">
                        <h1>Register</h1>
                    </div>

                    <InputError class="mt-2 text-white" :message="errors.first_name" />
                    <InputError class="mt-2 text-white" :message="errors.last_name" />
                    <InputError class="mt-2 text-white" :message="errors.sex" />
                    <InputError class="mt-2 text-white" :message="errors.date_of_birth" />
                    <InputError class="mt-2 text-white" :message="errors.mobile_number" />
                    <InputError class="mt-2 text-white" :message="errors.username" />
                    <InputError class="mt-2 text-white" :message="errors.password" />

                    <div class="alert alert-success" v-if="$page.props.flash.success">
                        <svg width="30" height="30" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M24 4C12.96 4 4 12.96 4 24C4 35.04 12.96 44 24 44C35.04 44 44 35.04 44 24C44 12.96 35.04 4 24 4ZM18.58 32.58L11.4 25.4C10.62 24.62 10.62 23.36 11.4 22.58C12.18 21.8 13.44 21.8 14.22 22.58L20 28.34L33.76 14.58C34.54 13.8 35.8 13.8 36.58 14.58C37.36 15.36 37.36 16.62 36.58 17.4L21.4 32.58C20.64 33.36 19.36 33.36 18.58 32.58Z"
                                fill="#00BA34" />
                        </svg>
                        <div class="flex justify-between items-center w-full">
                            <span class="text-content2">Account Creation Successful!</span>
                            <button type="button" @click="removeAlert"
                                class="ml-auto transition focus:outline-none  active:text-gray-400"><svg
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div>
                        <form @submit.prevent="submitRegistration" class="mt-8 grid grid-cols-6 gap-6" method="POST">

                            <div class="col-span-6 sm:col-span-3">
                                <label for="FirstName" class="block text-sm font-medium text-gray-700">
                                    First Name *
                                </label>

                                <input type="text" id="FirstName" v-model="form.first_name"
                                    class="mt-1 w-full rounded-md border-gray-200 bg-white text-sm text-gray-700 shadow-sm"
                                    :class="{ 'bg-red-50 border-red-500 text-red-900 placeholder-red-700 focus:ring-red-500 focus:border-red-500': errors.first_name }" />
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="middle_name" class="block text-sm font-medium text-gray-700">
                                    Middle Name
                                </label>

                                <input type="text" id="middle_name" v-model="form.middle_name"
                                    class="mt-1 w-full rounded-md border-gray-200 bg-white text-sm text-gray-700 shadow-sm" />
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="LastName" class="block text-sm font-medium text-gray-700">
                                    Last Name *
                                </label>

                                <input type="text" id="LastName" v-model="form.last_name"
                                    class="mt-1 w-full rounded-md border-gray-200 bg-white text-sm text-gray-700 shadow-sm"
                                    :class="{ 'bg-red-50 border-red-500 text-red-900 placeholder-red-700 focus:ring-red-500 focus:border-red-500': errors.last_name }" />
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="date_of_birth" class="block text-sm font-medium text-gray-700">
                                    Date of Birth *
                                </label>

                                <input type="date" id="date_of_birth" v-model="form.date_of_birth"
                                    class="mt-1 w-full rounded-md border-gray-200 bg-white text-sm text-gray-700 shadow-sm"
                                    :class="{ 'bg-red-50 border-red-500 text-red-900 placeholder-red-700 focus:ring-red-500 focus:border-red-500': errors.date_of_birth }" />
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="LastName" class="block text-sm font-medium text-gray-700">
                                    Sex *
                                </label>

                                <select v-model="form.sex" id="sex"
                                    class="mt-1 w-full rounded-md border-gray-200 bg-white text-sm text-gray-700 shadow-sm"
                                    :class="{ 'border-red-500 text-red-900 placeholder-red-700 focus:ring-red-500 focus:border-red-500': errors.sex }">
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>

                            <div class="relative col-span-6 sm:col-span-3">
                                <label for="mobile_number" class="block text-sm font-medium text-gray-700"> Mobile
                                    Number
                                    *</label>
                                <input type="text" id="mobile_number" v-model="form.mobile_number"
                                    class="mt-1 w-full rounded-md border-gray-200 bg-white text-sm text-gray-700 shadow-sm pl-12"
                                    :class="{ 'border-red-500 text-red-900 placeholder-red-700 focus:ring-red-500 focus:border-red-500': errors.mobile_number }"
                                    @input="validateNumber" pattern="^9\d{9}$" placeholder="9XXXXXXXXX" required
                                   />

                                <span class="absolute inset-y-11 left-3 text-sm inline-flex items-center text-gray-10">
                                    +63
                                </span>
                            </div>

                            <div class="col-span-6">
                                <label for="username" class="block text-sm font-medium text-gray-700"> Username
                                    *</label>
                                <input type="text" id="username" v-model="form.username"
                                    class="mt-1 w-full rounded-md border-gray-200 bg-white text-sm text-gray-700 shadow-sm "
                                    :class="{ 'border-red-500 text-red-900 placeholder-red-700 focus:ring-red-500 focus:border-red-500': errors.username }" />
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="Password" class="block text-sm font-medium text-gray-700"> Password
                                    *</label>

                                <input :type="isShowPassword ? 'text' : 'password'" id="Password"
                                    v-model="form.password"
                                    class="mt-1 w-full rounded-md border-gray-200 bg-white text-sm text-gray-700 shadow-sm"
                                    :class="{ 'border-red-500 text-red-900 placeholder-red-700 focus:ring-red-500 focus:border-red-500': errors.password }" />
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="PasswordConfirmation" class="block text-sm font-medium text-gray-700">
                                    Password Confirmation *
                                </label>

                                <input :type="isShowPassword ? 'text' : 'password'" id="PasswordConfirmation"
                                    v-model="form.password_confirmation"
                                    class="mt-1 w-full rounded-md border-gray-200 bg-white text-sm text-gray-700 shadow-sm" />
                            </div>

                            <div class="col-span-6">
                                <div class="flex justify-start items-center">
                                    <input type="checkbox" id="show_password" @change="handleShowPassword">
                                    <label for="show_password" class="ml-2">Show Password</label>
                                </div>
                            </div>

                            <div class="col-span-6">
                                <p class="text-sm text-gray-500">
                                    By creating an account, you agree to our
                                    <a href="#" class="text-gray-700 underline"> terms and conditions </a>
                                    and
                                    <a href="#" class="text-gray-700 underline">privacy policy</a>.
                                </p>
                            </div>

                            <div class="col-span-6 sm:flex sm:items-center sm:gap-4">
                                <button type="submit" :disabled="form.processing"
                                    class="inline-block shrink-0 rounded-md border border-blue-600 bg-blue-600 px-12 py-3 text-sm font-medium text-white transition hover:bg-transparent hover:text-blue-600 focus:outline-none focus:ring active:text-blue-500"
                                    :class="{ 'disabled opacity-25 pointer-events-none': form.processing }">
                                    Create an account <span v-if="form.processing"
                                        class="spinner-xl ml-2 spinner-circle [--spinner-color:var(--gray-8)]   "></span>
                                </button>

                                <p class="mt-4 text-sm text-gray-500 sm:mt-0">
                                    Already have an account?
                                    <Link :href="route('login')" class="text-gray-700 underline">Log in</Link>.
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </section>


</template>
