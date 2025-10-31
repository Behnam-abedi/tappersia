<?php
// tappersia/admin/views/banner-types/flight-ticket/flight-ticket-settings.php
?>
<div class="bg-[#434343] p-5 rounded-lg shadow-xl mr-2">
    <h3 class="font-bold text-xl text-white tracking-wide mb-5">Flight Settings</h3>
    <div class="flex gap-4">
        <button @click="openFlightModal" class="w-full flex gap-2 justify-center items-center bg-sky-600 text-white px-4 py-2 rounded-md hover:bg-sky-700">
            <span class="dashicons dashicons-airplane"></span>
            <span>Select Airports</span>
        </button>
    </div>
    <div class="mt-4 p-3 bg-[#292929] rounded-lg text-sm text-gray-300">
        <div class="flex justify-between items-center">
            <span class="font-semibold">Origin:</span>
            <span>{{ banner.flight_ticket.from ? `${banner.flight_ticket.from.city} (${banner.flight_ticket.from.iataCode})` : 'Not Selected' }}</span>
        </div>
        <hr class="border-gray-600 my-2">
        <div class="flex justify-between items-center">
            <span class="font-semibold">Destination:</span>
            <span>{{ banner.flight_ticket.to ? `${banner.flight_ticket.to.city} (${banner.flight_ticket.to.iataCode})` : 'Not Selected' }}</span>
        </div>
    </div>
    
    <div class="mt-4 p-3 bg-[#292929] rounded-lg text-sm text-gray-300">
        <h4 class="font-semibold text-base text-white mb-2">Cheapest Flight (Tomorrow)</h4>
        <div v-if="banner.flight_ticket.isLoadingFlights" class="flex items-center justify-center py-4">
             <div class="yab-spinner w-6 h-6"></div>
        </div>
        <div v-else-if="banner.flight_ticket.lastSearchError" class="text-center text-red-400">
            {{ banner.flight_ticket.lastSearchError }}
        </div>
         <div v-else-if="banner.flight_ticket.cheapestPrice !== null" class="text-center">
             <span class="text-2xl font-bold text-green-400">€{{ banner.flight_ticket.cheapestPrice.toFixed(2) }}</span>
        </div>
        <div v-else class="text-center text-gray-500">
            Select origin and destination to search for prices.
        </div>
    </div>
</div>

<div :set="settings = banner.flight_ticket.design">
    <div class="bg-[#434343] p-5 rounded-lg shadow-xl mr-2">
        <h3 class="font-bold text-xl text-white tracking-wide mb-5">Banner Layout</h3>
        <div class="space-y-4">
            <div>
                <label class="setting-label-sm">Min Height (px)</label>
                <input type="number" :value="settings.minHeight" @input="settings.minHeight = parseInt($event.target.value)" class="yab-form-input" placeholder="e.g., 150">
            </div>
            <div>
                <label class="setting-label-sm">Border Radius (px)</label>
                <input type="number" :value="settings.borderRadius" @input="settings.borderRadius = parseInt($event.target.value)" class="yab-form-input" placeholder="e.g., 16">
            </div>
            <div>
                <label class="setting-label-sm">Padding (px)</label>
                <input type="number" :value="settings.padding" @input="settings.padding = parseInt($event.target.value)" class="yab-form-input" placeholder="e.g., 12">
            </div>
        </div>
    </div>

    <div class="bg-[#434343] p-5 rounded-lg shadow-xl mr-2">
        <h3 class="font-bold text-xl text-white tracking-wide mb-5">Banner Background</h3>
        <div class="space-y-4">
             <div>
                <h4 class="section-title">Layers Control</h4>
                <div class="flex rounded-lg bg-[#292929] overflow-hidden">
                    <button @click="settings.layerOrder = 'image-below-overlay'" :class="settings.layerOrder === 'image-below-overlay' ? 'active-tab' : ''" class="flex-1 tab-button rounded-l-lg">Image Below Color</button>
                    <button @click="settings.layerOrder = 'overlay-below-image'" :class="settings.layerOrder === 'overlay-below-image' ? 'active-tab' : ''" class="flex-1 tab-button rounded-r-lg">Color Below Image</button>
                </div>
            </div>
            <hr class="section-divider">
            <div>
                <h4 class="section-title">Background Overlay</h4>
                <div class="flex gap-2 mb-2 bg-[#292929] rounded-lg border-none">
                    <button @click="settings.backgroundType = 'solid'" :class="{'active-tab': settings.backgroundType === 'solid'}" class="flex-1 tab-button rounded-l-lg border-none">Solid</button>
                    <button @click="settings.backgroundType = 'gradient'" :class="{'active-tab': settings.backgroundType === 'gradient'}" class="flex-1 tab-button rounded-r-lg border-none">Gradient</button>
                </div>
                <div v-if="settings.backgroundType === 'solid'" class="space-y-2">
                    <label class="setting-label-sm">Color</label>
                    <div class="flex items-center gap-1"><div :style="{ backgroundColor: settings.bgColor }" class="w-8 h-[40px] rounded border border-gray-500"></div><input type="text" :value="settings.bgColor" @input="settings.bgColor = $event.target.value" data-coloris class="yab-form-input clr-field flex-grow"></div>
                </div>
                <div v-else class="space-y-4">
                    <div><label class="setting-label-sm">Angle: {{ settings.gradientAngle }}deg</label><input type="range" :value="settings.gradientAngle" @input="settings.gradientAngle = parseInt($event.target.value)" min="0" max="360" class="w-full"></div>
                    <div>
                        <label class="setting-label-sm">Colors</label>
                        <div v-for="(stop, index) in settings.gradientStops" :key="index" class="bg-[#292929] p-3 rounded-lg mb-2 space-y-2">
                            <div class="flex items-center justify-between"><span class="text-xs font-bold text-gray-300">Stop #{{ index + 1 }}</span><button v-if="settings.gradientStops.length > 1" @click="removeGradientStop(settings, index)" class="text-red-500 text-xs">Remove</button></div>
                            <div class="grid grid-cols-2 gap-2">
                                <div class="flex items-center gap-1"><div :style="{ backgroundColor: stop.color }" class="w-8 h-[40px] rounded border border-gray-500"></div><input type="text" :value="stop.color" @input="event => stop.color = event.target.value" data-coloris class="yab-form-input clr-field flex-grow"></div>
                                <button @click="stop.color = 'transparent'" class="bg-gray-600 text-white text-xs rounded-md hover:bg-gray-500">Set Transparent</button>
                            </div>
                            <div><label class="setting-label-sm">Position: {{ stop.stop }}%</label><input type="range" :value="stop.stop" @input="stop.stop = parseInt($event.target.value)" min="0" max="100" class="w-full"></div>
                        </div>
                        <button @click="addGradientStop(settings)" class="w-full bg-blue-600 text-white text-sm py-2 rounded-md hover:bg-blue-700 mt-2">Add Color Stop</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-[#434343] p-5 rounded-lg shadow-xl mr-2">
        <h3 class="font-bold text-xl text-white tracking-wide mb-5">Banner Image</h3>
        <div class="space-y-4">
            <h4 class="section-title">Image (like Single Banner)</h4>
            <div class="flex gap-2 items-center">
                <button @click="openMediaUploader('flight_ticket_design')" class="flex-1 bg-blue-600 text-white px-3 py-1.5 rounded-md hover:bg-blue-700 text-sm">{{ settings.imageUrl ? 'Change Image' : 'Select Image' }}</button>
                <button v-if="settings.imageUrl" @click="removeImage('flight_ticket_design')" class="bg-red-600 text-white px-3 py-1.5 rounded-md hover:bg-red-700 text-sm">Remove</button>
            </div>
            <div v-if="settings.imageUrl" class="mt-3 space-y-3">
                <div class="flex items-center justify-between bg-[#292929] p-2 rounded-md"><label class="setting-label-sm">Custom Size</label><input type="checkbox" v-model="settings.enableCustomImageSize"></div>
                <div v-if="settings.enableCustomImageSize" class="grid grid-cols-2 gap-2">
                    <div><label class="setting-label-sm">Width</label><div class="flex items-center gap-1"><input type="number" :value="settings.imageWidth" @input="settings.imageWidth = parseInt($event.target.value)" class="yab-form-input"><select v-model="settings.imageWidthUnit" class="yab-form-input w-20"><option>px</option><option>%</option></select></div></div>
                    <div><label class="setting-label-sm">Height</label><div class="flex items-center gap-1"><input type="number" :value="settings.imageHeight" @input="settings.imageHeight = parseInt($event.target.value)" class="yab-form-input"><select v-model="settings.imageHeightUnit" class="yab-form-input w-20"><option>px</option><option>%</option></select></div></div>
                </div>
                <div class="grid grid-cols-2 gap-2 mt-2">
                    <div>
                        <label class="setting-label-sm">Right (px)</label>
                        <input type="number" 
                               :value="settings.imagePosRight" 
                               @input="settings.imagePosRight = parseInt($event.target.value)" 
                               class="yab-form-input">
                    </div>
                    <div><label class="setting-label-sm">Bottom (px)</label><input type="number" :value="settings.imagePosBottom" @input="settings.imagePosBottom = parseInt($event.target.value)" class="yab-form-input"></div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="bg-[#434343] p-5 rounded-lg shadow-xl mr-2">
        <h3 class="font-bold text-xl text-white tracking-wide mb-5">Text Content</h3>
        <div class="space-y-4">
            
            <div :set="content = settings.content1">
                <h4 class="section-title">Content 1 (e.g., "Offering")</h4>
                <div>
                    <label class="setting-label-sm">Text</label>
                    <input type="text" 
                           :value="content.text" 
                           @input="settings.content1.text = $event.target.value" 
                           class="yab-form-input">
                </div>
                <div class="grid grid-cols-3 gap-2 mt-2">
                    <div><label class="setting-label-sm">Color</label><div class="flex items-center gap-1"><div :style="{ backgroundColor: content.color }" class="w-8 h-[40px] rounded border border-gray-500"></div><input type="text" :value="content.color" @input="settings.content1.color = $event.target.value; console.log('DEBUG: SET content1.color ->', settings.content1.color)" data-coloris class="yab-form-input clr-field flex-grow"></div></div>
                    <div><label class="setting-label-sm">Size (px)</label><input type="number" :value="content.fontSize" @input="settings.content1.fontSize = parseInt($event.target.value)" class="yab-form-input"></div>
                    <div><label class="setting-label-sm">Weight</label><select :value="content.fontWeight" @input="settings.content1.fontWeight = $event.target.value" class="yab-form-input"><option value="400">400</option><option value="500">500</option><option value="600">600</option><option value="700">700</option></select></div>
                </div>
            </div>
            <hr class="section-divider">
            
            <div :set="content = settings.content2">
                <h4 class="section-title">Content 2 (e.g., "BEST DEALS")</h4>
                <div><label class="setting-label-sm">Text</label><input type="text" :value="content.text" @input="settings.content2.text = $event.target.value" class="yab-form-input"></div>
                <div class="grid grid-cols-3 gap-2 mt-2">
                    <div><label class="setting-label-sm">Color</label><div class="flex items-center gap-1"><div :style="{ backgroundColor: content.color }" class="w-8 h-[40px] rounded border border-gray-500"></div><input type="text" :value="content.color" @input="settings.content2.color = $event.target.value; console.log('DEBUG: SET content2.color ->', settings.content2.color)" data-coloris class="yab-form-input clr-field flex-grow"></div></div>
                    <div><label class="setting-label-sm">Size (px)</label><input type="number" :value="content.fontSize" @input="settings.content2.fontSize = parseInt($event.target.value)" class="yab-form-input"></div>
                    <div><label class="setting-label-sm">Weight</label><select :value="content.fontWeight" @input="settings.content2.fontWeight = $event.target.value" class="yab-form-input"><option value="400">400</option><option value="500">500</option><option value="600">600</option><option value="700">700</option></select></div>
                </div>
            </div>
            <hr class="section-divider">

            <div :set="content = settings.content3">
                <h4 class="section-title">Content 3 (e.g., "on Iran...")</h4>
                <div><label class="setting-label-sm">Text</label><input type="text" :value="content.text" @input="settings.content3.text = $event.target.value" class="yab-form-input"></div>
                <div class="grid grid-cols-3 gap-2 mt-2">
                    <div><label class="setting-label-sm">Color</label><div class="flex items-center gap-1"><div :style="{ backgroundColor: content.color }" class="w-8 h-[40px] rounded border border-gray-500"></div><input type="text" :value="content.color" @input="settings.content3.color = $event.target.value; console.log('DEBUG: SET content3.color ->', settings.content3.color)" data-coloris class="yab-form-input clr-field flex-grow"></div></div>
                    <div><label class="setting-label-sm">Size (px)</label><input type="number" :value="content.fontSize" @input="settings.content3.fontSize = parseInt($event.target.value)" class="yab-form-input"></div>
                    <div><label class="setting-label-sm">Weight</label><select :value="content.fontWeight" @input="settings.content3.fontWeight = $event.target.value" class="yab-form-input"><option value="400">400</option><option value="500">500</option><option value="600">600</option><option value="700">700</option></select></div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-[#434343] p-5 rounded-lg shadow-xl mr-2">
        <h3 class="font-bold text-xl text-white tracking-wide mb-5">Ticket Styling</h3>
        <div class="space-y-4">

            <div :set="content = settings.price">
                <h4 class="section-title">Price Amount</h4>
                <div class="grid grid-cols-3 gap-2 mt-2">
                    <div>
                        <label class="setting-label-sm">Color</label>
                        <div class="flex items-center gap-1">
                            <div :style="{ backgroundColor: content.color }" class="w-8 h-[40px] rounded border border-gray-500"></div>
                            <input type="text" 
                                   :value="content.color" 
                                   @input="settings.price.color = $event.target.value; console.log('DEBUG: SET price.color ->', settings.price.color)" 
                                   data-coloris 
                                   class="yab-form-input clr-field flex-grow">
                        </div>
                    </div>
                    <div><label class="setting-label-sm">Size (px)</label><input type="number" :value="content.fontSize" @input="settings.price.fontSize = parseInt($event.target.value)" class="yab-form-input"></div>
                    <div><label class="setting-label-sm">Weight</label><select :value="content.fontWeight" @input="settings.price.fontWeight = $event.target.value" class="yab-form-input"><option value="400">400</option><option value="500">500</option><option value="600">600</option><option value="700">700</option></select></div>
                </div>
            </div>
            <hr class="section-divider">

            <div :set="content = settings.button">
                <h4 class="section-title">Button</h4>
                <div class="grid grid-cols-2 gap-2 mt-2">
                    <div>
                        <label class="setting-label-sm">BG Color</label>
                        <div class="flex items-center gap-1">
                            <div :style="{ backgroundColor: content.bgColor }" class="w-8 h-[40px] rounded border border-gray-500"></div>
                            <input type="text" 
                                   :value="content.bgColor" 
                                   @input="settings.button.bgColor = $event.target.value; console.log('DEBUG: SET button.bgColor ->', settings.button.bgColor)" 
                                   data-coloris 
                                   class="yab-form-input clr-field flex-grow">
                        </div>
                    </div>
                    <div><label class="setting-label-sm">Text Color</label><div class="flex items-center gap-1"><div :style="{ backgroundColor: content.color }" class="w-8 h-[40px] rounded border border-gray-500"></div><input type="text" :value="content.color" @input="settings.button.color = $event.target.value" data-coloris class="yab-form-input clr-field flex-grow"></div></div>
                    <div><label class="setting-label-sm">Size (px)</label><input type="number" :value="content.fontSize" @input="settings.button.fontSize = parseInt($event.target.value)" class="yab-form-input"></div>
                    <div><label class="setting-label-sm">Weight</label><select :value="content.fontWeight" @input="settings.button.fontWeight = $event.target.value" class="yab-form-input"><option value="400">400</option><option value="500">500</option><option value="600">600</option><option value="700">700</option></select></div>
                </div>
            </div>
            <hr class="section-divider">

            <div :set="content = settings.fromCity">
                <h4 class="section-title">Origin City Text</h4>
                <div class="grid grid-cols-3 gap-2 mt-2">
                    <div>
                        <label class="setting-label-sm">Color</label>
                        <div class="flex items-center gap-1">
                            <div :style="{ backgroundColor: content.color }" class="w-8 h-[40px] rounded border border-gray-500"></div>
                            <input type="text" 
                                   :value="content.color" 
                                   @input="settings.fromCity.color = $event.target.value; console.log('DEBUG: SET fromCity.color ->', settings.fromCity.color)" 
                                   data-coloris 
                                   class="yab-form-input clr-field flex-grow">
                        </div>
                    </div>
                    <div><label class="setting-label-sm">Size (px)</label><input type="number" :value="content.fontSize" @input="settings.fromCity.fontSize = parseInt($event.target.value)" class="yab-form-input"></div>
                    <div><label class="setting-label-sm">Weight</label><select :value="content.fontWeight" @input="settings.fromCity.fontWeight = $event.target.value" class="yab-form-input"><option value="400">400</option><option value="500">500</option><option value="600">600</option><option value="700">700</option></select></div>
                </div>
            </div>
            <hr class="section-divider">

            <div :set="content = settings.toCity">
                <h4 class="section-title">Destination City Text</h4>
                <div class="grid grid-cols-3 gap-2 mt-2">
                    <div>
                        <label class="setting-label-sm">Color</label>
                        <div class="flex items-center gap-1">
                            <div :style="{ backgroundColor: content.color }" class="w-8 h-[40px] rounded border border-gray-500"></div>
                            <input type="text" 
                                   :value="content.color" 
                                   @input="settings.toCity.color = $event.target.value; console.log('DEBUG: SET toCity.color ->', settings.toCity.color)" 
                                   data-coloris 
                                   class="yab-form-input clr-field flex-grow">
                        </div>
                    </div>
                    <div><label class="setting-label-sm">Size (px)</label><input type="number" :value="content.fontSize" @input="settings.toCity.fontSize = parseInt($event.target.value)" class="yab-form-input"></div>
                    <div><label class="setting-label-sm">Weight</label><select :value="content.fontWeight" @input="settings.toCity.fontWeight = $event.target.value" class="yab-form-input"><option value="400">400</option><option value="500">500</option><option value="600">600</option><option value="700">700</option></select></div>
                </div>
            </div>
            
        </div>
    </div>
</div>