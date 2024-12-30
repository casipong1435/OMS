<script setup>
import Layout from '../Layout.vue';
import { Head, usePage, useForm } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';

defineProps({
    business: Object,
    payments: Array
});

const isOpenPayModal = ref(false);
const selectedPayment = ref([0]);
const isAllPaymentSelected = ref(false);
// Function to navigate back
function goBack() {
    window.history.back();
}

const getFullName = () => {
    return usePage().props.business.profile.first_name + " " + (usePage().props.business.profile.middle_name != null ? usePage().props.business.profile.middle_name + " " : "") + usePage().props.business.profile.last_name;
};

function getPaymentCycle(cycle) {
    switch (cycle) {
        case 0:
            return "Monthly";
        case 1:
            return "Quarterly";
        case 2:
            return "Bi-Annual";
        case 3:
            return "Annual";
    }
}

const form = useForm({
    vendor: getFullName(),
    business_id: usePage().props.business.id,
    name: usePage().props.business.name,
    selectedPayment: selectedPayment.value.map(index => usePage().props.payments[index]),
});

function pay() {
    form.post(route('treasurer.pay'),
        {
            onSuccess: (page) => {
                Swal.fire({
                    toast: true,
                    icon: 'success',
                    position: 'top-end',
                    showConfirmButton: true,
                    title: page.props.flash.success,
                });
                isOpenPayModal.value = false
            }
        });
}

// Ensure index 0 is always selected
function updateSelection() {
    // Get the last selected index
    const lastIndex = selectedPayment.value[selectedPayment.value.length - 1];

    if (lastIndex > 0) {
        // If any index greater than 0 is selected, select all indices from 0 to the last selected index
        selectedPayment.value = Array.from({ length: lastIndex + 1 }, (_, index) => index);
    }
}

function handleSelectPayment(index) {
    // If index 0 is selected, ensure it's always included in the selection
    if (index === 0) {
        if (!selectedPayment.value.includes(0)) {
            selectedPayment.value.push(0);
        }
    } else {
        if (selectedPayment.value.includes(0)) {
            if (!selectedPayment.value.includes(index)) {
                selectedPayment.value.push(index);
            }
        }
    }

    updateSelection();
}

// Toggle Select All
function handleSelectAllPayment() {
    if (isAllPaymentSelected.value) {
        selectedPayment.value = []; // Deselect all
    } else {
        selectedPayment.value = Array.from({ length: usePage().props.payments.length }, (_, index) => index); // Select all
    }

    isAllPaymentSelected.value = !isAllPaymentSelected.value;
}

// Watch to update selections dynamically
watch(selectedPayment, (value) => {
    // Always ensure 0 is included in the selection
    if (!value.includes(0)) {
        selectedPayment.value.push(0);
    }

    // Ensure the selectedPayment array only contains valid indices
    form.selectedPayment = selectedPayment.value
        .map(index => usePage().props.payments[index])
        .filter(payment => payment !== undefined); // Filter out undefined payments
});

// Computed property to calculate the sum of selected amounts
const selectedSum = computed(() => {
    return selectedPayment.value.reduce((total, index) => {
        const payment = usePage().props.payments[index]; // Get the payment at the current index

        // If the payment is undefined, skip this index
        if (!payment) return total;

        // Remove commas from amount before parsing it
        const amountString = payment.amount.replace(/,/g, ''); // Remove commas
        const amount = parseFloat(amountString); // Convert to a number

        // Ensure it's a valid number
        if (!isNaN(amount)) {
            return total + amount; // Add to total if it's a valid number
        }

        return total; // If the amount is invalid, return the current total
    }, 0); // Start with a total of 0
});



</script>

<template>

    <Head title="Vendors With Unpaid Payments" />
    <Layout>
        <div class="container px-6 mx-auto grid">
            <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
                Unpaid Payments
            </h2>

            <!-- Breadcrumbs -->
            <div class="breadcrumbs text-sm mb-5">
                <ul>
                    <li>
                        <a @click="goBack()">Compliance</a>
                    </li>
                    <li>
                        <a @click="goBack()">Payment Dues</a>
                    </li>
                    <li>Payment Portal</li>
                </ul>
            </div>

            <div class="grid grid-cols-1 gap-4 bg-gray-100">
                <div class="col-span-1 p-6 grid grid-cols-2 gap-2">
                    <div class="col-span-2 md:col-span-1">
                        <div class="w-full bg-white rounded-lg shadow-lg text-start p-5">
                            <h2 class="text-2xl font-bold text-gray-800 mb-4">Business Info</h2>
                            <div class="space-y-4">
                                <div>
                                    <span class="block text-sm font-medium text-gray-500">Business Name</span>
                                    <span class="text-lg font-semibold text-gray-800">{{ $page.props.business.name
                                        }}</span>
                                </div>
                                <div>
                                    <span class="block text-sm font-medium text-gray-500">Kind of Business</span>
                                    <span class="text-lg font-semibold text-gray-800">{{
                                        $page.props.business.kind_of_business }}</span>
                                </div>
                                <div>
                                    <span class="block text-sm font-medium text-gray-500">Permit Number</span>
                                    <span class="text-lg font-semibold text-gray-800">{{
                                        $page.props.business.permit_number }}</span>
                                </div>
                                <div>
                                    <span class="block text-sm font-medium text-gray-500">Owner</span>
                                    <span class="text-lg font-semibold">{{ getFullName() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-span-2 md:col-span-1">
                        <div class="w-full bg-white rounded-lg shadow-lg text-start p-5">
                            <div class="flex justify-between items-center">
                                <h2 class="text-2xl font-bold text-gray-800 mb-4">Next Payment</h2>
                                <button v-if="usePage().props.payments.length > 0" type="button" class="btn btn-primary rounded-none"
                                    @click="isOpenPayModal = true">Pay Now</button>
                            </div>
                            <div class="space-y-4">
                                <div>
                                    <span class="block text-sm font-medium text-gray-500">Payment Cycle</span>
                                    <span class="text-lg font-semibold text-gray-800">{{
                                        getPaymentCycle($page.props.business.payment_cycle) }}</span>
                                </div>
                                <div>
                                    <span class="block text-sm font-medium text-gray-500">Total Amount</span>
                                    <span class="text-lg font-semibold text-gray-800">{{ '₱' + selectedSum }}</span>
                                </div>
                                <div>
                                    <span class="block text-sm font-medium text-gray-500">Due Date</span>
                                    <span class="text-lg font-semibold text-gray-800">{{ usePage().props.payments.length > 0 ? new
                                        Date(payments[0].due_date).toLocaleDateString() : 'Not Yet' }}</span>
                                </div>
                                <div v-if="usePage().props.payments.length > 0">
                                    <span class="block text-sm font-medium text-gray-500">Status</span>
                                    <span class="text-lg font-semibold"
                                        :class="{ 'text-red-500': payments[0].status != 'Not Yet' }">{{ payments[0].status
                                        }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="w-full overflow-auto">

                    <div class="col-span-1 p-6 ">
                        <div class="font-bold text-2xl mb-2">Payment Cycle</div>
                        <table class="w-full w-full bg-white rounded-lg shadow-lg py-2 space-y-2  px-4 text-center">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 text-start">
                                        <input type="checkbox" id="pay_all" class="me-2" :check="isAllPaymentSelected"
                                            @change="handleSelectAllPayment()">
                                        <label for="pay_all">Pay All</label>
                                    </th>
                                    <th class="px-4 py-3 text-start">No. of Days</th>
                                    <th class="px-4 py-3 text-start">Due Date</th>
                                    <th class="px-4 py-3 text-start">Amount</th>
                                    <th class="px-4 py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(payment, index) in payments" :key="index">
                                    <td class="px-4 py-3 text-start">
                                        <input type="checkbox" :id="index" :value="index" v-model="selectedPayment"
                                            @change="handleSelectPayment(index)">
                                    </td>
                                    <td class="px-4 py-3 text-start">{{ payment.days }}</td>
                                    <td class="px-4 py-3 text-start">{{ new Date(payment.due_date).toLocaleDateString()
                                        }}</td>
                                    <td class="px-4 py-3 text-start">{{ '₱' + payment.amount }}</td>
                                    <td class="badge px-4 py-3 text-start"
                                        :class="{ 'badge-error': payment.status != 'Not Yet' }">{{
                                            payment.status }}</td>
                                </tr>
                                <tr>
                                    <td v-if="payments.length <= 0" class="px-4 py-3 text-center" colspan="4">No Payments Yet!</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        <input class="modal-state" id="isOpenPayModal" type="checkbox" v-model="isOpenPayModal" />
        <div class="modal">
            <label class="modal-overlay" for="isOpenPayModal"></label>
            <div class="modal-content flex flex-col gap-5">
                <label for="isOpenPayModal" class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</label>
                <h2 class="text-xl my-5">Would you like to confirm transaction?</h2>

                <div class="flex gap-3">
                    <button class="btn btn-primary btn-block" @click="pay()">Confirm</button>
                    <button class="btn btn-block" @click="isOpenPayModal = false">Cancel</button>
                </div>
            </div>
        </div>
    </Layout>
</template>
