<header class="sticky top-7 bg-[#434343]/60 backdrop-blur-md p-4 z-20 flex items-center justify-between shadow-lg ltr">
    <div class="flex items-center gap-4">
        <a :href="allBannersUrl" class="text-gray-400 hover:text-white">&larr; All Banners</a>
        <span class="text-gray-600">|</span>
        <span class="text-sm">Title:</span>
        <input type="text" v-model="banner.name" placeholder="Enter Banner Name..." class="bg-[#656565] text-white border border-gray-600 rounded px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-[#00baa4] w-64">
    </div>
    
    <div class="flex items-center gap-5">
        <div class="flex items-center gap-2">
            <span class="text-sm">Display Method:</span>
            <div class="flex rounded-lg bg-[#656565] overflow-hidden">
                <button @click="banner.displayMethod = 'Fixed'" :class="banner.displayMethod === 'Fixed' ? 'bg-[#00baa4] text-white' : 'text-gray-300'" class="px-3 py-1 text-sm transition-colors duration-300 flex-1">Fixed</button>
                <button @click="banner.displayMethod = 'Embeddable'" :class="banner.displayMethod === 'Embeddable' ? 'bg-[#00baa4] text-white' : 'text-gray-300'" class="px-3 py-1 text-sm transition-colors duration-300 flex-1">Embeddable</button>
            </div>
        </div>

        <div v-if="banner.displayMethod === 'Embeddable'" class="flex items-center gap-2">
            <span class="text-sm">Shortcode:</span>
            <input type="text" :value="shortcode" readonly @click="copyShortcode" class="w-52 bg-[#656565] text-white text-left rounded px-2 py-1 text-sm cursor-pointer" title="Click to copy">
        </div>

        <div class="flex items-center gap-2">
            <span class="text-sm">Status:</span>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" v-model="banner.isActive" class="sr-only peer">
                <div class="w-11 h-6 bg-gray-600 rounded-full peer peer-focus:ring-2 peer-focus:ring-red-500 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div>
            </label>
        </div>

        <button @click="saveBanner" :disabled="isSaving" class="bg-[#00baa4] text-white font-bold px-8 py-1.5 rounded hover:bg-opacity-80 transition-all flex items-center gap-2 disabled:bg-gray-500 disabled:cursor-not-allowed">
            <span v-if="isSaving" class="dashicons dashicons-update animate-spin"></span>
            {{ isSaving ? 'Saving...' : 'Save' }}
        </button>
    </div>
</header>

<main class="grid grid-cols-12 gap-6 p-6 ltr">
    <div class="col-span-4 overflow-y-auto ltr flex gap-3 flex-col [&>*:last-child]:mb-[40px]" style="max-height: calc(100vh - 120px);">
        <div v-for="(b, key) in { left: banner.left, right: banner.right }" :key="key" class="bg-[#434343] p-5 rounded-lg shadow-xl">
            
            <h3 class="font-bold text-xl text-white capitalize tracking-wide mb-5">{{ key }} Banner Settings</h3>
            
            <div class="flex flex-col gap-5">
                <div>
                    <h4 class="section-title">Background</h4>
                    <div class="flex gap-2 mb-2">
                        <button @click="b.backgroundType = 'solid'" :class="{'active-tab': b.backgroundType === 'solid'}" class="flex-1 tab-button">Solid Color</button>
                        <button @click="b.backgroundType = 'gradient'" :class="{'active-tab': b.backgroundType === 'gradient'}" class="flex-1 tab-button">Gradient</button>
                    </div>
                    <div v-if="b.backgroundType === 'solid'" class="space-y-2">
                        <label class="setting-label-sm">Background Color</label>
                        <div class="yab-color-input-wrapper">
                            <input type="color" v-model="b.bgColor" class="yab-color-picker">
                            <input type="text" v-model="b.bgColor" class="yab-hex-input" placeholder="#hexcode">
                        </div>
                    </div>
                    <div v-else class="space-y-2">
                         <div>
                            <label class="setting-label-sm">Gradient Colors</label>
                            <div class="grid grid-cols-2 gap-2">
                                <div class="yab-color-input-wrapper">
                                    <input type="color" v-model="b.gradientColor1" class="yab-color-picker">
                                    <input type="text" v-model="b.gradientColor1" class="yab-hex-input" placeholder="#hexcode">
                                </div>
                                <div class="yab-color-input-wrapper">
                                    <input type="color" v-model="b.gradientColor2" class="yab-color-picker">
                                    <input type="text" v-model="b.gradientColor2" class="yab-hex-input" placeholder="#hexcode">
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="setting-label-sm">Gradient Angle</label>
                            <div class="flex items-center gap-2">
                                <input type="number" v-model.number="b.gradientAngle" class="yab-form-input w-24" placeholder="e.g., 90">
                                <span class="text-sm text-gray-400">deg</span>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="section-divider">
                <div>
                    <h4 class="section-title">Image</h4>
                    <div class="flex gap-2 items-center">
                        <button @click="openMediaUploader(key)" class="flex-1 bg-blue-600 text-white px-3 py-1.5 rounded-md hover:bg-blue-700 text-sm">
                            {{ b.imageUrl ? 'Change Image' : 'Select Image' }}
                        </button>
                        <button v-if="b.imageUrl" @click="removeImage(key)" class="bg-red-600 text-white px-3 py-1.5 rounded-md hover:bg-red-700 text-sm">
                            Remove
                        </button>
                    </div>
                    <div v-if="b.imageUrl" class="mt-3 space-y-3">
                        <div v-if="!b.enableCustomImageSize" class="flex items-center gap-2">
                            <label class="setting-label-sm w-20">Image Fit:</label>
                            <select v-model="b.imageFit" class="yab-form-input flex-1">
                                <option value="cover">Cover</option>
                                <option value="contain">Contain</option>
                                <option value="fill">Fill</option>
                                <option value="none">None (Natural Size)</option>
                            </select>
                        </div>
                        <div class="flex items-center justify-between bg-[#292929] p-2 rounded-md">
                            <label class="setting-label-sm">Enable Custom Image Size</label>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" v-model="b.enableCustomImageSize" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-600 rounded-full peer peer-focus:ring-2 peer-focus:ring-blue-500 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div>
                            </label>
                        </div>
                         <div v-if="b.enableCustomImageSize" class="grid grid-cols-2 gap-2">
                             <div>
                                <label class="setting-label-sm">Width (px)</label>
                                <input type="number" v-model.number="b.imageWidth" class="yab-form-input" placeholder="Width">
                             </div>
                             <div>
                                <label class="setting-label-sm">Height (px)</label>
                                <input type="number" v-model.number="b.imageHeight" class="yab-form-input" placeholder="Height">
                            </div>
                        </div>
                         <div class="grid grid-cols-2 gap-2 mt-2">
                            <div>
                               <label class="setting-label-sm">Right (px)</label>
                               <input type="number" v-model.number="b.imagePosRight" class="yab-form-input" placeholder="Right">
                            </div>
                            <div>
                                <label class="setting-label-sm">Bottom (px)</label>
                                <input type="number" v-model.number="b.imagePosBottom" class="yab-form-input" placeholder="Bottom">
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="section-divider">
                <div>
                        <h4 class="section-title">Content Alignment</h4>
                        <div class="flex rounded-lg bg-[#292929] overflow-hidden ">
                        <button @click="b.alignment = 'left'" :class="b.alignment === 'left' ? 'bg-[#00baa4] text-white' : 'text-gray-300'" class="px-3 py-1 text-sm transition-colors duration-300 flex-1">Left</button>
                        <button @click="b.alignment = 'center'" :class="b.alignment === 'center' ? 'bg-[#00baa4] text-white' : 'text-gray-300'" class="px-3 py-1 text-sm transition-colors duration-300 flex-1">Center</button>
                        <button @click="b.alignment = 'right'" :class="b.alignment === 'right' ? 'bg-[#00baa4] text-white' : 'text-gray-300'" class="px-3 py-1 text-sm transition-colors duration-300 flex-1">Right</button>
                    </div>
                </div>
                <hr class="section-divider">
                <div class="space-y-2">
                    <h4 class="section-title">Title</h4>
                     <label class="setting-label-sm">Title Text</label>
                    <input type="text" v-model="b.titleText" class="yab-form-input mb-2" placeholder="Title Text">
                    <div class="grid grid-cols-2 gap-2">
                         <div>
                            <label class="setting-label-sm">Color</label>
                            <div class="yab-color-input-wrapper">
                                <input type="color" v-model="b.titleColor" class="yab-color-picker">
                                <input type="text" v-model="b.titleColor" class="yab-hex-input" placeholder="#hexcode">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="setting-label-sm">Size (px)</label>
                                <input type="number" v-model.number="b.titleSize" class="yab-form-input" placeholder="Size">
                            </div>
                            <div>
                                <label class="setting-label-sm">Weight</label>
                                <select v-model="b.titleWeight" class="yab-form-input">
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
                    <textarea v-model="b.descText" rows="3" class="yab-form-input mb-2" placeholder="Description Text"></textarea>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="setting-label-sm">Color</label>
                            <div class="yab-color-input-wrapper">
                                <input type="color" v-model="b.descColor" class="yab-color-picker">
                                <input type="text" v-model="b.descColor" class="yab-hex-input" placeholder="#hexcode">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="setting-label-sm">Size (px)</label>
                                <input type="number" v-model.number="b.descSize" class="yab-form-input" placeholder="Size">
                            </div>
                            <div>
                                <label class="setting-label-sm">Weight</label>
                                <select v-model="b.descWeight" class="yab-form-input">
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
                    <input type="text" v-model="b.buttonText" class="yab-form-input mb-2" placeholder="Button Text">
                    <label class="setting-label-sm">Button Link (URL)</label>
                    <input type="text" v-model="b.buttonLink" class="yab-form-input mb-2" placeholder="https://example.com">
                    <div class="grid grid-cols-2 gap-2 mb-2">
                        <div>
                            <label class="setting-label-sm">Background Color</label>
                            <div class="yab-color-input-wrapper">
                                <input type="color" v-model="b.buttonBgColor" class="yab-color-picker">
                                <input type="text" v-model="b.buttonBgColor" class="yab-hex-input" placeholder="BG #hex">
                            </div>
                        </div>
                        <div>
                            <label class="setting-label-sm">Text Color</label>
                            <div class="yab-color-input-wrapper">
                                <input type="color" v-model="b.buttonTextColor" class="yab-color-picker">
                                <input type="text" v-model="b.buttonTextColor" class="yab-hex-input" placeholder="Text #hex">
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="setting-label-sm">Hover BG Color</label>
                            <div class="yab-color-input-wrapper">
                                <input type="color" v-model="b.buttonBgHoverColor" class="yab-color-picker">
                                <input type="text" v-model="b.buttonBgHoverColor" class="yab-hex-input" placeholder="Hover #hex">
                            </div>
                        </div>
                         <div>
                            <label class="setting-label-sm">Font Size (px)</label>
                            <input type="number" v-model.number="b.buttonFontSize" class="yab-form-input" placeholder="Font Size">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-span-8 sticky top-[120px] space-y-4">
        <div class="bg-[#434343] p-4 rounded-lg">
            <h3 class="preview-title">Live Preview</h3>
            <div class="flex flex-row gap-2 justify-center">
                <div v-for="(b, key) in { left: banner.left, right: banner.right }" :key="`preview-${key}`" 
                    class="rounded-[11.35px] relative overflow-hidden flex flex-shrink-0" 
                    :style="{ background: bannerStyles(b), width: '432px', height: '177px' }">
                    
                    <img v-if="b.imageUrl" :src="b.imageUrl" :style="imageStyleObject(b)" />

                    <div class="w-full h-full py-[37px] px-[31px] flex flex-col z-10 relative" :style="{ alignItems: contentAlignment(b.alignment), textAlign: b.alignment }">
                        <h4 class="font-bold " :style="{ color: b.titleColor, fontSize: b.titleSize + 'px', fontWeight: b.titleWeight, margin: 0 }">{{ b.titleText }}</h4>
                        <p class="mt-2 leading-tight mb-[25px]" :style="{ color: b.descColor, fontSize: b.descSize + 'px', fontWeight: b.descWeight, whiteSpace: 'pre-wrap' ,}">{{ b.descText }}</p>
                        <a v-if="b.buttonText" :href="b.buttonLink" target="_blank" 
                            class="py-2 px-4 rounded mt-auto" 
                            :style="{ backgroundColor: b.buttonBgColor, color: b.buttonTextColor, fontSize: b.buttonFontSize + 'px', alignSelf: buttonAlignment(b.alignment) }">
                            {{ b.buttonText }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div v-if="banner.displayMethod === 'Fixed'">
            <?php require_once YAB_PLUGIN_DIR . 'admin/views/components/display-conditions.php'; ?>
        </div>

    </div>
</main>