<?php
// tappersia/admin/views/banner-types/hotel-carousel/hotel-carousel-settings.php
?>

<style>
    .hotel-label-base { margin-top: 7px; width: fit-content; border-radius: 3px; padding: 2px 6px; font-size: 11px; line-height: 1; display: inline-block; }
    .hotel-label-luxury { background: #333333; color: #fff; }
    .hotel-label-business { background: #DAF6FF; color: #04A5D8; }
    .hotel-label-boutique { background: #f8f3b0; color: #a8a350; }
    .hotel-label-traditional { background: #FAECE0; color: #B68960; }
    .hotel-label-economy { background: #FFE9F7; color: #FF48C3; }
    .hotel-label-hostel { background: #B0B0B0; color: #FFF; }
    .hotel-label-default { background: #e0e0e0; color: #555; }
</style>
<div class="bg-[#434343] p-5 rounded-lg shadow-xl mr-2">
    <h3 class="font-bold text-xl text-white tracking-wide mb-5">Content Source</h3>
    <div class="flex gap-4">
        <button @click="openHotelModal({ multiSelect: true })" class="w-full flex gap-2 justify-center items-center bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
             <span class="dashicons dashicons-building"></span> {{ banner.hotel_carousel.selectedHotels && banner.hotel_carousel.selectedHotels.length > 0 ? 'Edit Selected Hotels' : 'Select Hotels' }}
        </button>
    </div>
</div>
<div class="bg-[#434343] p-5 rounded-lg shadow-xl mr-2">
    <h3 class="font-bold text-xl text-white tracking-wide mb-5">Device</h3>
    <div class="flex  bg-[#292929] rounded-lg p-1">
        <button @click="currentView = 'desktop'" :class="{'active-tab': currentView === 'desktop'}" class="flex-1 tab-button rounded-md">Desktop</button>
        <button @click="currentView = 'mobile'" :class="{'active-tab': currentView === 'mobile'}" class="flex-1 tab-button rounded-md">Mobile</button>
    </div>
</div>
<div class="bg-[#434343] p-5 rounded-lg shadow-xl mr-2" v-if="currentView === 'desktop'">
     <div v-if="banner.hotel_carousel && banner.hotel_carousel.settings">
        <h3 class="font-bold text-xl text-white tracking-wide mb-5">Direction</h3>
        <div class="flex rounded-lg bg-[#292929] overflow-hidden p-1">
            <button @click="banner.hotel_carousel.settings.direction = 'ltr'" :class="{'active-tab': banner.hotel_carousel.settings.direction === 'ltr'}" class="flex-1 tab-button rounded-md">LTR</button>
            <button @click="banner.hotel_carousel.settings.direction = 'rtl'" :class="{'active-tab': banner.hotel_carousel.settings.direction === 'rtl'}" class="flex-1 tab-button rounded-md">RTL</button>
        </div>
    </div>
</div>

<div class="bg-[#434343] p-5 rounded-lg shadow-xl mr-2">
    <div v-if="banner.hotel_carousel" :key="currentView">
         <div :set="settings = currentView === 'desktop' ? banner.hotel_carousel.settings : banner.hotel_carousel.settings_mobile">
            <h3 class="font-bold text-xl text-white tracking-wide mb-5 capitalize">{{ currentView }} Carousel Settings</h3>
            <div class="space-y-4">

                <div v-if="currentView === 'desktop'">
                    <h4 class="section-title">Slides Per View</h4>
                     <div class="flex rounded-lg bg-[#292929] overflow-hidden p-1" >
                        <button v-for="num in [1, 2, 3, 4]" :key="num" @click="settings.slidesPerView = num" :class="{'active-tab': settings.slidesPerView === num}" class="flex-1 tab-button rounded-md">{{ num }}</button>
                    </div>
                </div>
                 <hr v-if="currentView === 'desktop'" class="section-divider">

                <div><div class="flex items-center justify-between bg-[#292929] p-2 rounded-md"><label class="setting-label-sm !mb-0">Loop Slides</label><label class="relative inline-flex items-center cursor-pointer"><input type="checkbox" v-model="settings.loop" class="sr-only peer"><div class="w-11 h-6 bg-gray-600 rounded-full peer peer-checked:after:translate-x-full after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div></label></div></div>
                <hr class="section-divider">

                <div><div class="flex items-center justify-between bg-[#292929] p-2 rounded-md"><label class="setting-label-sm !mb-0">Double Carousel (2 Rows)</label><label class="relative inline-flex items-center cursor-pointer"><input type="checkbox" v-model="settings.isDoubled" class="sr-only peer"><div class="w-11 h-6 bg-gray-600 rounded-full peer peer-checked:after:translate-x-full after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div></label></div></div>
                 <transition name="fade"><div v-if="settings.isDoubled && !settings.loop" class="mt-4"><label class="setting-label-sm">Grid Fill Direction</label><div class="flex rounded-lg bg-[#292929] overflow-hidden p-1"><button @click="settings.gridFill = 'column'" :class="{'active-tab': settings.gridFill === 'column'}" class="flex-1 tab-button rounded-md">Column</button><button @click="settings.gridFill = 'row'" :class="{'active-tab': settings.gridFill === 'row'}" class="flex-1 tab-button rounded-md">Row</button></div></div></transition>
                <hr class="section-divider">

                <div v-if="currentView === 'desktop'">
                    <h4 class="section-title">Autoplay</h4>
                    <div class="flex items-center justify-between bg-[#292929] p-2 rounded-md"><label class="setting-label-sm !mb-0">Enable Autoplay</label><label class="relative inline-flex items-center cursor-pointer"><input type="checkbox" v-model="settings.autoplay.enabled" class="sr-only peer"><div class="w-11 h-6 bg-gray-600 rounded-full peer peer-checked:after:translate-x-full after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div></label></div>
                    <div v-if="settings.autoplay.enabled" class="mt-4"><label class="setting-label-sm">Autoplay Delay (ms)</label><input type="number" v-model.number="settings.autoplay.delay" class="yab-form-input"></div>
                    <hr class="section-divider">
                </div>

                <div>
                    <h4 class="section-title">Controls</h4>
                    <div class="flex items-center justify-between bg-[#292929] p-2 rounded-md mb-2"><label class="setting-label-sm !mb-0">Enable Navigation</label><label class="relative inline-flex items-center cursor-pointer"><input type="checkbox" v-model="settings.navigation.enabled" class="sr-only peer"><div class="w-11 h-6 bg-gray-600 rounded-full peer peer-checked:after:translate-x-full after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div></label></div>
                    <div class="flex items-center justify-between bg-[#292929] p-2 rounded-md"><label class="setting-label-sm !mb-0">Enable Pagination</label><label class="relative inline-flex items-center cursor-pointer"><input type="checkbox" v-model="settings.pagination.enabled" class="sr-only peer"><div class="w-11 h-6 bg-gray-600 rounded-full peer peer-checked:after:translate-x-full after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div></label></div>
                    <div v-if="settings.pagination.enabled && currentView === 'desktop'" class="mt-4 space-y-2">
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="setting-label-sm">Pagination Color</label>
                                <div class="yab-color-input-wrapper"><input type="color" v-model="settings.pagination.paginationColor" class="yab-color-picker"><input type="text" v-model="settings.pagination.paginationColor" class="yab-hex-input"></div>
                            </div>
                            <div>
                                <label class="setting-label-sm">Pagination Active Color</label>
                                <div class="yab-color-input-wrapper"><input type="color" v-model="settings.pagination.paginationActiveColor" class="yab-color-picker"><input type="text" v-model="settings.pagination.paginationActiveColor" class="yab-hex-input"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="bg-[#434343] p-5 rounded-lg shadow-xl mr-2" v-if="currentView === 'desktop'">
     <div v-if="banner.hotel_carousel && banner.hotel_carousel.settings">
        <h3 class="font-bold text-xl text-white tracking-wide mb-5">Header of Carousel</h3>
        <div class="space-y-4">
             <h4 class="section-title">Content</h4>
            <div><label class="setting-label-sm">Header Text</label><input type="text" v-model="banner.hotel_carousel.settings.header.text" class="yab-form-input"></div>
            <hr class="section-divider my-6">
            <h4 class="section-title">Layout & Style</h4>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="setting-label-sm">Font Size (px)</label><input type="number" v-model.number="banner.hotel_carousel.settings.header.fontSize" class="yab-form-input"></div>
                <div><label class="setting-label-sm">Font Weight</label><select v-model="banner.hotel_carousel.settings.header.fontWeight" class="yab-form-input"><option value="400">Normal</option><option value="500">Medium</option><option value="600">Semi-Bold</option><option value="700">Bold</option><option value="800">Extra Bold</option></select></div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="setting-label-sm">Text Color</label><div class="yab-color-input-wrapper"><input type="color" v-model="banner.hotel_carousel.settings.header.color" class="yab-color-picker"><input type="text" v-model="banner.hotel_carousel.settings.header.color" class="yab-hex-input"></div></div>
                <div><label class="setting-label-sm">Accent Line Color</label><div class="yab-color-input-wrapper"><input type="color" v-model="banner.hotel_carousel.settings.header.lineColor" class="yab-color-picker"><input type="text" v-model="banner.hotel_carousel.settings.header.lineColor" class="yab-hex-input"></div></div>
            </div>
            <div><label class="setting-label-sm">Space between header and slider</label><input type="number" v-model.number="banner.hotel_carousel.settings.header.marginTop" class="yab-form-input"></div>
        </div>
     </div>
</div>

<div class="bg-[#434343] p-5 rounded-lg shadow-xl mr-2">
     <div v-if="banner.hotel_carousel" :key="'card_'+currentView">
         <div :set="settings = currentView === 'desktop' ? banner.hotel_carousel.settings : banner.hotel_carousel.settings_mobile">
            <h3 class="font-bold text-xl text-white tracking-wide mb-5 capitalize">{{ currentView }} Card Settings (Legacy)</h3>
             <p class="text-xs text-gray-400 mb-4">Note: The new card design uses predefined styles. Some settings below might not apply.</p>
             <div :set="card = settings.card">
                 <div class="space-y-4">
                     <div>
                        <h4 class="section-title">Layout</h4>
                        <div class="grid grid-cols-2 gap-2">
                            <div><label class="setting-label-sm">Card Height (px)</label><input type="number" v-model.number="card.height" class="yab-form-input" disabled title="Hardcoded in new design"></div>
                            <div><label class="setting-label-sm">Image Height (px)</label><input type="number" v-model.number="card.imageHeight" class="yab-form-input" disabled title="Hardcoded in new design"></div>
                        </div>
                    </div>
                 </div>
            </div>
        </div>
    </div>
</div>