<script setup>
import Container from './Container.vue';

</script>

<template>
	<div class="relative" id="services">
		<div aria-hidden="true" class="absolute inset-0 grid grid-cols-2 -space-x-52 opacity-40 dark:opacity-20">
			<div class="blur-[106px] h-56 bg-gradient-to-br from-green-700 to-purple-400 dark:from-blue-700"></div>
			<div class="blur-[106px] h-32 bg-gradient-to-r from-cyan-400 to-sky-300 dark:to-indigo-600"></div>
		</div>
		<Container>
			<div class="relative pt-10 ml-auto">
				<div class="max-w-4xl mx-auto rounded-lg shadow-md overflow-hidden">
					<div class="flex flex-col items-center text-center p-6">
						<!-- Logo Section -->
						<img src="/images/tangubcity.png" alt="Tangub City Logo" class="w-27 h-24 mb-4 rounded-full" />
						<h1 class="text-2xl font-bold text-gray-800">Tangub City CEEDO Online Monitoring System</h1>
						<p class="text-gray-600 mt-4 leading-relaxed">
							The <span class="font-semibold">Tangub City Economic Enterprise Development Office
								(CEEDO)</span> Online Monitoring System is an innovative platform designed to simplify
							the vendor application and monitoring process.
							This system allows the residents of Tangub City to apply for stalls or spaces in
							CEEDO-managed markets, including the <span class="font-semibold">Asenso Market</span>, <span
								class="font-semibold">People's Market</span>, and other areas with ease.
						</p>
					</div>

					<div class="px-6 py-4">
						<h2 class="text-lg font-semibold text-gray-700 mb-2">Key Features</h2>
						<ul class="list-disc list-inside text-gray-600">
							<li>Online application submission for vendor spaces.</li>
							<li>Real-time monitoring of application status and updates.</li>
							<li>Efficient market management for CEEDO administrators.</li>
						</ul>
					</div>

					<div class="bg-gray-50 px-6 py-4 text-center">
						<p class="text-sm text-gray-500">
							This initiative reflects Tangub City's commitment to fostering economic growth, supporting
							local entrepreneurs, and improving government services through technology.
						</p>
					</div>
				</div>
			</div>
		</Container>
	</div>
</template>