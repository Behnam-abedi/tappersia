<?php require YAB_PLUGIN_DIR . 'admin/views/components/banner-editor-header.php'; ?>

<main class="grid grid-cols-12 gap-6 p-6 ltr">
    <div class="col-span-4 overflow-y-auto flex flex-col gap-6 [&>*:last-child]:mb-[40px] mr-2" style="max-height: calc(100vh - 120px);">
        <div class="bg-[#434343] p-5 rounded-lg shadow-xl">
            <h3 class="font-bold text-xl text-white tracking-wide mb-5">Banner Settings</h3>
            
            <div class="flex flex-col gap-5">
                
                <div>
                    <h4 class="section-title">Layout</h4>
                    <div class="flex items-center justify-between bg-[#292929] p-2 rounded-md mb-2">
                        <label class="setting-label-sm">Enable Custom Dimensions</label>
                        <label class="relative inline-flex items-center cursor-pointer" title="Toggle custom banner dimensions">
                            <input type="checkbox" v-model="banner.single.enableCustomDimensions" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-600 rounded-full peer peer-focus:ring-2 peer-focus:ring-blue-500 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div>
                        </label>
                    </div>
                    <div v-if="banner.single.enableCustomDimensions" class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="setting-label-sm">Width</label>
                            <div class="flex items-center gap-1">
                                <input type="number" v-model.number="banner.single.width" class="yab-form-input" placeholder="Width" title="Banner Width">
                                <select v-model="banner.single.widthUnit" class="yab-form-input w-20" title="Width Unit"><option>px</option><option>%</option></select>
                            </div>
                        </div>
                        <div>
                            <label class="setting-label-sm">Height</label>
                            <div class="flex items-center gap-1">
                                <input type="number" v-model.number="banner.single.height" class="yab-form-input" placeholder="Height" title="Banner Height">
                                <select v-model="banner.single.heightUnit" class="yab-form-input w-20" title="Height Unit"><option>px</option><option>%</option></select>
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
                            <input type="checkbox" v-model="banner.single.enableBorder" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-600 rounded-full peer peer-focus:ring-2 peer-focus:ring-blue-500 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div>
                        </label>
                    </div>
                    <div v-if="banner.single.enableBorder" class="grid grid-cols-3 gap-2">
                         <div>
                            <label class="setting-label-sm">Color</label>
                            <div class="yab-color-input-wrapper">
                                <input type="color" v-model="banner.single.borderColor" class="yab-color-picker" title="Border Color Picker">
                                 <input type="text" v-model="banner.single.borderColor" class="yab-hex-input" placeholder="Color" title="Border Color Hex Code">
                            </div>
                        </div>
                        <div>
                            <label class="setting-label-sm">Width (px)</label>
                            <input type="number" v-model.number="banner.single.borderWidth" class="yab-form-input" placeholder="e.g., 1" title="Border Width">
                        </div>
                        <div>
                            <label class="setting-label-sm">Radius (px)</label>
                            <input type="number" v-model.number="banner.single.borderRadius" class="yab-form-input" placeholder="e.g., 8" title="Border Radius">
                        </div>
                    </div>
                </div>

                 <hr class="section-divider">
                <div>
                     <h4 class="section-title">Content Padding (px)</h4>
                     <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="setting-label-sm">Top</label>
                            <input type="number" v-model.number="banner.single.paddingTop" class="yab-form-input" placeholder="Top" title="Top Padding">
                        </div>
                        <div>
                            <label class="setting-label-sm">Right</label>
                            <input type="number" v-model.number="banner.single.paddingRight" class="yab-form-input" placeholder="Right" title="Right Padding">
                        </div>
                        <div>
                             <label class="setting-label-sm">Bottom</label>
                            <input type="number" v-model.number="banner.single.paddingBottom" class="yab-form-input" placeholder="Bottom" title="Bottom Padding">
                        </div>
                        <div>
                            <label class="setting-label-sm">Left</label>
                            <input type="number" v-model.number="banner.single.paddingLeft" class="yab-form-input" placeholder="Left" title="Left Padding">
                        </div>
                     </div>
                </div>
                
                <hr class="section-divider">
                <div>
                    <h4 class="section-title">Background</h4>
                    <div class="flex gap-2 mb-2 bg-[#292929] rounded-lg border-none">
                        <button @click="banner.single.backgroundType = 'solid'" :class="{'active-tab': banner.single.backgroundType === 'solid'}" class="flex-1 tab-button rounded-l-lg border-none" title="Set solid color background">Solid Color</button>
                        <button @click="banner.single.backgroundType = 'gradient'" :class="{'active-tab': banner.single.backgroundType === 'gradient'}" class="flex-1 tab-button rounded-r-lg border-none" title="Set gradient background">Gradient</button>
                    </div>
                    <div v-if="banner.single.backgroundType === 'solid'" class="space-y-2">
                        <label class="setting-label-sm">Background Color</label>
                        <div class="yab-color-input-wrapper">
                            <input type="color" v-model="banner.single.bgColor" class="yab-color-picker" title="Background Color Picker">
                            <input type="text" v-model="banner.single.bgColor" class="yab-hex-input" placeholder="#hexcode" title="Background Color Hex Code">
                        </div>
                    </div>
                    <div v-else class="space-y-2">
                        <div>
                            <label class="setting-label-sm">Gradient Colors</label>
                            <div class="grid grid-cols-2 gap-2">
                                <div class="yab-color-input-wrapper">
                                    <input type="color" v-model="banner.single.gradientColor1" class="yab-color-picker" title="Gradient Start Color Picker">
                                    <input type="text" v-model="banner.single.gradientColor1" class="yab-hex-input" placeholder="#hexcode" title="Gradient Start Color Hex Code">
                                </div>
                                <div class="yab-color-input-wrapper">
                                    <input type="color" v-model="banner.single.gradientColor2" class="yab-color-picker" title="Gradient End Color Picker">
                                    <input type="text" v-model="banner.single.gradientColor2" class="yab-hex-input" placeholder="#hexcode" title="Gradient End Color Hex Code">
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="setting-label-sm">Gradient Angle</label>
                            <div class="flex items-center gap-2">
                                <input type="number" v-model.number="banner.single.gradientAngle" class="yab-form-input w-full" placeholder="e.g., 90" title="Gradient Angle">
                                <span class="text-sm text-gray-400">deg</span>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="section-divider">
                <div>
                    <h4 class="section-title">Image</h4>
                    <div class="flex gap-2 items-center">
                        <button @click="openMediaUploader('single')" class="flex-1 bg-blue-600 text-white px-3 py-1.5 rounded-md hover:bg-blue-700 text-sm" title="Open WordPress Media Library to select an image">
                            {{ banner.single.imageUrl ? 'Change Image' : 'Select Image' }}
                        </button>
                        <button v-if="banner.single.imageUrl" @click="removeImage('single')" class="bg-red-600 text-white px-3 py-1.5 rounded-md hover:bg-red-700 text-sm" title="Remove selected image">
                            Remove
                        </button>
                    </div>
                    <div v-if="banner.single.imageUrl" class="mt-3 space-y-3">
                        <div v-if="!banner.single.enableCustomImageSize" class="flex items-center gap-2">
                            <label class="setting-label-sm w-20">Image Fit:</label>
                            <select v-model="banner.single.imageFit" class="yab-form-input flex-1" title="Select how the image should fit within its container">
                                <option value="cover">Cover</option>
                                <option value="contain">Contain</option>
                                <option value="fill">Fill</option>
                                <option value="none">None (Natural Size)</option>
                            </select>
                        </div>
                        <div class="flex items-center justify-between bg-[#292929] p-2 rounded-md">
                            <label class="setting-label-sm">Enable Custom Image Size</label>
                            <label class="relative inline-flex items-center cursor-pointer" title="Toggle custom image dimensions">
                                <input type="checkbox" v-model="banner.single.enableCustomImageSize" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-600 rounded-full peer peer-focus:ring-2 peer-focus:ring-blue-500 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div>
                            </label>
                        </div>
                         <div v-if="banner.single.enableCustomImageSize" class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="setting-label-sm">Width (px)</label>
                                <input type="number" v-model.number="banner.single.imageWidth" class="yab-form-input" placeholder="Width" title="Image Width">
                            </div>
                            <div>
                                <label class="setting-label-sm">Height (px)</label>
                                <input type="number" v-model.number="banner.single.imageHeight" class="yab-form-input" placeholder="Height" title="Image Height">
                            </div>
                        </div>
                         <div class="grid grid-cols-2 gap-2 mt-2">
                             <div>
                                <label class="setting-label-sm">Right (px)</label>
                                <input type="number" v-model.number="banner.single.imagePosRight" class="yab-form-input" placeholder="Right" title="Image Position from Right">
                             </div>
                             <div>
                                <label class="setting-label-sm">Bottom (px)</label>
                                <input type="number" v-model.number="banner.single.imagePosBottom" class="yab-form-input" placeholder="Bottom" title="Image Position from Bottom">
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="section-divider">
                <div>
                    <h4 class="section-title">Content Alignment</h4>
                    <div class="flex rounded-lg bg-[#292929] overflow-hidden">
                        <button @click="banner.single.alignment = 'left'" :class="banner.single.alignment === 'left' ? 'active-tab' : ''" class="flex-1 tab-button rounded-l-lg" title="Align content to the left">Left</button>
                        <button @click="banner.single.alignment = 'center'" :class="banner.single.alignment === 'center' ? 'active-tab' : ''" class="flex-1 tab-button" title="Align content to the center">Center</button>
                        <button @click="banner.single.alignment = 'right'" :class="banner.single.alignment === 'right' ? 'active-tab' : ''" class="flex-1 tab-button rounded-r-lg" title="Align content to the right">Right</button>
                    </div>
                </div>
                <hr class="section-divider">
                <div>
                    <h4 class="section-title">Spacing (px)</h4>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="setting-label-sm">Title to Description</label>
                            <input type="number" v-model.number="banner.single.marginTopDescription" class="yab-form-input" placeholder="e.g., 8" title="Space between title and description">
                        </div>
                        <div>
                            <label class="setting-label-sm">Description to Button</label>
                            <input type="number" v-model.number="banner.single.marginBottomDescription" class="yab-form-input" placeholder="e.g., 24" title="Space between description and button">
                        </div>
                    </div>
                </div>
                <hr class="section-divider">
                <div class="space-y-2">
                    <h4 class="section-title">Title</h4>
                    <label class="setting-label-sm">Title Text</label>
                    <input type="text" v-model="banner.single.titleText" class="yab-form-input mb-2" placeholder="Title Text" title="Banner Title Text">
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="setting-label-sm">Color</label>
                            <div class="yab-color-input-wrapper">
                                <input type="color" v-model="banner.single.titleColor" class="yab-color-picker" title="Title Color Picker">
                                <input type="text" v-model="banner.single.titleColor" class="yab-hex-input" placeholder="#hexcode" title="Title Color Hex Code">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="setting-label-sm">Size (px)</label>
                                <input type="number" v-model.number="banner.single.titleSize" class="yab-form-input" placeholder="Size" title="Title Font Size">
                            </div>
                            <div>
                                <label class="setting-label-sm">Weight</label>
                                <select v-model="banner.single.titleWeight" class="yab-form-input" title="Title Font Weight">
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
                    <textarea v-model="banner.single.descText" rows="3" class="yab-form-input mb-2" placeholder="Description Text" title="Banner Description Text"></textarea>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                             <label class="setting-label-sm">Color</label>
                             <div class="yab-color-input-wrapper">
                                <input type="color" v-model="banner.single.descColor" class="yab-color-picker" title="Description Color Picker">
                                <input type="text" v-model="banner.single.descColor" class="yab-hex-input" placeholder="#hexcode" title="Description Color Hex Code">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="setting-label-sm">Size (px)</label>
                                <input type="number" v-model.number="banner.single.descSize" class="yab-form-input" placeholder="Size" title="Description Font Size">
                            </div>
                            <div>
                                <label class="setting-label-sm">Weight</label>
                                <select v-model="banner.single.descWeight" class="yab-form-input" title="Description Font Weight">
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
                    <input type="text" v-model="banner.single.buttonText" class="yab-form-input mb-2" placeholder="Button Text" title="Button Text">
                    <label class="setting-label-sm">Button Link (URL)</label>
                    <input type="text" v-model="banner.single.buttonLink" class="yab-form-input mb-2" placeholder="https://example.com" title="Button Link URL">
                    <div class="grid grid-cols-2 gap-2 mb-2">
                        <div>
                            <label class="setting-label-sm">Background Color</label>
                            <div class="yab-color-input-wrapper">
                                <input type="color" v-model="banner.single.buttonBgColor" class="yab-color-picker" title="Button Background Color Picker">
                                <input type="text" v-model="banner.single.buttonBgColor" class="yab-hex-input" placeholder="BG #hex" title="Button Background Color Hex Code">
                            </div>
                        </div>
                        <div>
                            <label class="setting-label-sm">Text Color</label>
                            <div class="yab-color-input-wrapper">
                                <input type="color" v-model="banner.single.buttonTextColor" class="yab-color-picker" title="Button Text Color Picker">
                                <input type="text" v-model="banner.single.buttonTextColor" class="yab-hex-input" placeholder="Text #hex" title="Button Text Color Hex Code">
                            </div>
                        </div>
                    </div>
                     <div class="grid grid-cols-2 gap-2">
                        <div>
                           <label class="setting-label-sm">Hover BG Color</label>
                           <div class="yab-color-input-wrapper">
                                <input type="color" v-model="banner.single.buttonBgHoverColor" class="yab-color-picker" title="Button Hover Background Color Picker">
                                <input type="text" v-model="banner.single.buttonBgHoverColor" class="yab-hex-input" placeholder="Hover #hex" title="Button Hover Background Color Hex Code">
                            </div>
                        </div>
                        <div>
                            <label class="setting-label-sm">Font Size (px)</label>
                            <input type="number" v-model.number="banner.single.buttonFontSize" class="yab-form-input" placeholder="Font Size" title="Button Font Size">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2 mt-2">
                        <div>
                            <label class="setting-label-sm">Width</label>
                            <div class="flex items-center gap-1">
                                <input type="number" v-model.number="banner.single.buttonWidth" class="yab-form-input" placeholder="Width" title="Button Width">
                                <select v-model="banner.single.buttonWidthUnit" class="yab-form-input w-20" title="Button Width Unit"><option>px</option><option>%</option></select>
                            </div>
                        </div>
                        <div>
                            <label class="setting-label-sm">Height</label>
                            <div class="flex items-center gap-1">
                                 <input type="number" v-model.number="banner.single.buttonHeight" class="yab-form-input" placeholder="Height" title="Button Height">
                                <select v-model="banner.single.buttonHeightUnit" class="yab-form-input w-20" title="Button Height Unit"><option>px</option><option>%</option></select>
                            </div>
                        </div>
                         <div>
                            <label class="setting-label-sm">Min-Width</label>
                            <div class="flex items-center gap-1">
                                 <input type="number" v-model.number="banner.single.buttonMinWidth" class="yab-form-input" placeholder="Min-Width" title="Button Minimum Width">
                                <select v-model="banner.single.buttonMinWidthUnit" class="yab-form-input w-20" title="Button Minimum Width Unit"><option>px</option><option>%</option></select>
                            </div>
                        </div>
                        <div>
                             <label class="setting-label-sm">Border Radius (px)</label>
                             <input type="number" v-model.number="banner.single.buttonBorderRadius" class="yab-form-input" placeholder="e.g., 4" title="Button Border Radius">
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
                <div class="relative overflow-hidden flex flex-shrink-0" 
                    :style="{ 
                        background: bannerStyles(banner.single), 
                        width: banner.single.enableCustomDimensions ? `${banner.single.width}${banner.single.widthUnit}` : '886px', 
                        height: banner.single.enableCustomDimensions ? `${banner.single.height}${banner.single.heightUnit}` : '178px',
                        border: banner.single.enableBorder ? `${banner.single.borderWidth}px solid ${banner.single.borderColor}` : 'none',
                        borderRadius: `${banner.single.borderRadius}px`
                    }">
                    
                    <img v-if="banner.single.imageUrl" :src="banner.single.imageUrl" :style="imageStyleObject(banner.single)" />

                    <div class="w-full h-full flex flex-col z-10 relative" 
                        :style="{ 
                            alignItems: contentAlignment(banner.single.alignment), 
                            textAlign: banner.single.alignment,
                            padding: `${banner.single.paddingTop}px ${banner.single.paddingRight}px ${banner.single.paddingBottom}px ${banner.single.paddingLeft}px`
                        }">
                        <h4 :style="{ color: banner.single.titleColor, fontSize: banner.single.titleSize + 'px', fontWeight: banner.single.titleWeight, margin: 0 }">{{ banner.single.titleText }}</h4>
                        <p :style="{ 
                            color: banner.single.descColor, 
                            fontSize: banner.single.descSize + 'px', 
                            fontWeight: banner.single.descWeight, 
                            whiteSpace: 'pre-wrap',
                            marginTop: `${banner.single.marginTopDescription}px`,
                            marginBottom: `${banner.single.marginBottomDescription}px`
                        }">{{ banner.single.descText }}</p>
                        <a v-if="banner.single.buttonText" :href="banner.single.buttonLink" target="_blank" 
                            class="mt-auto" 
                            :style="{ 
                                backgroundColor: banner.single.buttonBgColor, 
                                color: banner.single.buttonTextColor, 
                                fontSize: banner.single.buttonFontSize + 'px', 
                                alignSelf: banner.single.alignment === 'center' ? 'center' : (banner.single.alignment === 'right' ? 'flex-end' : 'flex-start'),
                                width: banner.single.buttonWidth ? `${banner.single.buttonWidth}${banner.single.buttonWidthUnit}` : 'auto',
                                height: banner.single.buttonHeight ? `${banner.single.buttonHeight}${banner.single.buttonHeightUnit}` : 'auto',
                                minWidth: banner.single.buttonMinWidth ? `${banner.single.buttonMinWidth}${banner.single.buttonMinWidthUnit}` : 'auto',
                                borderRadius: `${banner.single.buttonBorderRadius}px`,
                                display: 'inline-flex',
                                alignItems: 'center',
                                justifyContent: 'center',
                                textDecoration: 'none',
                                padding: '8px 16px' // Default padding for button
                            }">
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