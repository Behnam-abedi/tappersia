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
    <h3 class="font-bold text-xl text-white tracking-wide mb-5">Device</h3>
    <div class="flex  bg-[#292929] rounded-lg p-1">
        <button @click="currentView = 'desktop'" :class="{'active-tab': currentView === 'desktop'}" class="flex-1 tab-button rounded-md">Desktop</button>
        <button @click="currentView = 'mobile'" :class="{'active-tab': currentView === 'mobile'}" class="flex-1 tab-button rounded-md">Mobile</button>
    </div>
</div>
        <div class="bg-[#434343] p-5 rounded-lg shadow-xl mr-2" v-if="currentView === 'desktop'">
            <div>
            <h3 class="font-bold text-xl text-white tracking-wide mb-5">Direction</h3>
            <div class="flex rounded-lg bg-[#292929] overflow-hidden p-1">
            <button @click="settings.direction = 'ltr'" :class="{'active-tab': settings.direction === 'ltr'}" class="flex-1 tab-button rounded-md">LTR</button>
            <button @click="settings.direction = 'rtl'" :class="{'active-tab': settings.direction === 'rtl'}" class="flex-1 tab-button rounded-md">RTL</button>
            </div>
            </div>

        </div>
<div class="bg-[#434343] p-5 rounded-lg shadow-xl mr-2">
    <div v-if="banner.tour_carousel" :key="currentView">
        <div :set="settings = currentView === 'desktop' ? banner.tour_carousel.settings : banner.tour_carousel.settings_mobile">
            <h3 class="font-bold text-xl text-white tracking-wide mb-5 capitalize">{{ currentView }} Carousel Settings</h3>
            <div class="space-y-4">

                <div v-if="currentView === 'desktop'">
                    <h4 class="section-title">Slides Per View</h4>
                    <div class="flex rounded-lg bg-[#292929] overflow-hidden p-1" >
                        <button v-for="num in (currentView === 'desktop' ? [1, 2, 3, 4] : [1])" :key="num" @click="settings.slidesPerView = num" :class="{'active-tab': settings.slidesPerView === num}" class="flex-1 tab-button rounded-md">{{ num }}</button>
                    </div>
                </div>
                <hr class="section-divider">
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
                </div>
            </div>
        </div>
    </div>
</div>

<div class="bg-[#434343] p-5 rounded-lg shadow-xl mr-2">
                <div v-if="currentView === 'desktop'">
                <h3 class="font-bold text-xl text-white tracking-wide mb-5">Header of Carousel</h3>
                <div class="space-y-4">
                    <h4 class="section-title">Content</h4>
                    <div>
                        <label class="setting-label-sm">Header Text</label>
                        <input type="text" v-model="settings.header.text" class="yab-form-input">
                    </div>
                    <hr class="section-divider my-6">
                    <h4 class="section-title">Layout</h4>
                    <div>
                        <label class="setting-label-sm">Header Text</label>
                        <input type="text" v-model="settings.header.text" class="yab-form-input">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="setting-label-sm">Font Size (px)</label><input type="number" v-model.number="settings.header.fontSize" class="yab-form-input"></div>
                        <div><label class="setting-label-sm">Font Weight</label><select v-model="settings.header.fontWeight" class="yab-form-input"><option value="400">Normal</option><option value="500">Medium</option><option value="600">Semi-Bold</option><option value="700">Bold</option><option value="800">Extra Bold</option></select></div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="setting-label-sm">Text Color</label><div class="yab-color-input-wrapper"><input type="color" v-model="settings.header.color" class="yab-color-picker"><input type="text" v-model="settings.header.color" class="yab-hex-input"></div></div>
                        <div><label class="setting-label-sm">Accent Line Color</label><div class="yab-color-input-wrapper"><input type="color" v-model="settings.header.lineColor" class="yab-color-picker"><input type="text" v-model="settings.header.lineColor" class="yab-hex-input"></div></div>
                    </div>
                    <div><label class="setting-label-sm">Space between header and slider</label><input type="number" v-model.number="settings.header.marginTop" class="yab-form-input"></div>
                </div>
                <hr class="section-divider my-6">
            </div>
    <div :key="currentView">
        <div :set="settings = currentView === 'desktop' ? banner.tour_carousel.settings : banner.tour_carousel.settings_mobile">
            <div :set="card = settings.card">
                <h3 class="font-bold text-xl text-white tracking-wide mb-5 capitalize">{{ currentView }} Card Styling</h3>
                <div class="space-y-4">
                    <div>
                        <h4 class="section-title">Layout</h4>
                        <div class="grid grid-cols-2 gap-2">
                            <div><label class="setting-label-sm">Card Height (px)</label><input type="number" v-model.number="card.height" class="yab-form-input"></div>
                            <div><label class="setting-label-sm">Padding (px)</label><input type="number" v-model.number="card.padding" class="yab-form-input"></div>
                        </div>
                    </div>
                    <hr class="section-divider">
                    
                    <div v-if="currentView === 'desktop'">
                        <h4 class="section-title">Background</h4>
                        <div class="flex gap-2 mb-2 bg-[#292929] rounded-lg border-none p-1"><button @click="card.backgroundType = 'solid'" :class="{'active-tab': card.backgroundType === 'solid'}" class="flex-1 tab-button rounded-md">Solid</button><button @click="card.backgroundType = 'gradient'" :class="{'active-tab': card.backgroundType === 'gradient'}" class="flex-1 tab-button rounded-md">Gradient</button></div>
                        <div v-if="card.backgroundType === 'solid'"><label class="setting-label-sm">Color</label><div class="yab-color-input-wrapper"><input type="color" v-model="card.bgColor" class="yab-color-picker"><input type="text" v-model="card.bgColor" class="yab-hex-input"></div></div>
                        <div v-else class="space-y-4"><div><label class="setting-label-sm">Gradient Angle: {{ card.gradientAngle }}deg</label><input type="range" v-model.number="card.gradientAngle" min="0" max="360" class="w-full"></div><div><label class="setting-label-sm">Colors</label><div v-for="(stop, index) in card.gradientStops" class="flex items-center gap-2 mb-2"><div class="yab-color-input-wrapper flex-grow"><input type="color" v-model="stop.color" class="yab-color-picker"><input type="text" v-model="stop.color" class="yab-hex-input"></div><input type="range" v-model.number="stop.stop" min="0" max="100" class="w-full flex-grow"><span>{{ stop.stop }}%</span></div></div></div>
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
                        <h4 class="section-title">Image</h4>
                        <label class="setting-label-sm">Image Height (px)</label>
                        <input type="number" v-model.number="card.imageHeight" class="yab-form-input">
                    </div>
                    <hr class="section-divider">
                    
                    <div v-if="currentView === 'desktop'">
                        <div :set="province = card.province">
                            <h4 class="section-title">Province Badge</h4>
                            <div class="grid grid-cols-2 gap-2">
                                <div><label class="setting-label-sm">Font Size (px)</label><input type="number" v-model.number="province.fontSize" class="yab-form-input"></div>
                                <div><label class="setting-label-sm">Font Weight</label><select v-model="province.fontWeight" class="yab-form-input"><option value="400">Normal</option><option value="500">Medium</option><option value="600">Semi-Bold</option></select></div>
                                <div><label class="setting-label-sm">Color</label><div class="yab-color-input-wrapper"><input type="color" v-model="province.color" class="yab-color-picker"><input type="text" v-model="province.color" class="yab-hex-input"></div></div>
                                <div><label class="setting-label-sm">Background Color</label><div class="yab-color-input-wrapper"><input type="color" v-model="province.bgColor" class="yab-color-picker"><input type="text" v-model="province.bgColor" class="yab-hex-input"></div></div>
                                <div><label class="setting-label-sm">Blur (px)</label><input type="number" v-model.number="province.blur" class="yab-form-input"></div>
                                <div><label class="setting-label-sm">Bottom Spacing (px)</label><input type="number" v-model.number="province.bottom" class="yab-form-input"></div>
                                <div class="col-span-2"><label class="setting-label-sm">Side Spacing (px)</label><input type="number" v-model.number="province.side" class="yab-form-input"></div>
                            </div>
                        </div>
                        <hr class="section-divider">
                        <div :set="title = card.title">
                            <h4 class="section-title">Title</h4>
                            <div class="grid grid-cols-2 gap-2">
                                <div><label class="setting-label-sm">Font Size (px)</label><input type="number" v-model.number="title.fontSize" class="yab-form-input"></div>
                                <div><label class="setting-label-sm">Font Weight</label><select v-model="title.fontWeight" class="yab-form-input"><option value="400">Normal</option><option value="500">Medium</option><option value="600">Semi-Bold</option><option value="700">Bold</option></select></div>
                                <div><label class="setting-label-sm">Color</label><div class="yab-color-input-wrapper"><input type="color" v-model="title.color" class="yab-color-picker"><input type="text" v-model="title.color" class="yab-hex-input"></div></div>
                                <div><label class="setting-label-sm">Line Height</label><input type="number" step="0.1" v-model.number="title.lineHeight" class="yab-form-input"></div>
                            </div>
                        </div>
                        <hr class="section-divider">
                        <div class="grid grid-cols-2 gap-4">
                            <div :set="price = card.price">
                                <h4 class="section-title">Price</h4>
                                <div class="grid grid-cols-2 gap-2">
                                    <div><label class="setting-label-sm">Font Size (px)</label><input type="number" v-model.number="price.fontSize" class="yab-form-input"></div>
                                    <div><label class="setting-label-sm">Font Weight</label><select v-model="price.fontWeight" class="yab-form-input"><option value="400">Normal</option><option value="500">Medium</option><option value="600">Semi-Bold</option></select></div>
                                    <div class="col-span-2"><label class="setting-label-sm">Color</label><div class="yab-color-input-wrapper"><input type="color" v-model="price.color" class="yab-color-picker"><input type="text" v-model="price.color" class="yab-hex-input"></div></div>
                                </div>
                            </div>
                            <div :set="duration = card.duration">
                                <h4 class="section-title">Duration</h4>
                                <div class="grid grid-cols-2 gap-2">
                                    <div><label class="setting-label-sm">Font Size (px)</label><input type="number" v-model.number="duration.fontSize" class="yab-form-input"></div>
                                    <div><label class="setting-label-sm">Font Weight</label><select v-model="duration.fontWeight" class="yab-form-input"><option value="400">Normal</option><option value="500">Medium</option></select></div>
                                    <div class="col-span-2"><label class="setting-label-sm">Color</label><div class="yab-color-input-wrapper"><input type="color" v-model="duration.color" class="yab-color-picker"><input type="text" v-model="duration.color" class="yab-hex-input"></div></div>
                                </div>
                            </div>
                        </div>
                        <hr class="section-divider">
                        <div class="grid grid-cols-2 gap-4">
                            <div :set="rating = card.rating">
                                <h4 class="section-title">Rating</h4>
                                <div class="grid grid-cols-2 gap-2">
                                    <div><label class="setting-label-sm">Font Size (px)</label><input type="number" v-model.number="rating.fontSize" class="yab-form-input"></div>
                                    <div><label class="setting-label-sm">Font Weight</label><select v-model="rating.fontWeight" class="yab-form-input"><option value="400">Normal</option><option value="700">Bold</option></select></div>
                                    <div class="col-span-2"><label class="setting-label-sm">Color</label><div class="yab-color-input-wrapper"><input type="color" v-model="rating.color" class="yab-color-picker"><input type="text" v-model="rating.color" class="yab-hex-input"></div></div>
                                </div>
                            </div>
                            <div :set="reviews = card.reviews">
                                <h4 class="section-title">Reviews</h4>
                                <div class="grid grid-cols-2 gap-2">
                                    <div><label class="setting-label-sm">Font Size (px)</label><input type="number" v-model.number="reviews.fontSize" class="yab-form-input"></div>
                                    <div><label class="setting-label-sm">Font Weight</label><select v-model="reviews.fontWeight" class="yab-form-input"><option value="400">Normal</option><option value="500">Medium</option></select></div>
                                    <div class="col-span-2"><label class="setting-label-sm">Color</label><div class="yab-color-input-wrapper"><input type="color" v-model="reviews.color" class="yab-color-picker"><input type="text" v-model="reviews.color" class="yab-hex-input"></div></div>
                                </div>
                            </div>
                        </div>
                        <hr class="section-divider">
                        <div :set="button = card.button">
                            <h4 class="section-title">Button</h4>
                            <div class="grid grid-cols-2 gap-2">
                                <div><label class="setting-label-sm">Font Size (px)</label><input type="number" v-model.number="button.fontSize" class="yab-form-input"></div>
                                <div><label class="setting-label-sm">Font Weight</label><select v-model="button.fontWeight" class="yab-form-input"><option value="400">Normal</option><option value="600">Semi-Bold</option></select></div>
                                <div><label class="setting-label-sm">Color</label><div class="yab-color-input-wrapper"><input type="color" v-model="button.color" class="yab-color-picker"><input type="text" v-model="button.color" class="yab-hex-input"></div></div>
                                <div><label class="setting-label-sm">Background</label><div class="yab-color-input-wrapper"><input type="color" v-model="button.bgColor" class="yab-color-picker"><input type="text" v-model="button.bgColor" class="yab-hex-input"></div></div>
                                <div class="col-span-2"><label class="setting-label-sm">Arrow Size (px)</label><input type="number" v-model.number="button.arrowSize" class="yab-form-input"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>