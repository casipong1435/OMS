<script setup>
import Layout from '../../Layout.vue';
import { Head, Link, useForm, usePage, router } from '@inertiajs/vue3';
import { ref, onMounted, watch } from 'vue';

const isAddEstablishmentModal = ref(false);
const iseditEstablishmentUnit = ref(false);
const isAdd = ref(false);
const establishmentID = ref(null);
const establishmentName = ref(null);
const isDrawerOpen = ref(false);
const canAddUnit = ref(false);
const isDeleteUnit = ref(false);
const isSeeMore = ref(false);

const { props } = usePage();

defineProps({
    establishments: Array,
    name: String,
    id: String,
    establishmentUnits: Array,
    errors: Object
});

onMounted(() => {
    props.establishmentUnits = [];
    //console.log(props.establishmentUnits.length)
});

const toggleAddModal = () => {
    isAddEstablishmentModal.value = true;
    isAdd.value = true;
    resetFields();
};

const toggleEditModal = (data) => {
    isAddEstablishmentModal.value = true;
    getEstablishmentData(data);
    isAdd.value = false;

};

const cancelModal = () => {
    isAddEstablishmentModal.value = false;
    resetFields();
}

const form = useForm({
    area_id: props.id,
    name: '',
    rate: ''
});

function validateInput(event) {
    // Only allow numbers and a single decimal point
    event.target.value = event.target.value.replace(/[^0-9.]/g, '');

    // Update the bound `amount` ref
    form.rate = event.target.value;

    // Allow only one decimal point and restrict to two decimal places
    const decimalIndex = form.rate.indexOf('.');
    if (decimalIndex !== -1) {
        form.rate = form.rate.slice(0, decimalIndex + 3); // Keep two digits after the decimal
    }
}

const addEstabishment = () => {
    form.post(route('ceedo.post-stall'), {
        onSuccess: page => {
            isAddEstablishmentModal.value = false;
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

const editEstablishment = () => {
    form.put(route('ceedo.put-stall', establishmentID.value), {
        onSuccess: page => {
            isAddEstablishmentModal.value = false;
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
const unit = useForm({
    quantity: '',
    establishment_ID: '',
});

const addUnit = ($id) => {
    unit.establishment_ID = $id;
    unit.post(route('ceedo.post-stall-addUnit'), {
        onSuccess: () => {
            isDrawerOpen.value = false;
        }
    });
};

const resetFields = () => {
    form.name = '';
    form.rate = '';
};

const getEstablishmentData = (data) => {
    establishmentID.value = data.id;
    form.name = data.name;
    form.rate = data.rate;
};

const openDrawer = (data) => {
    isDrawerOpen.value = true;
    establishmentID.value = data.id;
    establishmentName.value = data.name;
    props.establishmentUnits = data.establishment_units;
    //console.log(props.establishmentUnits);
};

watch(() => (unit.quantity), (newQuantity) => {
    if (newQuantity <= 0) {
        canAddUnit.value = false;
    } else {
        canAddUnit.value = true;
    }
});

const unitForm = useForm({
    images: [],
    status: '',
    establishmentUnitID: '',

});

const hasObjectURL = ref(false);

const imagesHolder = ref([]);
const removedImages = ref([]);

const handleImageUpload = (event) => {
    imagesHolder.value = Array.from(event.target.files);
    hasObjectURL.value = true;
};

const creatObjectURL = (image) => {
    return URL.createObjectURL(image);
};

const getFullName = (first_name, middle_name, last_name) => {
    return first_name + " " + (middle_name != null ? middle_name + " " : "") + last_name;
}

const owner_id = ref(null);
const owner_name = ref(null);
const owner_business_name = ref(null);
const owner_business_permit = ref(null);

const getUnitInformation = (unit) => {
    iseditEstablishmentUnit.value = true;
    unitForm.establishmentUnitID = unit.id;
    unitForm.images = unit.establishment_images;
    unitForm.status = unit.status;
    if(unit.business){
        owner_id.value = unit.business.profile.id;
        owner_name.value = getFullName(unit.business.profile.first_name,unit.business.profile.middle_name, unit.business.profile.last_name );
        owner_business_name.value = unit.business.name;
        owner_business_permit.value = unit.business.permit_number;
    }
};

const editEstablishmentUnit = () => {
    let formData = new FormData();

    formData.append('status', unitForm.status);
    formData.append('establishmentUnitID', unitForm.establishmentUnitID);
    Array.from(imagesHolder.value).forEach((file) => {
        formData.append('images[]', file);
    });

    Array.from(removedImages.value).forEach((object) => {
        formData.append('removedImages[]', JSON.stringify(object));
    });

    router.post(route('ceedo.put-stall-unit'), formData,
        {
            forceFormData: true, // Ensure FormData is sent correctly
            onSuccess: (page) => {
                iseditEstablishmentUnit.value = false;
                imagesHolder.value = [];
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

const deleteEstablishmentID = ref(null);

const getStallData = (id) => {
    isDeleteUnit.value = true;
    deleteEstablishmentID.value = id;

};

const deleteUnit = () => {
    router.delete(route('ceedo.stall-delete', deleteEstablishmentID.value), {
        onSuccess: () => {
            isDeleteUnit.value = false;
            removeUnit(deleteEstablishmentID.value);
        }
    });
};

const getStatusName = (status) => {
    switch (status) {
        case 0:
            return 'Acquired';
        case 1:
            return 'Available';
        case 2:
            return 'On Application';
        case 3:
            return 'Maintenance';
        case 4:
            return 'Archived'
    }
};


function removeImage(index, unit_image) {
    unitForm.images.splice(index, 1); // Remove the image at the specified index
    removedImages.value.push(unit_image);
    console.log(removedImages.value);
}

function removeObjectURL(index) {
    imagesHolder.value.splice(index, 1);
}

function removeUnit(id) {
    props.establishmentUnits = props.establishmentUnits.filter(unit => unit.id !== id);
}

</script>

<template>

    <Head title="Establishments" />
    <Layout>
        <div class="container px-6 mx-auto grid">
            <div class="flex justify-between items-center flex-column sm:flex-row">
                <div class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
                    <h1 class="text-2xl font-bold mb-8 text-gray-700">Sections Overview</h1>
                </div>
                <label @click="toggleAddModal()" class="cursor-pointer"><svg xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                        class="size-10 hover:text-gray-400 text-orange-600">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>

                </label>
            </div>

            <div class="mb-2">
                <div class="breadcrumbs text-sm">
                    <ul>
                        <li>
                            <Link :href="route('ceedo.establishments')" class="text-gray-800">
                            Area</Link>
                        </li>
                        <li><a>{{ $page.props.name }}</a></li>
                    </ul>
                </div>
            </div>
            <!-- CTA -->
            <hr>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-4">

                <!-- Fish Section -->
                <div class="bg-white shadow-lg rounded-lg p-6 transform hover:scale-105 hover:shadow-2xl transition duration-300"
                    v-for="establishment in establishments" :key="establishment.id">
                    <h2 class="text-xl font-semibold text-blue-600 mb-4">{{ establishment.name }}</h2>
                    <div class="text-gray-600 cursor-pointer" @click="openDrawer(establishment)">
                        <p class="mb-2">
                            <span class="font-medium text-gray-700">Vendors:</span> 15
                        </p>
                        <p class="mb-2">
                            <span class="font-medium text-gray-700">Stalls:</span> {{
                                establishment.establishment_units.length }}
                        </p>
                        <p class="mb-4">
                            <span class="font-medium text-gray-700">Rate/Day:</span> {{ establishment.rate }}
                        </p>

                    </div>
                    <button type="button" style="z-index: 9999"
                        class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition duration-300"
                        @click="toggleEditModal(establishment)">
                        Edit
                    </button>
                </div>
                <div v-if="establishments.length <= 0" class="col-span-12 text-center text-xl">
                    No Establishment Added Yet!
                </div>
            </div>
        </div>


        <input class="modal-state" id="addEstablishmentModal" type="checkbox" v-model="isAddEstablishmentModal" />
        <div class="modal">
            <label class="modal-overlay"></label>
            <form @submit.prevent="isAdd ? addEstabishment() : editEstablishment()">
                <div class="modal-content flex flex-col gap-5 bg-white p-6 rounded-lg shadow-lg">
                    <label class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" @click="cancelModal">✕</label>
                    <h2 class="text-2xl font-bold text-gray-700">{{ isAdd ? 'Add Section' : 'Edit Section' }}</h2>
                    <div class="flex flex-col gap-4">
                        <!-- Section Name Input -->
                        <div>
                            <label for="sectionName" class="block text-sm font-medium text-gray-700">Section
                                Name</label>
                            <input type="text" id="sectionName" class="input input-bordered w-full" v-model="form.name"
                                placeholder="Enter section name" required />
                        </div>
                        <!-- Daily Rate Input -->
                        <div>
                            <label for="rate" class="block text-sm font-medium text-gray-700">Daily Rate (₱)</label>
                            <div class="flex items-center">
                                <input type="text" id="rate" class="input input-bordered w-full" v-model="form.rate"
                                    placeholder="0.00" @input="validateInput" required />
                                <span class="ml-2 text-gray-700">/day</span>
                            </div>
                        </div>
                    </div>
                    <!-- Actions -->
                    <div class="flex justify-end gap-2 mt-4">
                        <button class="btn btn-primary" type="submit">{{ isAdd ? 'Add' : 'Save' }}</button>
                        <label class="btn btn-secondary" @click="cancelModal">Cancel</label>
                    </div>
                </div>
            </form>
        </div>


        <!--Open Side Drawer for Stalls-->
        <input type="checkbox" id="drawer-right" class="drawer-toggle hidden" v-model="isDrawerOpen" />
        <label class="overlay"></label>
        <div class="drawer drawer-right">
            <div class="drawer-content pt-10 flex flex-col h-full">
                <label for="drawer-right" class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</label>
                <div>
                    <div class="flex justify-between items-center mb-2 gap-2">
                        <h2 class="text-xl font-medium mb-2">{{ establishmentName }}</h2>
                        <form @submit.prevent="addUnit(establishmentID)">
                            <div class="flex">
                                <input type="number" min="0" max="10" class="w-20 input input-ghost py-0"
                                    v-model="unit.quantity">
                                <button class="btn text-primary bg-transparent hover:text-gray-700"
                                    :class="{ 'disabled opacity-50 pointer-events-none': !canAddUnit }"
                                    type="submit"><svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                </button>
                            </div>
                        </form>
                    </div>
                    <hr>
                    <div class="gap-2 px-1 py-2 flex flex-col">
                        <div v-for="unit in props.establishmentUnits" :key="unit.id" class="relative">
                            <div class="py-3 px-4 mr-7 rounded text-md bg-gray-100 hover:bg-gray-200 transition-ease duration-200 flex flex-row items-center justify-between"
                                style="cursor: pointer;" @click="getUnitInformation(unit)">

                                <div class="flex flex-row">
                                    <span class="mr-2"><svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                                        </svg>
                                    </span>
                                    <span>ID: {{ unit.id }}</span>
                                </div>

                                <div>
                                    <span
                                        :class="{ 'text-green-700': unit.status == 1, 'text-red-700': unit.status == 0, 'text-orange-700': unit.status == 3 }">{{
                                            getStatusName(unit.status) }}</span>
                                </div>
                            </div>
                            <div class="absolute text-red-700 right-0 top-3">
                                <label @click="getStallData(unit.id)"><svg xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                        class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14" />
                                    </svg></label>
                            </div>
                        </div>
                        <div v-if="props.establishmentUnits <= 0" class="text-center">No Stalls Yet!</div>
                    </div>
                </div>
            </div>
        </div>
        <!--End Drawer-->


        <!--Modal for Units-->

        <input class="modal-state" id="editEstablishmentUnit" type="checkbox" v-model="iseditEstablishmentUnit" />
        <div class="modal !h-full !flex !items-center overflow-y-auto  w-screen" style="z-index: 10000;">
            <label class="modal-overlay"></label>
            <form @submit.prevent="editEstablishmentUnit()">
                <div class="modal-content flex flex-col gap-5">
                    <label for="editEstablishmentUnit"
                        class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</label>
                    <h2 class="text-xl">Unit Images</h2>
                    <div class="max-w-sm col-span-12 md:col-span-6 sm:col-span-4 rounded overflow-hidden">
                        <div class="flex justify-end items-center">
                            <label for="status" class="mr-2">
                                Status:
                            </label>
                            <select class="" v-model="unitForm.status">
                                <option value="0">Acquired</option>
                                <option value="1">Available</option>
                                <option value="2">On Application</option>
                                <option value="3">Maintenance</option>
                                <option value="4">Archived</option>
                            </select>
                        </div>
                        <div class="grid grid-cols-12 gap-2 mb-2">
                            <div class="col-span-4 gap-5">
                                <input type="file" hidden id="image-file" accept=".jpg,.jpeg,.png"
                                    @input="handleImageUpload" multiple>
                                <label for="image-file" class="" style="cursor: pointer;">
                                    <div
                                        class="bg-gray-100 max-w-50 h-full rounded flex items-center justify-center hover:bg-gray-200">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 4.5v15m7.5-7.5h-15" />
                                        </svg>
                                    </div>
                                </label>
                            </div>
                            <div class="col-span-4 gap-5" v-for="(unit_image, index) in imagesHolder" :key="index">
                                <div
                                    class="bg-gray-100 p-2 w-50 h-50 rounded flex items-center justify-center relative">
                                    <img :src="creatObjectURL(unit_image)" alt="" class="w-full h-full">
                                    <button type="button" @click="removeObjectURL(index)"
                                        class="text-sm text-red-600 absolute top-0 right-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6 18 18 6M6 6l12 12" />
                                        </svg>

                                    </button>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="grid grid-cols-12 gap-2 mt-2">
                            <div v-if="unitForm.images.length <= 0" class="col-span-12 text-center">No Images Added Yet!
                            </div>
                            <div class="col-span-4 gap-5" v-for="(unit_image, index) in unitForm.images"
                                :key="unit_image.id">
                                <div
                                    class="bg-gray-100 p-3 w-50 h-50 rounded flex items-center justify-center relative">
                                    <img :src="'/images/Areas/Establishment/' + unit_image.image" alt=""
                                        class="w-full h-full">
                                    <button type="button" @click="removeImage(index, unit_image)"
                                        class="text-sm text-red-600 absolute top-0 right-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6 18 18 6M6 6l12 12" />
                                        </svg>

                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="px-6 pt-4 pb-2 flex gap-2 justify-end">
                            <button class="btn btn-primary" type="submit">Save</button>
                            <label class="btn" for="editEstablishmentUnit">Cancel</label>
                        </div>
                        <div v-if="unitForm.status == 0">
                            <div class="text-center my-3 flex justify-center items-center">
                                <label for="" class="text-blue-700 cursor-pointer" @click="isSeeMore = !isSeeMore">Acquired
                                by </label>
                            <span class="text-blue-700">
                                <svg v-if="!isSeeMore" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                </svg>
                                <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                </svg>

                            </span>
                            </div>
                            <hr>
                            <div v-if="isSeeMore" class="my-2">
                                <div class="mb-2 flex justify-between items-center p-2">
                                    <span class="font-bold">Name: </span>
                                    <span class="text-purple-700"><a target="_blank" :href="route('ceedo.vendorProfile', owner_id)">{{ owner_name }}</a></span>
                                </div>
                                <div class="mb-2 flex justify-between items-center p-2">
                                    <span class="font-bold">Business Name: </span>
                                    <span>{{ owner_business_name }}</span>
                                </div>
                                <div class="mb-2 flex justify-between items-center p-2">
                                    <span class="font-bold">Business Permit: </span>
                                    <span>{{ owner_business_permit }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </form>
        </div>
        <!--End modal for edit unit-->


        <!--Modal For Delete Unit-->
        <input class="modal-state" id="deleteUnit" type="checkbox" v-model="isDeleteUnit" />
        <div class="modal w-screen" style="z-index: 10000;">
            <label class="modal-overlay"></label>
            <div class="modal-content flex flex-col gap-5 max-w-3xl">
                <label for="deleteUnit" class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</label>
                <h2 class="text-xl">Delete Stall</h2>
                <div class="flex flex-col justify-center items-center px-1 py-2">
                    <div class="text-warning"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="size-14">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                        </svg>
                    </div>
                    <span>Are you sure you want to delete this stall?</span>
                </div>
                <div class="flex gap-3">
                    <button @click="deleteUnit()" class="btn bg-red-500 text-white btn-block">Delete</button>
                    <label for="deleteUnit" class="btn btn-block">Cancel</label>
                </div>
            </div>
        </div>

        <!-- End delete modal unit-->
    </Layout>
</template>