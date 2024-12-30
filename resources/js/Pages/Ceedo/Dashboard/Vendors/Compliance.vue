<script setup>
import Layout from '../../Layout.vue';
import { Head } from '@inertiajs/vue3';

function gotoRenewal(){
    window.location.href = route('ceedo.renewal');
}

function gotoPaymentDue(){
    window.location.href = route('ceedo.payment-due');
}

</script>

<template>

  <Head title="Dashboard" />
  <Layout>
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-semibold text-center text-gray-800 mb-6">Compliance Tracker</h1>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-6">
            <!-- Business Permit Renewal Card -->
            <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition duration-300 ease-in-out transform hover:scale-105" @click="gotoRenewal()">
                <a href="#" class="block">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-blue-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14 2l7 7-7 7M5 3l7 7-7 7"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-800">Business Permit Renewal</h3>
                            <p class="mt-2 text-gray-600">Monitor Vendor's Business Permit Status.</p>
                        </div>
                    </div>
                    <div class="mt-4 text-blue-600 font-semibold hover:underline" >Go to Renewal</div>
                </a>
            </div>

            <!-- Payment Dues Card -->
            <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition duration-300 ease-in-out transform hover:scale-105" @click="gotoPaymentDue()">
                <a href="#" class="block">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-green-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-800">Payment Dues</h3>
                            <p class="mt-2 text-gray-600">Check and settle vendor's outstanding payments to avoid penalties.</p>
                        </div>
                    </div>
                    <div class="mt-4 text-green-600 font-semibold hover:underline" >Go to Payment Dues</div>
                </a>
            </div>
        </div>
    </div>
  </Layout>
</template>