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
    <h3 class="font-bold text-xl text-white tracking-wide mb-5">Header Settings</h3>
    <div class="space-y-4">
        <div>
            <label class="setting-label-sm">Header Text</label>
            <input type="text" v-model="banner.tour_carousel.settings.header.text" class="yab-form-input">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="setting-label-sm">Font Size (px)</label>
                <input type="number" v-model.number="banner.tour_carousel.settings.header.fontSize" class="yab-form-input">
            </div>
            <div>
                <label class="setting-label-sm">Font Weight</label>
                <select v-model="banner.tour_carousel.settings.header.fontWeight" class="yab-form-input">
                    <option value="400">Normal</option>
                    <option value="500">Medium</option>
                    <option value="600">Semi-Bold</option>
                    <option value="700">Bold</option>
                    <option value="800">Extra Bold</option>
                </select>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="setting-label-sm">Text Color</label>
                <div class="yab-color-input-wrapper">
                    <input type="color" v-model="banner.tour_carousel.settings.header.color" class="yab-color-picker">
                    <input type="text" v-model="banner.tour_carousel.settings.header.color" class="yab-hex-input">
                </div>
            </div>
            <div>
                <label class="setting-label-sm">Accent Line Color</label>
                <div class="yab-color-input-wrapper">
                    <input type="color" v-model="banner.tour_carousel.settings.header.lineColor" class="yab-color-picker">
                    <input type="text" v-model="banner.tour_carousel.settings.header.lineColor" class="yab-hex-input">
                </div>
            </div>
        </div>
         <div>
            <label class="setting-label-sm">space between header and slider</label>
            <input type="number" v-model.number="banner.tour_carousel.settings.header.marginTop" class="yab-form-input" placeholder="Space between header and slider">
        </div>
    </div>
</div>

<div class="bg-[#434343] p-5 rounded-lg shadow-xl mr-2">
    <h3 class="font-bold text-xl text-white tracking-wide mb-5">Carousel Settings</h3>
    <div class="space-y-4">
        <div>
            <label class="setting-label-sm">Direction</label>
            <div class="flex rounded-lg bg-[#292929] overflow-hidden p-1">
                <button @click="banner.tour_carousel.settings.direction = 'ltr'" :class="{'active-tab': banner.tour_carousel.settings.direction === 'ltr'}" class="flex-1 tab-button rounded-md">LTR</button>
                <button @click="banner.tour_carousel.settings.direction = 'rtl'" :class="{'active-tab': banner.tour_carousel.settings.direction === 'rtl'}" class="flex-1 tab-button rounded-md">RTL</button>
            </div>
        </div>
        <hr class="section-divider">
        <div>
            <label class="setting-label-sm">Slides Per View</label>
            <div class="flex rounded-lg bg-[#292929] overflow-hidden p-1">
                <button v-for="num in [1, 2, 3, 4]" :key="num" @click="banner.tour_carousel.settings.slidesPerView = num" 
                        :class="{'active-tab': banner.tour_carousel.settings.slidesPerView === num}" 
                        class="flex-1 tab-button rounded-md">
                    {{ num }}
                </button>
            </div>
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
        </div>
        
        <hr class="section-divider">
        <div>
            <div class="flex items-center justify-between bg-[#292929] p-2 rounded-md">
                <label class="setting-label-sm !mb-0">Double Carousel (2 Rows)</label>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" v-model="banner.tour_carousel.settings.isDoubled" class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-600 rounded-full peer peer-checked:after:translate-x-full after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div>
                </label>
            </div>
        </div>
        
        <transition name="fade">
            <div v-if="banner.tour_carousel.settings.isDoubled && !banner.tour_carousel.settings.loop" class="mt-4">
                <label class="setting-label-sm">Grid Fill Direction</label>
                <div class="flex rounded-lg bg-[#292929] overflow-hidden p-1">
                    <button @click="banner.tour_carousel.settings.gridFill = 'column'" :class="{'active-tab': banner.tour_carousel.settings.gridFill === 'column'}" class="flex-1 tab-button rounded-md">Column</button>
                    <button @click="banner.tour_carousel.settings.gridFill = 'row'" :class="{'active-tab': banner.tour_carousel.settings.gridFill === 'row'}" class="flex-1 tab-button rounded-md">Row</button>
                </div>
            </div>
        </transition>

        <hr class="section-divider">

        <div>
            <h4 class="section-title">Autoplay</h4>
            <div class="flex items-center justify-between bg-[#292929] p-2 rounded-md">
                <label class="setting-label-sm !mb-0">Enable Autoplay</label>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" v-model="banner.tour_carousel.settings.autoplay.enabled" class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-600 rounded-full peer peer-focus:ring-2 peer-focus:ring-blue-500 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div>
                </label>
            </div>
            <div v-if="banner.tour_carousel.settings.autoplay.enabled" class="mt-4">
                <label class="setting-label-sm">Autoplay Delay (ms)</label>
                <input type="number" v-model.number="banner.tour_carousel.settings.autoplay.delay" class="yab-form-input" placeholder="e.g., 3000">
            </div>
        </div>

        <hr class="section-divider">
        
        <div>
            <h4 class="section-title">Controls</h4>
            <div class="flex items-center justify-between bg-[#292929] p-2 rounded-md mb-2">
                <label class="setting-label-sm !mb-0">Enable Navigation Buttons</label>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" v-model="banner.tour_carousel.settings.navigation.enabled" class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-600 rounded-full peer peer-focus:ring-2 peer-focus:ring-blue-500 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div>
                </label>
            </div>
             <div class="flex items-center justify-between bg-[#292929] p-2 rounded-md">
                <label class="setting-label-sm !mb-0">Enable Pagination Dots</label>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" v-model="banner.tour_carousel.settings.pagination.enabled" class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-600 rounded-full peer peer-focus:ring-2 peer-focus:ring-blue-500 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div>
                </label>
            </div>
        </div>

    </div>
</div>