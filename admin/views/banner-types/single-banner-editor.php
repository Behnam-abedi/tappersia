<?php require YAB_PLUGIN_DIR . 'admin/views/components/banner-editor-header.php'; ?>

<main class="grid grid-cols-12 gap-6 p-6 ltr">
    <div class="col-span-4 overflow-y-auto flex flex-col gap-6 [&>*:last-child]:mb-[40px]" style="max-height: calc(100vh - 120px);">
        <div class="bg-[#434343] p-5 rounded-lg shadow-xl">
            <h3 class="font-bold text-xl text-white tracking-wide mb-5">Banner Settings</h3>
            
            <div class="flex flex-col gap-5">
                <div>
                    <h4 class="section-title">Background</h4>
                    <div class="flex gap-2 mb-2">
                        <button @click="banner.single.backgroundType = 'solid'" :class="{'active-tab': banner.single.backgroundType === 'solid'}" class="flex-1 tab-button">Solid Color</button>
                        <button @click="banner.single.backgroundType = 'gradient'" :class="{'active-tab': banner.single.backgroundType === 'gradient'}" class="flex-1 tab-button">Gradient</button>
                    </div>
                    <div v-if="banner.single.backgroundType === 'solid'" class="space-y-2">
                        <label class="setting-label-sm">Background Color</label>
                        <div class="yab-color-input-wrapper">
                            <input type="color" v-model="banner.single.bgColor" class="yab-color-picker">
                            <input type="text" v-model="banner.single.bgColor" class="yab-hex-input" placeholder="#hexcode">
                        </div>
                    </div>
                    <div v-else class="space-y-2">
                        <div>
                            <label class="setting-label-sm">Gradient Colors</label>
                            <div class="grid grid-cols-2 gap-2">
                                <div class="yab-color-input-wrapper">
                                    <input type="color" v-model="banner.single.gradientColor1" class="yab-color-picker">
                                    <input type="text" v-model="banner.single.gradientColor1" class="yab-hex-input" placeholder="#hexcode">
                                </div>
                                <div class="yab-color-input-wrapper">
                                    <input type="color" v-model="banner.single.gradientColor2" class="yab-color-picker">
                                    <input type="text" v-model="banner.single.gradientColor2" class="yab-hex-input" placeholder="#hexcode">
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="setting-label-sm">Gradient Angle</label>
                            <div class="flex items-center gap-2">
                                <input type="number" v-model.number="banner.single.gradientAngle" class="yab-form-input w-24" placeholder="e.g., 90">
                                <span class="text-sm text-gray-400">deg</span>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="section-divider">
                <div>
                    <h4 class="section-title">Image</h4>
                    <div class="flex gap-2 items-center">
                        <button @click="openMediaUploader('single')" class="flex-1 bg-blue-600 text-white px-3 py-1.5 rounded-md hover:bg-blue-700 text-sm">
                            {{ banner.single.imageUrl ? 'Change Image' : 'Select Image' }}
                        </button>
                        <button v-if="banner.single.imageUrl" @click="removeImage('single')" class="bg-red-600 text-white px-3 py-1.5 rounded-md hover:bg-red-700 text-sm">
                            Remove
                        </button>
                    </div>
                    <div v-if="banner.single.imageUrl" class="mt-3 space-y-3">
                        <div v-if="!banner.single.enableCustomImageSize" class="flex items-center gap-2">
                            <label class="setting-label-sm w-20">Image Fit:</label>
                            <select v-model="banner.single.imageFit" class="yab-form-input flex-1">
                                <option value="cover">Cover</option>
                                <option value="contain">Contain</option>
                                <option value="fill">Fill</option>
                                <option value="none">None (Natural Size)</option>
                            </select>
                        </div>
                        <div class="flex items-center justify-between bg-[#292929] p-2 rounded-md">
                            <label class="setting-label-sm">Enable Custom Image Size</label>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" v-model="banner.single.enableCustomImageSize" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-600 rounded-full peer peer-focus:ring-2 peer-focus:ring-blue-500 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div>
                            </label>
                        </div>
                         <div v-if="banner.single.enableCustomImageSize" class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="setting-label-sm">Width (px)</label>
                                <input type="number" v-model.number="banner.single.imageWidth" class="yab-form-input" placeholder="Width">
                            </div>
                            <div>
                                <label class="setting-label-sm">Height (px)</label>
                                <input type="number" v-model.number="banner.single.imageHeight" class="yab-form-input" placeholder="Height">
                            </div>
                        </div>
                         <div class="grid grid-cols-2 gap-2 mt-2">
                             <div>
                                <label class="setting-label-sm">Right (px)</label>
                                <input type="number" v-model.number="banner.single.imagePosRight" class="yab-form-input" placeholder="Right">
                             </div>
                             <div>
                                <label class="setting-label-sm">Bottom (px)</label>
                                <input type="number" v-model.number="banner.single.imagePosBottom" class="yab-form-input" placeholder="Bottom">
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="section-divider">
                <div>
                    <h4 class="section-title">Content Alignment</h4>
                    <div class="flex rounded-lg bg-[#292929] overflow-hidden">
                        <button @click="banner.single.alignment = 'right'" :class="banner.single.alignment === 'right' ? 'bg-[#00baa4] text-white' : 'text-gray-300'" class="px-3 py-1 text-sm transition-colors duration-300 flex-1">Left</button>
                        <button @click="banner.single.alignment = 'center'" :class="banner.single.alignment === 'center' ? 'bg-[#00baa4] text-white' : 'text-gray-300'" class="px-3 py-1 text-sm transition-colors duration-300 flex-1">Center</button>
                        <button @click="banner.single.alignment = 'left'" :class="banner.single.alignment === 'left' ? 'bg-[#00baa4] text-white' : 'text-gray-300'" class="px-3 py-1 text-sm transition-colors duration-300 flex-1">Right</button>
                    </div>
                </div>
                <hr class="section-divider">
                <div class="space-y-2">
                    <h4 class="section-title">Title</h4>
                    <label class="setting-label-sm">Title Text</label>
                    <input type="text" v-model="banner.single.titleText" class="yab-form-input mb-2" placeholder="Title Text">
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="setting-label-sm">Color</label>
                            <div class="yab-color-input-wrapper">
                                <input type="color" v-model="banner.single.titleColor" class="yab-color-picker">
                                <input type="text" v-model="banner.single.titleColor" class="yab-hex-input" placeholder="#hexcode">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="setting-label-sm">Size (px)</label>
                                <input type="number" v-model.number="banner.single.titleSize" class="yab-form-input" placeholder="Size">
                            </div>
                            <div>
                                <label class="setting-label-sm">Weight</label>
                                <select v-model="banner.single.titleWeight" class="yab-form-input">
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
                    <textarea v-model="banner.single.descText" rows="3" class="yab-form-input mb-2" placeholder="Description Text"></textarea>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                             <label class="setting-label-sm">Color</label>
                             <div class="yab-color-input-wrapper">
                                <input type="color" v-model="banner.single.descColor" class="yab-color-picker">
                                <input type="text" v-model="banner.single.descColor" class="yab-hex-input" placeholder="#hexcode">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="setting-label-sm">Size (px)</label>
                                <input type="number" v-model.number="banner.single.descSize" class="yab-form-input" placeholder="Size">
                            </div>
                            <div>
                                <label class="setting-label-sm">Weight</label>
                                <select v-model="banner.single.descWeight" class="yab-form-input">
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
                    <input type="text" v-model="banner.single.buttonText" class="yab-form-input mb-2" placeholder="Button Text">
                    <label class="setting-label-sm">Button Link (URL)</label>
                    <input type="text" v-model="banner.single.buttonLink" class="yab-form-input mb-2" placeholder="https://example.com">
                    <div class="grid grid-cols-2 gap-2 mb-2">
                        <div>
                            <label class="setting-label-sm">Background Color</label>
                            <div class="yab-color-input-wrapper">
                                <input type="color" v-model="banner.single.buttonBgColor" class="yab-color-picker">
                                <input type="text" v-model="banner.single.buttonBgColor" class="yab-hex-input" placeholder="BG #hex">
                            </div>
                        </div>
                        <div>
                            <label class="setting-label-sm">Text Color</label>
                            <div class="yab-color-input-wrapper">
                                <input type="color" v-model="banner.single.buttonTextColor" class="yab-color-picker">
                                <input type="text" v-model="banner.single.buttonTextColor" class="yab-hex-input" placeholder="Text #hex">
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                           <label class="setting-label-sm">Hover BG Color</label>
                           <div class="yab-color-input-wrapper">
                                <input type="color" v-model="banner.single.buttonBgHoverColor" class="yab-color-picker">
                                <input type="text" v-model="banner.single.buttonBgHoverColor" class="yab-hex-input" placeholder="Hover #hex">
                            </div>
                        </div>
                        <div>
                            <label class="setting-label-sm">Font Size (px)</label>
                            <input type="number" v-model.number="banner.single.buttonFontSize" class="yab-form-input" placeholder="Font Size">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-span-8 sticky top-[120px] space-y-4">
        <div class="bg-[#434343] p-4 rounded-lg">
            <h3 class="preview-title">Live Preview</h3>
            <div class="flex justify-center">
                <div class="rounded-lg relative overflow-hidden flex flex-shrink-0" 
                    :style="{ background: bannerStyles(banner.single), width: '886px', height: '178px' }">
                    
                    <img v-if="banner.single.imageUrl" :src="banner.single.imageUrl" :style="imageStyleObject(banner.single)" />

                    <div class="w-full h-full p-8 flex flex-col z-10 relative" :style="{ alignItems: contentAlignment(banner.single.alignment), textAlign: banner.single.alignment === 'left' ? 'right' : (banner.single.alignment === 'right' ? 'left' : 'center') }">
                        <h4 :style="{ color: banner.single.titleColor, fontSize: banner.single.titleSize + 'px', fontWeight: banner.single.titleWeight, margin: 0 }">{{ banner.single.titleText }}</h4>
                        <p class="mt-2 mb-6" :style="{ color: banner.single.descColor, fontSize: banner.single.descSize + 'px', fontWeight: banner.single.descWeight, whiteSpace: 'pre-wrap' }">{{ banner.single.descText }}</p>
                        <a v-if="banner.single.buttonText" :href="banner.single.buttonLink" target="_blank" 
                            class="py-2 px-4 rounded mt-auto" 
                            :style="{ backgroundColor: banner.single.buttonBgColor, color: banner.single.buttonTextColor, fontSize: banner.single.buttonFontSize + 'px', alignSelf: buttonAlignment(banner.single.alignment) }">
                            {{ banner.single.buttonText }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <transition name="yab-modal-fade">
            <div v-if="banner.displayMethod === 'Fixed'">
                <?php require YAB_PLUGIN_DIR . 'admin/views/components/display-conditions.php'; ?>
            </div>
        </transition>

    </div>
</main>