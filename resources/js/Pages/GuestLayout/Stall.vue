<script setup>
import Container from './Container.vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import { ref, computed, reactive, watch, onMounted } from 'vue';

defineProps({
    errors: Object,
    establishment_units: Array,
    establishment_info: Array,
    inBusiness: Array
});

const dailyRate = ref(usePage().props.establishment_info.rate);

const isViewDrawerOpen = ref(false);
const stallID = ref(null);
const stallImages = ref([]);
const isApplyModalOpen = ref(false);
const stallStatus = ref(null);

const getStatusInfo = (status) => {
    switch (status) {
        case 0:
            return 'Acquired';
        case 1:
            return 'Available';
        case 2:
            return 'On Application';
        case 3:
            return 'On Maintenance';
        case 4:
            return 'Archived';
    }
};

function toggleModal(establishment) {
    form.establishment_unit_id = establishment.id;
    isViewDrawerOpen.value = true;
    stallStatus.value = establishment.status;
    getStallData(establishment);

}

function closeApplyModal() {
    resetFields();
    isApplyModalOpen.value = false;
    usePage().props.errors = [{}];
}

const getStallData = (establishment) => {
    stallID.value = establishment.id;
    stallImages.value = establishment.establishment_images;
};

const currentIndex = ref(0)

function nextSlide() {
    currentIndex.value = (currentIndex.value + 1) % stallImages.value.length
}

function prevSlide() {
    currentIndex.value =
        (currentIndex.value - 1 + stallImages.value.length) % stallImages.value.length
}

function goToSlide(index) {
    currentIndex.value = index
}

const getFullName = () => {
    return (
        usePage().props.auth.profile.first_name +
        " " +
        (usePage().props.auth.profile.middle_name != null ? usePage().props.auth.profile.middle_name + " " : "") +
        usePage().props.auth.profile.last_name
    );
};

const monthlyRate = computed(() => (dailyRate.value * 30).toFixed(2)); // Assume 30 days in a month
const quarterlyRate = computed(() => (dailyRate.value * 30 * 3).toFixed(2)); // 3 months
const biAnnualRate = computed(() => (dailyRate.value * 30 * 6).toFixed(2)); // 6 months
const annualRate = computed(() => (dailyRate.value * 365).toFixed(2)); // 1 year (365 days)


const form = reactive({
    establishment_unit_id: null,
    kind_of_business: null,
    name: null,
    plate: null,
    permit_number: null,
    dti_reg_number: null,
    payment_cycle: null,
    cedula: null,

    cedula_cert: null,
    brgy_clearance: null,
    pmo_ceedo_clearance: null,
    dti_cert: null,
    medical_cert: null,
    business_permit: null,
    permit_expiration_date:null

});

function handleCedulaImage(event) {
    form.cedula_cert = event.target.files[0];
}
function handleBarangayClearanceImage(event) {
    form.brgy_clearance = event.target.files[0];
}
function handleCeedoCLearanceImage(event) {
    form.pmo_ceedo_clearance = event.target.files[0];
}
function handleDtiImage(event) {
    form.dti_cert = event.target.files[0];
}
function handleMedicalImage(event) {
    form.medical_cert = event.target.files[0];
}
function handleBusinesspermitImage(event) {
    form.business_permit = event.target.files[0];
}

function submitApplication() {
    router.post(route('user.apply'), form, {
        onSuccess: page => {
            Swal.fire({
                toast: true,
                icon: 'success',
                position: 'top-end',
                showConfirmButton: true,
                title: page.props.flash.success,
            });
            closeApplyModal();
        }
    })
}

const rate = ref('');

watch(() => form.payment_cycle, (value) => {
    if (value) {
        switch (value) {
            case '0':
                rate.value = monthlyRate.value;

                break;
            case '1':
                rate.value = quarterlyRate.value;

                break;
            case '2':
                rate.value = biAnnualRate.value;

                break;
            case '3':
                rate.value = annualRate.value;
                console.log(rate.value);
                break;
            default:
                rate.value = '';

                break;
        }
    }
});


function resetFields() {
    form.establishment_unit_id = null;
    form.kind_of_business = null;
    form.name = null;
    form.plate = null;
    form.permit_number = null;
    form.dti_reg_number = null;
    form.payment_cycle = null;
    form.cedula = null;

    form.cedula_cert = null;
    form.brgy_clearance = null;
    form.pmo_ceedo_clearance = null;
    form.dti_cert = null;
    form.medical_cert = null;
    form.business_permit = null;
    form.permit_expiration_date = null;
}

function getBusinessStatus(status){
    switch (status) {
        case 0:
            return 'You can only apply and acquire one stall';
        case 1:
            return 'Application Pending';
        case 2:
            return 'Has already acquired';
    }
}
</script>

<template>
    <div class="relative" id="services">
        <div aria-hidden="true" class="absolute inset-0 grid grid-cols-2 -space-x-52 opacity-40 dark:opacity-20">
            <div class="blur-[106px] h-56 bg-gradient-to-br from-green-700 to-purple-400 dark:from-blue-700"></div>
            <div class="blur-[106px] h-32 bg-gradient-to-r from-cyan-400 to-sky-300 dark:to-indigo-600"></div>
        </div>
        <Container>
            <div class="relative ml-auto">
                <div class="text-2xl mb-10 text-center">{{ $page.props.establishment_info.name }}</div>
                <div class="mb-2">
                    <div class="breadcrumbs text-sm">
                        <ul>
                            <li>
                                <Link :href="route('services')" class="text-gray-800">
                                Area</Link>
                            </li>
                            <li>
                                <Link
                                    :href="route('guest.establishment', { name: $page.props.establishment_info.area.name, id: $page.props.establishment_info.area.id })"
                                    class="text-gray-800">
                                {{ $page.props.establishment_info.area.name }}</Link>
                            </li>
                            <li>{{ $page.props.establishment_info.name }}</li>
                        </ul>
                    </div>
                </div>

                <!-- CTA -->
                <hr>
                <div class="my-5 flex justify-end items-center gap-2">

                    <label for="costModal" class="text-blue-500 cursor-pointer flex flex-row">
                        <span class="me-2">Costs</span>
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>

                        </span>
                    </label>

                    <label for="requirementModal" class="cursor-pointer flex flex-row btn btn-primary">
                        <span class="me-2">Requirements</span>
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                            </svg>
                        </span>
                    </label>

                </div>
                
                <div class="grid md:grid-cols-2 lg:grid-cols-3 mt-5 gap-y-3">
                    <div class="max-w-xs bg-gray-200 col-span-2 md:col-span-1 sm:col-span-1 rounded overflow-hidden hover:shadow-lg hover:shadow-blue-200 relative"
                        v-for="establishment in $page.props.establishment_units" :key="establishment.id"
                        style="cursor: pointer"
                        :class="{ 'bg-gray-400': establishment.status == 2, 'disbabled opacity-50 pointer-events-none': establishment.status == 3 || establishment.status != 1}"
                        @click="establishment.status != 1 ? '' : toggleModal(establishment)">

                        <div v-if="establishment.status == 3" class="w-full h-full absolute flex justify-center items-center bg-gray-800 opacity-75 text-white">
                            <h2>Under Maintenance</h2>
                        </div>
                        <div class="px-6 py-4 flex justify-between items-center">
                            <span><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                                </svg>
                            </span>
                            <div class="font-bold text-xl mb-2">Stall #{{ establishment.id }}</div>
                        </div>

                        <div class="px-6  flex justify-end gap-x-5 items-center mb-2">

                            <div class="flex justify-between items-center gap-2">
                                <div class="flex flex-row items-center">
                                    <span class="inline text-sm font-semibold text-gray-700 mr-2"><svg
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                                        </svg>

                                    </span>
                                    <span v-if="establishment.status != 3" class="text-sm text-gray-700"
                                        :class="{ 'text-red-700': establishment.status != 1, 'text-green-700': establishment.status == 1 }">{{
                                            getStatusInfo(establishment.status) }}</span>
                                </div>
                            </div>
                            
                        </div>
                        
                    </div>
                    <div v-if="$page.props.establishment_units <= 0" class="col-span-12 text-center text-xl">
                        No Establishment Added Yet!
                    </div>
                </div>

                <!--Cost Modal-->
                <input class="modal-state" id="costModal" type="checkbox" />
                <div class="modal">
                    <label class="modal-overlay"></label>
                    <div class="modal-content flex flex-col gap-5">
                        <label for="costModal" class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</label>
                        <h2 class="text-xl">Cost Calculation</h2>
                        <div class="p-2">
                            <div class="flex flex-row items-center justify-between mb-2">
                                <span class="me-2">Daily Rate: </span>
                                <span>₱ {{ dailyRate }}</span>
                            </div>
                            <div class="flex flex-row items-center justify-between mb-2">
                                <span class="me-2">Monthly Rate: </span>
                                <span>₱ {{ monthlyRate }}</span>
                            </div>
                            <div class="flex flex-row items-center justify-between mb-2">
                                <span class="me-2">Quarterly Rate: </span>
                                <span>₱ {{ quarterlyRate }}</span>
                            </div>
                            <div class="flex flex-row items-center justify-between mb-2">
                                <span class="me-2">Bi-Annual Rate: </span>
                                <span>₱ {{ biAnnualRate }}</span>
                            </div>
                            <div class="flex flex-row items-center justify-between mb-2">
                                <span class="me-2">Annual Rate: </span>
                                <span>₱ {{ annualRate }}</span>
                            </div>
                        </div>
                        <div class="flex">
                            <label for="costModal" class="btn btn-block">Close</label>
                        </div>
                    </div>
                </div>

                <!--Requirement Modal-->
                <input class="modal-state" id="requirementModal" type="checkbox" />
                <div class="modal">
                    <label class="modal-overlay"></label>
                    <div class="modal-content flex flex-col gap-5">
                        <label for="requirementModal"
                            class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</label>
                        <h2 class="text-xl">Requirements</h2>
                        <div class="text-start mb-2">
                            These are the requirements for the stall application.
                        </div>
                        <ul>
                            <li>- Barangay Clearance</li>
                            <li>- PMO & CEEDO Clearance</li>
                            <li>- DTI Certificate</li>
                            <li>- Health or Medical Certificate</li>
                            <li>- Voter's Affidavit or Cedula</li>
                            <li>- CTO Form for Business Permit</li>

                        </ul>
                        <div class="flex">
                            <label for="requirementModal" class="btn btn-block">Close</label>
                        </div>
                    </div>
                </div>


            </div>
        </Container>
    </div>



    <input type="checkbox" id="modalView" class="drawer-toggle" v-model="isViewDrawerOpen" />
    <div class="drawer drawer-bottom h-3/4">
        <div class="drawer-content relative">
            <label for="modalView" class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</label>
            <div class="relative w-full max-w-3xl mx-auto overflow-hidden" v-if="stallImages.length > 0">
                <div class="flex transition-transform duration-500 ease-in-out"
                    :style="{ transform: `translateX(-${currentIndex * 100}%)` }">
                    <!-- Carousel Images -->
                    <div v-for="(unit, index) in stallImages" :key="index" class="w-full flex-shrink-0">
                        <img :src="'/images/Areas/Establishment/' + unit.image" alt="carousel image"
                            class="w-full h-64 object-cover rounded-md shadow-lg" />
                    </div>
                </div>

                <!-- Left and Right Navigation Buttons -->
                <button
                    class="absolute top-1/2 transform -translate-y-1/2 left-2 bg-gray-800 text-white rounded-full p-2"
                    @click="prevSlide">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>

                <button
                    class="absolute top-1/2 transform -translate-y-1/2 right-2 bg-gray-800 text-white rounded-full p-2"
                    @click="nextSlide">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>

                <!-- Carousel Indicators -->
                <div class="absolute bottom-2 left-0 right-0 flex justify-center space-x-2">
                    <span v-for="(unit, index) in stallImages" :key="index" @click="goToSlide(index)" :class="{
                        'w-3 h-3 bg-white rounded-full cursor-pointer': true,
                        'bg-gray-800': currentIndex === index,
                    }"></span>
                </div>
            </div>
            <div v-else class="flex justify-center items-center p-2">
                <div class="text-4xl text-center">No Images Added Yet!</div>
            </div>
            <div class="py-2"></div>
            <hr>
            <div class="text-center py-5">Stall ID: {{ stallID }}</div>
        </div>
        <div class="absolute bottom-5 right-5" v-if="!usePage().props.inBusiness">
            <label for="applyModal" class="btn bg-green-600 text-white">Apply Now</label>
        </div>
        <div class="absolute bottom-5 right-5" v-if="usePage().props.inBusiness">
            <div class="text-lg">{{ getBusinessStatus(usePage().props.inBusiness.status) }}</div>
        </div>
    </div>

    <input class="modal-state" id="applyModal" type="checkbox" v-model="isApplyModalOpen" />
    <div class="modal !h-full w-screen" style="z-index: 99999; margin-top: 0">
        <label class="modal-overlay"></label>
        <div class="modal-content">
            <label for="applyModal" class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</label>
            <h2 class="text-xl mb-3">Application</h2>
            <div v-if="!$page.props.auth.user" class="flex flex-col gap-5">
                You need to have an account to apply.
                <div class="flex gap-3">
                    <Link class="btn bg-green-600 btn-block text-white" :href="route('login')">Login</Link>
                    <Link class="btn btn-block" :href="route('register')">Register</Link>
                </div>
            </div>
            <div v-else class="flex flex-col gap-5">
                <div v-if="$page.props.auth.profile.region" class="flex flex-col gap-5">
                    <div class="overflow-hidden flex justify-center mb-2">
                        <img v-if="usePage().props.auth.image" class="object-cover w-20 h-20 rounded-full"
                            :src="'/images/clients/' + usePage().props.auth.image" alt="" aria-hidden="true" />
                        <img v-else class="object-cover w-20 h-20 rounded-full" src="/images/avatars/avatar.png" alt=""
                            aria-hidden="true" />
                    </div>
                    <div class="text-2xl font-bold text-center">{{ getFullName() }}</div>
                    <hr>
                    <div class="text-md">Business Information *</div>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="col-span-2 md:col-span-1 flex flex-col gap-2">
                            <label for="name">Business Name</label>
                            <input type="text" class="rounded" placeholder="Business Name" id="name" v-model="form.name"
                                :class="{ 'border-red-700 ring-red-400': errors.name }">
                        </div>
                        <div class="col-span-2 md:col-span-1 flex flex-col gap-2">
                            <label for="kind_of_business">Kind of Business</label>
                            <input type="text" class="rounded" placeholder="Kind of Business" id="kind_of_business"
                                v-model="form.kind_of_business"
                                :class="{ 'border-red-700 ring-red-400': errors.kind_of_business }">
                        </div>
                        <div class="col-span-2 md:col-span-1 flex flex-col gap-2">
                            <label for="plate">Business Plate</label>
                            <input type="text" class="rounded" placeholder="Business Plate" id="plate" v-model="form.plate"
                                :class="{ 'border-red-700 ring-red-400': errors.plate }">
                        </div>
                        <div class="col-span-2 md:col-span-1 flex flex-col gap-2">
                            <label for="permit_number">Business Permit Number</label>
                            <input type="text" class="rounded" placeholder="Permit number" id="permit_number" v-model="form.permit_number"
                                :class="{ 'border-red-700 ring-red-400': errors.permit_number }">
                        </div>
                        <div class="col-span-2 md:col-span-1 flex flex-col gap-2">
                            <label for="permit_expiration_date">Business Permit Expiration</label>
                            <input type="date" class="rounded" placeholder="Permit Expiration Date" v-model="form.permit_expiration_date" id="permit_expiration_date"
                                :class="{ 'border-red-700 ring-red-400': errors.permit_expiration_date }">
                        </div>
                        <div class="col-span-2 md:col-span-1 flex flex-col gap-2">
                            <label for="dti_reg_number">DTI Registration Number</label>
                            <input type="text" class="rounded" placeholder="DTI registration number" id="dti_reg_number"
                                v-model="form.dti_reg_number"
                                :class="{ 'border-red-700 ring-red-400': errors.dti_reg_number }">
                        </div>
                        <div class="col-span-2 md:col-span-1 flex flex-col gap-2">
                            <label for="cedula">CTC number</label>
                            <input type="text" class="rounded" placeholder="CTC number" v-model="form.cedula" id="cedula"
                                :class="{ 'border-red-700 ring-red-400': errors.cedula }">
                        </div>
                        <div class="col-span-2 md:col-span-1 flex flex-col gap-2">
                            <label for="payment_cycle">Payment Cycle</label>
                            <select class="rounded" v-model="form.payment_cycle" id="payment_cycle"
                                :class="{ 'border-red-700 ring-red-400': errors.payment_cycle }">

                                <option value="0">Monthly</option>
                                <option value="1">Quarterly</option>
                                <option value="2">Bi-annual</option>
                                <option value="3">Annual</option>
                            </select>
                        </div>
                        <div class="col-span-2 md:col-span-1 flex flex-col gap-2">
                            <label>Cost</label>
                            <input type="text" class="rounded" :value="'₱' + rate" disabled>
                        </div>
                    </div>
                    <hr>
                    <div class="text-md">Document Images *</div>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="col-span-2 md:col-span-1 flex flex-col gap-2">
                            <label for="cedula_img">Cedula</label>
                            <input @input="handleCedulaImage" id="cedula_img" type="file" class="rounded input-file" accept="image/png, image/jpeg, image/jpg, image/svg+xml, image/webp">
                            <span v-if="errors.cedula_cert" class="text-red-700">Cedula Required</span>
                        </div>

                        <div class="col-span-2 md:col-span-1 flex flex-col gap-2">
                            <label for="barangay_clearance_img">Barangay Clearance</label>
                            <input @input="handleBarangayClearanceImage" id="barangay_clearance_img" type="file"
                                class="rounded input-file" accept="image/png, image/jpeg, image/jpg, image/svg+xml, image/webp">
                            <span v-if="errors.brgy_clearance" class="text-red-700">Barangay Clearance Required</span>
                        </div>

                        <div class="col-span-2 md:col-span-1 flex flex-col gap-2">
                            <label for="pmo_ceedo_clearance">PMO or Ceedo Clearance</label>
                            <input @input="handleCeedoCLearanceImage" id="pmo_ceedo_clearance" type="file"
                                class="rounded input-file" accept="image/png, image/jpeg, image/jpg, image/svg+xml, image/webp">
                            <span v-if="errors.pmo_ceedo_clearance" class="text-red-700">PMO or Ceedo Clearance
                                Required</span>
                        </div>

                        <div class="col-span-2 md:col-span-1 flex flex-col gap-2">
                            <label for="dti_cert">DTI Certificate</label>
                            <input @input="handleDtiImage" id="dti_cert" type="file" class="rounded input-file" accept="image/png, image/jpeg, image/jpg, image/svg+xml, image/webp">
                            <span v-if="errors.dti_cert" class="text-red-700">DTI Required</span>
                        </div>

                        <div class="col-span-2 md:col-span-1 flex flex-col gap-2">
                            <label for="medical_cert">Medical Certificate</label>
                            <input @input="handleMedicalImage" id="medical_cert" type="file" class="rounded input-file" accept="image/png, image/jpeg, image/jpg, image/svg+xml, image/webp">
                            <span v-if="errors.medical_cert" class="text-red-700">Medical Required</span>
                        </div>

                        <div class="col-span-2 md:col-span-1 flex flex-col gap-2">
                            <label for="business_permit">Business Permit</label>
                            <input @input="handleBusinesspermitImage" id="business_permit" type="file"
                                class="rounded input-file" accept="image/png, image/jpeg, image/jpg, image/svg+xml, image/webp">
                            <span v-if="errors.business_permit" class="text-red-700">Business Permit Required</span>
                        </div>


                    </div>
                    <div class="flex gap-3">
                        <button class="btn bg-green-600 btn-block text-white"
                            @click="submitApplication()">Confirm</button>
                        <button class="btn btn-block" @click="closeApplyModal()">Cancel</button>
                    </div>
                </div>
                <div v-else class="flex flex-col gap-5 mt-5">
                    <div class="text-center mb-2">
                        <h2>You need to setup your address first in your profile.</h2>
                    </div>
                    <div class="text-end">
                        <Link :href="route('user.profile')" class="btn">Setup Now</Link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>