<?php
// tappersia/admin/views/banner-types/tour-carousel/tour-carousel-settings.php
?>
<div class="bg-[#434343] p-5 rounded-lg shadow-xl mr-2">
    <h3 class="font-bold text-xl text-white tracking-wide mb-5">Content Source</h3>
    <div class="flex gap-4">
        <button @click="openTourModal({ multiSelect: true })" class="w-full flex gap-2 justify-center items-center bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700">
            <span class="dashicons dashicons-palmtree"></span>
            {{ banner.tour_carousel.selectedTours && banner.tour_carousel.selectedTours.length > 0 ? 'Edit Selected Tours' : 'Select Tours' }}
        </button>
    </div>
</div>

<div class="bg-[#434343] p-5 rounded-lg shadow-xl mr-2">
    <h3 class="font-bold text-xl text-white tracking-wide mb-5">Carousel Settings</h3>
    <div class="space-y-4">
        <div>
            <label class="setting-label-sm">Slides Per View</label>
            <div class="flex rounded-lg bg-[#292929] overflow-hidden p-1">
                <button v-for="num in [1, 2, 3, 4]" :key="num" @click="banner.tour_carousel.settings.slidesPerView = num" 
                        :class="{'active-tab': banner.tour_carousel.settings.slidesPerView === num}" 
                        class="flex-1 tab-button rounded-md">
                    {{ num }}
                </button>
            </div>
            <p class="text-gray-400 text-xs mt-2">Set the number of slides to show at once.</p>
        </div>
        <hr class="section-divider">
        <div>
            <div class="flex items-center justify-between bg-[#292929] p-2 rounded-md">
                <label class="setting-label-sm !mb-0">Loop Slides</label>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" v-model="banner.tour_carousel.settings.loop" class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-600 rounded-full peer peer-focus:ring-2 peer-focus:ring-blue-500 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div>
                </label>
            </div>
             <p class="text-gray-400 text-xs mt-2">Enable to repeat the carousel from the beginning after the last slide.</p>
        </div>
    </div>
</div>