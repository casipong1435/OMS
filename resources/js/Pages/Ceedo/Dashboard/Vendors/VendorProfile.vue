<script setup>
import Layout from '../../Layout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    profile: Object
});

const response = ref(null);
const responseModal = ref(false);
const remarks = ref('');

function reopenBusiness(){
    response.value = 1;
    responseModal.value = true;
}

function openCloseResponseModal() {
    response.value = 3;
    responseModal.value = true;
}

function submitResponse() {
    if (response.value == 1) {
        router.put(route('ceedo.reopenBusiness', usePage().props.profile.business.id), {}, {
            onSuccess: (page) => {
                Swal.fire({
                    toast: true,
                    icon: 'success',
                    position: 'top-end',
                    showConfirmButton: true,
                    title: page.props.flash.success,
                });
                responseModal.value = false;
            }
        });
    } else {
        router.put(route('ceedo.closeBusiness', usePage().props.profile.business.id), {remarks: remarks.value}, {
            onSuccess: page => {
                Swal.fire({
                    toast: true,
                    icon: 'success',
                    position: 'top-end',
                    showConfirmButton: true,
                    title: page.props.flash.success,
                });
                responseModal.value = false;
            }
        });
    }
}

const getFullName = (first_name, middle_name, last_name) => {
    return first_name + " " + (middle_name != null ? middle_name + " " : "") + last_name;
}

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

function goBack() {
    window.history.back();
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
</script>

<template>

    <Head title="Vendors" />
    <Layout>
        <div class="container px-6 mx-auto grid">
            <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
                Vendor Profile
            </h2>
            <!-- CTA -->
            <div class="">
                <div class="breadcrumbs text-sm mb-4">
                    <ul>
                        <li>
                            <a @click="goBack()" class="text-gray-800">Vendors</a>
                        </li>
                        <li>
                            <a>Vendor Profile</a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Profile Form -->
            <div class="container mx-auto p-6 bg-white rounded-lg shadow-lg">
                <div v-if="profile.business.status == 1" class="flex justify-end items-center gap-3 mb-7">
                    <button type="button" class="btn btn-error rounded-none"
                        @click="openCloseResponseModal">Close Business</button>
                </div>
                <div v-else class="flex justify-end items-center gap-3 mb-7 cursor-pointer hover:opacity-90">
                    <label for="statusModal" class="badge badge-error">Closed</label>
                    <button type="button" class="btn btn-primary "
                        @click="reopenBusiness">Reopen Business</button>
                </div>
                <hr>
                <div class="flex flex-col md:flex-row md:space-x-6 mt-5">
                    <!-- Profile Image Section -->
                    <div class="w-full md:w-1/3 text-center mb-6 md:mb-0">
                        <div class="relative">
                            <img v-if="profile.image" :src="'/images/clients/' + profile.image" alt="Profile Image"
                                class="w-32 h-32 rounded-full mx-auto mb-4 object-cover" />
                            <img v-else src="/images/avatars/avatar.png" alt="Profile Image"
                                class="w-32 h-32 rounded-full mx-auto mb-4 object-cover" />
                        </div>


                        <div class="w-full text-lg font-bold py-2 px-4">
                            {{ getFullName(profile.first_name, profile.middle_name, profile.last_name,) }}
                        </div>

                        <div class="text-start">
                            <div class="mt-3">
                                <label for="sex" class="block text-sm font-medium text-gray-700">Sex</label>
                                <input type="text" id="sex" name="sex" :value="profile.sex"
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
                                <input type="text" id="mobile_number" :value="profile.user.mobile_number" disabled
                                    class="w-full mt-2 p-3 border rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none pl-12 pe-12" />

                                <span class="absolute left-3 text-1sm text-gray-10" style="top: 41px">
                                    +63
                                </span>
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

                            </div>
                        </div>

                        <div class="grid lg:grid-cols-2 gap-6">
                            <div>
                                <label for="region" class="block text-sm font-medium text-gray-700">Region</label>
                                <input type="text" id="region" name="region" :value="profile.region.regDesc"
                                    class="w-full mt-2 p-3 border rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                    disabled />
                            </div>
                            <div>
                                <label for="province" class="block text-sm font-medium text-gray-700">Province</label>
                                <input type="text" id="province" name="province" :value="profile.province.provDesc"
                                    class="w-full mt-2 p-3 border rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                    disabled />
                            </div>

                            <div>
                                <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                                <input type="text" id="city" name="city" :value="profile.city.citymunDesc"
                                    class="w-full mt-2 p-3 border rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                    disabled />
                            </div>

                            <div>
                                <label for="barangay_id"
                                    class="block text-sm font-medium text-gray-700">Barangay</label>
                                <input type="text" id="barangay_id" name="barangay_id"
                                    :value="profile.barangay.brgyDesc"
                                    class="w-full mt-2 p-3 border rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                    disabled />
                            </div>

                            <div>
                                <label for="purok" class="block text-sm font-medium text-gray-700">Purok</label>
                                <input type="text" id="purok" name="purok" :value="profile.purok"
                                    class="w-full mt-2 p-3 border rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                    disabled />
                            </div>
                        </div>


                    </div>

                </div>

                <div class="my-3 col-span-2 py-2">
                    <hr>
                    <div class="text-2xl font-semibold text-gray-800 text-center my-4">
                        Business Information
                    </div>

                    <div class="my-3 text-2xl">Stall # {{ profile.business.establishment_unit_id }}</div>

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
                                <input type="text" :value="profile.business.establishment_unit.establishment.area.name"
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

            <!--Respond Modal-->
            <input class="modal-state" id="responseModal" type="checkbox" v-model="responseModal" />
            <div class="modal">
                <label class="modal-overlay"></label>
                <div class="modal-content flex flex-col gap-5">
                    <label for="responseModal" class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</label>
                    <h2 class="text-xl">Approve Application</h2>
                    <div class="flex justify-center items-center flex-col gap-2" :class="{'text-primary':response == 1, 'text-error':response == 3}">
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="w-20 h-20">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                            </svg>
                        </span>
                        <span class="text-gray-700">Are you sure you want to {{ response == 1 ? 'Reopen' : 'Close' }} this business?</span>
                    </div>
                    <div class="my-3" v-if="response == 3">
                        <label for="remarks">Reason:</label>
                        <textarea id="remarks" v-model="remarks" class="w-full" required></textarea>
                    </div>
                    <div class="flex gap-3">
                        <button class="btn btn-block" :class="{'btn-primary':response == 1, 'btn-error':response == 3}" @click="submitResponse()">Confirm</button>
                        <button class="btn btn-block"  @click="responseModal = false">Cancel</button>
                    </div>
                </div>
            </div>

            <!-- Additional Style for Buttons and Form -->
            <input class="modal-state" id="statusModal" type="checkbox" />
            <div class="modal !h-full" style="z-index: 99999; margin-top: 0;">
                <label class="modal-overlay"></label>
                <div class="modal-content">
                    <label for="statusModal" class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</label>
                    <h2 class="text-xl mb-3">Business Status</h2>

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
                        v-if="profile.business.status == 2 || profile.business.status == 3" :value="profile.business.remarks">
                        </textarea>
                    </div>

                    <div class="flex gap-3">
                        <label class="btn btn-block" for="statusModal">Close</label>
                    </div>
                </div>
            </div>

        </div>
    </Layout>
</template>