<script setup>
import Dashboard from '../Dashboard.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, watch, reactive, onMounted } from 'vue';

defineProps({
    my_businesses: Array
});

// Function to navigate back
function goBack() {
    window.history.back();
}

const searchQuery = ref('');
const filteredBusiness = ref([...usePage().props.my_businesses]);

// Method to filter transactions based on the search query
const searchBusiness = () => {
    filteredBusiness.value = usePage().props.my_businesses.filter(business =>
        business.name.toLowerCase().includes(searchQuery.value.toLowerCase()));
};

watch(searchQuery, (value) => {
    if (value === '') {
        filteredBusiness.value = usePage().props.my_businesses;
    }
});

function gotoBusiness(id) {
    window.location.href = route('user.business', id);
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

function isExpired(date){
   return new Date(date) < new Date();
}

</script>

<template>

    <Head title="Dashboard" />
    <Dashboard>
        <div class="container px-6 mx-auto grid">
            <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
                My Businesses
            </h2>

            <!-- Breadcrumbs -->
            <div class="breadcrumbs text-sm">
                <ul>
                    <li>
                        <a @click="goBack()">Business List</a>
                    </li>
                    <li>My Business</li>
                </ul>
            </div>


            <!-- Table -->
            <div class="w-full overflow-hidden rounded-lg shadow-xs">
                <div class="flex justify-end p-4">
                    <input type="text" placeholder="Search..." v-model="searchQuery"
                        class="border rounded-md px-3 py-2 text-gray-700" />
                    <button @click="searchBusiness"
                        class="ml-2 px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                        Search
                    </button>
                </div>
                <div class="w-full overflow-x-auto">
                    <div class="mb-2 p-2"></div>

                    <div class="overflow-x-auto mb-4">
                        <!-- Table for Each Business -->
                        <table
                            class="min-w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg shadow-md">

                            <!-- Table Header (Appears once) -->
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr
                                    class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700">
                                    <th class="px-4 py-3">ID</th>
                                    <th class="px-4 py-3">Business Name</th>
                                    <th class="px-4 py-3">Business Plate</th>
                                    <th class="px-4 py-3">Business Permit</th>
                                    <th class="px-4 py-3">Permit Expirationd Date</th>
                                    <th class="px-4 py-3">Remark</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <!-- Table Content (Business Data) -->
                            <tbody>
                                <tr v-for="(business, index) in filteredBusiness" :key="business.id"
                                    class="cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                                    @click="gotoBusiness(business.id)">
                                    <td class="px-4 py-3">{{ index + 1 }}</td>
                                    <td class="px-4 py-3">{{ business.name }}</td>
                                    <td class="px-4 py-3">{{ business.plate }}</td>
                                    <td class="px-4 py-3">{{ business.permit_number }}</td>
                                    <td class="px-4 py-3">{{ new Date(business.permit_expiration_date).toLocaleDateString() }}</td>
                                    <td class="px-4 py-3">
                                        <span class="badge" :class="isExpired(business.permit_expiration_date) ? 'badge-error' : 'badge-success'">
                                            {{ isExpired(business.permit_expiration_date) ? 'Expired':'Valid'}}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3" :class="{ 'text-red-700': business.status == 3 || business.status == 2, 'text-green-700': business.status == 1, 'text-warning-700': business.status == 0 }">{{
                                        getStatusName(business.status) }}</td>
                                </tr>
                            </tbody>

                        </table>
                    </div>
                    <div v-if="filteredBusiness.length <= 0">
                        <div class="text-center text-2xl">No Data Found</div>
                    </div>
                </div>
            </div>
        </div>
    </Dashboard>
</template>