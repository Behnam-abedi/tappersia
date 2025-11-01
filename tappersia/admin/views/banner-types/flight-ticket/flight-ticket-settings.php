<?php
// tappersia/admin/views/banner-types/flight-ticket/flight-ticket-settings.php
?>
<div class="bg-[#434343] p-5 rounded-lg shadow-xl mr-2">
    <div class="flex mb-4 bg-[#292929] rounded-lg p-1">
        <button @click="currentView = 'desktop'" :class="{'active-tab': currentView === 'desktop'}" class="flex-1 tab-button rounded-md">Desktop</button>
        <button @click="currentView = 'mobile'" :class="{'active-tab': currentView === 'mobile'}" class="flex-1 tab-button rounded-md">Mobile</button>
    </div>
</div>
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

<div class="gap-6 flex flex-col" :set="settings = (currentView === 'desktop' ? banner.flight_ticket.design : banner.flight_ticket.design_mobile)" :key="currentView">
    <div class="bg-[#434343] p-5 rounded-lg shadow-xl mr-2 ">
        <h3 class="font-bold text-xl text-white tracking-wide mb-5 capitalize">{{ currentView }} Banner Layout</h3>
        <div class="space-y-4">
            <div>
                <label class="setting-label-sm">Min Height (px)</label>
                <input type="number" v-model.number="settings.minHeight" class="yab-form-input" placeholder="e.g., 150">
            </div>
            <div>
                <label class="setting-label-sm">Border Radius (px)</label>
                <input type="number" v-model.number="settings.borderRadius" class="yab-form-input" placeholder="e.g., 16">
            </div>
            <div>
                <label class="setting-label-sm">Padding (px)</label>
                <input type="number" v-model.number="settings.padding" class="yab-form-input" placeholder="e.g., 12">
            </div>
        </div>
    </div>

    <div class="bg-[#434343] p-5 rounded-lg shadow-xl mr-2" v-if="currentView === 'desktop'">
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
                    <div class="flex items-center gap-1"><div :style="{ backgroundColor: settings.bgColor }" class="w-8 h-[40px] rounded border border-gray-500"></div><input type="text" v-model="settings.bgColor" data-coloris class="yab-form-input clr-field flex-grow"></div>
                </div>
                <div v-else class="space-y-4">
                    <div><label class="setting-label-sm">Angle: {{ settings.gradientAngle }}deg</label><input type="range" v-model.number="settings.gradientAngle" min="0" max="360" class="w-full"></div>
                    <div>
                        <label class="setting-label-sm">Colors</label>
                        <div v-for="(stop, index) in settings.gradientStops" :key="index" class="bg-[#292929] p-3 rounded-lg mb-2 space-y-2">
                            <div class="flex items-center justify-between"><span class="text-xs font-bold text-gray-300">Stop #{{ index + 1 }}</span><button v-if="settings.gradientStops.length > 1" @click="removeGradientStop(settings, index)" class="text-red-500 text-xs">Remove</button></div>
                            <div class="grid grid-cols-2 gap-2">
                                <div class="flex items-center gap-1"><div :style="{ backgroundColor: stop.color }" class="w-8 h-[40px] rounded border border-gray-500"></div><input type="text" :value="stop.color" @input="event => stop.color = event.target.value" data-coloris class="yab-form-input clr-field flex-grow"></div>
                                <button @click="stop.color = 'transparent'" class="bg-gray-600 text-white text-xs rounded-md hover:bg-gray-500">Set Transparent</button>
                            </div>
                            <div><label class="setting-label-sm">Position: {{ stop.stop }}%</label><input type="range" v-model.number="stop.stop" min="0" max="100" class="w-full"></div>
                        </div>
                        <button @click="addGradientStop(settings)" class="w-full bg-blue-600 text-white text-sm py-2 rounded-md hover:bg-blue-700 mt-2">Add Color Stop</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-[#434343] p-5 rounded-lg shadow-xl mr-2" v-if="currentView === 'desktop'">
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
                    <div><label class="setting-label-sm">Width</label><div class="flex items-center gap-1"><input type="number" v-model.number="settings.imageWidth" class="yab-form-input"><select v-model="settings.imageWidthUnit" class="yab-form-input w-20"><option>px</option><option>%</option></select></div></div>
                    <div><label class="setting-label-sm">Height</label><div class="flex items-center gap-1"><input type="number" v-model.number="settings.imageHeight" class="yab-form-input"><select v-model="settings.imageHeightUnit" class="yab-form-input w-20"><option>px</option><option>%</option></select></div></div>
                </div>
                <div class="grid grid-cols-2 gap-2 mt-2">
                    <div>
                        <label class="setting-label-sm">Left (px)</label>
                        <input type="number" 
                               v-model.number="settings.imagePosLeft" 
                               class="yab-form-input">
                    </div>
                    <div><label class="setting-label-sm">Bottom (px)</label><input type="number" v-model.number="settings.imagePosBottom" class="yab-form-input"></div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="bg-[#434343] p-5 rounded-lg shadow-xl mr-2">
        <h3 class="font-bold text-xl text-white tracking-wide mb-5 capitalize">{{ currentView }} Text Content</h3>
        <div class="space-y-4">
            
            <div :set="content = settings.content1">
                <h4 class="section-title">Content 1 (e.g., "Offering")</h4>
                <div v-if="currentView === 'desktop'">
                    <label class="setting-label-sm">Text</label>
                    <input type="text" 
                           v-model="settings.content1.text" 
                           class="yab-form-input">
                </div>
                <div class="grid grid-cols-3 gap-2 mt-2">
                    <div v-if="currentView === 'desktop'"><label class="setting-label-sm">Color</label><div class="flex items-center gap-1"><div :style="{ backgroundColor: content.color }" class="w-8 h-[40px] rounded border border-gray-500"></div><input type="text" v-model="settings.content1.color" data-coloris class="yab-form-input clr-field flex-grow"></div></div>
                    <div :class="currentView === 'mobile' ? 'col-span-2' : ''"><label class="setting-label-sm">Size (px)</label><input type="number" v-model.number="settings.content1.fontSize" class="yab-form-input"></div>
                    <div><label class="setting-label-sm">Weight</label><select v-model="settings.content1.fontWeight" class="yab-form-input"><option value="400">400</option><option value="500">500</option><option value="600">600</option><option value="700">700</option></select></div>
                </div>
            </div>
            <hr class="section-divider">
            
            <div :set="content = settings.content2">
                 <h4 class="section-title">Content 2 (e.g., "BEST DEALS")</h4>
                <div v-if="currentView === 'desktop'"><label class="setting-label-sm">Text</label><input type="text" v-model="settings.content2.text" class="yab-form-input"></div>
                <div class="grid grid-cols-3 gap-2 mt-2">
                    <div v-if="currentView === 'desktop'"><label class="setting-label-sm">Color</label><div class="flex items-center gap-1"><div :style="{ backgroundColor: content.color }" class="w-8 h-[40px] rounded border border-gray-500"></div><input type="text" v-model="settings.content2.color" data-coloris class="yab-form-input clr-field flex-grow"></div></div>
                    <div :class="currentView === 'mobile' ? 'col-span-2' : ''"><label class="setting-label-sm">Size (px)</label><input type="number" v-model.number="settings.content2.fontSize" class="yab-form-input"></div>
                    <div><label class="setting-label-sm">Weight</label><select v-model="settings.content2.fontWeight" class="yab-form-input"><option value="400">400</option><option value="500">500</option><option value_500="600">600</option><option value="700">700</option></select></div>
                </div>
            </div>
            <hr class="section-divider">

            <div :set="content = settings.content3">
                <h4 class="section-title">Content 3 (e.g., "on Iran...")</h4>
                <div v-if="currentView === 'desktop'"><label class="setting-label-sm">Text</label><input type="text" v-model="settings.content3.text" class="yab-form-input"></div>
                <div class="grid grid-cols-3 gap-2 mt-2">
                    <div v-if="currentView === 'desktop'"><label class="setting-label-sm">Color</label><div class="flex items-center gap-1"><div :style="{ backgroundColor: content.color }" class="w-8 h-[40px] rounded border border-gray-500"></div><input type="text" v-model="settings.content3.color" data-coloris class="yab-form-input clr-field flex-grow"></div></div>
                    <div :class="currentView === 'mobile' ? 'col-span-2' : ''"><label class="setting-label-sm">Size (px)</label><input type="number" v-model.number="settings.content3.fontSize" class="yab-form-input"></div>
                    <div><label class="setting-label-sm">Weight</label><select v-model="settings.content3.fontWeight" class="yab-form-input"><option value="400">400</option><option value="500">500</option><option value="600">600</option><option value="700">700</option></select></div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-[#434343] p-5 rounded-lg shadow-xl mr-2">
        <h3 class="font-bold text-xl text-white tracking-wide mb-5 capitalize">{{ currentView }} Ticket Styling</h3>
        <div class="space-y-4">

            <div :set="content = settings.price">
                <h4 class="section-title">Price Amount</h4>
                <div class="grid grid-cols-3 gap-2 mt-2">
                    <div v-if="currentView === 'desktop'">
                        <label class="setting-label-sm">Color</label>
                        <div class="flex items-center gap-1">
                            <div :style="{ backgroundColor: content.color }" class="w-8 h-[40px] rounded border border-gray-500"></div>
                            <input type="text" 
                                   v-model="settings.price.color" 
                                   data-coloris 
                                   class="yab-form-input clr-field flex-grow">
                        </div>
                    </div>
                    <div><label class="setting-label-sm">Size (px)</label><input type="number" v-model.number="settings.price.fontSize" class="yab-form-input"></div>
                    <div><label class="setting-label-sm">Weight</label><select v-model="settings.price.fontWeight" class="yab-form-input"><option value="400">400</option><option value="500">500</option><option value="600">600</option><option value="700">700</option></select></div>
                    <div :class="currentView === 'mobile' ? 'col-span-3' : 'col-span-2'">
                        <label class="setting-label-sm">"From" Text Size (px)</label>
                        <input type="number" v-model.number="settings.price.fromFontSize" class="yab-form-input">
                    </div>
                    </div>
            </div>
            <hr class="section-divider">

            <div :set="content = settings.button">
                <h4 class="section-title">Button</h4>
                <div class="grid grid-cols-2 gap-2 mt-2">
                    <div v-if="currentView === 'desktop'"><label class="setting-label-sm">BG Color</label><div class="flex items-center gap-1"><div :style="{ backgroundColor: content.bgColor }" class="w-8 h-[40px] rounded border border-gray-500"></div><input type="text" v-model="settings.button.bgColor" data-coloris class="yab-form-input clr-field flex-grow"></div></div>
                    <div v-if="currentView === 'desktop'"><label class="setting-label-sm">Text Color</label><div class="flex items-center gap-1"><div :style="{ backgroundColor: content.color }" class="w-8 h-[40px] rounded border border-gray-500"></div><input type="text" v-model="settings.button.color" data-coloris class="yab-form-input clr-field flex-grow"></div></div>
                    <div><label class="setting-label-sm">Size (px)</label><input type="number" v-model.number="settings.button.fontSize" class="yab-form-input"></div>
                    <div><label class="setting-label-sm">Weight</label><select v-model="settings.button.fontWeight" class="yab-form-input"><option value="400">400</option><option value="500">500</option><option value="600">600</option><option value="700">700</option></select></div>
                    <div><label class="setting-label-sm">Padding Y (px)</label><input type="number" v-model.number="settings.button.paddingY" class="yab-form-input"></div>
                    <div><label class="setting-label-sm">Padding X (px)</label><input type="number" v-model.number="settings.button.paddingX" class="yab-form-input"></div>
                    <div class="col-span-2"><label class="setting-label-sm">Radius (px)</label><input type="number" v-model.number="settings.button.borderRadius" class="yab-form-input"></div>
                    </div>
            </div>
            <hr class="section-divider">

            <div :set="content = settings.fromCity">
                <h4 class="section-title">Origin City Text</h4>
                <div class="grid grid-cols-3 gap-2 mt-2">
                    <div v-if="currentView === 'desktop'"><label class="setting-label-sm">Color</label><div class="flex items-center gap-1"><div :style="{ backgroundColor: content.color }" class="w-8 h-[40px] rounded border border-gray-500"></div><input type="text" v-model="settings.fromCity.color" data-coloris class="yab-form-input clr-field flex-grow"></div></div>
                    <div :class="currentView === 'mobile' ? 'col-span-2' : ''"><label class="setting-label-sm">Size (px)</label><input type="number" v-model.number="settings.fromCity.fontSize" class="yab-form-input"></div>
                    <div><label class="setting-label-sm">Weight</label><select v-model="settings.fromCity.fontWeight" class="yab-form-input"><option value="400">400</option><option value="500">500</option><option value="600">600</option><option value="700">700</option></select></div>
                </div>
            </div>
            <hr class="section-divider">

            <div :set="content = settings.toCity">
                <h4 class="section-title">Destination City Text</h4>
                <div class="grid grid-cols-3 gap-2 mt-2">
                    <div v-if="currentView === 'desktop'"><label class="setting-label-sm">Color</label><div class="flex items-center gap-1"><div :style="{ backgroundColor: content.color }" class="w-8 h-[40px] rounded border border-gray-500"></div><input type="text" v-model="settings.toCity.color" data-coloris class="yab-form-input clr-field flex-grow"></div></div>
                    <div :class="currentView === 'mobile' ? 'col-span-2' : ''"><label class="setting-label-sm">Size (px)</label><input type="number" v-model.number="settings.toCity.fontSize" class="yab-form-input"></div>
                    <div><label class="setting-label-sm">Weight</label><select v-model="settings.toCity.fontWeight" class="yab-form-input"><option value="400">400</option><option value="500">500</option><option value="600">600</option><option value="700">700</option></select></div>
                </div>
            </div>
            
        </div>
    </div>
</div>