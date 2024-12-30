<script setup>
import Container from './Container.vue';
import "leaflet/dist/leaflet.css";
import { LMap, LTileLayer, LPolygon, LMarker, LPopup } from "@vue-leaflet/vue-leaflet";
import { reactive } from 'vue';

const map_attributes = reactive({
  'zoom': 14,
  'center': [8.06667, 123.75],
});

</script>

<template>
  <div id="features">
    <Container data-aos="fade-up" data-aos-duration="500" data-aos-easing="ease-in-out">
      <div class="md:w-2/3 lg:w-1/2">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6 text-orange-500">
          <path fill-rule="evenodd"
            d="M9 4.5a.75.75 0 01.721.544l.813 2.846a3.75 3.75 0 002.576 2.576l2.846.813a.75.75 0 010 1.442l-2.846.813a3.75 3.75 0 00-2.576 2.576l-.813 2.846a.75.75 0 01-1.442 0l-.813-2.846a3.75 3.75 0 00-2.576-2.576l-2.846-.813a.75.75 0 010-1.442l2.846-.813A3.75 3.75 0 007.466 7.89l.813-2.846A.75.75 0 019 4.5zM18 1.5a.75.75 0 01.728.568l.258 1.036c.236.94.97 1.674 1.91 1.91l1.036.258a.75.75 0 010 1.456l-1.036.258c-.94.236-1.674.97-1.91 1.91l-.258 1.036a.75.75 0 01-1.456 0l-.258-1.036a2.625 2.625 0 00-1.91-1.91l-1.036-.258a.75.75 0 010-1.456l1.036-.258a2.625 2.625 0 001.91-1.91l.258-1.036A.75.75 0 0118 1.5zM16.5 15a.75.75 0 01.712.513l.394 1.183c.15.447.5.799.948.948l1.183.395a.75.75 0 010 1.422l-1.183.395c-.447.15-.799.5-.948.948l-.395 1.183a.75.75 0 01-1.422 0l-.395-1.183a1.5 1.5 0 00-.948-.948l-1.183-.395a.75.75 0 010-1.422l1.183-.395c.447-.15.799-.5.948-.948l.395-1.183A.75.75 0 0116.5 15z"
            clip-rule="evenodd" />
        </svg>

        <h2 class="my-5 text-2xl font-bold dark:text-white md:text-4xl text-blue-600">
          Redefining Entrepreneurship with Tech-Enabled Solutions
        </h2>
        <p class="text-gray-600 dark:text-gray-300">Empowers entrepreneurs with innovative tools and technology to
          streamline business operations, enhance decision-making, and drive growth in the modern market.</p>
      </div>
      <div class="mt-16 grid px-10 gap-5 md:px-0 py-10 grid-cols-1 overflow-hidden rounded-3xl border border-gray-100">
        <div class="col-span-1">
          <div class="mb-2 p-2 flex items-center justify-center">
            <div class="h-[500px] w-full relative">
              <l-map ref="map" v-model:zoom="map_attributes.zoom" :center="map_attributes.center" class="h-full w-full"
                style="z-index: 1;">
                <l-tile-layer url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png" layer-type="base"
                  name="OpenStreetMap">
                </l-tile-layer>
                <l-marker :lat-lng="map_attributes.center" />
              </l-map>
            </div>
          </div>
        </div>
      </div>
    </Container>
  </div>
</template>