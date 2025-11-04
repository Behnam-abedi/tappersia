<div :set="settings = currentView === 'desktop' ? banner.single : banner.single_mobile" class="flex flex-col gap-3">
    <div>
        <h4 class="section-title">Layout</h4>
        <div class="grid grid-cols-1 gap-2">
                        <div>
                <label class="setting-label-sm">Content Width</label>
                <div class="flex items-center gap-1">
                    <input type="number" v-model.number="settings.contentWidth" class="yab-form-input" placeholder="100">
                    <select v-model="settings.contentWidthUnit" class="yab-form-input w-20"><option>%</option><option>px</option></select>
                </div>
            </div>
            <div>
                <label class="setting-label-sm">Min Height (px)</label>
                <input type="number" v-model.number="settings.minHeight" class="yab-form-input" placeholder="e.g., 190">
            </div>

        </div>
    </div>
    <hr class="section-divider">
    <div>
            <h4 class="section-title">Content Padding (px)</h4>
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="setting-label-sm">Padding Y (Top/Bottom)</label>
                    <input type="number" v-model.number="settings.paddingY" class="yab-form-input" placeholder="e.g., 34">
                </div>
                <div>
                    <label class="setting-label-sm">Padding X (Left/Right)</label>
                    <input type="number" v-model.number="settings.paddingX" class="yab-form-input" placeholder="e.g., 34">
                </div>
         </div>

    </div>
        <hr class="section-divider">
    <div>
        <h4 class="section-title">Border</h4>
         <div class="flex items-center justify-between bg-[#292929] p-2 rounded-md mb-2">
            <label class="setting-label-sm">Enable Border</label>
            <label class="relative inline-flex items-center cursor-pointer" title="Toggle banner border">
                <input type="checkbox" v-model="settings.enableBorder" class="sr-only peer">
                <div class="w-11 h-6 bg-gray-600 rounded-full peer peer-focus:ring-2 peer-focus:ring-blue-500 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div>
            </label>
        </div>
        <div v-if="settings.enableBorder" class="grid grid-cols-3 gap-2">
             <div v-if="currentView === 'desktop'">
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
            <div :class="{'col-span-3': currentView === 'mobile', 'col-span-2': currentView === 'desktop'}">
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="setting-label-sm">Width (px)</label>
                        <input type="number" v-model.number="settings.borderWidth" class="yab-form-input" placeholder="e.g., 1">
                    </div>
                    <div>
                        <label class="setting-label-sm">Radius (px)</label>
                        <input type="number" v-model.number="settings.borderRadius" class="yab-form-input" placeholder="e.g., 8">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div v-if="currentView === 'desktop'">
         <hr class="section-divider">
        <div >
            <h4 class="section-title">Layers Control</h4>
            <div class="flex rounded-lg bg-[#292929] overflow-hidden p-1">
                <button @click="settings.layerOrder = 'image-below-overlay'" :class="settings.layerOrder === 'image-below-overlay' ? 'active-tab' : ''" class="flex-1 tab-button rounded-md">Image Below Color</button>
                <button @click="settings.layerOrder = 'overlay-below-image'" :class="settings.layerOrder === 'overlay-below-image' ? 'active-tab' : ''" class="flex-1 tab-button rounded-md">Color Below Image</button>
            </div>
        </div>
    </div>


    <hr class="section-divider">
    <div>
        <h4 class="section-title">Background Overlay</h4>
        <div class="flex rounded-lg bg-[#292929] overflow-hidden p-1">
            <button @click="settings.backgroundType = 'solid'" :class="{'active-tab': settings.backgroundType === 'solid'}" class="flex-1 tab-button rounded-md">Solid Color</button>
            <button @click="settings.backgroundType = 'gradient'" :class="{'active-tab': settings.backgroundType === 'gradient'}" class="flex-1 tab-button rounded-md">Gradient</button>
        </div>
        <div v-if="settings.backgroundType === 'solid'" class="space-y-2">
            <label class="setting-label-sm">Color</label>
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
                    placeholder="e.g., rgba(0,0,0,0.5)">
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
                         <button v-if="settings.gradientStops.length > 1" @click="removeGradientStop(settings.gradientStops, index)" class="text-red-500 hover:text-red-400 text-xs">Remove</button> <?php // FIX: Pass settings.gradientStops ?>
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
                                placeholder="e.g., transparent">
                        </div>
                        <button @click="stop.color = 'transparent'" class="bg-gray-600 text-white text-xs rounded-md hover:bg-gray-500">Set Transparent</button>
                    </div>
                    <div>
                        <label class="setting-label-sm">Position: {{ stop.stop }}%</label>
                        <input type="range" v-model.number="stop.stop" min="0" max="100" class="w-full">
                    </div>
                </div>
                 <button @click="addGradientStop(settings.gradientStops)" class="w-full bg-blue-600 text-white text-sm py-2 rounded-md hover:bg-blue-700 mt-2">Add Color Stop</button> <?php // FIX: Pass settings.gradientStops ?>
            </div>
        </div>
    </div>
    
    <?php // --- START: MODIFIED IMAGE SECTION --- ?>
    <div v-if="currentView === 'desktop'">
        <hr class="section-divider">
        <h4 class="section-title">Image</h4>
        <div  class="flex gap-2 items-center">
            <button @click="openMediaUploader('single')" class="flex-1 bg-blue-600 text-white px-3 py-1.5 rounded-md hover:bg-blue-700 text-sm">
                {{ settings.imageUrl ? 'Change Image' : 'Select Image' }}
            </button>
            <button v-if="settings.imageUrl" @click="removeImage('single')" class="bg-red-600 text-white px-3 py-1.5 rounded-md hover:bg-red-700 text-sm">
                Remove
            </button>
        </div>
    </div>

    <?php // --- Image Size & Position (Desktop & Mobile) --- ?>
    <div v-if="settings.imageUrl" class="mt-3 space-y-3">
        <hr class="section-divider" v-if="currentView !== 'desktop'"> <?php // Add separator for mobile ?>
        <h4 class="section-title" v-if="currentView !== 'desktop'">Image Layout</h4> <?php // Add title for mobile ?>
        
        <div class="flex items-center justify-between bg-[#292929] p-2 rounded-md">
            <label class="setting-label-sm">Enable Custom Image Size</label>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" v-model="settings.enableCustomImageSize" class="sr-only peer">
                <div class="w-11 h-6 bg-gray-600 rounded-full peer peer-focus:ring-2 peer-focus:ring-blue-500 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div>
            </label>
        </div>
         <div v-if="settings.enableCustomImageSize" class="grid grid-cols-2 gap-2">
            <div>
                <label class="setting-label-sm">Width</label>
                <div class="flex items-center gap-1">
                    <input type="number" v-model.number="settings.imageWidth" class="yab-form-input" placeholder="Auto">
                    <select v-model="settings.imageWidthUnit" class="yab-form-input w-20"><option>px</option><option>%</option></select>
                </div>
            </div>
            <div>
                <label class="setting-label-sm">Height</label>
                <div class="flex items-center gap-1">
                    <input type="number" v-model.number="settings.imageHeight" class="yab-form-input" placeholder="100%">
                    <select v-model="settings.imageHeightUnit" class="yab-form-input w-20"><option>px</option><option>%</option></select>
                </div>
            </div>
        </div>
         <div class="grid grid-cols-2 gap-2 mt-2">
             <div>
                <label class="setting-label-sm">Right (px)</label>
                <input type="number" v-model.number="settings.imagePosRight" class="yab-form-input" placeholder="Right">
             </div>
             <div>
                <label class="setting-label-sm">Bottom (px)</label>
                <input type="number" v-model.number="settings.imagePosBottom" class="yab-form-input" placeholder="Bottom">
            </div>
        </div>
    </div>
    <?php // --- END: MODIFIED IMAGE SECTION --- ?>
    <hr class="section-divider">

    <div class="space-y-2">
        <h4 class="section-title">Title</h4>
        <div v-if="currentView === 'desktop'">
            <label class="setting-label-sm">Title Text</label>
            <input type="text" v-model="settings.titleText" class="yab-form-input mb-2" placeholder="Title Text">
        </div>
        <div class="grid grid-cols-2 gap-2">
            <div v-if="currentView === 'desktop'">
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
                        placeholder="#hexcode">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-2" :class="{'col-span-2': currentView === 'mobile'}">
                <div>
                    <label class="setting-label-sm">Size (px)</label>
                    <input type="number" v-model.number="settings.titleSize" class="yab-form-input" placeholder="Size">
                </div>
                <div>
                    <label class="setting-label-sm">Weight</label>
                    <select v-model="settings.titleWeight" class="yab-form-input">
                        <option value="400">Normal</option><option value="500">Medium</option><option value="600">Semi-Bold</option><option value="700">Bold</option><option value="800">Extra Bold</option>
                    </select>
                </div>
            </div>
            </div>
    </div>
    <hr class="section-divider">
    <div class="space-y-2">
        <h4 class="section-title">Description</h4>
        <div v-if="currentView === 'desktop'">
            <label class="setting-label-sm">Description Text</label>
            <textarea v-model="settings.descText" rows="3" class="yab-form-input mb-2" placeholder="Description Text"></textarea>
        </div>
        <div class="grid grid-cols-2 gap-2">
            <div v-if="currentView === 'desktop'">
                 <label class="setting-label-sm">Color</label>
                 <div class="flex items-center gap-1">
                    <div
                        :style="{ backgroundColor: settings.descColor }"
                        class="w-8 h-[40px] rounded border border-gray-500 flex-shrink-0"
                        title="Selected color preview">
                    </div>
                    <input
                        aria-label="Description color input"
                        type="text"
                        :value="settings.descColor"
                        @input="event => settings.descColor = event.target.value"
                        data-coloris
                        class="yab-form-input clr-field flex-grow"
                        placeholder="#hexcode">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-2" :class="{'col-span-2': currentView === 'mobile'}">
                <div>
                    <label class="setting-label-sm">Size (px)</label>
                    <input type="number" v-model.number="settings.descSize" class="yab-form-input" placeholder="Size">
                </div>
                <div>
                    <label class="setting-label-sm">Weight</label>
                    <select v-model="settings.descWeight" class="yab-form-input">
                        <option value="400">Normal</option><option value="500">Medium</option><option value="600">Semi-Bold</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-2 mt-2">
            <div>
                <label class="setting-label-sm">Margin Top (px)</label>
                <input type="number" v-model.number="settings.marginTopDescription" class="yab-form-input" placeholder="e.g., 12">
            </div>
             <div>
                <label class="setting-label-sm">Margin Bottom (px)</label>
                <input type="number" v-model.number="settings.marginBottomDescription" class="yab-form-input" placeholder="e.g., 15">
            </div>
        </div>
    </div>
    <hr class="section-divider">
    <div class="space-y-2">
        <h4 class="section-title">Button</h4>
        <div v-if="currentView === 'desktop'">
            <label class="setting-label-sm">Button Text</label>
            <input type="text" v-model="settings.buttonText" class="yab-form-input mb-2" placeholder="Button Text">
            <label class="setting-label-sm">Button Link (URL)</label>
            <input type="text" v-model="settings.buttonLink" class="yab-form-input mb-2" placeholder="https://example.com">
        </div>
        <div v-if="currentView === 'desktop'" class="grid grid-cols-2 gap-2 mb-2">
            <div>
                <label class="setting-label-sm">Background Color</label>
                <div class="flex items-center gap-1">
                    <div
                        :style="{ backgroundColor: settings.buttonBgColor }"
                        class="w-8 h-[40px] rounded border border-gray-500 flex-shrink-0"
                        title="Selected color preview">
                    </div>
                    <input
                        aria-label="Button background color input"
                        type="text"
                        :value="settings.buttonBgColor"
                        @input="event => settings.buttonBgColor = event.target.value"
                        data-coloris
                        class="yab-form-input clr-field flex-grow"
                        placeholder="BG #hex">
                </div>
            </div>
            <div>
                <label class="setting-label-sm">Text Color</label>
                <div class="flex items-center gap-1">
                    <div
                        :style="{ backgroundColor: settings.buttonTextColor }"
                        class="w-8 h-[40px] rounded border border-gray-500 flex-shrink-0"
                        title="Selected color preview">
                    </div>
                    <input
                        aria-label="Button text color input"
                        type="text"
                        :value="settings.buttonTextColor"
                        @input="event => settings.buttonTextColor = event.target.value"
                        data-coloris
                        class="yab-form-input clr-field flex-grow"
                        placeholder="Text #hex">
                </div>
            </div>
        </div>
         <div class="grid grid-cols-2 gap-2">
            <div v-if="currentView === 'desktop'">
               <label class="setting-label-sm">Hover BG Color</label>
               <div class="flex items-center gap-1">
                    <div
                        :style="{ backgroundColor: settings.buttonBgHoverColor }"
                        class="w-8 h-[40px] rounded border border-gray-500 flex-shrink-0"
                        title="Selected color preview">
                    </div>
                    <input
                        aria-label="Button hover background color input"
                        type="text"
                        :value="settings.buttonBgHoverColor"
                        @input="event => settings.buttonBgHoverColor = event.target.value"
                        data-coloris
                        class="yab-form-input clr-field flex-grow"
                        placeholder="Hover #hex">
                </div>
            </div>
            <div :class="{'col-span-2': currentView === 'mobile'}">
                <label class="setting-label-sm">Font Size (px)</label>
                <input type="number" v-model.number="settings.buttonFontSize" class="yab-form-input" placeholder="Font Size">
            </div>
        </div>
        <div class="grid grid-cols-2 gap-2 mt-2">
             <div class="col-span-2">
                 <label class="setting-label-sm">Border Radius (px)</label>
                 <input type="number" v-model.number="settings.buttonBorderRadius" class="yab-form-input" placeholder="e.g., 8">
            </div>
            </div>
        <div>
             <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="setting-label-sm">Padding Y (Top/Bottom)</label>
                    <input type="number" v-model.number="settings.buttonPaddingY" class="yab-form-input" placeholder="e.g., 12">
                </div>
                <div>
                    <label class="setting-label-sm">Padding X (Left/Right)</label>
                    <input type="number" v-model.number="settings.buttonPaddingX" class="yab-form-input" placeholder="e.g., 24">
                </div>
             </div>
        </div>
    </div>
</div>