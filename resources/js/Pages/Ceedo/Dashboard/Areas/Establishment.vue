<script setup>
import "leaflet/dist/leaflet.css";
import { LMap, LTileLayer, LPolygon, LMarker, LPopup } from "@vue-leaflet/vue-leaflet";
import L from "leaflet";
import Layout from '../../Layout.vue';
import { ref, reactive, onMounted, watch } from 'vue';
import { Head, Link, useForm, usePage, router } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';

defineProps({
  errors: Object,
  areas: Array
});

const isListView = ref(true);
const isAddArea = ref(false);
const isAreaModalInMap = ref(false);
const imagePreviewUrl = ref(null);
const isEditData = ref(false);
const isEditingCoords = ref(false);
const areaHasCoords = ref(false);
const canSaveCoords = ref(false);
const area_id = ref(null);

const toggleView = () => {
  isListView.value = !isListView.value;
};

const toggleAddModal = () => {
  isEditData.value = false;
};

const toggleEditModal = (area) => {
  isEditData.value = true;
  getform(area);
};

const map_attributes = reactive({
  'zoom': 14,
  'center': [8.06667, 123.75],
});

onMounted(() => {
  fetchCityBoundary();
});

const cityBoundary = ref([]);

function fetchCityBoundary() {
  const url =
    "https://overpass-api.de/api/interpreter?data=[out:json][timeout:25];nwr['name'='Tangub'];out geom;";

  fetch(url)
    .then((response) => response.json())
    .then((data) => {
      // Convert data to polygon structure suitable for LPolygon
      cityBoundary.value = convertToLatLngArray(data);
      //console.log(cityBoundary.value);
    })
    .catch((error) => {
      console.error("Error fetching city boundary:", error);
    });
}

// Convert Overpass API response into LatLng format for Leaflet polygons
function convertToLatLngArray(data) {
  const polygons = [];

  data.elements.forEach((element) => {
    if (element.type === "relation" && element.members) {
      const polygon = element.members
        .filter((member) => member.geometry)
        .map((member) =>
          member.geometry.map((coord) => [coord.lat, coord.lon])
        );

      polygons.push(polygon.flat()); // Flatten to get an array of coordinates
    }
  });

  return polygons;
}

const form = useForm({
  name: '',
  description: '',
  image: ''
});

const latlongForm = useForm({
  lat: '',
  long: ''
});

const handleImageUpload = (event) => {
  form.image = event.target.files[0];
  //console.log(form.image);
  imagePreviewUrl.value = URL.createObjectURL(form.image);
};

const addArea = () => {
  form.post(route('ceedo.post-establishments'), {
    onSuccess: page => {
      isAddArea.value = false;
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

const editArea = () => {
  router.post(route('ceedo.put-establishments', area_id.value),
    {
      _method: 'put',
      ...form,
    },
    {
      onSuccess: page => {
        isAddArea.value = false;
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

const updateAreaCoordinate = () => {
  latlongForm.put(route('ceedo.put-establishments-coords', area_id.value), {
    onSuccess: page => {
      Swal.fire({
        toast: true,
        icon: 'success',
        position: 'top-end',
        showConfirmButton: true,
        title: page.props.flash.success,
      });
      isEditingCoords.value = false;
    }
  });
};

const resetFields = () => {
  form.name = '';
  form.description = '';
  form.image = null;
  area_id.value = null;
  usePage().props.errors = [];
  imagePreviewUrl.value = null;
};

const getform = (area) => {
  area_id.value = area.id;
  form.name = area.name;
  form.description = area.description;
  form.image = area.image;
  imagePreviewUrl.value = '/images/Areas/Establishment/' + form.image;
};

const tempLat = ref(null);
const tempLong = ref(null);

const getAreaCoords = (area) => {
  isAreaModalInMap.value = true;
  area_id.value = area.id;
  latlongForm.lat = area.lat;
  latlongForm.long = area.long;
  isAreaHasCoords(area.lat, area.long);
  console.log(areaHasCoords.value)
  tempLat.value = area.lat;
  tempLong.value = area.long;
};

const cancelEditCoords = () => {
  latlongForm.lat = tempLat.value;
  latlongForm.long = tempLong.value;
  isEditingCoords.value = false;
};

function isAreaHasCoords(lat, long) {
  areaHasCoords.value = lat && long;
}

const customIcon = L.icon({
  iconUrl: '/vendor.png',
  iconSize: [32, 32],
  iconAnchor: [16, 32],
  popupAnchor: [0, -32]
});

watch(() => [latlongForm.lat, latlongForm.long], ([value1, value2]) => {
  canSaveCoords.value = value1 && value2;
});

</script>

<template>

  <Head title="Establishments" />
  <Layout>
    <div class="container px-6 mx-auto grid">
      <div class="flex justify-between items-center flex-column sm:flex-row">
        <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
          Areas
        </h2>

        <div class="flex justify-end gap-5">
          <div class="btn-group">
            <button class="btn" :class="{ 'btn-primary disabled pointer-events-none': isListView }"
              @click="toggleView()"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round"
                  d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
              </svg>

            </button>
            <button class="btn" :class="{ 'btn-primary text-orange-400 disabled pointer-events-none': !isListView }"
              @click="toggleView()"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round"
                  d="M9 6.75V15m6-6v8.25m.503 3.498 4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 0 0-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0Z" />
              </svg>
            </button>
          </div>
          <label v-if="isListView" for="addArea" @click="toggleAddModal()" style="cursor: pointer;"><svg
              xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
              stroke="currentColor" class="size-10 hover:text-gray-400 text-orange-600">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>

          </label>
        </div>
      </div>

      <div class="mb-2">
        <div class="breadcrumbs text-sm">
          <ul>
            <li>
              <Link :href="route('ceedo.establishments')" class="text-gray-800"
                :class="{ 'text-gray-400 disabled:opacity-50 pointer-events-none': $page.component.startsWith('Ceedo/Dashboard/Areas/Establishment') }">
              Area</Link>
            </li>
            <li></li>
          </ul>
        </div>
      </div>
      <!-- CTA -->
      <hr>

      <div class="mt-2 grid grid-cols-12 gap-5" v-if="isListView">
        <div
          class="max-w-sm col-span-12 md:col-span-4 sm:col-span-6 rounded overflow-hidden transition-all ease delay-150  hover:shadow-lg hover:shadow-orange-200 relative"
          v-for="area in areas" :key="area.id">
          <div v-if="!area.image">
            <label for="addArea" @click="toggleEditModal(area)"
              class="absolute text-dark rounded-full opacity-0 hover:opacity-100 transition-all duration-200 outline outline-offset-2 outline-gray-300 p-1 m-2"
              style="z-index: 10; cursor: pointer;"><svg xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round"
                  d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
              </svg></label>
            <img
              class="object-none h-48 w-96 transition ease-in-out delay-150 hover:-translate-y-1 hover:scale-110  duration-300"
              src="/images/Areas/Establishment/vendor.jpg" alt="Sunset in the mountains">
          </div>
          <div v-else>
            <label for="addArea" @click="toggleEditModal(area)"
              class="absolute text-dark rounded-full opacity-0 hover:opacity-100 transition-all duration-200 outline outline-offset-2 outline-gray-300 p-1 m-2"
              style="z-index: 10; cursor: pointer;"><svg xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round"
                  d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
              </svg></label>
            <img
              class="object-none h-48 w-96 transition ease-in-out delay-150 hover:-translate-y-1 hover:scale-110  duration-300"
              :src="'/images/Areas/Establishment/' + area.image" alt="Sunset in the mountains">
          </div>
          <Link :href="route('ceedo.stalls', area.id)">
          <div class="px-6 py-4">
            <div class="font-bold text-xl mb-2">{{ area.name }}</div>
            <p class="text-gray-700 text-base">
              {{ area.description }}
            </p>
          </div>
          <div class="px-6 pt-4 pb-2 flex justify-between items-center mb-2">
            <div class="flex justify-center items-center">
              <span class="inline text-sm font-semibold text-gray-700 mr-2"><svg xmlns="http://www.w3.org/2000/svg"
                  fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                </svg>

              </span>
              <span class="text-sm text-gray-700">{{ area.business.length }}</span>
            </div>
            <div class="flex justify-center items-center">
              <span class="inline text-sm font-semibold text-gray-700 mr-2"><svg xmlns="http://www.w3.org/2000/svg"
                  fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" />
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" />
                </svg>

              </span>
              <span class="text-sm text-gray-700">{{ area.establishment.length }}</span>
            </div>
            <div class="flex justify-between items-center gap-2">
              <div class="flex flex-row items-center">
                <span class="inline text-sm font-semibold text-gray-700 mr-2"><svg xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                      d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                  </svg>

                </span>
                <span class="text-sm text-gray-700">
                  {{
                    area?.establishment
                      ? area.establishment.reduce((total, est) => total + (est.establishment_units?.length || 0), 0)
                      : 0
                  }}
                </span>
              </div>

            </div>
          </div>
          </Link>
        </div>
        <div v-if="areas.length <= 0" class="col-span-12 text-center text-xl">
          No Area Added Yet!
        </div>
      </div>

      <div class="mb-2 p-2 flex items-center justify-center" v-if="!isListView">
        <div class="h-[500px] w-full relative">
          <l-map ref="map" v-model:zoom="map_attributes.zoom" :center="map_attributes.center" class="h-full w-full"
            style="z-index: 1;">
            <l-tile-layer url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png" layer-type="base"
              name="OpenStreetMap">
            </l-tile-layer>
            <div v-for="area in $page.props.areas" :key="area.id">
              <l-marker v-if="area.lat" :lat-lng="[area.lat, area.long]" :icon="customIcon">
                <l-popup class="bg-blue w-100 flex justify-center items-center flex-col">
                  <div class="mb-2 font-bold border-b ">{{ area.name }}</div>
                  <div class="mb-2">Vendors: {{ area.businesses_count }}</div>
                  <div class="mb-2">Sections {{ area.establishment_count }}: 
                  </div>
                  <div class="mb-2">Rate: {{'₱' + area.lowest_rate + ' - ' + '₱' + area.highest_rate }}</div>
                  <div>
                    <Link
                      class="btn rounded-none"
                      :href="route('ceedo.stalls', area.id)">More Details
                    </Link>
                  </div>
                </l-popup>
              </l-marker>
            </div>
            <l-polygon v-for="(coords, index) in cityBoundary" :key="index" :lat-lngs="coords" color="orange"
              :weight="2" />
          </l-map>
          <label for="drawer-right"
            class="absolute top-5 right-5 bg-gray-300 rounded hover:bg-gray-200 transition-all duration-200 text-orange-700 p-2"
            style="z-index: 10; cursor: pointer;"><svg xmlns="http://www.w3.org/2000/svg" fill="none"
              viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M6.429 9.75 2.25 12l4.179 2.25m0-4.5 5.571 3 5.571-3m-11.142 0L2.25 7.5 12 2.25l9.75 5.25-4.179 2.25m0 0L21.75 12l-4.179 2.25m0 0 4.179 2.25L12 21.75 2.25 16.5l4.179-2.25m11.142 0-5.571 3-5.571-3" />
            </svg>
          </label>
        </div>
      </div>

    </div>



    <!--Add Area Modal-->
    <input class="modal-state" id="addArea" type="checkbox" v-model="isAddArea" />
    <div class="modal ">
      <label class="modal-overlay"></label>
      <form @submit.prevent="isEditData ? editArea() : addArea()">
        <div class="modal-content flex flex-col gap-5">
          <label for="addArea" @click="resetFields()"
            class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</label>
          <h2 class="text-xl">{{ isEditData ? 'Edit Area' : 'Add Area' }}</h2>
          <div class="max-w-sm col-span-12 md:col-span-6 sm:col-span-4 rounded overflow-hidden">
            <InputError :message="errors.image" />
            <div v-if="!imagePreviewUrl || !form.image" class="relative">
              <label for="image-file"
                class="h-48 w-96 absolute transition-all duration-300 opacity-0 hover:opacity-70 hover:bg-gray-10 flex justify-center items-center flex-col text-xl hover:text-white hover:show"
                style="cursor: pointer;">
                <span class="inline-block"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                      d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                  </svg></span>
                <span class="inline-block">Upload Image</span>
              </label>
              <img class="object-none h-48 w-96" src="/images/Areas/Establishment/vendor.jpg" alt="Area Default Image">
            </div>
            <div v-else class="relative">
              <label for="image-file"
                class="h-48 w-96 absolute transition-all duration-300 opacity-0 hover:opacity-70 hover:bg-gray-10 flex justify-center items-center flex-col text-xl hover:text-white hover:show"
                style="cursor: pointer;">
                <span class="inline-block"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                      d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                  </svg></span>
                <span class="inline-block">Upload Image</span>
              </label>
              <img class="object-none h-48 w-96" :src="imagePreviewUrl" alt="Area Default Image">
            </div>
            <input @input="handleImageUpload" id="image-file" type="file" class="hidden" />
            <div class="py-4">
              <input type="text" class="input-ghost-primary input max-w-full mb-2" placeholder="Area Name"
                v-model="form.name" required>
              <InputError :message="errors.name" />
              <textarea class="textarea max-w-full mb-2" placeholder="Description" v-model="form.description"
                required></textarea>
            </div>
            <div class="px-6 pt-4 pb-2 flex gap-2 justify-end">
              <button class="btn btn-primary" type="submit">{{ isEditData ? 'Save' : 'Add' }}</button>
              <label class="btn" @click="resetFields()" for="addArea">Cancel</label>
            </div>
          </div>

        </div>
      </form>
    </div>
    <!--End Add Area Modal-->

    <!--Open Overlay for Area in Map View-->
    <input type="checkbox" id="drawer-right" class="drawer-toggle hidden" />
    <label class="overlay"></label>
    <div class="drawer drawer-right">
      <div class="drawer-content pt-10 flex flex-col h-full">
        <label for="drawer-right" class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</label>
        <div>
          <h2 class="text-xl font-medium">Area List</h2>
          <div class="gap-2 px-1 py-2 flex flex-col">
            <div v-for="area in areas" :key="area.id"
              class="p-1 py-3 rounded text-md bg-gray-100 hover:bg-gray-200 transition-ease duration-200 flex flex-row items-center"
              style="cursor: pointer;" @click="getAreaCoords(area)">

              <span class="mr-2"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                  stroke-width="1.5" stroke="currentColor" class="size-6">
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                </svg>
              </span>
              <span>{{ area.name }}</span>
            </div>

          </div>
        </div>
      </div>
    </div>
    <!--End Overlay for Area in Map View-->

    <input class="modal-state" id="mapAreaModal" type="checkbox" v-model="isAreaModalInMap" />
    <div class="modal " style="z-index: 10000;">
      <label class="modal-overlay"></label>
      <form @submit.prevent="updateAreaCoordinate()">
        <div class="modal-content flex flex-col gap-5">
          <label for="mapAreaModal" class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</label>
          <h2 class="text-xl">Area Coordinates</h2>
          <div class="max-w-sm col-span-12 md:col-span-6 sm:col-span-4 rounded overflow-hidden">
            <div class="form-group">
              <label for="lat">Latitude</label>
              <input type="text" class="input input-primary" id="lat" v-model="latlongForm.lat"
                :disabled="!isEditingCoords">
            </div>
            <div class="form-group">
              <label for="long">Longitude</label>
              <input type="text" class="input input-primary" id="long" v-model="latlongForm.long"
                :disabled="!isEditingCoords">
            </div>
            <div class="px-6 pt-4 pb-2 flex gap-2 justify-end">
              <button class="btn btn-primary" type="button" v-if="!isEditingCoords"
                @click="isEditingCoords = true">Edit</button>
              <button class="btn btn-primary" type="submit" v-else :disabled="!canSaveCoords">Save</button>
              <label class="btn" @click="cancelEditCoords()" v-if="isEditingCoords">Cancel</label>
            </div>
          </div>

        </div>
      </form>
    </div>

  </Layout>
</template>