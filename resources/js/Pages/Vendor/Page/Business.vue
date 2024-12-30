<script setup>
import Dashboard from '../Dashboard.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, watch, reactive } from 'vue';
import axios from 'axios';

defineProps({
    profile: Object,
    dateNow: String,
    errors: Object
});

const today = ref(new Date().toISOString().split('T')[0]); // Format: YYYY-MM-DD
const isUpdatePermit = ref(false);

function getPaymentCycle(cycle) {
    switch (cycle) {
        case 0:
            return 'Monthly';
        case 1:
            return 'Quarterly';
        case 2:
            return 'Bi-Annual';
        case 3:
            return 'Annual';
    }
}

const fullscreenImage = ref(null);

function openImage(image) {
    fullscreenImage.value = image;
}

function closeImage() {
    fullscreenImage.value = null;
}

function getStatusName(status) {
    switch (status) {
        //0 on checking, 1 Approved, 2 Rejected, 3 Closed
        case 0:
            return 'Under Review';
        case 1:
            return 'Approved';
        case 2:
            return 'Rejected';
        case 3:
            return 'Closed';

    }
}

const form = reactive({
    new_permit_number: null,
    new_expiration_date: null,
    new_permit_image: null,
    business_id: usePage().props.profile?.business?.id ?? null,

});

const updatePermit = () => {
    router.post(route('user.update-permit'),
        {
            _method: 'put',
            ...form,
        },
        {
            onSuccess: page => {
                isUpdatePermit.value = false;
                Swal.fire({
                    toast: true,
                    icon: 'success',
                    position: 'top-end',
                    showConfirmButton: true,
                    title: page.props.flash.success,
                });
                resetFields();
            }
        });
};

const handleImageUpload = (event) => {
    form.new_permit_image = event.target.files[0];
    //console.log(form.image);
};

function resetFields() {
    form.new_expiration_date = null;
    form.new_permit_image = null;
    form.new_permit_number = null;
}


</script>

<template>

    <Head title="Dashboard" />
    <Dashboard>
        <div class="container px-6 mx-auto grid">
            <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
                My Business
            </h2>

            <div v-if="profile.business" class="container px-6 mx-auto grid">
                <!-- Profile Form -->
                <div class="container mx-auto p-6 bg-white rounded-lg shadow-lg">
                    <div class="flex justify-end items-center gap-3 mb-4">
                        <div class="flex flex-row">
                            <span class="me-3">Status: </span>
                            <label for="statusModal" class="badge cursor-pointer hover:opacity-90"
                                :class="{ 'badge-success': profile.business.status == 1, 'badge-warning': profile.business.status == 0, 'badge-error': profile.business.status == 2 || profile.business.status == 3 }">{{
                                    getStatusName(profile.business.status) }}</label>
                        </div>
                    </div>

                    <div class="my-3 col-span-2 py-2">
                        <hr>
                        <div class="text-2xl font-semibold text-gray-800 text-center my-4">
                            Business Information
                        </div>

                        <div class="flex justify-between items-center mb-2">
                            <div class="my-3 text-2xl">Stall # {{ profile.business.establishment_unit_id }}</div>
                            <div v-if="profile.business.permit_expiration_date < today" class="flex gap-2">
                                <span class="me-3">Permit: </span>
                                <label v-if="profile.business.status == 1" for="updatePermit" class="text-blue-600 cursor-pointer">Update Business
                                    Permit</label>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-5">
                            <div class="col-span-3 sm:col-span-2 md:col-span-1">
                                <div class="flex flex-col">
                                    <label for="business_name">Business Name</label>
                                    <input type="text" :value="profile.business.name"
                                        class="w-full mt-2 p-3 border rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                        id="business_name" disabled>
                                </div>
                            </div>

                            <div class="col-span-3 sm:col-span-2 md:col-span-1">
                                <div class="flex flex-col">
                                    <label for="kind_of_business">Kind of Business</label>
                                    <input type="text" :value="profile.business.kind_of_business"
                                        class="w-full mt-2 p-3 border rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                        id="kind_of_business" disabled>
                                </div>
                            </div>

                            <div class="col-span-3 sm:col-span-2 md:col-span-1">
                                <div class="flex flex-col">
                                    <label for="plate">Business Plate</label>
                                    <input type="text" :value="profile.business.plate"
                                        class="w-full mt-2 p-3 border rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                        id="plate" disabled>
                                </div>
                            </div>

                            <div class="col-span-3 sm:col-span-2 md:col-span-1">
                                <div class="flex flex-col">
                                    <label for="permit_number">Permit Number</label>
                                    <input type="text" :value="profile.business.permit_number"
                                        class="w-full mt-2 p-3 border rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                        id="permit_number" disabled>
                                </div>
                            </div>

                            <div class="col-span-3 sm:col-span-2 md:col-span-1">
                                <div class="flex flex-col">
                                    <label for="permit_number">Business Permit Expiration</label>
                                    <input type="date" :value="profile.business.permit_expiration_date"
                                        class="w-full mt-2 p-3 border rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                        id="permit_number" disabled>
                                </div>
                            </div>

                            <div class="col-span-3 sm:col-span-2 md:col-span-1">
                                <div class="flex flex-col">
                                    <label for="dti_reg_number">DTI Registratered Number</label>
                                    <input type="text" :value="profile.business.dti_reg_number"
                                        class="w-full mt-2 p-3 border rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                        id="kind_of_business" disabled>
                                </div>
                            </div>

                            <div class="col-span-3 sm:col-span-2 md:col-span-1">
                                <div class="flex flex-col">
                                    <label for="plate">Cedula</label>
                                    <input type="text" :value="profile.business.cedula"
                                        class="w-full mt-2 p-3 border rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                        id="plate" disabled>
                                </div>
                            </div>
                            <div class="col-span-3 sm:col-span-2 md:col-span-1">
                                <div class="flex flex-col">
                                    <label for="payment_cycle">Payment Cycle</label>
                                    <input type="text" :value="getPaymentCycle(profile.business.payment_cycle)"
                                        class="w-full mt-2 p-3 border rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                        id="payment_cycle" disabled>
                                </div>
                            </div>
                            <div class="col-span-3 sm:col-span-2 md:col-span-1">
                                <div class="flex flex-col">
                                    <label for="payment_cycle">Area</label>
                                    <input type="text"
                                        :value="profile.business.establishment_unit.establishment.area.name"
                                        class="w-full mt-2 p-3 border rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                        id="payment_cycle" disabled>
                                </div>
                            </div>
                            <div class="col-span-3 sm:col-span-2 md:col-span-1">
                                <div class="flex flex-col">
                                    <label for="payment_cycle">Section</label>
                                    <input type="text" :value="profile.business.establishment_unit.establishment.name"
                                        class="w-full mt-2 p-3 border rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                        id="payment_cycle" disabled>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="my-3 col-span-2 py-2">
                        <hr>
                        <div class="text-2xl font-semibold text-gray-800 text-center my-4">
                            Business Document Images
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                            <div class="relative group cursor-pointer"
                                @click="openImage(profile.business.requirement_image.cedula)">
                                <img :src="'/images/business/' + profile.business.requirement_image.cedula"
                                    alt="Gallery Image"
                                    class="object-cover rounded-lg shadow-md transition-transform duration-300 hover:scale-105" />
                            </div>
                            <div class="relative group cursor-pointer"
                                @click="openImage(profile.business.requirement_image.brgy_clearance)">
                                <img :src="'/images/business/' + profile.business.requirement_image.brgy_clearance"
                                    alt="Gallery Image"
                                    class="object-cover rounded-lg shadow-md transition-transform duration-300 hover:scale-105" />
                            </div>
                            <div class="relative group cursor-pointer"
                                @click="openImage(profile.business.requirement_image.pmo_ceedo_clearance)">
                                <img :src="'/images/business/' + profile.business.requirement_image.pmo_ceedo_clearance"
                                    alt="Gallery Image"
                                    class="object-cover rounded-lg shadow-md transition-transform duration-300 hover:scale-105" />
                            </div>
                            <div class="relative group cursor-pointer"
                                @click="openImage(profile.business.requirement_image.dti_cert)">
                                <img :src="'/images/business/' + profile.business.requirement_image.dti_cert"
                                    alt="Gallery Image"
                                    class="object-cover rounded-lg shadow-md transition-transform duration-300 hover:scale-105" />
                            </div>
                            <div class="relative group cursor-pointer"
                                @click="openImage(profile.business.requirement_image.medical_cert)">
                                <img :src="'/images/business/' + profile.business.requirement_image.medical_cert"
                                    alt="Gallery Image"
                                    class="object-cover rounded-lg shadow-md transition-transform duration-300 hover:scale-105" />
                            </div>
                            <div class="relative group cursor-pointer"
                                @click="openImage(profile.business.requirement_image.business_permit)">
                                <img :src="'/images/business/' + profile.business.requirement_image.business_permit"
                                    alt="Gallery Image"
                                    class="object-cover rounded-lg shadow-md transition-transform duration-300 hover:scale-105" />
                            </div>

                        </div>

                        <!-- Fullscreen Modal -->
                        <div v-if="fullscreenImage"
                            class="fixed inset-0 z-50 bg-black bg-opacity-80 flex items-center justify-center">
                            <button class="absolute top-5 right-5 text-white text-3xl" @click="closeImage">
                                ✕
                            </button>
                            <img :src="'/images/business/' + fullscreenImage" alt="Fullscreen Image"
                                class="max-w-full max-h-full rounded-lg shadow-lg" />
                        </div>
                    </div>
                </div>

                <!-- Additional Style for Buttons and Form -->
                <input class="modal-state" id="statusModal" type="checkbox" />
                <div class="modal !h-full" style="z-index: 99999; margin-top: 0;">
                    <label class="modal-overlay"></label>
                    <div class="modal-content">
                        <label for="statusModal"
                            class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</label>
                        <h2 class="text-xl mb-3">Business Status</h2>

                        <div v-if="profile.business.status == 0 || profile.business.status == 1">
                            <div class="flex my-2 justify-between gap-5">
                                <span class="font-bold">Status:</span>
                                <span class="badge"
                                    :class="{ 'badge-success': profile.business.status == 1, 'badge-warning': profile.business.status == 0, 'badge-error': profile.business.status == 2 || profile.business.status == 3 }">{{
                                        getStatusName(profile.business.status) }}</span>
                            </div>

                            <div class="flex my-2 justify-between gap-5">
                                <span class="font-bold">Date Approved:</span>
                                <span class="">{{ profile.business.date_approved ? profile.business.date_approved : '--'
                                    }}</span>
                            </div>
                        </div>

                        <div v-if="profile.business.status == 2">
                            <div class="flex my-2 justify-between gap-5">
                                <span class="font-bold">Status:</span>
                                <span class="badge"
                                    :class="{ 'badge-success': profile.business.status == 1, 'badge-warning': profile.business.status == 0, 'badge-error': profile.business.status == 2 || profile.business.status == 3 }">{{
                                        getStatusName(profile.business.status) }}</span>
                            </div>

                            <div class="flex my-2 justify-between gap-5">
                                <span class="font-bold">Date Rejected:</span>
                                <span class="">{{ profile.business.date_rejected ? profile.business.date_rejected : '--'
                                    }}</span>
                            </div>
                        </div>

                        <div v-if="profile.business.status == 3">
                            <div class="flex my-2 justify-between gap-5">
                                <span class="font-bold">Status:</span>
                                <span class="badge"
                                    :class="{ 'badge-success': profile.business.status == 1, 'badge-warning': profile.business.status == 0, 'badge-error': profile.business.status == 2 || profile.business.status == 3 }">{{
                                        getStatusName(profile.business.status) }}</span>
                            </div>

                            <div class="flex my-2 justify-between gap-5">
                                <span class="font-bold">Date Closed:</span>
                                <span class="">{{ profile.business.date_closed ? profile.business.date_closed : '--'
                                    }}</span>
                            </div>
                        </div>

                        <div v-if="profile.business.status == 2 || profile.business.status == 3">
                            <span class="font-bold">Remarks:</span>
                            <textarea name="" id="" class="w-full text-start" cols="30" disabled
                                v-if="profile.business.status == 2 || profile.business.status == 3"
                                :value="profile.business.remarks">
                        </textarea>
                        </div>

                        <div class="flex gap-3">
                            <label class="btn btn-block" for="statusModal">Close</label>
                        </div>
                    </div>
                </div>

                <!-- Additional Style for Buttons and Form -->
                <input class="modal-state" id="updatePermit" type="checkbox" v-model="isUpdatePermit" />
                <div class="modal !h-full" style="z-index: 99999; margin-top: 0;">
                    <label class="modal-overlay"></label>
                    <div class="modal-content">
                        <label for="updatePermit"
                            class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</label>
                        <h2 class="text-xl mb-3">Update Business Permit</h2>

                        <div class="mb-2 flex flex-col gap-2">
                            <label for="new_permit_number">Business Permit Number</label>
                            <input type="text" placeholder="Business Permit Number" id="new_permit_number"
                                :class="{ 'border-red-700': errors.new_permit_number }" v-model="form.new_permit_number">
                            <span class="text-red-700">{{ errors.new_permit_number }}</span>
                        </div>

                        <div class="mb-2 flex flex-col gap-2">
                            <label for="new_expiration_date">Expiration Date</label>
                            <input type="date" placeholder="Expiration Date" id="new_expiration_date"
                                :class="{ 'border-red-700': errors.new_expiration_date }"
                                v-model="form.new_expiration_date">
                            <span class="text-red-700">{{ errors.new_expiration_date }}</span>
                        </div>

                        <div class="mb-3 flex flex-col gap-2">
                            <label for="new_permit_image">Business Permit Image</label>
                            <input type="file" accept="image/png, image/jpeg, image/jpg, image/svg+xml, image/webp"
                                id="new_permit_image" @input="handleImageUpload">
                            <span class="text-red-700">{{ errors.new_permit_image }}</span>
                        </div>

                        <div class="flex gap-3 justify-end">
                            <button type="button" class="btn btn-primary" @click="updatePermit()">Update Now</button>
                            <label class="btn" for="updatePermit">Close</label>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else class="flex flex-col justify-center items-center text-center w-full"
                    style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%)">
                    <span class="text-2xl mb-3">You currently have no stalls applied!</span>
                    <Link class="btn btn-primary rounded-none" :href="route('services')">Apply Now!</Link>
                </div>
        </div>
    </Dashboard>
</template>