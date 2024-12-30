<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';

import { ref, computed, onMounted } from 'vue';

const {status, canReset, mobile_number} = usePage().props;

const hasOTP = ref(false);
const errorOTP = ref(null);
const showPassword = ref(false);

function togglePassword(){
    showPassword.value = !showPassword.value;
}

const form = useForm({
    mobile_number: '',
    otp: '',
    password: '',
    password_confirmation: ''
});

onMounted(() => {
    if (canReset){
        form.mobile_number = mobile_number
    }
});

const isCounting = ref(false); // Tracks if the countdown is active
const timeLeft = ref(300); // Countdown duration in seconds (5 minutes)

// Compute minutes and seconds from timeLeft
const minutes = computed(() => Math.floor(timeLeft.value / 60));
const seconds = computed(() => timeLeft.value % 60);

let intervalId;

function startCountdown() {
    isCounting.value = true; // Hide the button
    timeLeft.value = 300; // Set count time to 5 minutes (300 seconds)

    intervalId = setInterval(() => {
        if (timeLeft.value > 0) {
            timeLeft.value--;
        } else {
            clearInterval(intervalId); // Stop count
            isCounting.value = false; // Show the button again
        }
    }, 1000);
}

const sendOTP = () => {
    
    form.post(route('password.sendOTP'), {
        onSuccess: () => {
            startCountdown();
            hasOTP.value = true;
        },
    });
};

const submit = () => {
    form.post(route('password.verifyOTP'), {
        onSuccess: (page) => {
            if(page.props.flash.errorOTP){
                errorOTP.value = page.props.flash.errorOTP;
            }else{
                window.location.reload();
            }
        },
    });
};

const resetPassword = () => {
    form.post(route('password.store'), {
        onSuccess: () => {
            alert("Password Changed!");
        },
    });
};


</script>

<template>
    <GuestLayout>

        <Head title="Forgot Password" />

        <div  v-if="!canReset" class="mb-4 text-sm text-gray-600">
            Forgot your password? No problem. Just enter your mobile number registered and verify the OTP and you will
            be redirected to password reset page that will allow
            you to choose a new one.
        </div>

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>


        <form v-if="!canReset" @submit.prevent="submit">
            <div class="relative mb-2">
                <InputLabel for="mobile_number" value="Mobile Number" />

                <TextInput id="mobile_number" type="mobile_number" class="mt-1 block min-w-full py-2 input pl-10"
                    v-model="form.mobile_number" required autofocus autocomplete="mobile_number"
                    placeholder="9XXXXXXXXX" :disable="hasOTP" />

                <InputError class="mt-2" :message="form.errors.mobile_number" />
                <span class="absolute left-2 top-8 text-gray-500">+63</span>
                <span v-if="!isCounting" class="absolute right-2 top-8 cursor-pointer text-purple-600"
                    @click="sendOTP" :class="{'text-gray-700 pointer-events-none':form.processing}">Send OTP</span>
                <span v-else class="absolute right-2 top-8 cursor-pointer text-gray-600">{{ minutes }}:{{ seconds < 10
                        ? '0' : '' }}{{ seconds }}</span>
            </div>
            <div>
                <InputLabel for="otp" value="Enter OTP" />

                <TextInput id="otp" type="otp" class="mt-1 block min-w-full py-2 input" v-model="form.otp"
                    :disabled="!hasOTP" required autofocus autocomplete="otp" />

                <InputError class="mt-2" :message="errorOTP" />
            </div>
            <div class="mt-4 flex items-center justify-end">
                <PrimaryButton class="bg-green-700 hover:bg-green-800"
                    :class="{ 'disabled opacity-25': form.processing || !hasOTP }"
                    :disabled="form.processing || !hasOTP">
                    Verify OTP
                </PrimaryButton>
            </div>
        </form>

        <form v-else @submit.prevent="resetPassword">
            <div class="relative mb-2">
                <InputLabel for="mobile_number" value="Mobile Number" />

                <TextInput id="mobile_number" type="mobile_number" class="mt-1 block min-w-full py-2 input pl-10"
                    v-model="form.mobile_number" required autofocus autocomplete="mobile_number"
                    placeholder="9XXXXXXXXX" :disabled="canReset"/>

                <InputError class="mt-2" :message="form.errors.mobile_number" />
                <span class="absolute left-2 top-8 text-gray-500">+63</span>
            </div>
            <div>
                <InputLabel for="password" value="New Password" />

                <TextInput id="password" :type="showPassword ? 'text' : 'password'" class="mt-1 block min-w-full py-2 input" v-model="form.password"
                     required autofocus autocomplete="password" />

                <InputError class="mt-2" :message="usePage().props.errors.password" />
            </div>
            <div>
                <InputLabel for="password_confirmation" value="Confirm Password" />

                <TextInput id="password_confirmation" :type="showPassword ? 'text' : 'password'" class="mt-1 block min-w-full py-2 input" v-model="form.password_confirmation"
                     required autofocus autocomplete="password_confirmation" />

                <InputError class="mt-2" :message="usePage().props.errors.password_confirmation" />
            </div>
            <div class="my-2 flex gap-2 items-center">
                <input type="checkbox" id="showpassword" v-model="showPassword" @click="togglePassword()">
                <label for="showpassword">Show Password</label>
            </div>
            <div class="mt-4 flex items-center justify-end">
                <PrimaryButton class="bg-green-700 hover:bg-green-800"
                    :class="{ 'disabled opacity-25': form.processing}"
                    :disabled="form.processing">
                    Change Password
                </PrimaryButton>
            </div>
        </form>

    </GuestLayout>
</template>
