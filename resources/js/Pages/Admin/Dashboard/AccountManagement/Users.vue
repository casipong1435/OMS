<script setup>
import Layout from '../Layout.vue';
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import { ref, watch, onMounted, reactive } from 'vue';
import axios from 'axios';
import InputError from '@/Components/InputError.vue';

let isEditUserModalChecked = ref(false);
let isRestrictOfficialModalChecked = ref(false);
let isEdit = ref(false);
let isAddressHidden = ref(true);
let user_id = null;
const searchInput = ref('');
const filter_barangay = ref('');

defineProps({
  users: Array,
  regions: Array,
  errors: Object
});

const { props } = usePage();

const form = useForm({
  //Primary Information
  username: '',
  first_name: '',
  middle_name: '',
  last_name: '',
  mobile_number: '',
  date_of_birth: '',
  sex: '',

  //Address
  purok: '',
  barangay: '',
  city: '',
  province: '',
  region: ''

});

const resetFields = () => {
  form.username = '';
  form.first_name = '';
  form.middle_name = '';
  form.last_name = '';
  form.mobile_number = '';
  form.date_of_birth = '';
  form.sex = '';
  form.purok = '';
  form.barangay = '';
  form.city = '';
  form.province = '';
  form.region = '';

  user_id = null;
  props.errors = [];
}

const getFullName = (first_name, middle_name, last_name) => {
  return first_name + " " + (middle_name != null ? middle_name + " " : "") + last_name;
}

const getUserInfo = (user) => {
  isEditUserModalChecked.value = true;
  user_id = user.id;
  form.username = user.username;
  form.first_name = user.profile.first_name;
  form.last_name = user.profile.last_name;
  form.middle_name = user.profile.middle_name;
  form.sex = user.profile.sex;
  form.date_of_birth = user.profile.date_of_birth;
  form.mobile_number = user.mobile_number;
  form.purok = user.profile.purok;
  form.barangay = user.profile.barangay.brgyCode;
  form.city = user.profile.city.citymunCode;
  form.province = user.profile.province.provCode;
  form.region = user.profile.region.regCode;
};

const editUser = () => {
  form.put(route('admin.editUser', user_id), {
    onSuccess: page => {
      isEditUserModalChecked.value = false;
      isEdit.value = false;
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

const cancelShowData = () => {
  isEdit.value = false;
  resetFields();
};


const restrictUser = (id, status) => {
  form.put(route('admin.restrictUser', { id: id, status: status }), {
    onSuccess: page => {
      //isRestrictOfficialModalChecked.value = false;
      Swal.fire({
        toast: true,
        icon: 'success',
        position: 'top-end',
        showConfirmButton: true,
        title: page.props.flash.success,
      });
      //user_id = null;
    }
  });
};

const getAddress = (barangay, city) => {
  const txtBarangay = barangay;
  const txtCity = city;

  return `${txtBarangay.toLowerCase()}, ${txtCity.toLowerCase()}`;
};

watch(searchInput, (value) => {
  router.get(route('admin.users'),
    { searchInput: value },
    {
      preserveState: true,
      replace: true
    })
});

watch(filter_barangay, (value) => {
  router.get(route('admin.users'),
    { filter_barangay: value },
    {
      preserveState: true,
      replace: true
    })
});

const provinces = ref([]);
const cities = ref([]);
const barangays = ref([]);

const validateNumber = (event) => {
  // Remove any non-numeric characters from the input
  let value = event.target.value.replace(/\D/g, '');

  if (value.charAt(0) !== '9') {
    value = ''; // Clear the input if the first digit is not 9
  }
  form.mobile_number = value.slice(0, 10);
};

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

watch(() => form.region, async (region) => {
  if(region){
    await fetchProvinces();
  }else{
    provinces.value = [];
    form.province = null;
  }
});

watch(() => form.province, async (province) => {
  if(province){
    await fetchCities();
  }else{
    cities.value = [];
    form.city = null;
  }
});

watch(() => form.city, async (city) => {
  if(city){
    await fetchBarangays();
  }else{
    barangays.value = [];
    form.barangay = null;
  }
});


</script>

<template>

  <Head title="Users List" />
  <Layout>
    <div class="container px-6 mx-auto grid">
      <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
        User Accounts
      </h2>
      <!-- CTA -->

      <div class="w-full overflow-hidden rounded-lg shadow-xs">
        <div class="w-full overflow-x-auto">
          <div class="mb-2 p-2 ">

            <div class="block sm:flex sm:justify-end sm:items-center gap-3">
              
              <div class="relative w-full max-w-xl focus-within:text-orange-500 overflow-x-auto mt-2 md:mt-0">
                <div class="absolute inset-y-0 flex items-center pl-2">
                  <svg class="w-4 h-4" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                      d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                      clip-rule="evenodd"></path>
                  </svg>
                </div>
                <input v-model="searchInput"
                  class="w-full pl-8 pr-2 text-sm text-gray-700 focus:border-orange-600 rounded focus:rounded outline-none"
                  type="text" placeholder="Search User" aria-label="Search" />
              </div>
            </div>
          </div>
          <table class="w-full whitespace-no-wrap">
            <thead>
              <tr
                class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800">
                <th class="px-4 py-3">ID</th>
                <th class="px-4 py-3">Name</th>
                <th class="px-4 py-3">Date of Birth</th>
                <th class="px-4 py-3">Address</th>
                <th class="px-4 py-3">Mobile Number</th>
                <th class="px-4 py-3">Action</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800">
              <tr class="text-gray-700 dark:text-gray-400" v-for="user in $page.props.users" :key="user.id">
                <td class="px-4 py-3 text-sm">
                  <span class="px-2 py-1 font-semibold rounded-full dark:bg-green-700 dark:text-green-100">
                    {{ user.id }}
                  </span>
                </td>
                <td class="px-4 py-3">
                  <div class="flex items-center text-sm">
                    <div class="relative hidden w-8 h-8 mr-3 rounded-full md:block">
                      <img class="object-cover w-full h-full rounded-full" src="/images/avatars/avatar.png" alt=""
                        loading="lazy" />
                      <div class="absolute inset-0 rounded-full shadow-inner" aria-hidden="true"></div>
                    </div>
                    <div>
                      <p class="font-semibold">{{ getFullName(user.profile.first_name, user.profile.middle_name,
                        user.profile.last_name) }}</p>
                    </div>
                  </div>
                </td>
                <td class="px-4 py-3 text-sm">
                  <span class="px-2 py-1 font-semibold rounded-full dark:bg-green-700 dark:text-green-100">
                    {{ user.profile.date_of_birth }}
                  </span>
                </td>
                <td class="px-4 py-3 text-sm">
                  <span class="px-2 py-1 font-semibold rounded-full dark:bg-green-700 dark:text-green-100"
                    :class="{ 'text-gray-400': !user.profile.province }">
                    {{ !user.profile.province ? 'Not Set' : getAddress(user.profile.barangay.brgyDesc,user.profile.city.citymunDesc) }}

                  </span>
                </td>
                <td class="px-4 py-3 text-sm">
                  <span class="px-2 py-1 font-semibold rounded-full dark:bg-green-700 dark:text-green-100">
                    {{ user.mobile_number }}
                  </span>
                </td>
                <td class="px-4 py-3 text-xs">
                  <button @click="getUserInfo(user)" class="text-orange-600 hover:text-gray-700"
                    style="cursor: pointer;"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                      stroke-width="1.5" stroke="currentColor" class="size-6">
                      <path stroke-linecap="round" stroke-linejoin="round"
                        d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>

                  </button>
                  <button type="button" @click="user.status == 0 ? restrictUser(user.id, 1) : restrictUser(user.id, 0);"
                    class="hover:text-gray-700"
                    :class="{ 'text-gray-700 disbaled opacity-50': form.processing, 'text-red-600': user.status == 0, 'text-green-600': user.status != 0 }"
                    :disabled="form.processing"><svg v-if="user.status == 0" xmlns="http://www.w3.org/2000/svg"
                      fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                      <path stroke-linecap="round" stroke-linejoin="round"
                        d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                    <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                      stroke="currentColor" class="size-6">
                      <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>


                  </button>
                </td>
              </tr>
              <tr v-if="users.length <= 0" class="text-center">
                <td colspan="6" class="p-2">No Users Yet!</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </Layout>



  <!--Edit Modal-->
  <input class="modal-state" id="editOfficialModal" type="checkbox" v-model="isEditUserModalChecked" />
  <div class="modal  !items-start overflow-y-auto overflow-x-hidden">
    <label class="modal-overlay"></label>
    <form @submit.prevent="editUser">
      <div class="modal-content flex flex-col gap-5">

        <label for="editOfficialModal" class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2"
          @click="cancelShowData">✕</label>
        <h2 class="text-xl flex">User Info <button type="button" class="ml-2 text-xs text-warning hover:text-orange-300"
            @click="isEdit = !isEdit"><svg v-if="!isEdit" xmlns="http://www.w3.org/2000/svg" fill="none"
              viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
            </svg>
            <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
              stroke="currentColor" class="size-6">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
          </button></h2>
        <div class="grid grid-cols-6 gap-5">
          <div class="col-span-6 flex flex-col">
            <label for="edit_username" class="ml-2">Username</label>
            <input class="input-ghost-primary max-w-full input " disabled id="edit_username" v-model="form.username" />
            <InputError :message="errors.username" />
          </div>
          <div class="col-span-6">
            <div class="text-2xs">
              Primary Information
            </div>
          </div>
          <div class="col-span-6 md:col-span-3">
            <label for="first_name" class="ml-2">First Name</label>
            <input class="input-ghost-primary input" type="text" :disabled="!isEdit" id="first_name"
              v-model="form.first_name" />
            <InputError :message="errors.first_name" />
          </div>
          <div class="col-span-6 md:col-span-3">
            <label for="middle_name" class="ml-2">Middle Name</label>
            <input class="input-ghost-primary input" type="text" :disabled="!isEdit" id="middle_name"
              v-model="form.middle_name" />
            <InputError :message="errors.middle_name" />
          </div>
          <div class="col-span-6 md:col-span-3">
            <label for="last_name" class="ml-2">Last Name</label>
            <input class="input-ghost-primary input" type="text" :disabled="!isEdit" id="last_name"
              v-model="form.last_name" />
            <InputError :message="errors.last_name" />
          </div>
          <div class="col-span-6 md:col-span-3">
            <label for="date_of_birth" class="ml-2">Date of Birth</label>
            <input class="input-ghost-primary input" type="date" :disabled="!isEdit" id="date_of_birth"
              v-model="form.date_of_birth" />
            <InputError :message="errors.date_of_birth" />
          </div>
          <div class="col-span-6 md:col-span-3">
            <label for="sex" class="ml-2">Sex</label>
            <select id="sex" class="select input-ghost-primary" :disabled="!isEdit" v-model="form.sex">
              <option selected value="Male">Male</option>
              <option value="Female">Female</option>
            </select>
            <InputError :message="errors.sex" />
          </div>
          <div class="relative col-span-6 md:col-span-3">
            <label for="mobile_number" class="ml-2"> Mobile
              Number
              *</label>
            <input type="text" id="mobile_number" v-model="form.mobile_number" :disabled="!isEdit"
              class="input-ghost-primary input text-sm  pl-12"
              :class="{ 'border-red-500 text-red-900 placeholder-red-700 focus:ring-red-500 focus:border-red-500': errors.mobile_number }"
              @input="validateNumber" pattern="^9\d{9}$" placeholder="9XXXXXXXXX" required />

            <span class="absolute top-9 left-3 text-sm inline-flex items-center text-gray-10">
              +63
            </span>
          </div>
          <div class="col-span-6">
            <div class="text-2xs flex justify-between items-center">
              <span>Address</span>
              <button type="button" class="hover:text-orange-600" @click="isAddressHidden = !isAddressHidden">

                <svg v-if="!isAddressHidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                  stroke-width="1.5" stroke="currentColor" class="size-6">
                  <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                </svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                  stroke="currentColor" class="size-6">
                  <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                </svg>


              </button>
            </div>
          </div>
          <div class="col-span-6 grid grid-cols-6 gap-5" :class="{ 'hidden': isAddressHidden }">
            <div class="col-span-6 flex flex-col">
              <label for="region" class="ml-2">Region</label>
              <select id="region" class="select max-w-xl input-ghost-primary" :disabled="!isEdit" v-model="form.region"
                @change="fetchProvinces">
                <option v-for="region in $page.props.regions" :key="region.regCode" :value="region.regCode">
                  {{ region.regDesc }}
                </option>
              </select>
              <InputError :message="errors.region" />
            </div>
            <div class="col-span-6 md:col-span-3">
              <label for="province" class="ml-2">Province</label>
              <select id="province" class="select input-ghost-primary" :disabled="!isEdit" v-model="form.province" @change="fetchCities">
                <option v-for="province in provinces" :key="province.provCode" :value="province.provCode">
                  {{ province.provDesc }}
                </option>
              </select>
              <InputError :message="errors.province" />
            </div>
            <div class="col-span-6 md:col-span-3">
              <label for="city" class="ml-2">City/Municipality</label>
              <select id="city" class="select input-ghost-primary" :disabled="!isEdit" v-model="form.city" @change="fetchBarangays">
                <option v-for="city in cities" :key="city.citymunCode" :value="city.citymunCode">
                  {{ city.citymunDesc }}
                </option>
              </select>
              <InputError :message="errors.city" />
            </div>
            <div class="col-span-6 md:col-span-3">
              <label for="barangay" class="ml-2">Barangay</label>
              <select id="barangay" class="select input-ghost-primary" :disabled="!isEdit" v-model="form.barangay">
                <option v-for="barangay in barangays" :key="barangay.brgyCode" :value="barangay.brgyCode">
                  {{ barangay.brgyDesc }}
                </option>
              </select>
              <InputError :message="errors.barangay" />
            </div>
            <div class="col-span-6 md:col-span-3">
              <label for="purok" class="ml-2">Purok/Street</label>
              <input class="input-ghost-primary input" type="text" :disabled="!isEdit" id="purok"
                v-model="form.purok" />
                <InputError :message="errors.purok" />
            </div>
            
          </div>
          <div class="col-span-6 flex gap-3">
            <button type="submit" class="btn btn-primary btn-block"
              :class="{ 'disabled opacity-25 pointer-events-none': form.processing || !isEdit }">Update <span
                v-if="form.processing"
                class="spinner-xs ml-2 spinner-circle [--spinner-color:var(--gray-8)]   "></span></button>
            <label class="btn btn-block" for="editOfficialModal" @click="cancelShowData">Cancel</label>
          </div>
        </div>
      </div>
    </form>
  </div>
  <!--End of Edit Modal-->

  <!--Delete Modal-->
  <input class="modal-state" id="deleteOfficialModal" type="checkbox" v-model="isRestrictOfficialModalChecked" />
  <div class="modal">
    <label class="modal-overlay"></label>
    <form @submit.prevent="deleteOfficial">
      <div class="modal-content flex flex-col gap-5">

        <label for="deleteOfficialModal" class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</label>
        <h2 class="text-xl">Delete User</h2>
        <div class="p-4 text-center md:-p-5">
          <svg class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200" aria-hidden="true"
            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
          </svg>
          <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Are you sure you want to delete this
            user?</h3>
        </div>
        <div class="flex gap-3">
          <button type="submit" class="btn btn-error btn-block"
            :class="{ 'disabled opacity-25 pointer-events-none': form.processing }">Confirm <span v-if="form.processing"
              class="spinner-xs ml-2 spinner-circle [--spinner-color:var(--gray-8)]   "></span></button>
          <label class="btn btn-block" for="deleteOfficialModal">Cancel</label>
        </div>
      </div>
    </form>
  </div>
  <!--End of Delete Modal-->
</template>
