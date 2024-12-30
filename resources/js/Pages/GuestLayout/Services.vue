<script setup>
import "leaflet/dist/leaflet.css";
import { LMap, LTileLayer, LPolygon, LMarker, LPopup } from "@vue-leaflet/vue-leaflet";
import L from "leaflet";
import Container from './Container.vue';
import { Link } from '@inertiajs/vue3';
import { ref, onMounted, reactive } from 'vue';

defineProps({
    areas: Array
});

const isListView = ref(true);
const isOpenAreaModal = ref(false);

const toggleView = () => {
    isListView.value = !isListView.value;
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

const customIcon = L.icon({
    iconUrl: '/vendor.png',
    iconSize: [32, 32],
    iconAnchor: [16, 32],
    popupAnchor: [0, -32]
});

const areaData = ref([]);

function getAreaData(area) {
    isOpenAreaModal.value = true;
    areaData.value = area;
}

function exploreArea(name,id){
    window.location.href = route('guest.establishment', {name:name, id:id});
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
                <div class="text-2xl mb-10 text-center">Available Areas</div>
                <div class="flex justify-end gap-5 my-5">
                    <div class="btn-group">
                        <button class="btn" :class="{ 'btn-primary disabled pointer-events-none': isListView }"
                            @click="toggleView()"><svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                            </svg>

                        </button>
                        <button class="btn"
                            :class="{ 'btn-primary text-orange-400 disabled pointer-events-none': !isListView }"
                            @click="toggleView()"><svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 6.75V15m6-6v8.25m.503 3.498 4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 0 0-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0Z" />
                            </svg>
                        </button>
                    </div>

                </div>
                <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3" v-if="isListView">
                    <div class="group p-3 sm:p-8 rounded-3xl  border border-gray-100 dark:shadow-none dark:border-gray-700 dark:bg-gray-800 bg-opacity-50 shadow-2xl shadow-gray-600/10"
                        v-for="area in $page.props.areas" :key="area.id">
                        <div class="relative overflow-hidden rounded-xl">
                            <img v-if="area.image" :src="'/images/Areas/Establishment/' + area.image" alt="art cover"
                                loading="lazy" width="1000" height="667"
                                class="h-64 w-full object-cover object-top transition duration-500 group-hover:scale-105" />
                            <img v-else src="/images/Areas/Establishment/vendor.jpg" alt="art cover" loading="lazy"
                                width="1000" height="667"
                                class="h-64 w-full object-cover object-top transition duration-500 group-hover:scale-105" />
                        </div>
                        <div class="mt-6 relative">
                            <h3 class="text-2xl font-semibold text-gray-800 dark:text-white">
                                {{ area.name }}
                            </h3>
                            <p class="mt-6 mb-8 text-gray-600 dark:text-gray-300">
                                {{ area.description }}
                            </p>
                            <Link class="inline-block"
                                :href="route('guest.establishment', area.id)">
                            <span class="text-info dark:text-blue-300 hover:text-blue-600">Explore</span>
                            </Link>
                        </div>

                    </div>
                </div>

                <div class="mb-2 p-2 flex items-center justify-center" v-if="!isListView">
                    <div class="h-[500px] w-full relative">
                        <l-map ref="map" v-model:zoom="map_attributes.zoom" :center="map_attributes.center"
                            class="h-full w-full" style="z-index: 1;">
                            <l-tile-layer url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png" layer-type="base"
                                name="OpenStreetMap">
                            </l-tile-layer>
                            <div v-for="area in $page.props.areas" :key="area.id">
                                <l-marker v-if="area.lat" :lat-lng="[area.lat, area.long]" :icon="customIcon"
                                    @click="getAreaData(area)" />
                            </div>
                            <l-polygon v-for="(coords, index) in cityBoundary" :key="index" :lat-lngs="coords"
                                color="orange" :weight="2" />
                        </l-map>
                    </div>
                </div>
            </div>
        </Container>
    </div>

    <input class="modal-state" id="modal-1" type="checkbox" v-model="isOpenAreaModal" />
    <div class="modal" style="margin-top: 0;">
        <label class="modal-overlay" for="modal-1"></label>
        <div class="modal-content flex flex-col gap-5">
            <label for="modal-1" class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</label>
            <h2 class="text-xl">{{ areaData.name }}</h2>
            <div class="relative overflow-hidden rounded-xl">
                <img v-if="areaData.image" :src="'/images/Areas/Establishment/' + areaData.image" alt="art cover"
                    loading="lazy" width="1000" height="667"
                    class="h-64 w-full object-cover object-top transition duration-500 group-hover:scale-105" />
                <img v-else src="/images/Areas/Establishment/vendor.jpg" alt="art cover" loading="lazy" width="1000"
                    height="667"
                    class="h-64 w-full object-cover object-top transition duration-500 group-hover:scale-105" />
            </div>
            <div class="mt-6 relative">
                <p class="mt-6 mb-8 text-gray-600 dark:text-gray-300">
                    {{ areaData.description }}
                </p>
            </div>
            <div class="flex gap-3">
                <button class="btn btn-primary btn-block"
                    @click="exploreArea(areaData.name, areaData.id)">Explore</button>
                <button class="btn btn-block" @click="isOpenAreaModal = false">Close</button>
            </div>
        </div>
    </div>
</template>