<div :set="settings = currentView === 'desktop' ? banner.single : banner.single_mobile">
    <div>
        <h4 class="section-title">Layout</h4>
        <div class="flex items-center justify-between bg-[#292929] p-2 rounded-md mb-2">
            <label class="setting-label-sm">Enable Custom Dimensions</label>
            <label class="relative inline-flex items-center cursor-pointer" title="Toggle custom banner dimensions">
                <input type="checkbox" v-model="settings.enableCustomDimensions" class="sr-only peer">
                <div class="w-11 h-6 bg-gray-600 rounded-full peer peer-focus:ring-2 peer-focus:ring-blue-500 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div>
            </label>
        </div>
        <div v-if="settings.enableCustomDimensions" class="grid grid-cols-2 gap-2">
            <div>
                <label class="setting-label-sm">Width</label>
                <div class="flex items-center gap-1">
                    <input type="number" v-model.number="settings.width" class="yab-form-input" placeholder="Width">
                    <select v-model="settings.widthUnit" class="yab-form-input w-20"><option>px</option><option>%</option></select>
                </div>
            </div>
            <div>
                <label class="setting-label-sm">Height</label>
                <div class="flex items-center gap-1">
                    <input type="number" v-model.number="settings.height" class="yab-form-input" placeholder="Height">
                    <select v-model="settings.heightUnit" class="yab-form-input w-20"><option>px</option><option>%</option></select>
                </div>
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
             <div>
                <label class="setting-label-sm">Color</label>
                <div class="yab-color-input-wrapper">
                    <input type="color" v-model="settings.borderColor" class="yab-color-picker">
                     <input type="text" v-model="settings.borderColor" class="yab-hex-input" placeholder="Color">
                </div>
            </div>
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
    <hr class="section-divider">
    <div>
         <h4 class="section-title">Content Padding (px)</h4>
         <div class="grid grid-cols-2 gap-2">
            <div>
                <label class="setting-label-sm">Top</label>
                <input type="number" v-model.number="settings.paddingTop" class="yab-form-input" placeholder="Top">
            </div>
            <div>
                <label class="setting-label-sm">Right</label>
                <input type="number" v-model.number="settings.paddingRight" class="yab-form-input" placeholder="Right">
            </div>
            <div>
                 <label class="setting-label-sm">Bottom</label>
                <input type="number" v-model.number="settings.paddingBottom" class="yab-form-input" placeholder="Bottom">
            </div>
            <div>
                <label class="setting-label-sm">Left</label>
                <input type="number" v-model.number="settings.paddingLeft" class="yab-form-input" placeholder="Left">
            </div>
         </div>
    </div>
    <hr class="section-divider">
    <div>
        <h4 class="section-title">Background</h4>
        <div class="flex gap-2 mb-2 bg-[#292929] rounded-lg border-none">
            <button @click="settings.backgroundType = 'solid'" :class="{'active-tab': settings.backgroundType === 'solid'}" class="flex-1 tab-button rounded-l-lg border-none">Solid Color</button>
            <button @click="settings.backgroundType = 'gradient'" :class="{'active-tab': settings.backgroundType === 'gradient'}" class="flex-1 tab-button rounded-r-lg border-none">Gradient</button>
        </div>
        <div v-if="settings.backgroundType === 'solid'" class="space-y-2">
            <label class="setting-label-sm">Background Color</label>
            <div class="yab-color-input-wrapper">
                <input type="color" v-model="settings.bgColor" class="yab-color-picker">
                <input type="text" v-model="settings.bgColor" class="yab-hex-input" placeholder="#hexcode">
            </div>
        </div>
        <div v-else class="space-y-2">
            <div>
                <label class="setting-label-sm">Gradient Colors</label>
                <div class="grid grid-cols-2 gap-2">
                    <div class="yab-color-input-wrapper">
                        <input type="color" v-model="settings.gradientColor1" class="yab-color-picker">
                        <input type="text" v-model="settings.gradientColor1" class="yab-hex-input" placeholder="#hexcode">
                    </div>
                    <div class="yab-color-input-wrapper">
                        <input type="color" v-model="settings.gradientColor2" class="yab-color-picker">
                        <input type="text" v-model="settings.gradientColor2" class="yab-hex-input" placeholder="#hexcode">
                    </div>
                </div>
            </div>
            <div>
                <label class="setting-label-sm">Gradient Angle</label>
                <div class="flex items-center gap-2">
                    <input type="number" v-model.number="settings.gradientAngle" class="yab-form-input w-full" placeholder="e.g., 90">
                    <span class="text-sm text-gray-400">deg</span>
                </div>
            </div>
        </div>
    </div>
    <hr class="section-divider">
    <div>
        <h4 class="section-title">Image</h4>
        <div class="flex gap-2 items-center">
            <button @click="openMediaUploader(currentView === 'desktop' ? 'single' : 'single_mobile')" class="flex-1 bg-blue-600 text-white px-3 py-1.5 rounded-md hover:bg-blue-700 text-sm">
                {{ settings.imageUrl ? 'Change Image' : 'Select Image' }}
            </button>
            <button v-if="settings.imageUrl" @click="removeImage(currentView === 'desktop' ? 'single' : 'single_mobile')" class="bg-red-600 text-white px-3 py-1.5 rounded-md hover:bg-red-700 text-sm">
                Remove
            </button>
        </div>
        <div v-if="settings.imageUrl" class="mt-3 space-y-3">
            <div v-if="!settings.enableCustomImageSize" class="flex items-center gap-2">
                <label class="setting-label-sm w-20">Image Fit:</label>
                <select v-model="settings.imageFit" class="yab-form-input flex-1">
                    <option value="cover">Cover</option>
                    <option value="contain">Contain</option>
                    <option value="fill">Fill</option>
                    <option value="none">None</option>
                </select>
            </div>
            <div class="flex items-center justify-between bg-[#292929] p-2 rounded-md">
                <label class="setting-label-sm">Enable Custom Image Size</label>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" v-model="settings.enableCustomImageSize" class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-600 rounded-full peer peer-focus:ring-2 peer-focus:ring-blue-500 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div>
                </label>
            </div>
             <div v-if="settings.enableCustomImageSize" class="grid grid-cols-2 gap-2">
                <div>
                    <label class="setting-label-sm">Width (px)</label>
                    <input type="number" v-model.number="settings.imageWidth" class="yab-form-input" placeholder="Width">
                </div>
                <div>
                    <label class="setting-label-sm">Height (px)</label>
                    <input type="number" v-model.number="settings.imageHeight" class="yab-form-input" placeholder="Height">
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
    </div>
    <hr class="section-divider">
    <div>
        <h4 class="section-title">Content Alignment</h4>
        <div class="flex rounded-lg bg-[#292929] overflow-hidden">
            <button @click="settings.alignment = 'left'" :class="settings.alignment === 'left' ? 'active-tab' : ''" class="flex-1 tab-button rounded-l-lg">Left</button>
            <button @click="settings.alignment = 'center'" :class="settings.alignment === 'center' ? 'active-tab' : ''" class="flex-1 tab-button">Center</button>
            <button @click="settings.alignment = 'right'" :class="settings.alignment === 'right' ? 'active-tab' : ''" class="flex-1 tab-button rounded-r-lg">Right</button>
        </div>
    </div>
    <hr class="section-divider">
    <div>
        <h4 class="section-title">Spacing (px)</h4>
        <div class="grid grid-cols-2 gap-2">
            <div>
                <label class="setting-label-sm">Title to Description</label>
                <input type="number" v-model.number="settings.marginTopDescription" class="yab-form-input" placeholder="e.g., 8">
            </div>
            <div>
                <label class="setting-label-sm">Description to Button</label>
                <input type="number" v-model.number="settings.marginBottomDescription" class="yab-form-input" placeholder="e.g., 24">
            </div>
        </div>
    </div>
    <hr class="section-divider">
    <div class="space-y-2">
        <h4 class="section-title">Title</h4>
        <label class="setting-label-sm">Title Text</label>
        <input type="text" v-model="settings.titleText" class="yab-form-input mb-2" placeholder="Title Text">
        <div class="grid grid-cols-2 gap-2">
            <div>
                <label class="setting-label-sm">Color</label>
                <div class="yab-color-input-wrapper">
                    <input type="color" v-model="settings.titleColor" class="yab-color-picker">
                    <input type="text" v-model="settings.titleColor" class="yab-hex-input" placeholder="#hexcode">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-2">
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
        <label class="setting-label-sm">Description Text</label>
        <textarea v-model="settings.descText" rows="3" class="yab-form-input mb-2" placeholder="Description Text"></textarea>
        <div class="grid grid-cols-2 gap-2">
            <div>
                 <label class="setting-label-sm">Color</label>
                 <div class="yab-color-input-wrapper">
                    <input type="color" v-model="settings.descColor" class="yab-color-picker">
                    <input type="text" v-model="settings.descColor" class="yab-hex-input" placeholder="#hexcode">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-2">
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
    </div>
    <hr class="section-divider">
    <div class="space-y-2">
        <h4 class="section-title">Button</h4>
        <label class="setting-label-sm">Button Text</label>
        <input type="text" v-model="settings.buttonText" class="yab-form-input mb-2" placeholder="Button Text">
        <label class="setting-label-sm">Button Link (URL)</label>
        <input type="text" v-model="settings.buttonLink" class="yab-form-input mb-2" placeholder="https://example.com">
        <div class="grid grid-cols-2 gap-2 mb-2">
            <div>
                <label class="setting-label-sm">Background Color</label>
                <div class="yab-color-input-wrapper">
                    <input type="color" v-model="settings.buttonBgColor" class="yab-color-picker">
                    <input type="text" v-model="settings.buttonBgColor" class="yab-hex-input" placeholder="BG #hex">
                </div>
            </div>
            <div>
                <label class="setting-label-sm">Text Color</label>
                <div class="yab-color-input-wrapper">
                    <input type="color" v-model="settings.buttonTextColor" class="yab-color-picker">
                    <input type="text" v-model="settings.buttonTextColor" class="yab-hex-input" placeholder="Text #hex">
                </div>
            </div>
        </div>
         <div class="grid grid-cols-2 gap-2">
            <div>
               <label class="setting-label-sm">Hover BG Color</label>
               <div class="yab-color-input-wrapper">
                    <input type="color" v-model="settings.buttonBgHoverColor" class="yab-color-picker">
                    <input type="text" v-model="settings.buttonBgHoverColor" class="yab-hex-input" placeholder="Hover #hex">
                </div>
            </div>
            <div>
                <label class="setting-label-sm">Font Size (px)</label>
                <input type="number" v-model.number="settings.buttonFontSize" class="yab-form-input" placeholder="Font Size">
            </div>
        </div>
        <div class="grid grid-cols-2 gap-2 mt-2">
            <div>
                <label class="setting-label-sm">Width</label>
                <div class="flex items-center gap-1">
                    <input type="number" v-model.number="settings.buttonWidth" class="yab-form-input" placeholder="Width">
                    <select v-model="settings.buttonWidthUnit" class="yab-form-input w-20"><option>px</option><option>%</option></select>
                </div>
            </div>
            <div>
                <label class="setting-label-sm">Height</label>
                <div class="flex items-center gap-1">
                     <input type="number" v-model.number="settings.buttonHeight" class="yab-form-input" placeholder="Height">
                    <select v-model="settings.buttonHeightUnit" class="yab-form-input w-20"><option>px</option><option>%</option></select>
                </div>
            </div>
             <div>
                <label class="setting-label-sm">Min-Width</label>
                <div class="flex items-center gap-1">
                     <input type="number" v-model.number="settings.buttonMinWidth" class="yab-form-input" placeholder="Min-Width">
                    <select v-model="settings.buttonMinWidthUnit" class="yab-form-input w-20"><option>px</option><option>%</option></select>
                </div>
            </div>
            <div>
                 <label class="setting-label-sm">Border Radius (px)</label>
                 <input type="number" v-model.number="settings.buttonBorderRadius" class="yab-form-input" placeholder="e.g., 4">
            </div>
        </div>
    </div>
</div>