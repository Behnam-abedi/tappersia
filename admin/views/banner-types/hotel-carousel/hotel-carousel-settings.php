<?php
// tappersia/admin/views/banner-types/hotel-carousel/hotel-carousel-settings.php
?>
<div class="bg-[#434343] p-5 rounded-lg shadow-xl mr-2">
    <h3 class="font-bold text-xl text-white tracking-wide mb-5">Content Source</h3>
    <div class="flex gap-4">
        <button @click="openHotelModal({ multiSelect: true })" class="w-full flex gap-2 justify-center items-center bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
             <span class="dashicons dashicons-building"></span> {{ banner.hotel_carousel.selectedHotels && banner.hotel_carousel.selectedHotels.length > 0 ? 'Edit Selected Hotels (' + banner.hotel_carousel.selectedHotels.length + ')' : 'Select Hotels' }}
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

<!-- Settings Container -->
<div v-if="banner.hotel_carousel" :key="currentView" class="flex flex-col gap-6">

    <!-- Direction (Desktop Only) -->
    <div v-if="currentView === 'desktop'" class="bg-[#434343] p-5 rounded-lg shadow-xl mr-2">
         <div v-if="banner.hotel_carousel.settings">
            <h3 class="font-bold text-xl text-white tracking-wide mb-5">Direction</h3>
            <div class="flex rounded-lg bg-[#292929] overflow-hidden p-1">
                <button @click="banner.hotel_carousel.settings.direction = 'ltr'" :class="{'active-tab': banner.hotel_carousel.settings.direction === 'ltr'}" class="flex-1 tab-button rounded-md">LTR</button>
                <button @click="banner.hotel_carousel.settings.direction = 'rtl'" :class="{'active-tab': banner.hotel_carousel.settings.direction === 'rtl'}" class="flex-1 tab-button rounded-md">RTL</button>
            </div>
        </div>
    </div>

    <!-- Carousel Settings -->
    <div class="bg-[#434343] p-5 rounded-lg shadow-xl mr-2">
         <div :set="settings = currentView === 'desktop' ? banner.hotel_carousel.settings : banner.hotel_carousel.settings_mobile">
            <h3 class="font-bold text-xl text-white tracking-wide mb-5 capitalize">{{ currentView }} Carousel Settings</h3>
            <div class="space-y-4">

                <div v-if="currentView === 'desktop'">
                    <h4 class="section-title">Slides Per View</h4>
                     <div class="flex rounded-lg bg-[#292929] overflow-hidden p-1" >
                        <button v-for="num in [1, 2, 3, 4]" :key="num" @click="settings.slidesPerView = num" :class="{'active-tab': settings.slidesPerView === num}" class="flex-1 tab-button rounded-md">{{ num }}</button>
                    </div>
                </div>
                 <hr class="section-divider">
                 <div>
                    <label class="setting-label-sm">Space Between Slides (px)</label>
                    <input type="number" v-model.number="settings.spaceBetween" class="yab-form-input">
                 </div>
                 <hr class="section-divider">
                <div><div class="flex items-center justify-between bg-[#292929] p-2 rounded-md"><label class="setting-label-sm !mb-0">Loop Slides</label><label class="relative inline-flex items-center cursor-pointer"><input type="checkbox" v-model="settings.loop" class="sr-only peer"><div class="w-11 h-6 bg-gray-600 rounded-full peer peer-checked:after:translate-x-full after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div></label></div></div>
                <hr class="section-divider">
                <div v-if="currentView === 'desktop'"><div class="flex items-center justify-between bg-[#292929] p-2 rounded-md"><label class="setting-label-sm !mb-0">Double Carousel (2 Rows)</label><label class="relative inline-flex items-center cursor-pointer"><input type="checkbox" v-model="settings.isDoubled" class="sr-only peer"><div class="w-11 h-6 bg-gray-600 rounded-full peer peer-checked:after:translate-x-full after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div></label></div></div>
                 <transition name="fade"><div v-if="currentView === 'desktop' && settings.isDoubled && !settings.loop" class="mt-4"><label class="setting-label-sm">Grid Fill Direction</label><div class="flex rounded-lg bg-[#292929] overflow-hidden p-1"><button @click="settings.gridFill = 'column'" :class="{'active-tab': settings.gridFill === 'column'}" class="flex-1 tab-button rounded-md">Column</button><button @click="settings.gridFill = 'row'" :class="{'active-tab': settings.gridFill === 'row'}" class="flex-1 tab-button rounded-md">Row</button></div></div></transition>
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
                                <div class="yab-color-input-wrapper">
                                    <input type="color" class="yab-color-picker" v-model="settings.pagination.paginationColor">
                                    <input type="text" v-model="settings.pagination.paginationColor" class="yab-hex-input">
                                </div>
                            </div>
                            <div>
                                <label class="setting-label-sm">Pagination Active Color</label>
                                <div class="yab-color-input-wrapper">
                                    <input type="color" v-model="settings.pagination.paginationActiveColor" class="yab-color-picker">
                                    <input type="text" v-model="settings.pagination.paginationActiveColor" class="yab-hex-input">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Header Settings (Desktop Only for Text/Colors) -->
    <div v-if="currentView === 'desktop'" class="bg-[#434343] p-5 rounded-lg shadow-xl mr-2">
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

    <!-- Card Settings -->
    <div class="bg-[#434343] p-5 rounded-lg shadow-xl mr-2">
         <div :set="settings = currentView === 'desktop' ? banner.hotel_carousel.settings : banner.hotel_carousel.settings_mobile">
             <div :set="card = settings.card">
                 <h3 class="font-bold text-xl text-white tracking-wide mb-5 capitalize">{{ currentView }} Card Styling</h3>
                 <div class="space-y-4">

                     <div>
                        <h4 class="section-title">Layout</h4>
                        <div class="grid grid-cols-2 gap-2">
                            <!-- Removed Card Height Input -->
                            <div><label class="setting-label-sm">Overall Padding (px)</label><input type="number" v-model.number="card.padding" class="yab-form-input"></div>
                        </div>
                    </div>
                     <hr class="section-divider">

                     <div v-if="currentView === 'desktop'">
                         <h4 class="section-title">Background</h4>
                         <div><label class="setting-label-sm">Color</label><div class="yab-color-input-wrapper"><input type="color" v-model="card.bgColor" class="yab-color-picker"><input type="text" v-model="card.bgColor" class="yab-hex-input"></div></div>
                         <hr class="section-divider">
                    </div>

                     <div>
                        <h4 class="section-title">Border</h4>
                        <div class="grid grid-cols-3 gap-2">
                            <div><label class="setting-label-sm">Width (px)</label><input type="number" v-model.number="card.borderWidth" class="yab-form-input"></div>
                            <div><label class="setting-label-sm">Radius (px)</label><input type="number" v-model.number="card.borderRadius" class="yab-form-input"></div>
                            <div v-if="currentView === 'desktop'"><label class="setting-label-sm">Color</label><div class="yab-color-input-wrapper"><input type="color" v-model="card.borderColor" class="yab-color-picker"><input type="text" v-model="card.borderColor" class="yab-hex-input"></div></div>
                        </div>
                    </div>
                     <hr class="section-divider">

                    <div>
                        <h4 class="section-title">Image Area</h4>
                        <div class="grid grid-cols-2 gap-2">
                            <!-- Removed Image Height Input -->
                             <div><label class="setting-label-sm">Image Radius (px)</label><input type="number" v-model.number="card.image.radius" class="yab-form-input"></div>
                        </div>
                        <div class="grid grid-cols-2 gap-2 mt-2">
                             <div><label class="setting-label-sm">Inner Padding Y (px)</label><input type="number" v-model.number="card.imageContainer.paddingY" class="yab-form-input"></div>
                             <div><label class="setting-label-sm">Inner Padding X (px)</label><input type="number" v-model.number="card.imageContainer.paddingX" class="yab-form-input"></div>
                        </div>
                    </div>
                    <hr class="section-divider">

                    <div v-if="currentView === 'desktop'">
                         <div :set="overlay = card.imageOverlay">
                            <h4 class="section-title">Image Overlay (Gradient)</h4>
                             <div class="grid grid-cols-2 gap-2">
                                <div><label class="setting-label-sm">Start Color (Top)</label><div class="yab-color-input-wrapper"><input type="color" v-model="overlay.gradientStartColor" class="yab-color-picker"><input type="text" v-model="overlay.gradientStartColor" class="yab-hex-input"></div></div>
                                <div><label class="setting-label-sm">End Color (Bottom)</label><div class="yab-color-input-wrapper"><input type="color" v-model="overlay.gradientEndColor" class="yab-color-picker"><input type="text" v-model="overlay.gradientEndColor" class="yab-hex-input"></div></div>
                            </div>
                             <div class="grid grid-cols-2 gap-2 mt-2">
                                 <div><label class="setting-label-sm">Start Position (%)</label><input type="number" v-model.number="overlay.gradientStartPercent" min="0" max="100" class="yab-form-input"></div>
                                 <div><label class="setting-label-sm">End Position (%)</label><input type="number" v-model.number="overlay.gradientEndPercent" min="0" max="100" class="yab-form-input"></div>
                            </div>
                        </div>
                        <hr class="section-divider">
                    </div>

                     <div :set="badges = card.badges">
                        <h4 class="section-title">Badges (Top of Image)</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <!-- Best Seller Badge -->
                            <div>
                                <label class="setting-label-sm font-bold text-gray-300">Best Seller</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <div v-if="currentView === 'desktop'"><label class="setting-label-sm">Text Color</label><div class="yab-color-input-wrapper"><input type="color" v-model="badges.bestSeller.textColor" class="yab-color-picker"><input type="text" v-model="badges.bestSeller.textColor" class="yab-hex-input"></div></div>
                                    <div v-if="currentView === 'desktop'"><label class="setting-label-sm">BG Color</label><div class="yab-color-input-wrapper"><input type="color" v-model="badges.bestSeller.bgColor" class="yab-color-picker"><input type="text" v-model="badges.bestSeller.bgColor" class="yab-hex-input"></div></div>
                                    <div><label class="setting-label-sm">Font Size (px)</label><input type="number" v-model.number="badges.bestSeller.fontSize" class="yab-form-input"></div>
                                    <div><label class="setting-label-sm">Radius (px)</label><input type="number" v-model.number="badges.bestSeller.radius" class="yab-form-input"></div>
                                    <div><label class="setting-label-sm">Padding X (px)</label><input type="number" v-model.number="badges.bestSeller.paddingX" class="yab-form-input"></div>
                                    <div><label class="setting-label-sm">Padding Y (px)</label><input type="number" v-model.number="badges.bestSeller.paddingY" class="yab-form-input"></div>
                                </div>
                            </div>
                            <!-- Discount Badge -->
                            <div>
                                <label class="setting-label-sm font-bold text-gray-300">Discount</label>
                                 <div class="grid grid-cols-2 gap-2">
                                    <div v-if="currentView === 'desktop'"><label class="setting-label-sm">Text Color</label><div class="yab-color-input-wrapper"><input type="color" v-model="badges.discount.textColor" class="yab-color-picker"><input type="text" v-model="badges.discount.textColor" class="yab-hex-input"></div></div>
                                    <div v-if="currentView === 'desktop'"><label class="setting-label-sm">BG Color</label><div class="yab-color-input-wrapper"><input type="color" v-model="badges.discount.bgColor" class="yab-color-picker"><input type="text" v-model="badges.discount.bgColor" class="yab-hex-input"></div></div>
                                    <div><label class="setting-label-sm">Font Size (px)</label><input type="number" v-model.number="badges.discount.fontSize" class="yab-form-input"></div>
                                    <div><label class="setting-label-sm">Radius (px)</label><input type="number" v-model.number="badges.discount.radius" class="yab-form-input"></div>
                                    <div><label class="setting-label-sm">Padding X (px)</label><input type="number" v-model.number="badges.discount.paddingX" class="yab-form-input"></div>
                                    <div><label class="setting-label-sm">Padding Y (px)</label><input type="number" v-model.number="badges.discount.paddingY" class="yab-form-input"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                     <hr class="section-divider">

                     <div :set="stars = card.stars">
                         <h4 class="section-title">Stars (Bottom of Image)</h4>
                         <div class="grid grid-cols-2 gap-2">
                             <div><label class="setting-label-sm">Shape Size (px)</label><input type="number" v-model.number="stars.shapeSize" class="yab-form-input"></div>
                             <div v-if="currentView === 'desktop'"><label class="setting-label-sm">Shape Color</label><div class="yab-color-input-wrapper"><input type="color" v-model="stars.shapeColor" class="yab-color-picker"><input type="text" v-model="stars.shapeColor" class="yab-hex-input"></div></div>
                             <div><label class="setting-label-sm">Text Size (px)</label><input type="number" v-model.number="stars.textSize" class="yab-form-input"></div>
                             <div v-if="currentView === 'desktop'"><label class="setting-label-sm">Text Color</label><div class="yab-color-input-wrapper"><input type="color" v-model="stars.textColor" class="yab-color-picker"><input type="text" v-model="stars.textColor" class="yab-hex-input"></div></div>
                         </div>
                    </div>
                    <hr class="section-divider">

                     <div :set="bodyContent = card.bodyContent">
                         <h4 class="section-title">Body Content Area</h4>
                         <div class="grid grid-cols-2 gap-2">
                            <div><label class="setting-label-sm">Margin Top (px)</label><input type="number" v-model.number="bodyContent.marginTop" class="yab-form-input"></div>
                            <div><label class="setting-label-sm">Margin X (px)</label><input type="number" v-model.number="bodyContent.marginX" class="yab-form-input"></div>
                             <div class="col-span-2" v-if="currentView === 'desktop'"><label class="setting-label-sm">Default Text Color</label><div class="yab-color-input-wrapper"><input type="color" v-model="bodyContent.textColor" class="yab-color-picker"><input type="text" v-model="bodyContent.textColor" class="yab-hex-input"></div></div>
                         </div>
                    </div>
                    <hr class="section-divider">

                     <div :set="title = card.title">
                        <h4 class="section-title">Title Text</h4>
                         <div class="grid grid-cols-2 gap-2">
                             <div><label class="setting-label-sm">Font Size (px)</label><input type="number" v-model.number="title.fontSize" class="yab-form-input"></div>
                             <div><label class="setting-label-sm">Font Weight</label><select v-model="title.fontWeight" class="yab-form-input"><option value="400">Normal</option><option value="500">Medium</option><option value="600">Semi-Bold</option><option value="700">Bold</option></select></div>
                             <div v-if="currentView === 'desktop'"><label class="setting-label-sm">Color</label><div class="yab-color-input-wrapper"><input type="color" v-model="title.color" class="yab-color-picker"><input type="text" v-model="title.color" class="yab-hex-input"></div></div>
                             <div><label class="setting-label-sm">Line Height</label><input type="number" step="0.1" v-model.number="title.lineHeight" class="yab-form-input"></div>
                             <div><label class="setting-label-sm">Min Height (px)</label><input type="number" v-model.number="title.minHeight" class="yab-form-input"></div>
                        </div>
                    </div>
                     <hr class="section-divider">

                     <div :set="rating = card.rating">
                        <h4 class="section-title">Rating Section</h4>
                        <div class="grid grid-cols-2 gap-2">
                            <div><label class="setting-label-sm">Margin Top (px)</label><input type="number" v-model.number="rating.marginTop" class="yab-form-input"></div>
                            <div><label class="setting-label-sm">Gap (px)</label><input type="number" v-model.number="rating.gap" class="yab-form-input"></div>
                        </div>
                        <div class="mt-2 grid grid-cols-2 gap-4">
                            <div>
                                <label class="setting-label-sm font-bold text-gray-300">Rating Box</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <div v-if="currentView === 'desktop'"><label class="setting-label-sm">BG Color</label><div class="yab-color-input-wrapper"><input type="color" v-model="rating.boxBgColor" class="yab-color-picker"><input type="text" v-model="rating.boxBgColor" class="yab-hex-input"></div></div>
                                    <div v-if="currentView === 'desktop'"><label class="setting-label-sm">Text Color</label><div class="yab-color-input-wrapper"><input type="color" v-model="rating.boxColor" class="yab-color-picker"><input type="text" v-model="rating.boxColor" class="yab-hex-input"></div></div>
                                    <div><label class="setting-label-sm">Font Size (px)</label><input type="number" v-model.number="rating.boxFontSize" class="yab-form-input"></div>
                                    <div><label class="setting-label-sm">Radius (px)</label><input type="number" v-model.number="rating.boxRadius" class="yab-form-input"></div>
                                    <div><label class="setting-label-sm">Padding X (px)</label><input type="number" v-model.number="rating.boxPaddingX" class="yab-form-input"></div>
                                    <div><label class="setting-label-sm">Padding Y (px)</label><input type="number" v-model.number="rating.boxPaddingY" class="yab-form-input"></div>
                                </div>
                            </div>
                            <div>
                                <label class="setting-label-sm font-bold text-gray-300">Rating Label</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <div class="col-span-2" v-if="currentView === 'desktop'"><label class="setting-label-sm">Color</label><div class="yab-color-input-wrapper"><input type="color" v-model="rating.labelColor" class="yab-color-picker"><input type="text" v-model="rating.labelColor" class="yab-hex-input"></div></div>
                                    <div><label class="setting-label-sm">Font Size (px)</label><input type="number" v-model.number="rating.labelFontSize" class="yab-form-input"></div>
                                </div>
                             </div>
                             <div>
                                <label class="setting-label-sm font-bold text-gray-300">Rating Count</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <div class="col-span-2" v-if="currentView === 'desktop'"><label class="setting-label-sm">Color</label><div class="yab-color-input-wrapper"><input type="color" v-model="rating.countColor" class="yab-color-picker"><input type="text" v-model="rating.countColor" class="yab-hex-input"></div></div>
                                    <div><label class="setting-label-sm">Font Size (px)</label><input type="number" v-model.number="rating.countFontSize" class="yab-form-input"></div>
                                </div>
                             </div>
                        </div>
                    </div>
                     <hr class="section-divider">

                     <div :set="tags = card.tags">
                        <h4 class="section-title">Tags</h4>
                         <div class="grid grid-cols-2 gap-2">
                            <div><label class="setting-label-sm">Margin Top (px)</label><input type="number" v-model.number="tags.marginTop" class="yab-form-input"></div>
                            <div><label class="setting-label-sm">Gap (px)</label><input type="number" v-model.number="tags.gap" class="yab-form-input"></div>
                            <div><label class="setting-label-sm">Font Size (px)</label><input type="number" v-model.number="tags.fontSize" class="yab-form-input"></div>
                            <div><label class="setting-label-sm">Radius (px)</label><input type="number" v-model.number="tags.radius" class="yab-form-input"></div>
                            <div><label class="setting-label-sm">Padding X (px)</label><input type="number" v-model.number="tags.paddingX" class="yab-form-input"></div>
                            <div><label class="setting-label-sm">Padding Y (px)</label><input type="number" v-model.number="tags.paddingY" class="yab-form-input"></div>
                         </div>
                         <p class="text-xs text-gray-400 mt-2">Tag colors are dynamic based on tag name.</p>
                    </div>
                    <hr class="section-divider">

                    <div :set="divider = card.divider">
                         <h4 class="section-title">Divider</h4>
                         <div class="grid grid-cols-2 gap-2">
                            <div><label class="setting-label-sm">Margin Top (px)</label><input type="number" v-model.number="divider.marginTop" class="yab-form-input"></div>
                            <div><label class="setting-label-sm">Margin Bottom (px)</label><input type="number" v-model.number="divider.marginBottom" class="yab-form-input"></div>
                             <div class="col-span-2" v-if="currentView === 'desktop'"><label class="setting-label-sm">Color</label><div class="yab-color-input-wrapper"><input type="color" v-model="divider.color" class="yab-color-picker"><input type="text" v-model="divider.color" class="yab-hex-input"></div></div>
                         </div>
                    </div>
                    <hr class="section-divider">

                     <div :set="price = card.price">
                        <h4 class="section-title">Price Section</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="setting-label-sm font-bold text-gray-300">'From' Text</label>
                                <div class="grid grid-cols-2 gap-2">
                                     <div class="col-span-2" v-if="currentView === 'desktop'"><label class="setting-label-sm">Color</label><div class="yab-color-input-wrapper"><input type="color" v-model="price.fromColor" class="yab-color-picker"><input type="text" v-model="price.fromColor" class="yab-hex-input"></div></div>
                                     <div><label class="setting-label-sm">Size (px)</label><input type="number" v-model.number="price.fromSize" class="yab-form-input"></div>
                                </div>
                            </div>
                            <div>
                                <label class="setting-label-sm font-bold text-gray-300">Price Amount</label>
                                <div class="grid grid-cols-2 gap-2">
                                     <div v-if="currentView === 'desktop'"><label class="setting-label-sm">Color</label><div class="yab-color-input-wrapper"><input type="color" v-model="price.amountColor" class="yab-color-picker"><input type="text" v-model="price.amountColor" class="yab-hex-input"></div></div>
                                     <div><label class="setting-label-sm">Size (px)</label><input type="number" v-model.number="price.amountSize" class="yab-form-input"></div>
                                     <div><label class="setting-label-sm">Weight</label><select v-model="price.amountWeight" class="yab-form-input"><option value="400">Normal</option><option value="500">Medium</option><option value="600">Semi-Bold</option><option value="700">Bold</option></select></div>
                                </div>
                            </div>
                             <div>
                                <label class="setting-label-sm font-bold text-gray-300">'/ night' Text</label>
                                <div class="grid grid-cols-2 gap-2">
                                     <div class="col-span-2" v-if="currentView === 'desktop'"><label class="setting-label-sm">Color</label><div class="yab-color-input-wrapper"><input type="color" v-model="price.nightColor" class="yab-color-picker"><input type="text" v-model="price.nightColor" class="yab-hex-input"></div></div>
                                     <div><label class="setting-label-sm">Size (px)</label><input type="number" v-model.number="price.nightSize" class="yab-form-input"></div>
                                </div>
                            </div>
                             <div>
                                <label class="setting-label-sm font-bold text-gray-300">Original Price</label>
                                <div class="grid grid-cols-2 gap-2">
                                     <div class="col-span-2" v-if="currentView === 'desktop'"><label class="setting-label-sm">Color</label><div class="yab-color-input-wrapper"><input type="color" v-model="price.originalColor" class="yab-color-picker"><input type="text" v-model="price.originalColor" class="yab-hex-input"></div></div>
                                     <div><label class="setting-label-sm">Size (px)</label><input type="number" v-model.number="price.originalSize" class="yab-form-input"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                 </div>
            </div>
        </div>
    </div>

</div>

