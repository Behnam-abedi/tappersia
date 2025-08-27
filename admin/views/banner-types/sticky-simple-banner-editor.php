<?php require YAB_PLUGIN_DIR . 'admin/views/components/banner-editor-header.php'; ?>

<main class="grid grid-cols-12 gap-6 p-6 ltr">
    <div class="col-span-4 overflow-y-auto flex flex-col gap-6 [&>*:last-child]:mb-[40px]" style="max-height: calc(100vh - 120px);">
        <div class="bg-[#434343] p-5 rounded-lg shadow-xl">
            <h3 class="font-bold text-xl text-white tracking-wide mb-5">Banner Settings</h3>
            
            <div class="flex flex-col gap-5">
                <div>
                    <h4 class="section-title">Background</h4>
                    <div class="flex gap-2 mb-2 bg-[#292929] rounded-lg border-none">
                        <button @click="banner.sticky_simple.backgroundType = 'solid'" :class="{'active-tab': banner.sticky_simple.backgroundType === 'solid'}" class="flex-1 tab-button rounded-l-lg border-none">Solid Color</button>
                        <button @click="banner.sticky_simple.backgroundType = 'gradient'" :class="{'active-tab': banner.sticky_simple.backgroundType === 'gradient'}" class="flex-1 tab-button rounded-r-lg border-none">Gradient</button>
                    </div>
                    <div v-if="banner.sticky_simple.backgroundType === 'solid'" class="space-y-2">
                        <label class="setting-label-sm">Background Color</label>
                        <div class="yab-color-input-wrapper">
                            <input type="color" v-model="banner.sticky_simple.bgColor" class="yab-color-picker">
                            <input type="text" v-model="banner.sticky_simple.bgColor" class="yab-hex-input" placeholder="#hexcode">
                        </div>
                    </div>
                    <div v-else class="space-y-2">
                        <div>
                            <label class="setting-label-sm">Gradient Colors</label>
                            <div class="grid grid-cols-2 gap-2">
                                <div class="yab-color-input-wrapper">
                                    <input type="color" v-model="banner.sticky_simple.gradientColor1" class="yab-color-picker">
                                    <input type="text" v-model="banner.sticky_simple.gradientColor1" class="yab-hex-input" placeholder="#hexcode">
                                </div>
                                <div class="yab-color-input-wrapper">
                                    <input type="color" v-model="banner.sticky_simple.gradientColor2" class="yab-color-picker">
                                    <input type="text" v-model="banner.sticky_simple.gradientColor2" class="yab-hex-input" placeholder="#hexcode">
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="setting-label-sm">Gradient Angle</label>
                            <div class="flex items-center gap-2">
                                <input type="number" v-model.number="banner.sticky_simple.gradientAngle" class="yab-form-input w-full" placeholder="e.g., 90">
                                <span class="text-sm text-gray-400">deg</span>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="section-divider">
                <div>
                    <h4 class="section-title">Layout</h4>
                     <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="setting-label-sm">Height (px)</label>
                            <input type="number" v-model.number="banner.sticky_simple.height" class="yab-form-input" placeholder="e.g., 74">
                         </div>
                         <div>
                            <label class="setting-label-sm">Border Radius (px)</label>
                            <input type="number" v-model.number="banner.sticky_simple.borderRadius" class="yab-form-input" placeholder="e.g., 10">
                        </div>
                        <div>
                            <label class="setting-label-sm">Padding Top/Bottom (px)</label>
                            <input type="number" v-model.number="banner.sticky_simple.paddingY" class="yab-form-input" placeholder="e.g., 26">
                         </div>
                         <div>
                            <label class="setting-label-sm">Padding Left/Right (px)</label>
                            <input type="number" v-model.number="banner.sticky_simple.paddingX" class="yab-form-input" placeholder="e.g., 40">
                        </div>
                    </div>
                     <div class="mt-4">
                        <label class="setting-label-sm">Content Direction</label>
                        <div class="flex rounded-lg bg-[#292929] overflow-hidden">
                            <button @click="banner.sticky_simple.direction = 'ltr'" :class="banner.sticky_simple.direction === 'ltr' ? 'active-tab' : ''" class="flex-1 tab-button rounded-l-lg">Left</button>
                            <button @click="banner.sticky_simple.direction = 'rtl'" :class="banner.sticky_simple.direction === 'rtl' ? 'active-tab' : ''" class="flex-1 tab-button rounded-r-lg">Right</button>
                        </div>
                    </div>
                </div>
                <hr class="section-divider">
                <div class="space-y-2">
                    <h4 class="section-title">Text</h4>
                    <label class="setting-label-sm">Text</label>
                    <input type="text" v-model="banner.sticky_simple.text" class="yab-form-input mb-2" placeholder="Banner Text">
                    <div class="grid grid-cols-3 gap-2">
                         <div class="col-span-1">
                            <label class="setting-label-sm">Color</label>
                            <div class="yab-color-input-wrapper">
                                <input type="color" v-model="banner.sticky_simple.textColor" class="yab-color-picker">
                                <input type="text" v-model="banner.sticky_simple.textColor" class="yab-hex-input" placeholder="#hexcode">
                            </div>
                        </div>
                        <div>
                            <label class="setting-label-sm">Font Size (px)</label>
                            <input type="number" v-model.number="banner.sticky_simple.textSize" class="yab-form-input" placeholder="e.g., 17">
                        </div>
                        <div>
                            <label class="setting-label-sm">Font Weight</label>
                            <select v-model="banner.sticky_simple.textWeight" class="yab-form-input">
                                <option value="400">Normal</option><option value="500">Medium</option><option value="600">Semi-Bold</option><option value="700">Bold</option><option value="800">Extra Bold</option>
                            </select>
                        </div>
                    </div>
                </div>
                <hr class="section-divider">
                <div class="space-y-2">
                    <h4 class="section-title">Button</h4>
                    <label class="setting-label-sm">Button Text</label>
                    <input type="text" v-model="banner.sticky_simple.buttonText" class="yab-form-input mb-2" placeholder="Button Text">
                    <label class="setting-label-sm">Button Link (URL)</label>
                    <input type="text" v-model="banner.sticky_simple.buttonLink" class="yab-form-input mb-4" placeholder="https://example.com">
                    
                    <div class="grid grid-cols-2 gap-2 mb-2">
                        <div>
                            <label class="setting-label-sm">Background Color</label>
                            <div class="yab-color-input-wrapper">
                                <input type="color" v-model="banner.sticky_simple.buttonBgColor" class="yab-color-picker">
                                <input type="text" v-model="banner.sticky_simple.buttonBgColor" class="yab-hex-input" placeholder="#1EC2AF">
                            </div>
                        </div>
                        <div>
                            <label class="setting-label-sm">Text Color</label>
                            <div class="yab-color-input-wrapper">
                                <input type="color" v-model="banner.sticky_simple.buttonTextColor" class="yab-color-picker">
                                <input type="text" v-model="banner.sticky_simple.buttonTextColor" class="yab-hex-input" placeholder="#FFFFFF">
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                           <label class="setting-label-sm">Border Radius (px)</label>
                           <input type="number" v-model.number="banner.sticky_simple.buttonBorderRadius" class="yab-form-input" placeholder="e.g., 3">
                        </div>
                        <div>
                            <label class="setting-label-sm">Font Size (px)</label>
                            <input type="number" v-model.number="banner.sticky_simple.buttonFontSize" class="yab-form-input" placeholder="e.g., 8">
                        </div>
                        <div>
                            <label class="setting-label-sm">Font Weight</label>
                             <select v-model="banner.sticky_simple.buttonFontWeight" class="yab-form-input">
                                <option value="400">Normal</option><option value="500">Medium</option><option value="600">Semi-Bold</option><option value="700">Bold</option>
                            </select>
                        </div>
                         <div>
                            <label class="setting-label-sm">Min Width (px)</label>
                            <input type="number" v-model.number="banner.sticky_simple.buttonMinWidth" class="yab-form-input" placeholder="e.g., 72">
                        </div>
                        <div>
                            <label class="setting-label-sm">Padding Top/Bottom (px)</label>
                            <input type="number" v-model.number="banner.sticky_simple.buttonPaddingY" class="yab-form-input" placeholder="e.g., 7">
                         </div>
                         <div>
                            <label class="setting-label-sm">Padding Left/Right (px)</label>
                            <input type="number" v-model.number="banner.sticky_simple.buttonPaddingX" class="yab-form-input" placeholder="e.g., 15">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-span-8 sticky top-[120px] space-y-4">
        <div class="bg-[#434343] p-4 rounded-lg">
            <h3 class="preview-title">Live Preview</h3>
            <div class="flex justify-center items-center h-auto bg-[#292929] rounded-lg p-4">
                 <div class="yab-simple-banner-wrapper" 
                     :style="{ 
                        width: '100%', 
                        height: banner.sticky_simple.height + 'px', 
                        minHeight: banner.sticky_simple.height + 'px',
                        borderRadius: banner.sticky_simple.borderRadius + 'px', 
                        background: bannerStyles(banner.sticky_simple),
                        padding: banner.sticky_simple.paddingY + 'px ' + banner.sticky_simple.paddingX + 'px',
                        display: 'flex',
                        alignItems: 'center',
                        justifyContent: 'space-between',
                        boxSizing: 'border-box',
                        direction: banner.sticky_simple.direction,
                        flexDirection: banner.sticky_simple.direction === 'rtl' ? 'row-reverse' : 'row'
                     }">
                    <span :style="{ 
                        fontSize: banner.sticky_simple.textSize + 'px',
                        fontWeight: banner.sticky_simple.textWeight,
                        color: banner.sticky_simple.textColor,
                        order: banner.sticky_simple.direction === 'rtl' ? 1 : 0
                    }">
                        {{ banner.sticky_simple.text }}
                    </span>
                    <a :href="banner.sticky_simple.buttonLink" 
                       target="_blank" 
                       :style="{ 
                          backgroundColor: banner.sticky_simple.buttonBgColor,
                          borderRadius: banner.sticky_simple.buttonBorderRadius + 'px',
                          color: banner.sticky_simple.buttonTextColor,
                          fontSize: banner.sticky_simple.buttonFontSize + 'px',
                          fontWeight: banner.sticky_simple.buttonFontWeight,
                          padding: banner.sticky_simple.buttonPaddingY + 'px ' + banner.sticky_simple.buttonPaddingX + 'px',
                          minWidth: banner.sticky_simple.buttonMinWidth + 'px',
                          textDecoration: 'none',
                          textAlign: 'center',
                          boxSizing: 'border-box',
                          order: banner.sticky_simple.direction === 'rtl' ? 0 : 1
                       }">
                        {{ banner.sticky_simple.buttonText }}
                    </a>
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