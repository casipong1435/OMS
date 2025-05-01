<script setup>
import Layout from '../../Layout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    business_info: Object
});

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
const isImageShow = ref(false);

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
            return 'Active';
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
                            <a>Vendors</a>
                        </li>
                        <li>
                            <a @click="goBack()" class="text-gray-800">Vendor Profile</a>
                        </li>
                        <li>
                            <a>Business Info</a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Profile Form -->
            <div class="container mx-auto p-6 bg-white rounded-lg shadow-lg">

                <div class="my-3 col-span-2 py-2">
                    <hr>
                    <div class="flex justify-end items-center gap-3 my-4">
                        <div class="flex flex-row">
                            <span class="me-3">Status: </span>
                            <label for="statusModal" class="badge cursor-pointer hover:opacity-90"
                                :class="{ 'badge-success': business_info.status == 1, 'badge-warning': business_info.status == 0, 'badge-error': business_info.status == 2 || business_info.status == 3 }">{{
                                    getStatusName(business_info.status) }}</label>
                        </div>
                    </div>

                    <div class="text-2xl font-semibold text-gray-800 text-center my-4">
                        Business Information
                    </div>

                    <div class="my-3 text-2xl">Stall # {{ business_info.establishment_unit_id }}</div>

                    <div class="grid grid-cols-3 gap-5">
                        <div class="col-span-3 sm:col-span-2 md:col-span-1">
                            <div class="flex flex-col">
                                <label for="business_name">Business Name</label>
                                <input type="text" :value="business_info.name"
                                    class="w-full mt-2 p-3 border rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                    id="business_name" disabled>
                            </div>
                        </div>

                        <div class="col-span-3 sm:col-span-2 md:col-span-1">
                            <div class="flex flex-col">
                                <label for="kind_of_business">Kind of Business</label>
                                <input type="text" :value="business_info.kind_of_business"
                                    class="w-full mt-2 p-3 border rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                    id="kind_of_business" disabled>
                            </div>
                        </div>

                        <div class="col-span-3 sm:col-span-2 md:col-span-1">
                            <div class="flex flex-col">
                                <label for="plate">Business Plate</label>
                                <input type="text" :value="business_info.plate"
                                    class="w-full mt-2 p-3 border rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                    id="plate" disabled>
                            </div>
                        </div>

                        <div class="col-span-3 sm:col-span-2 md:col-span-1">
                            <div class="flex flex-col">
                                <label for="permit_number">Permit Number</label>
                                <input type="text" :value="business_info.permit_number"
                                    class="w-full mt-2 p-3 border rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                    id="permit_number" disabled>
                            </div>
                        </div>

                        <div class="col-span-3 sm:col-span-2 md:col-span-1">
                            <div class="flex flex-col">
                                <label for="dti_reg_number">DTI Registratered Number</label>
                                <input type="text" :value="business_info.dti_reg_number"
                                    class="w-full mt-2 p-3 border rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                    id="kind_of_business" disabled>
                            </div>
                        </div>

                        <div class="col-span-3 sm:col-span-2 md:col-span-1">
                            <div class="flex flex-col">
                                <label for="plate">Cedula</label>
                                <input type="text" :value="business_info.cedula"
                                    class="w-full mt-2 p-3 border rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                    id="plate" disabled>
                            </div>
                        </div>
                        <div class="col-span-3 sm:col-span-2 md:col-span-1">
                            <div class="flex flex-col">
                                <label for="payment_cycle">Payment Cycle</label>
                                <input type="text" :value="getPaymentCycle(business_info.payment_cycle)"
                                    class="w-full mt-2 p-3 border rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                    id="payment_cycle" disabled>
                            </div>
                        </div>
                        <div class="col-span-3 sm:col-span-2 md:col-span-1">
                            <div class="flex flex-col">
                                <label for="payment_cycle">Area</label>
                                <input type="text" :value="business_info.establishment_unit.establishment.area.name"
                                    class="w-full mt-2 p-3 border rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                    id="payment_cycle" disabled>
                            </div>
                        </div>
                        <div class="col-span-3 sm:col-span-2 md:col-span-1">
                            <div class="flex flex-col">
                                <label for="payment_cycle">Section</label>
                                <input type="text" :value="business_info.establishment_unit.establishment.name"
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
                    <div class="my-4 text-center cursor-pointer"
                        :class="isImageShow ? 'text-purple-600' : 'text-orange-600'"
                        @click="isImageShow = !isImageShow">-- {{ isImageShow ? 'Hide' : 'Show' }} Images --</div>

                    <div v-if="isImageShow" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                        <div class="relative group cursor-pointer"
                            @click="openImage(business_info.requirement_image.cedula)">
                            <img :src="'/images/business/' + business_info.requirement_image.cedula" alt="Gallery Image"
                                class="object-cover rounded-lg shadow-md transition-transform duration-300 hover:scale-105" />
                        </div>
                        <div class="relative group cursor-pointer"
                            @click="openImage(business_info.requirement_image.brgy_clearance)">
                            <img :src="'/images/business/' + business_info.requirement_image.brgy_clearance"
                                alt="Gallery Image"
                                class="object-cover rounded-lg shadow-md transition-transform duration-300 hover:scale-105" />
                        </div>
                        <div class="relative group cursor-pointer"
                            @click="openImage(business_info.requirement_image.pmo_ceedo_clearance)">
                            <img :src="'/images/business/' + business_info.requirement_image.pmo_ceedo_clearance"
                                alt="Gallery Image"
                                class="object-cover rounded-lg shadow-md transition-transform duration-300 hover:scale-105" />
                        </div>
                        <div class="relative group cursor-pointer"
                            @click="openImage(business_info.requirement_image.dti_cert)">
                            <img :src="'/images/business/' + business_info.requirement_image.dti_cert"
                                alt="Gallery Image"
                                class="object-cover rounded-lg shadow-md transition-transform duration-300 hover:scale-105" />
                        </div>
                        <div class="relative group cursor-pointer"
                            @click="openImage(business_info.requirement_image.medical_cert)">
                            <img :src="'/images/business/' + business_info.requirement_image.medical_cert"
                                alt="Gallery Image"
                                class="object-cover rounded-lg shadow-md transition-transform duration-300 hover:scale-105" />
                        </div>
                        <div class="relative group cursor-pointer"
                            @click="openImage(business_info.requirement_image.business_permit)">
                            <img :src="'/images/business/' + business_info.requirement_image.business_permit"
                                alt="Gallery Image"
                                class="object-cover rounded-lg shadow-md transition-transform duration-300 hover:scale-105" />
                        </div>

                    </div>


                </div>

                <!-- Payment History Table -->
            <div class="my-4">
                <hr>
              <h2 class="text-xl font-semibold my-4 text-center">Payment History</h2>
              <table class="min-w-full bg-white border rounded">
                <thead>
                  <tr>
                    <th class="px-4 py-2 text-left">Days</th>
                    <th class="px-4 py-2 text-left">Due Date</th>
                    <th class="px-4 py-2 text-left">Amount</th>
                    <th class="px-4 py-2 text-left">Penalty</th>
                    <th class="px-4 py-2 text-left">Paid At</th>
                    <th class="px-4 py-2 text-left">Remark</th>
                    <th class="px-4 py-2 text-left">Status</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="payment in business_info.payment" :key="payment.id">
                    <td class="px-4 py-2"> {{  payment.days }}</td>
                    <td class="px-4 py-2"> {{ new Date(payment.due_date).toLocaleDateString() }}</td>
                    <td class="px-4 py-2"> {{ '₱' + payment.amount }}</td>
                    <td class="px-4 py-2"> {{ '₱' + payment.penalty }}</td>
                    <td class="px-4 py-2">{{ new Date(payment.paid_at).toLocaleString() }}</td>
                    <td class="px-4 py-2" :class="payment.remark == 'Not Yet' ? '' : 'text-red-700'"> {{ payment.remark == 'Not Yet' ? 'Advanced' : payment.remark }}</td>
                    <td class="px-4 py-2 badge badge-primary">Paid</td>
                  </tr>
                  <tr v-if="business_info.payment.length <= 0">
                    <td class="px-4 py-2 text-center" colspan="7">No Payment Activity</td>
                  </tr>
                </tbody>
              </table>
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

            <!-- Additional Style for Buttons and Form -->
            <input class="modal-state" id="statusModal" type="checkbox" />
            <div class="modal !h-full" style="z-index: 99999; margin-top: 0;">
                <label class="modal-overlay"></label>
                <div class="modal-content">
                    <label for="statusModal" class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</label>
                    <h2 class="text-xl mb-3">Business Status</h2>

                    <div v-if="business_info.status == 0 || business_info.status == 1">
                        <div class="flex my-2 justify-between gap-5">
                            <span class="font-bold">Status:</span>
                            <span class="badge"
                                :class="{ 'badge-success': business_info.status == 1, 'badge-warning': business_info.status == 0, 'badge-error': business_info.status == 2 || business_info.status == 3 }">{{
                                    getStatusName(business_info.status) }}</span>
                        </div>

                        <div class="flex my-2 justify-between gap-5">
                            <span class="font-bold">Date Approved:</span>
                            <span class="">{{ business_info.date_approved ? business_info.date_approved
                                : '--'
                                }}</span>
                        </div>
                    </div>

                    <div v-if="business_info.status == 2">
                        <div class="flex my-2 justify-between gap-5">
                            <span class="font-bold">Status:</span>
                            <span class="badge"
                                :class="{ 'badge-success': business_info.status == 1, 'badge-warning': business_info.status == 0, 'badge-error': business_info.status == 2 || business_info.status == 3 }">{{
                                    getStatusName(business_info.status) }}</span>
                        </div>

                        <div class="flex my-2 justify-between gap-5">
                            <span class="font-bold">Date Rejected:</span>
                            <span class="">{{ business_info.date_rejected ? business_info.date_rejected
                                : '--'
                                }}</span>
                        </div>
                    </div>

                    <div v-if="business_info.status == 3">
                        <div class="flex my-2 justify-between gap-5">
                            <span class="font-bold">Status:</span>
                            <span class="badge"
                                :class="{ 'badge-success': business_info.status == 1, 'badge-warning': business_info.status == 0, 'badge-error': business_info.status == 2 || business_info.status == 3 }">{{
                                    getStatusName(business_info.status) }}</span>
                        </div>

                        <div class="flex my-2 justify-between gap-5">
                            <span class="font-bold">Date Closed:</span>
                            <span class="">{{ business_info.date_closed ? business_info.date_closed :
                                '--'
                                }}</span>
                        </div>
                    </div>

                    <div v-if="business_info.status == 2 || business_info.status == 3">
                        <span class="font-bold">Remarks:</span>
                        <textarea name="" id="" class="w-full text-start" cols="30" disabled
                            v-if="business_info.status == 2 || business_info.status == 3"
                            :value="business_info.remarks">
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