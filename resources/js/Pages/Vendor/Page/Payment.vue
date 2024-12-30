<script setup>
import Dashboard from '../Dashboard.vue';
import { Head, usePage, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

// Get data from Inertia page props
const payments = usePage().props.payments;
const business = usePage().props.business;

// Extract data
const nextPayment = computed(() => payments[0]); // First payment object (next payment)
const overduePayments = computed(() => payments.slice(1)); // Overdue payments are all payments after the first one
const paymentHistory = usePage().props.paymentHistory;
</script>

<template>

  <Head title="Dashboard" />
  <Dashboard>
    <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200 text-center md:text-start">
      My Payments
    </h2>
    <div v-if="business">
      <div v-if="business.status == 1 || business.status == 3">
        <div v-if="payments" class="container mx-auto p-6 bg-white rounded-lg shadow-lg">
          <div class="container mx-auto-grid">
            <!-- Next Payment Card -->
            <div class="mb-6 p-4 bg-blue-100 rounded shadow">
              <h2 class="text-xl font-semibold">Next Payment</h2>
              <p class="text-gray-700">
                <strong>Total Amount: </strong> {{ '₱' + nextPayment.amount }} <br>
                <strong>Due Date:</strong> {{ new Date(nextPayment.due_date).toLocaleDateString() }} <br>
                <span>Daily Rate: </span> {{ '₱' + nextPayment.rate }} <br>
                <span>No. of Days: </span>{{ nextPayment.days }}
              </p>
            </div>

            <!-- Unpaid Payments (Next Payment + Overdue Combined) -->
            <div class="mb-5">
              <h2 class="text-xl font-semibold mb-4">Unpaid Payments</h2>
              <table class="min-w-full bg-white border rounded">
                <thead>
                  <tr>
                    <th class="px-4 py-2 text-left">No. of Days</th>
                    <th class="px-4 py-2 text-left">Due Date</th>
                    <th class="px-4 py-2 text-left">Amount</th>
                    <th class="px-4 py-2 text-left">Status</th>
                  </tr>
                </thead>
                <tbody>
                  <!-- Display next payment -->
                  <tr>
                    <td class="px-4 py-2"> {{nextPayment.days }}</td>
                    <td class="px-4 py-2">{{ new Date(nextPayment.due_date).toLocaleDateString() }}</td>
                    <td class="px-4 py-2"> {{ '₱' + nextPayment.amount }}</td>
                    <td><span class="badge" :class="{ 'badge-error': nextPayment.status === 'Overdue' }">{{
                      nextPayment.status
                    }}</span></td>
                  </tr>
                  <!-- Loop through overdue payments -->
                  <tr v-for="payment in overduePayments" :key="payment.due_date">
                    <td class="px-4 py-2"> {{ payment.days }}</td>
                    <td class="px-4 py-2">{{ new Date(payment.due_date).toLocaleDateString() }}</td>
                    <td class="px-4 py-2"> {{ '₱' + payment.amount }}</td>
                    <td><span class="badge" :class="{ 'badge-error': payment.status != 'Not Yet' }">{{ payment.status
                        }}</span></td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Payment History Table -->
            <div class="">
              <h2 class="text-xl font-semibold mb-4">Payment History</h2>
              <table class="min-w-full bg-white border rounded">
                <thead>
                  <tr>
                    <th class="px-4 py-2 text-left">Amount</th>
                    <th class="px-4 py-2 text-left">Paid At</th>
                    <th class="px-4 py-2 text-left">Status</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="payment in paymentHistory" :key="payment.id">
                    <td class="px-4 py-2"> {{ '₱' + payment.amount }}</td>
                    <td class="px-4 py-2">{{ new Date(payment.paid_at).toLocaleDateString() }}</td>
                    <td class="px-4 py-2 badge badge-primary">Paid</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div v-if="business.status == 0" class="flex flex-col justify-center items-center mt-20 md:mt-0"
          style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%)">
          <span class="text-2xl mb-3 text-justify">Your applicaction is currently under review. You will receive an SMS from CEEDO once your application is approved or rejected!</span>
        </div>
    </div>
    <div v-else class="flex flex-col justify-center items-center text-center w-full"
      style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%)">
      <span class="text-2xl mb-3">You currently have no stalls applied!</span>
      <Link class="btn btn-primary rounded-none" :href="route('services')">Apply Now!</Link>
    </div>
  </Dashboard>
</template>
