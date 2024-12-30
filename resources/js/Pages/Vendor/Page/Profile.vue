<script setup>
import Dashboard from "../Dashboard.vue";
import { Head, usePage, router, useForm } from "@inertiajs/vue3";
import { onMounted, watch, ref, onUnmounted, reactive, computed } from "vue";
import axios from "axios";

const props = defineProps({
    profile: Object,
    regions: Array,
    errors: Object,
});

const isEdit = ref(false);
const ischangeMobileNumberOpen = ref(false);
const hasOTP = ref(false);

const form = useForm({
    purok: null,
    barangay: null,
    city: null,
    province: null,
    region: null
});

const mobile_number = ref(null);

onMounted(() => {
    form.purok = usePage().props.profile.purok;
    form.barangay = usePage().props.profile.barangay;
    form.city = usePage().props.profile.city;
    form.province = usePage().props.profile.province;
    form.region = usePage().props.profile.region;
    mobile_number.value = usePage().props.profile.user.mobile_number;
});



const newForm = useForm({
    new_mobile_number: null,
    otp: null
});

const imageData = reactive({
    image: null
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


const getFullName = () => {
    return (
        usePage().props.profile.first_name +
        " " +
        (usePage().props.profile.middle_name != null ? usePage().props.profile.middle_name + " " : "") +
        usePage().props.profile.last_name
    );
};

const provinces = ref([]);
const cities = ref([]);
const barangays = ref([]);

const fetchProvinces = async () => {
    if (!form.region) return;
    const response = await axios.get(`/api/provinces/${form.region}`);
    provinces.value = response.data;
    //form.province = null; // Reset province
    //cities.value = []; // Clear cities
    //barangays.value = []; // Clear barangays
};

const fetchCities = async () => {
    if (!form.province) return;
    const response = await axios.get(`/api/cities/${form.province}`);
    cities.value = response.data;
    //console.log();
    //form.city = null; // Reset city
    //barangays.value = []; // Clear barangays
};

const fetchBarangays = async () => {
    if (!form.city) return;
    const response = await axios.get(`/api/barangays/${form.city}`);
    barangays.value = response.data;
    //form.barangay = null; // Reset barangay
};

watch(
    () => form.region,
    async (region) => {
        if (region) {
            await fetchProvinces();
        } else {
            provinces.value = [];
            form.province = null;
        }
    }
);

watch(
    () => form.province,
    async (province) => {
        if (province) {
            await fetchCities();
        } else {
            cities.value = [];
            form.city = null;
        }
    }
);

watch(
    () => form.city,
    async (city) => {
        if (city) {
            await fetchBarangays();
        } else {
            barangays.value = [];
            form.barangay = null;
        }
    }
);

const cancelEditMobileNUmber = () => {
    ischangeMobileNumberOpen.value = false;
}

const cancelEdit = () => {
    isEdit.value = false;
    form.purok = usePage().props.profile.purok;
    form.barangay = usePage().props.profile.barangay;
    form.city = usePage().props.profile.city;
    form.province = usePage().props.profile.province;
    form.region = usePage().props.profile.region;
}

const updateProfile = () => {
    form.put(route('user.update-profile'), {
        onSuccess: (page) => {
            Swal.fire({
                toast: true,
                icon: 'success',
                position: 'top-end',
                showConfirmButton: true,
                title: page.props.flash.success,
            });
            isEdit.value = false;
        }
    });
};

const handleImageUpload = (event) => {
    imageData.image = event.target.files[0];

    router.post(route('user.change-profile-image'),
        {
            _method: 'put',
            imageData,
        },
        {
            onSuccess: (page) => {
                Swal.fire({
                    toast: true,
                    icon: 'success',
                    position: 'top-end',
                    showConfirmButton: true,
                    title: page.props.flash.success,
                });
            }
        });
};

const validateNumber = (event) => {
    // Remove any non-numeric characters from the input
    let value = event.target.value.replace(/\D/g, "");

    if (value.charAt(0) !== "9") {
        value = ""; // Clear the input if the first digit is not 9
    }
    form.mobile_number = value.slice(0, 10);
};

const sendOTP = () => {
    newForm.post(route('user.sendOTP'), {
        onSuccess: (page) => {
            hasOTP.value = true;   
            Swal.fire({
                toast: true,
                icon: 'success',
                position: 'top-end',
                showConfirmButton: true,
                title: page.props.flash.success,
            });
            startCountdown();
        },
        onError: (page) => {
            hasOTP.value = false;
            Swal.fire({
                toast: true,
                icon: 'success',
                position: 'top-end',
                showConfirmButton: true,
                title: page.props.flash.error,
            });
            
        }
    })
    
};

const errorOtp = ref(null);

const verifyOTP = () => {
    newForm.put(route('user.verifyOTP'), {
        onSuccess: (page) => {
            if(page.props.flash.success){
                ischangeMobileNumberOpen.value = false;
                newForm.reset();
                Swal.fire({
                    toast: true,
                    icon: 'success',
                    position: 'top-end',
                    showConfirmButton: true,
                    title: page.props.flash.success,
                });
            }else{
                errorOtp.value = page.props.flash.errorOTP
            }
        }
    })
};

</script>

<style>
body {
    background-color: #f7fafc;
}

.container {
    background-color: #ffffff;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

input[type="file"] {
    background-color: #ebf2f7;
}

select,
input {
    border: 1px solid #e2e8f0;
    transition: all 0.3s ease-in-out;
}

select:focus,
input:focus {
    border-color: #4299e1;
    box-shadow: 0 0 0 2px rgba(66, 153, 225, 0.5);
}

.text-sm {
    color: #4a5568;
}

.text-2xl {
    color: #2d3748;
}
</style>

<template>

    <Head title="Profile" />
    <Dashboard>
        <div class="p-6 rounded-lg shadow-md">
            <!-- Profile Form -->
            <div class="container mx-auto p-6 bg-white rounded-lg shadow-lg">
                <div class="flex flex-col md:flex-row md:space-x-6">
                    <!-- Profile Image Section -->
                    <div class="w-full md:w-1/3 text-center mb-6 md:mb-0">
                        <div class="relative">
                            <img v-if="$page.props.profile.image" :src="'/images/clients/' + $page.props.profile.image"
                                alt="Profile Image" class="w-32 h-32 rounded-full mx-auto mb-4 object-cover" />
                            <img v-else src="/images/avatars/avatar.png" alt="Profile Image"
                                class="w-32 h-32 rounded-full mx-auto mb-4 object-cover" />
                            <label for="image"
                                class="w-32 h-32 rounded-full absolute transition-all duration-300 opacity-0 hover:opacity-70 hover:bg-gray-10 flex justify-center items-center flex-col hover:text-white hover:show"
                                style="cursor: pointer; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                                <span class="inline-block"><svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                    </svg></span>
                                <span class="inline-block " style="font: 7px">Change Image</span>
                            </label>
                        </div>
                        <input @input="handleImageUpload" type="file" id="image" name="image"
                            accept="image/png, image/jpeg, image/jpg, image/svg+xml, image/webp"
                            class="hidden w-full text-sm text-gray-700 py-2 px-4 bg-gray-200 rounded-md border focus:outline-none" />

                        <div class="w-full text-lg font-bold py-2 px-4">
                            {{ getFullName() }}
                        </div>

                        <div class="text-start">
                            <div class="mt-3">
                                <label for="sex" class="block text-sm font-medium text-gray-700">Sex</label>
                                <input type="text" id="date_of_birth" name="date_of_birth" :value="profile.sex"
                                    class="w-full mt-2 p-3 border rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                    disabled />
                            </div>

                            <div class="mt-3">
                                <label for="date_of_birth" class="block text-sm font-medium text-gray-700">Date of
                                    Birth</label>
                                <input type="date" id="date_of_birth" name="date_of_birth"
                                    :value="profile.date_of_birth"
                                    class="w-full mt-2 p-3 border rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                    disabled />
                            </div>

                            <div class="relative mt-3">
                                <label for="mobile_number" class="block text-sm font-medium text-gray-700">
                                    Mobile Number *</label>
                                <input type="text" id="mobile_number" :value="mobile_number" disabled
                                    class="w-full mt-2 p-3 border rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none pl-12 pe-12"
                                    @input="validateNumber" pattern="^9\d{9}$" placeholder="9XXXXXXXXX" required />

                                <span class="absolute left-3 text-1sm text-gray-10" style="top: 41px">
                                    +63
                                </span>
                                <label for="ischangeMobileNumberOpen"
                                    class="absolute cursor-pointer text-primary right-3 text-1sm text-gray-10"
                                    style="top: 41px">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Personal Information -->
                    <div class="w-full lg:w-2/3  mb-6 md:mb-0">

                        <!-- Address Information -->


                        <div class="flex justify-between items-start flex-col md:flex-row mb-4">
                            <div class="text-2xl font-semibold text-gray-800">
                                Address Information
                            </div>
                            <div class="my-5 sm:my-0 space-x-2 flex justify-center items-center">
                                <button class="btn btn-primary rounded-none shadow " @click="isEdit = true"
                                    v-if="!isEdit">
                                    <span class="me-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                        </svg>
                                    </span>
                                    <span>Edit</span>
                                </button>
                                <button v-if="isEdit" class="btn bg-green-500 rounded-none shadow"
                                    @click="updateProfile()">
                                    <span>Save</span>
                                </button>
                                <button v-if="isEdit" class="btn bg-gray-300 rounded-none shadow" @click="cancelEdit()">
                                    <span>Cancel</span>
                                </button>
                            </div>
                        </div>

                        <div class="grid lg:grid-cols-2 gap-6">
                            <div>
                                <label for="region" class="block text-sm font-medium text-gray-700">Region</label>
                                <select id="region"
                                    class="w-full mt-2 p-3 border rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                    :disabled="!isEdit" :class="{ 'ring-2 ring-red-500': errors.region }"
                                    v-model="form.region" @change="fetchProvinces">
                                    <option v-for="region in props.regions" :key="region.regCode"
                                        :value="region.regCode">
                                        {{ region.regDesc }}
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label for="province" class="block text-sm font-medium text-gray-700">Province</label>
                                <select id="province"
                                    class="w-full mt-2 p-3 border rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                    :disabled="!isEdit" :class="{ 'ring-2 ring-red-500': errors.province }"
                                    v-model="form.province" @change="fetchCities">
                                    <option v-for="province in provinces" :key="province.provCode"
                                        :value="province.provCode">
                                        {{ province.provDesc }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                                <select id="city"
                                    class="w-full mt-2 p-3 border rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                    :disabled="!isEdit" :class="{ 'ring-2 ring-red-500': errors.city }"
                                    v-model="form.city" @change="fetchBarangays">
                                    <option v-for="city in cities" :key="city.citymunCode" :value="city.citymunCode">
                                        {{ city.citymunDesc }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label for="barangay_id"
                                    class="block text-sm font-medium text-gray-700">Barangay</label>
                                <select id="barangay_id"
                                    class="w-full mt-2 p-3 border rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                    :disabled="!isEdit" :class="{ 'ring-2 ring-red-500': errors.barangay }"
                                    v-model="form.barangay">
                                    <option v-for="barangay in barangays" :key="barangay.brgyCode"
                                        :value="barangay.brgyCode">
                                        {{ barangay.brgyDesc }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label for="purok" class="block text-sm font-medium text-gray-700">Purok</label>
                                <input type="text" id="purok" name="purok" v-model="form.purok"
                                    class="w-full mt-2 p-3 border rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                    :disabled="!isEdit" :class="{ 'ring-2 ring-red-500': errors.purok }" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Style for Buttons and Form -->
        </div>


        <input class="modal-state" id="ischangeMobileNumberOpen" type="checkbox" v-model="ischangeMobileNumberOpen" />
        <div class="modal" style="z-index: 99999; margin-top: 0">
            <label class="modal-overlay"></label>
            <div class="modal-content">
                <div class="flex flex-col gap-5">
                    <label @click="cancelEditMobileNUmber()"
                        class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</label>
                    <h2 class="text-xl">Change Mobile Number</h2>
                    <div class="relative">
                        <label for="mobile_number" class="block text-sm font-medium text-gray-700">
                            New Mobile Number</label>
                        <input type="text" id="mobile_number" v-model="newForm.new_mobile_number"
                            class="w-full mt-2 p-3 border rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none pl-12 pe-12"
                            :class="{
                                'border-red-500 text-red-900 placeholder-red-700 focus:ring-red-500 focus:border-red-500':
                                    errors.new_mobile_number,
                            }" @input="validateNumber" pattern="^9\d{9}$" placeholder="9XXXXXXXXX" required />

                        <span class="absolute left-3 text-1sm text-gray-10" style="top: 41px">
                            +63
                        </span>
                        <button v-if="!isCounting" type="button" class="absolute cursor-pointer text-primary right-3" :class="{'disabled opacity-50 text-gray-500 pointer-events-none':newForm.processing}" @click="sendOTP"
                            style="top: 41px; font-size: 15px;">
                            Send OTP
                            
                        </button>
                        <span v-else class="absolute bg-gray-200 right-3 rounded px-2"
                            style="top: 41px; font-size: 15px;">
                            {{ minutes }}:{{ seconds < 10 ? '0' : '' }}{{ seconds }}
                        </span>
                    </div>
                    <span v-if="errors.new_mobile_number" class="text-red-700">{{ errors.new_mobile_number }}</span>
                    <input type="text" class="input" placeholder="Enter OTP" :disabled="!hasOTP" v-model="newForm.otp">
                    <span v-if="errorOtp != null" class="text-red-700">{{ errorOtp }}</span>
                    <div class="flex gap-3 justify-end">
                        <button type="button" class="btn bg-green-600 text-white" :class="{'disabled opacity-50 text-gray-500 pointer-events-none':newForm.processing}"  @click="verifyOTP">Update</button>
                        <button type="button" class="btn" @click="cancelEditMobileNUmber()">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </Dashboard>
</template>