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
                    <div v-if="settings.pagination.enabled && currentView === 'desktop'" class="mt-4 space-y-2">
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="setting-label-sm">Pagination Color</label>
                                <div class="flex items-center gap-1">
                                    <div :style="{ backgroundColor: settings.pagination.paginationColor }" class="w-8 h-[40px] rounded border border-gray-500 flex-shrink-0" title="Selected color preview"></div>
                                    <input aria-label="Pagination color input" type="text" :value="settings.pagination.paginationColor" @input="event => settings.pagination.paginationColor = event.target.value" data-coloris class="yab-form-input clr-field flex-grow" placeholder="Select color...">
                                </div>
                            </div>
                            <div>
                                <label class="setting-label-sm">Pagination Active Color</label>
                                <div class="flex items-center gap-1">
                                    <div :style="{ backgroundColor: settings.pagination.paginationActiveColor }" class="w-8 h-[40px] rounded border border-gray-500 flex-shrink-0" title="Selected color preview"></div>
                                    <input aria-label="Pagination active color input" type="text" :value="settings.pagination.paginationActiveColor" @input="event => settings.pagination.paginationActiveColor = event.target.value" data-coloris class="yab-form-input clr-field flex-grow" placeholder="Select color...">
                                </div>
                            </div>
                        </div>
                    </div>
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
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="setting-label-sm">Font Size (px)</label><input type="number" v-model.number="settings.header.fontSize" class="yab-form-input"></div>
                        <div><label class="setting-label-sm">Font Weight</label><select v-model="settings.header.fontWeight" class="yab-form-input"><option value="400">Normal</option><option value="500">Medium</option><option value="600">Semi-Bold</option><option value="700">Bold</option><option value="800">Extra Bold</option></select></div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="setting-label-sm">Text Color</label><div class="flex items-center gap-1"><div :style="{ backgroundColor: settings.header.color }" class="w-8 h-[40px] rounded border border-gray-500 flex-shrink-0" title="Selected color preview"></div><input aria-label="Text color input" type="text" :value="settings.header.color" @input="event => settings.header.color = event.target.value" data-coloris class="yab-form-input clr-field flex-grow" placeholder="Select color..."></div></div>
                        <div><label class="setting-label-sm">Accent Line Color</label><div class="flex items-center gap-1"><div :style="{ backgroundColor: settings.header.lineColor }" class="w-8 h-[40px] rounded border border-gray-500 flex-shrink-0" title="Selected color preview"></div><input aria-label="Accent line color input" type="text" :value="settings.header.lineColor" @input="event => settings.header.lineColor = event.target.value" data-coloris class="yab-form-input clr-field flex-grow" placeholder="Select color..."></div></div>
                    </div>
                    <div><label class="setting-label-sm">Space between header and slider</label><input type="number" v-model.number="settings.header.marginTop" class="yab-form-input"></div>
                </div>
                <hr class="section-divider my-6">
            </div>
    <div :key="currentView" v-if="currentView === 'desktop'">
        <div :set="settings = currentView === 'desktop' ? banner.tour_carousel.settings : banner.tour_carousel.settings_mobile" >
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
                                        <div>
                        <h4 class="section-title">Border</h4>
                        <div class="grid grid-cols-3 gap-2">
                            <div><label class="setting-label-sm">Width (px)</label><input type="number" v-model.number="card.borderWidth" class="yab-form-input"></div>
                            <div><label class="setting-label-sm">Radius (px)</label><input type="number" v-model.number="card.borderRadius" class="yab-form-input"></div>
                            <div v-if="currentView === 'desktop'"><label class="setting-label-sm">Color</label><div class="flex items-center gap-1"><div :style="{ backgroundColor: card.borderColor }" class="w-8 h-[40px] rounded border border-gray-500 flex-shrink-0" title="Selected color preview"></div><input aria-label="Border color input" type="text" :value="card.borderColor" @input="event => card.borderColor = event.target.value" data-coloris class="yab-form-input clr-field flex-grow" placeholder="Select color..."></div></div>
                        </div>
                    </div>
                    <hr class="section-divider">
                    <div v-if="currentView === 'desktop'">
                        <h4 class="section-title">Background</h4>
                        <div class="flex gap-2 mb-2 bg-[#292929] rounded-lg border-none p-1"><button @click="card.backgroundType = 'solid'" :class="{'active-tab': card.backgroundType === 'solid'}" class="flex-1 tab-button rounded-md">Solid</button><button @click="card.backgroundType = 'gradient'" :class="{'active-tab': card.backgroundType === 'gradient'}" class="flex-1 tab-button rounded-md">Gradient</button></div>
                        <div v-if="card.backgroundType === 'solid'"><label class="setting-label-sm">Color</label><div class="flex items-center gap-1"><div :style="{ backgroundColor: card.bgColor }" class="w-8 h-[40px] rounded border border-gray-500 flex-shrink-0" title="Selected color preview"></div><input aria-label="Background color input" type="text" :value="card.bgColor" @input="event => card.bgColor = event.target.value" data-coloris class="yab-form-input clr-field flex-grow" placeholder="Select color..."></div></div>
                        <div v-else class="space-y-4"><div><label class="setting-label-sm">Gradient Angle: {{ card.gradientAngle }}deg</label><input type="range" v-model.number="card.gradientAngle" min="0" max="360" class="w-full"></div><div><label class="setting-label-sm">Colors</label><div v-for="(stop, index) in card.gradientStops" class="flex items-center gap-2 mb-2"><div class="flex items-center gap-1 flex-grow"><div :style="{ backgroundColor: stop.color }" class="w-8 h-[40px] rounded border border-gray-500 flex-shrink-0" title="Selected color preview"></div><input aria-label="Gradient color input" type="text" :value="stop.color" @input="event => stop.color = event.target.value" data-coloris class="yab-form-input clr-field flex-grow" placeholder="Select color..."></div><input type="range" v-model.number="stop.stop" min="0" max="100" class="w-full flex-grow"><span>{{ stop.stop }}%</span></div></div></div>
                        <hr class="section-divider">
                    </div>





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
                                <div><label class="setting-label-sm">Color</label><div class="flex items-center gap-1"><div :style="{ backgroundColor: province.color }" class="w-8 h-[40px] rounded border border-gray-500 flex-shrink-0" title="Selected color preview"></div><input aria-label="Province color input" type="text" :value="province.color" @input="event => province.color = event.target.value" data-coloris class="yab-form-input clr-field flex-grow" placeholder="Select color..."></div></div>
                                <div><label class="setting-label-sm">Background Color</label><div class="flex items-center gap-1"><div :style="{ backgroundColor: province.bgColor }" class="w-8 h-[40px] rounded border border-gray-500 flex-shrink-0" title="Selected color preview"></div><input aria-label="Province background color input" type="text" :value="province.bgColor" @input="event => province.bgColor = event.target.value" data-coloris class="yab-form-input clr-field flex-grow" placeholder="Select color..."></div></div>
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
                                <div><label class="setting-label-sm">Color</label><div class="flex items-center gap-1"><div :style="{ backgroundColor: title.color }" class="w-8 h-[40px] rounded border border-gray-500 flex-shrink-0" title="Selected color preview"></div><input aria-label="Title color input" type="text" :value="title.color" @input="event => title.color = event.target.value" data-coloris class="yab-form-input clr-field flex-grow" placeholder="Select color..."></div></div>
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
                                    <div class="col-span-2"><label class="setting-label-sm">Color</label><div class="flex items-center gap-1"><div :style="{ backgroundColor: price.color }" class="w-8 h-[40px] rounded border border-gray-500 flex-shrink-0" title="Selected color preview"></div><input aria-label="Price color input" type="text" :value="price.color" @input="event => price.color = event.target.value" data-coloris class="yab-form-input clr-field flex-grow" placeholder="Select color..."></div></div>
                                </div>
                            </div>
                            <div :set="duration = card.duration">
                                <h4 class="section-title">Duration</h4>
                                <div class="grid grid-cols-2 gap-2">
                                    <div><label class="setting-label-sm">Font Size (px)</label><input type="number" v-model.number="duration.fontSize" class="yab-form-input"></div>
                                    <div><label class="setting-label-sm">Font Weight</label><select v-model="duration.fontWeight" class="yab-form-input"><option value="400">Normal</option><option value="500">Medium</option></select></div>
                                    <div class="col-span-2"><label class="setting-label-sm">Color</label><div class="flex items-center gap-1"><div :style="{ backgroundColor: duration.color }" class="w-8 h-[40px] rounded border border-gray-500 flex-shrink-0" title="Selected color preview"></div><input aria-label="Duration color input" type="text" :value="duration.color" @input="event => duration.color = event.target.value" data-coloris class="yab-form-input clr-field flex-grow" placeholder="Select color..."></div></div>
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
                                    <div class="col-span-2"><label class="setting-label-sm">Color</label><div class="flex items-center gap-1"><div :style="{ backgroundColor: rating.color }" class="w-8 h-[40px] rounded border border-gray-500 flex-shrink-0" title="Selected color preview"></div><input aria-label="Rating color input" type="text" :value="rating.color" @input="event => rating.color = event.target.value" data-coloris class="yab-form-input clr-field flex-grow" placeholder="Select color..."></div></div>
                                </div>
                            </div>
                            <div :set="reviews = card.reviews">
                                <h4 class="section-title">Reviews</h4>
                                <div class="grid grid-cols-2 gap-2">
                                    <div><label class="setting-label-sm">Font Size (px)</label><input type="number" v-model.number="reviews.fontSize" class="yab-form-input"></div>
                                    <div><label class="setting-label-sm">Font Weight</label><select v-model="reviews.fontWeight" class="yab-form-input"><option value="400">Normal</option><option value="500">Medium</option></select></div>
                                    <div class="col-span-2"><label class="setting-label-sm">Color</label><div class="flex items-center gap-1"><div :style="{ backgroundColor: reviews.color }" class="w-8 h-[40px] rounded border border-gray-500 flex-shrink-0" title="Selected color preview"></div><input aria-label="Reviews color input" type="text" :value="reviews.color" @input="event => reviews.color = event.target.value" data-coloris class="yab-form-input clr-field flex-grow" placeholder="Select color..."></div></div>
                                </div>
                            </div>
                        </div>
                        <hr class="section-divider">
                        <div :set="button = card.button">
                            <h4 class="section-title">Button</h4>
                            <div class="grid grid-cols-2 gap-2">
                                <div><label class="setting-label-sm">Font Size (px)</label><input type="number" v-model.number="button.fontSize" class="yab-form-input"></div>
                                <div><label class="setting-label-sm">Font Weight</label><select v-model="button.fontWeight" class="yab-form-input"><option value="400">Normal</option><option value="600">Semi-Bold</option></select></div>
                                <div><label class="setting-label-sm">Color</label><div class="flex items-center gap-1"><div :style="{ backgroundColor: button.color }" class="w-8 h-[40px] rounded border border-gray-500 flex-shrink-0" title="Selected color preview"></div><input aria-label="Button color input" type="text" :value="button.color" @input="event => button.color = event.target.value" data-coloris class="yab-form-input clr-field flex-grow" placeholder="Select color..."></div></div>
                                <div><label class="setting-label-sm">Background</label><div class="flex items-center gap-1"><div :style="{ backgroundColor: button.bgColor }" class="w-8 h-[40px] rounded border border-gray-500 flex-shrink-0" title="Selected color preview"></div><input aria-label="Button background color input" type="text" :value="button.bgColor" @input="event => button.bgColor = event.target.value" data-coloris class="yab-form-input clr-field flex-grow" placeholder="Select color..."></div></div>
                                
                                <div v-if="currentView === 'desktop'" class="col-span-2">
                                    <label class="setting-label-sm">Background Hover</label>
                                    <div class="flex items-center gap-1">
                                        <div :style="{ backgroundColor: button.BgHoverColor }" class="w-8 h-[40px] rounded border border-gray-500 flex-shrink-0" title="Selected color preview"></div>
                                        <input aria-label="Button background hover color input" type="text" :value="button.BgHoverColor" @input="event => button.BgHoverColor = event.target.value" data-coloris class="yab-form-input clr-field flex-grow" placeholder="Select hover color...">
                                    </div>
                                </div>
                                
                                <div class="col-span-2"><label class="setting-label-sm">Arrow Size (px)</label><input type="number" v-model.number="button.arrowSize" class="yab-form-input"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>