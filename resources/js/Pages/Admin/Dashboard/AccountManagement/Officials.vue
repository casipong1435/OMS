<script setup>
import Layout from '../Layout.vue';
import { Head, useForm, usePage, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import InputError from '@/Components/InputError.vue';

let isAddOfficialModalChecked = ref(false);
let isEditOfficialModalChecked = ref(false);
let isDeleteOfficialModalChecked = ref(false);
let isChangePassword = ref(false);
let user_id = null;
const searchInput = ref('');
const filter_role = ref('');

defineProps({
  'officials': Array,
  errors: Object
});

const form = useForm({
  username: '',
  role: '',
  password: '',

});

const resetFields = () => {
  form.role = '';
  form.password = '';
  form.username = '';
  user_id = null;
}

function getRoleName(role) {
  switch (role) {
    case 'admin':
      return "Administrator";

    case 'ceedo':
      return "CEEDO Staff";

    case 'treasurer':
      return "Treasurer";

    case 'mayor':
      return "Mayor";

  }
}

const addOfficial = () => {
  form.post(route('admin.addOfficial'), {
    onSuccess: page => {
      isAddOfficialModalChecked.value = false;
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

const getOfficialInfo = (official) => {
  isEditOfficialModalChecked.value = true;
  user_id = official.id;
  form.username = official.username;


  switch (official.role) {
    case 'admin':
      form.role = '1';
      break;

    case 'ceedo':
      form.role = '2';
      break;
    case 'treasurer':
      form.role = '3';
      break;
    case 'mayor':
      form.role = '4';
      break;
  }

};

const updateOfficial = () => {
  form.put(route('admin.updateOfficial', user_id), {
    onSuccess: page => {
      isEditOfficialModalChecked.value = false;
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


const propmtDelete = (id) => {
  isDeleteOfficialModalChecked.value = true;
  user_id = id;
};

const deleteOfficial = () => {
  form.delete(route('admin.deleteOfficial', user_id), {
    onSuccess: page => {
      isDeleteOfficialModalChecked.value = false;
      Swal.fire({
        toast: true,
        icon: 'success',
        position: 'top-end',
        showConfirmButton: true,
        title: page.props.flash.success,
      });
      user_id = null;
    }
  });
};

function isAuthenticatedUser(id) {
  return id == usePage().props.auth.user.id ? true : false;
}

watch(searchInput, (value) => {
  router.get(route('admin.officials'),
    { searchInput: value },
    {
      preserveState: true,
      replace: true
    })
});

watch(filter_role, (value) => {
  router.get(route('admin.officials'),
    { filter_role: value },
    {
      preserveState: true,
      replace: true
    })
});

</script>

<template>

  <Head title="Officials" />
  <Layout>
    <div class="container px-6 mx-auto grid">
      <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
        Official Accounts
      </h2>
      <!-- CTA -->

      <div class="w-full overflow-hidden rounded-lg shadow-xs">
        <div class="w-full overflow-x-auto">
          <div class="mb-2 p-2 flex justify-between items-center">
            <label class="btn btn-primary ms-1" for="addOfficialModal"><svg xmlns="http://www.w3.org/2000/svg"
                fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
              </svg>
            </label>

            <div class="flex flex-row gap-3">
              <div class="form-group">
                <select class="select" v-model="filter_role">
                  <option selected value="">All</option>
                  <option value="1">Admin</option>
                  <option value="2">CEEDO Staff</option>
                  <option value="3">Treasurer</option>
                  <option value="4">Mayor</option>
                </select>
              </div>
              <div class="relative w-full max-w-xl ms-1 focus-within:text-orange-500 overflow-x-auto">
                <div class="absolute inset-y-0 flex items-center pl-2">
                  <svg class="w-4 h-4" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                      d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                      clip-rule="evenodd"></path>
                  </svg>
                </div>
                <input v-model="searchInput"
                  class="w-full pl-8 pr-2 text-sm text-gray-700 focus:border-orange-600 rounded focus:rounded outline-none"
                  type="text" placeholder="Search Officials" aria-label="Search" />
              </div>
            </div>
          </div>
          <table class="w-full whitespace-no-wrap">
            <thead>
              <tr
                class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800">
                <th class="px-4 py-3">Username</th>
                <th class="px-4 py-3">Role</th>
                <th class="px-4 py-3">Action</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800">
              <tr class="text-gray-700 dark:text-gray-400" v-for="official in $page.props.officials" :key="official.id">
                <td class="px-4 py-3">
                  <div class="flex items-center text-sm">
                    <div class="relative hidden w-8 h-8 mr-3 rounded-full md:block">
                      <img class="object-cover w-full h-full rounded-full" src="/images/avatars/avatar.png" alt=""
                        loading="lazy" />
                      <div class="absolute inset-0 rounded-full shadow-inner" aria-hidden="true"></div>
                    </div>
                    <div>
                      <p class="font-semibold">{{ official.username }}</p>
                    </div>
                  </div>
                </td>
                <td class="px-4 py-3 text-sm">
                  <span
                    class="px-2 py-1 font-semibold leading-tight text-green-700 bg-green-100 rounded-full dark:bg-green-700 dark:text-green-100">
                    {{ getRoleName(official.role) }}
                  </span>
                </td>
                <td class="px-4 py-3 text-xs" :disabled="isAuthenticatedUser(official.id)">
                  <button @click="getOfficialInfo(official)" class="text-orange-600 p-1 mx-1 hover:text-gray-700"
                    style="cursor: pointer;"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                      stroke-width="1.5" stroke="currentColor" class="size-6">
                      <path stroke-linecap="round" stroke-linejoin="round"
                        d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                    </svg>
                  </button>
                  <button type="button" @click="propmtDelete(official.id)" class="text-red-600 p-1 mx-1 hover:text-gray-700"><svg xmlns="http://www.w3.org/2000/svg"
                      fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                      <path stroke-linecap="round" stroke-linejoin="round"
                        d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                    </svg>

                  </button>
                </td>
              </tr>
              <tr v-if="officials.length <= 0">
                <td colspan="3">No Officials Yet!</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </Layout>

  <!--Add Modal-->
  <input class="modal-state" id="addOfficialModal" type="checkbox" v-model="isAddOfficialModalChecked" />
  <div class="modal">
    <label class="modal-overlay"></label>
    <form @submit.prevent="addOfficial">
      <div class="modal-content flex flex-col gap-5">
        <label for="addOfficialModal" class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2"
          @click="resetFields">✕</label>
        <h2 class="text-xl">Create Official</h2>
        <div class="form-group">
          <label for="role" class="">Select Role</label>
          <select class="select" id="role" v-model="form.role">
            <option disabled selected>Select</option>
            <option value="1">Admin</option>
            <option value="2">CEEDO Staff</option>
            <option value="3">Treasurer</option>
            <option value="4">Mayor</option>
          </select>
        </div>
        <InputError :message="errors.role" />
        <div class="">
          <label for="username">Username</label>
          <input class="input-ghost-primary input" id="username" v-model="form.username" />
        </div>
        <InputError :message="errors.username" />
        <div class="">
          <label for="password">Password</label>
          <input class="input-ghost-primary input" type="password" id="password" v-model="form.password" />
        </div>
        <InputError :message="errors.password" />
        <div class="flex gap-3">
          <button type="submit" class="btn btn-primary btn-block"
            :class="{ 'disabled opacity-25 pointer-events-none': form.processing }">Add <span v-if="form.processing"
              class="spinner-xs ml-2 spinner-circle [--spinner-color:var(--gray-8)]   "></span></button>
          <label class="btn btn-block" for="addOfficialModal" @click="resetFields">Cancel</label>
        </div>
      </div>
    </form>
  </div>

  <!--End of Add Modal-->


  <!--Edit Modal-->
  <input class="modal-state" id="editOfficialModal" type="checkbox" v-model="isEditOfficialModalChecked" />
  <div class="modal">
    <label class="modal-overlay"></label>
    <form @submit.prevent="updateOfficial">
      <div class="modal-content flex flex-col gap-5">

        <label for="editOfficialModal" class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2"
          @click="resetFields">✕</label>
        <h2 class="text-xl">{{ !isChangePassword ? 'Update Role' : 'Update Password' }}</h2>
        <div class="btn-group btn-group-rounded btn-group-scrollable">
          <button type="button" class="btn" :class="{ 'btn-active': !isChangePassword }"
            @click="isChangePassword = false">Role</button>
          <button type="button" class="btn" :class="{ 'btn-active': isChangePassword }"
            @click="isChangePassword = true">Password</button>
        </div>
        <div class="form-group" v-if="!isChangePassword">
          <label for="edit_role" class="">Select New Role</label>
          <select class="select" :class="{ 'disabled': isChangePassword }" id="edit_role" v-model="form.role">
            <option disabled selected>Select</option>
            <option value="1">Admin</option>
            <option value="2">CEEDO Staff</option>
            <option value="3">Treasurer</option>
            <option value="4">Mayor</option>
          </select>
        </div>
        <InputError :message="errors.role" />
        <div class="">
          <label for="edit_username">Username</label>
          <input class="input-ghost-primary input" disabled id="edit_username" v-model="form.username" />
        </div>
        <InputError :message="errors.username" />
        <div class="" v-if="isChangePassword">
          <label for="edit_password">New Password</label>
          <input class="input-ghost-primary input" type="password" id="edit_password" v-model="form.password" />
        </div>
        <InputError :message="errors.password" />
        <div class="flex gap-3">
          <button type="submit" class="btn btn-primary btn-block"
            :class="{ 'disabled opacity-25 pointer-events-none': form.processing }">Update <span v-if="form.processing"
              class="spinner-xs ml-2 spinner-circle [--spinner-color:var(--gray-8)]   "></span></button>
          <label class="btn btn-block" for="editOfficialModal" @click="resetFields">Cancel</label>
        </div>
      </div>
    </form>
  </div>
  <!--End of Edit Modal-->

  <!--Delete Modal-->
  <input class="modal-state" id="deleteOfficialModal" type="checkbox" v-model="isDeleteOfficialModalChecked" />
  <div class="modal">
    <label class="modal-overlay"></label>
    <form @submit.prevent="deleteOfficial">
      <div class="modal-content flex flex-col gap-5">

        <label for="deleteOfficialModal" class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2"
          >✕</label>
        <h2 class="text-xl">Delete User</h2>
        <div class="p-4 text-center md:-p-5">
          <svg class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                </svg>
                <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Are you sure you want to delete this user?</h3>
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
