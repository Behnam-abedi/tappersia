<?php
// tappersia/admin/views/banner-types/api-banner/api-banner-settings.php
?>
<div class="flex flex-col gap-3">
    <div>
        <h4 class="section-title">Layout</h4>
        <div class="flex items-center justify-between bg-[#292929] p-2 rounded-md mb-2">
            <label class="setting-label-sm">Enable Custom Dimensions</label>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" v-model="settings.enableCustomDimensions" class="sr-only peer">
                <div class="w-11 h-6 bg-gray-600 rounded-full peer peer-checked:after:translate-x-full after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div>
            </label>
        </div>
        <div v-if="settings.enableCustomDimensions" class="grid grid-cols-2 gap-2 mb-4">
            <div>
                <label class="setting-label-sm">Width</label>
                <div class="flex items-center gap-1">
                    <input type="number" v-model.number="settings.width" class="yab-form-input" placeholder="Width">
                    <select v-model="settings.widthUnit" class="yab-form-input w-20"><option>%</option><option>px</option></select>
                </div>
            </div>
            <div>
                <label class="setting-label-sm">Min Height</label>
                <div class="flex items-center gap-1">
                    <input type="number" v-model.number="settings.height" class="yab-form-input" placeholder="Height">
                    <select v-model="settings.heightUnit" class="yab-form-input w-20"><option>px</option><option>%</option></select>
                </div>
            </div>
        </div>

        <div class="mt-4">
             <label class="setting-label-sm">Image Container Width (px)</label>
            <input type="number" v-model.number="settings.imageContainerWidth" class="yab-form-input">
        </div>
    </div>
    <hr class="section-divider">
     <div>
        <h4 class="section-title">Content Padding (px)</h4>
        <div class="grid grid-cols-2 gap-2">
            <div><label class="setting-label-sm">Top</label><input type="number" v-model.number="settings.paddingTop" class="yab-form-input"></div>
            <div><label class="setting-label-sm">Bottom</label><input type="number" v-model.number="settings.paddingBottom" class="yab-form-input"></div>
            <div><label class="setting-label-sm">Left</label><input type="number" v-model.number="settings.paddingLeft" class="yab-form-input"></div>
            <div><label class="setting-label-sm">Right</label><input type="number" v-model.number="settings.paddingRight" class="yab-form-input"></div>
        </div>
    </div>
    <hr class="section-divider">
    <div>
        <h4 class="section-title">Border</h4>
        <div class="flex items-center justify-between bg-[#292929] p-2 rounded-md mb-2">
            <label class="setting-label-sm">Enable Border</label>
            <label class="relative inline-flex items-center cursor-pointer"><input type="checkbox" v-model="settings.enableBorder" class="sr-only peer"><div class="w-11 h-6 bg-gray-600 rounded-full peer peer-checked:after:translate-x-full after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div></label>
        </div>
         <div v-if="settings.enableBorder" class="grid grid-cols-3 gap-2">
            <div>
                <label class="setting-label-sm">Color</label>
                <div class="flex items-center gap-1">
                    <div
                        :style="{ backgroundColor: settings.borderColor }"
                        class="w-8 h-[40px] rounded border border-gray-500 flex-shrink-0"
                        title="Selected color preview">
                    </div>
                    <input
                        aria-label="Border color input"
                        type="text"
                        :value="settings.borderColor"
                        @input="event => settings.borderColor = event.target.value"
                        data-coloris
                        class="yab-form-input clr-field flex-grow"
                        placeholder="Select color...">
                </div>
            </div>
            <div>
                <label class="setting-label-sm">Width (px)</label>
                <input type="number" v-model.number="settings.borderWidth" class="yab-form-input" placeholder="e.g., 1">
            </div>
            <div>
                <label class="setting-label-sm">Radius (px)</label>
                <input type="number" v-model.number="settings.borderRadius" class="yab-form-input" placeholder="e.g., 15">
            </div>
        </div>
    </div>
    <hr class="section-divider">
    <div>
        <h4 class="section-title">Background</h4>
        <div class="flex bg-[#292929] rounded-lg p-1">
            <button @click="settings.backgroundType = 'solid'" :class="{'active-tab': settings.backgroundType === 'solid'}" class="flex-1 tab-button rounded-md">Solid Color</button>
            <button @click="settings.backgroundType = 'gradient'" :class="{'active-tab': settings.backgroundType === 'gradient'}" class="flex-1 tab-button rounded-md">Gradient</button>
        </div>
        <div v-if="settings.backgroundType === 'solid'" class="space-y-2">
            <label class="setting-label-sm">Background Color</label>
            <div class="flex items-center gap-1">
                <div
                    :style="{ backgroundColor: settings.bgColor }"
                    class="w-8 h-[40px] rounded border border-gray-500 flex-shrink-0"
                    title="Selected color preview">
                </div>
                <input
                    aria-label="Background color input"
                    type="text"
                    :value="settings.bgColor"
                    @input="event => settings.bgColor = event.target.value"
                    data-coloris
                    class="yab-form-input clr-field flex-grow"
                    placeholder="Select color...">
            </div>
        </div>
        <div v-else class="space-y-4">
            <div>
                <label class="setting-label-sm">Gradient Angle: {{ settings.gradientAngle }}deg</label>
                    <div class="flex items-center gap-2">
                    <input type="range" v-model.number="settings.gradientAngle" min="0" max="360" class="w-full">
                    <input type="number" v-model.number="settings.gradientAngle" class="yab-form-input w-20 text-center">
                </div>
            </div>
            <div>
                <label class="setting-label-sm">Gradient Colors</label>
                <div v-for="(stop, index) in settings.gradientStops" :key="index" class="bg-[#292929] p-3 rounded-lg mb-2 space-y-2">
                    <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-gray-300">Color Stop #{{ index + 1 }}</span>
                            <button v-if="settings.gradientStops.length > 1" @click="removeGradientStop(settings, index)" class="text-red-500 hover:text-red-400 text-xs">Remove</button>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="flex items-center gap-1">
                            <div
                                :style="{ backgroundColor: stop.color }"
                                class="w-8 h-[40px] rounded border border-gray-500 flex-shrink-0"
                                title="Selected color preview">
                            </div>
                            <input
                                aria-label="Gradient stop color input"
                                type="text"
                                :value="stop.color"
                                @input="event => stop.color = event.target.value"
                                data-coloris
                                class="yab-form-input clr-field flex-grow"
                                placeholder="Select color...">
                        </div>
                        <button @click="stop.color = 'transparent'" class="bg-gray-600 text-white text-xs rounded-md hover:bg-gray-500">Set Transparent</button>
                    </div>
                    <div>
                        <label class="setting-label-sm">Position: {{ stop.stop }}%</label>
                        <input type="range" v-model.number="stop.stop" min="0" max="100" class="w-full">
                    </div>
                </div>
                    <button @click="addGradientStop(settings)" class="w-full bg-blue-600 text-white text-sm py-2 rounded-md hover:bg-blue-700 mt-2">Add Color Stop</button>
            </div>
        </div>
    </div>
    <hr class="section-divider">
    <div>
        <h4 class="section-title">Title</h4>
        <div class="grid grid-cols-2 gap-2">
            <div class="col-span-2">
                <label class="setting-label-sm">Color</label>
                <div class="flex items-center gap-1">
                    <div
                        :style="{ backgroundColor: settings.titleColor }"
                        class="w-8 h-[40px] rounded border border-gray-500 flex-shrink-0"
                        title="Selected color preview">
                    </div>
                    <input
                        aria-label="Title color input"
                        type="text"
                        :value="settings.titleColor"
                        @input="event => settings.titleColor = event.target.value"
                        data-coloris
                        class="yab-form-input clr-field flex-grow"
                        placeholder="Select color...">
                </div>
            </div>
            <div>
                <label class="setting-label-sm">Font Size (px)</label>
                <input type="number" v-model.number="settings.titleSize" class="yab-form-input" placeholder="e.g., 18">
            </div>
            <div>
                <label class="setting-label-sm">Font Weight</label>
                <select v-model="settings.titleWeight" class="yab-form-input">
                    <option value="400">Normal</option><option value="500">Medium</option><option value="600">Semi-Bold</option><option value="700">Bold</option><option value="800">Extra Bold</option>
                </select>
            </div>
        </div>
    </div>
    <hr class="section-divider">
    <div>
        <h4 class="section-title">Stars & City</h4>
         <div class="grid grid-cols-2 gap-2">
            <div>
                <label class="setting-label-sm">Star Size (px)</label>
                <input type="number" v-model.number="settings.starSize" class="yab-form-input w-full">
            </div>
            <div>
                <label class="setting-label-sm">City Font Size (px)</label>
                <input type="number" v-model.number="settings.citySize" class="yab-form-input w-full">
            </div>
            <div class="col-span-2">
                 <label class="setting-label-sm">City Color</label>
                <div class="flex items-center gap-1">
                    <div
                        :style="{ backgroundColor: settings.cityColor }"
                        class="w-8 h-[40px] rounded border border-gray-500 flex-shrink-0"
                        title="Selected color preview">
                    </div>
                    <input
                        aria-label="City color input"
                        type="text"
                        :value="settings.cityColor"
                        @input="event => settings.cityColor = event.target.value"
                        data-coloris
                        class="yab-form-input clr-field flex-grow"
                        placeholder="Select color...">
                </div>
            </div>
        </div>
    </div>
    <hr class="section-divider">
    <div>
        <h4 class="section-title">Rating & Reviews</h4>
        <div class="grid grid-cols-2 gap-2">
            <div>
                <label class="setting-label-sm">Rating Box BG</label>
                <div class="flex items-center gap-1">
                    <div
                        :style="{ backgroundColor: settings.ratingBoxBgColor }"
                        class="w-8 h-[40px] rounded border border-gray-500 flex-shrink-0"
                        title="Selected color preview">
                    </div>
                    <input
                        aria-label="Rating box background color input"
                        type="text"
                        :value="settings.ratingBoxBgColor"
                        @input="event => settings.ratingBoxBgColor = event.target.value"
                        data-coloris
                        class="yab-form-input clr-field flex-grow"
                        placeholder="Select color...">
                </div>
            </div>
            <div>
                <label class="setting-label-sm">Rating Box Text</label>
                <div class="flex items-center gap-1">
                    <div
                        :style="{ backgroundColor: settings.ratingBoxColor }"
                        class="w-8 h-[40px] rounded border border-gray-500 flex-shrink-0"
                        title="Selected color preview">
                    </div>
                    <input
                        aria-label="Rating box text color input"
                        type="text"
                        :value="settings.ratingBoxColor"
                        @input="event => settings.ratingBoxColor = event.target.value"
                        data-coloris
                        class="yab-form-input clr-field flex-grow"
                        placeholder="Select color...">
                </div>
            </div>
            <div>
                <label class="setting-label-sm">Rating Box Font Size (px)</label>
                <input type="number" v-model.number="settings.ratingBoxSize" class="yab-form-input">
            </div>
            <div>
                <label class="setting-label-sm">Rating Box Font Weight</label>
                <select v-model="settings.ratingBoxWeight" class="yab-form-input">
                    <option value="400">Normal</option><option value="500">Medium</option><option value="600">Semi-Bold</option><option value="700">Bold</option>
                </select>
            </div>
            <div>
                <label class="setting-label-sm">Rating Text Color</label>
                <div class="flex items-center gap-1">
                    <div
                        :style="{ backgroundColor: settings.ratingTextColor }"
                        class="w-8 h-[40px] rounded border border-gray-500 flex-shrink-0"
                        title="Selected color preview">
                    </div>
                    <input
                        aria-label="Rating text color input"
                        type="text"
                        :value="settings.ratingTextColor"
                        @input="event => settings.ratingTextColor = event.target.value"
                        data-coloris
                        class="yab-form-input clr-field flex-grow"
                        placeholder="Select color...">
                </div>
            </div>
             <div>
                <label class="setting-label-sm">Rating Text Size (px)</label>
                <input type="number" v-model.number="settings.ratingTextSize" class="yab-form-input flex-1">
            </div>
            <div class="col-span-2">
                <label class="setting-label-sm">Rating Text Font Weight</label>
                <select v-model="settings.ratingTextWeight" class="yab-form-input">
                    <option value="400">Normal</option><option value="500">Medium</option><option value="600">Semi-Bold</option><option value="700">Bold</option>
                </select>
            </div>
            <div>
                <label class="setting-label-sm">Review Count Color</label>
                <div class="flex items-center gap-1">
                    <div
                        :style="{ backgroundColor: settings.reviewColor }"
                        class="w-8 h-[40px] rounded border border-gray-500 flex-shrink-0"
                        title="Selected color preview">
                    </div>
                    <input
                        aria-label="Review count color input"
                        type="text"
                        :value="settings.reviewColor"
                        @input="event => settings.reviewColor = event.target.value"
                        data-coloris
                        class="yab-form-input clr-field flex-grow"
                        placeholder="Select color...">
                </div>
            </div>
             <div>
                <label class="setting-label-sm">Review Count Size (px)</label>
                <input type="number" v-model.number="settings.reviewSize" class="yab-form-input flex-1">
            </div>
        </div>
    </div>
    <hr class="section-divider">
    <div>
        <h4 class="section-title">Price</h4>
         <div class="grid grid-cols-2 gap-2">
            <div>
                <label class="setting-label-sm">Amount Color</label>
                <div class="flex items-center gap-1">
                    <div
                        :style="{ backgroundColor: settings.priceAmountColor }"
                        class="w-8 h-[40px] rounded border border-gray-500 flex-shrink-0"
                        title="Selected color preview">
                    </div>
                    <input
                        aria-label="Price amount color input"
                        type="text"
                        :value="settings.priceAmountColor"
                        @input="event => settings.priceAmountColor = event.target.value"
                        data-coloris
                        class="yab-form-input clr-field flex-grow"
                        placeholder="Select color...">
                </div>
            </div>
            <div>
                <label class="setting-label-sm">Amount Size (px)</label>
                <input type="number" v-model.number="settings.priceAmountSize" class="yab-form-input flex-1">
            </div>
            <div>
                <label class="setting-label-sm">"From" & "/ night" Color</label>
                <div class="flex items-center gap-1">
                    <div
                        :style="{ backgroundColor: settings.priceFromColor }"
                        class="w-8 h-[40px] rounded border border-gray-500 flex-shrink-0"
                        title="Selected color preview">
                    </div>
                    <input
                        aria-label="Price from text color input"
                        type="text"
                        :value="settings.priceFromColor"
                        @input="event => settings.priceFromColor = event.target.value"
                        data-coloris
                        class="yab-form-input clr-field flex-grow"
                        placeholder="Select color...">
                </div>
            </div>
            <div>
                <label class="setting-label-sm">"From" & "/ night" Size (px)</label>
                <input type="number" v-model.number="settings.priceFromSize" class="yab-form-input flex-1">
            </div>
        </div>
    </div>
</div>
