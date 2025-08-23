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
    <div class="col-span-4 overflow-y-auto flex flex-col gap-6 [&>*:last-child]:mb-[40px]" style="max-height: calc(100vh - 120px);">
        <div class="bg-[#434343] p-5 rounded-lg shadow-xl">
            <h3 class="font-bold text-xl text-white tracking-wide mb-5">Banner Settings</h3>
            
            <div class="flex flex-col gap-5">
                <div>
                    <h4 class="section-title">Background</h4>
                    <div class="flex gap-2 mb-2 bg-[#292929] rounded-lg border-none">
                        <button @click="banner.simple.backgroundType = 'solid'" :class="{'active-tab': banner.simple.backgroundType === 'solid'}" class="flex-1 tab-button rounded-l-lg border-none">Solid Color</button>
                        <button @click="banner.simple.backgroundType = 'gradient'" :class="{'active-tab': banner.simple.backgroundType === 'gradient'}" class="flex-1 tab-button rounded-r-lg border-none">Gradient</button>
                    </div>
                    <div v-if="banner.simple.backgroundType === 'solid'" class="space-y-2">
                        <label class="setting-label-sm">Background Color</label>
                        <div class="yab-color-input-wrapper">
                            <input type="color" v-model="banner.simple.bgColor" class="yab-color-picker">
                            <input type="text" v-model="banner.simple.bgColor" class="yab-hex-input" placeholder="#hexcode">
                        </div>
                    </div>
                    <div v-else class="space-y-2">
                        <div>
                            <label class="setting-label-sm">Gradient Colors</label>
                            <div class="grid grid-cols-2 gap-2">
                                <div class="yab-color-input-wrapper">
                                    <input type="color" v-model="banner.simple.gradientColor1" class="yab-color-picker">
                                    <input type="text" v-model="banner.simple.gradientColor1" class="yab-hex-input" placeholder="#hexcode">
                                </div>
                                <div class="yab-color-input-wrapper">
                                    <input type="color" v-model="banner.simple.gradientColor2" class="yab-color-picker">
                                    <input type="text" v-model="banner.simple.gradientColor2" class="yab-hex-input" placeholder="#hexcode">
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="setting-label-sm">Gradient Angle</label>
                            <div class="flex items-center gap-2">
                                <input type="number" v-model.number="banner.simple.gradientAngle" class="yab-form-input w-full" placeholder="e.g., 90">
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
                            <input type="number" v-model.number="banner.simple.height" class="yab-form-input" placeholder="e.g., 74">
                         </div>
                         <div>
                            <label class="setting-label-sm">Border Radius (px)</label>
                            <input type="number" v-model.number="banner.simple.borderRadius" class="yab-form-input" placeholder="e.g., 10">
                        </div>
                        <div>
                            <label class="setting-label-sm">Padding Top/Bottom (px)</label>
                            <input type="number" v-model.number="banner.simple.paddingY" class="yab-form-input" placeholder="e.g., 26">
                         </div>
                         <div>
                            <label class="setting-label-sm">Padding Left/Right (px)</label>
                            <input type="number" v-model.number="banner.simple.paddingX" class="yab-form-input" placeholder="e.g., 40">
                        </div>
                    </div>
                     <div class="mt-4">
                        <label class="setting-label-sm">Content Direction</label>
                        <div class="flex rounded-lg bg-[#292929] overflow-hidden">
                            <button @click="banner.simple.direction = 'ltr'" :class="banner.simple.direction === 'ltr' ? 'active-tab' : ''" class="flex-1 tab-button rounded-l-lg">Left to Right</button>
                            <button @click="banner.simple.direction = 'rtl'" :class="banner.simple.direction === 'rtl' ? 'active-tab' : ''" class="flex-1 tab-button rounded-r-lg">Right to Left</button>
                        </div>
                    </div>
                </div>
                <hr class="section-divider">
                <div class="space-y-2">
                    <h4 class="section-title">Text</h4>
                    <label class="setting-label-sm">Text</label>
                    <input type="text" v-model="banner.simple.text" class="yab-form-input mb-2" placeholder="Banner Text">
                    <div class="grid grid-cols-3 gap-2">
                         <div class="col-span-1">
                            <label class="setting-label-sm">Color</label>
                            <div class="yab-color-input-wrapper">
                                <input type="color" v-model="banner.simple.textColor" class="yab-color-picker">
                                <input type="text" v-model="banner.simple.textColor" class="yab-hex-input" placeholder="#hexcode">
                            </div>
                        </div>
                        <div>
                            <label class="setting-label-sm">Font Size (px)</label>
                            <input type="number" v-model.number="banner.simple.textSize" class="yab-form-input" placeholder="e.g., 17">
                        </div>
                        <div>
                            <label class="setting-label-sm">Font Weight</label>
                            <select v-model="banner.simple.textWeight" class="yab-form-input">
                                <option value="400">Normal</option><option value="500">Medium</option><option value="600">Semi-Bold</option><option value="700">Bold</option><option value="800">Extra Bold</option>
                            </select>
                        </div>
                    </div>
                </div>
                <hr class="section-divider">
                <div class="space-y-2">
                    <h4 class="section-title">Button</h4>
                    <label class="setting-label-sm">Button Text</label>
                    <input type="text" v-model="banner.simple.buttonText" class="yab-form-input mb-2" placeholder="Button Text">
                    <label class="setting-label-sm">Button Link (URL)</label>
                    <input type="text" v-model="banner.simple.buttonLink" class="yab-form-input mb-4" placeholder="https://example.com">
                    
                    <div class="grid grid-cols-2 gap-2 mb-2">
                        <div>
                            <label class="setting-label-sm">Background Color</label>
                            <div class="yab-color-input-wrapper">
                                <input type="color" v-model="banner.simple.buttonBgColor" class="yab-color-picker">
                                <input type="text" v-model="banner.simple.buttonBgColor" class="yab-hex-input" placeholder="#1EC2AF">
                            </div>
                        </div>
                        <div>
                            <label class="setting-label-sm">Text Color</label>
                            <div class="yab-color-input-wrapper">
                                <input type="color" v-model="banner.simple.buttonTextColor" class="yab-color-picker">
                                <input type="text" v-model="banner.simple.buttonTextColor" class="yab-hex-input" placeholder="#FFFFFF">
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                           <label class="setting-label-sm">Border Radius (px)</label>
                           <input type="number" v-model.number="banner.simple.buttonBorderRadius" class="yab-form-input" placeholder="e.g., 3">
                        </div>
                        <div>
                            <label class="setting-label-sm">Font Size (px)</label>
                            <input type="number" v-model.number="banner.simple.buttonFontSize" class="yab-form-input" placeholder="e.g., 8">
                        </div>
                        <div>
                            <label class="setting-label-sm">Font Weight</label>
                             <select v-model="banner.simple.buttonFontWeight" class="yab-form-input">
                                <option value="400">Normal</option><option value="500">Medium</option><option value="600">Semi-Bold</option><option value="700">Bold</option>
                            </select>
                        </div>
                         <div>
                            <label class="setting-label-sm">Min Width (px)</label>
                            <input type="number" v-model.number="banner.simple.buttonMinWidth" class="yab-form-input" placeholder="e.g., 72">
                        </div>
                        <div>
                            <label class="setting-label-sm">Padding Top/Bottom (px)</label>
                            <input type="number" v-model.number="banner.simple.buttonPaddingY" class="yab-form-input" placeholder="e.g., 7">
                         </div>
                         <div>
                            <label class="setting-label-sm">Padding Left/Right (px)</label>
                            <input type="number" v-model.number="banner.simple.buttonPaddingX" class="yab-form-input" placeholder="e.g., 15">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-span-8 sticky top-[120px] space-y-4">
        <div class="bg-[#434343] p-4 rounded-lg">
            <h3 class="preview-title">Live Preview</h3>
            <div class="flex justify-center items-center h-48 bg-[#292929] rounded-lg p-4">
                 <div class="yab-simple-banner-wrapper" 
                     :style="{ 
                        width: '100%', 
                        height: banner.simple.height + 'px', 
                        minHeight: banner.simple.height + 'px',
                        borderRadius: banner.simple.borderRadius + 'px', 
                        background: bannerStyles(banner.simple),
                        padding: banner.simple.paddingY + 'px ' + banner.simple.paddingX + 'px',
                        display: 'flex',
                        alignItems: 'center',
                        justifyContent: 'space-between',
                        boxSizing: 'border-box',
                        direction: banner.simple.direction
                     }">
                    <span :style="{ 
                        fontSize: banner.simple.textSize + 'px',
                        fontWeight: banner.simple.textWeight,
                        color: banner.simple.textColor,
                        order: banner.simple.direction === 'rtl' ? 1 : 0
                    }">
                        {{ banner.simple.text }}
                    </span>
                    <a :href="banner.simple.buttonLink" 
                       target="_blank" 
                       :style="{ 
                          backgroundColor: banner.simple.buttonBgColor,
                          borderRadius: banner.simple.buttonBorderRadius + 'px',
                          color: banner.simple.buttonTextColor,
                          fontSize: banner.simple.buttonFontSize + 'px',
                          fontWeight: banner.simple.buttonFontWeight,
                          padding: banner.simple.buttonPaddingY + 'px ' + banner.simple.buttonPaddingX + 'px',
                          minWidth: banner.simple.buttonMinWidth + 'px',
                          textDecoration: 'none',
                          textAlign: 'center',
                          boxSizing: 'border-box',
                          order: banner.simple.direction === 'rtl' ? 0 : 1
                       }">
                        {{ banner.simple.buttonText }}
                    </a>
                </div>
            </div>
        </div>
        
        <div v-if="banner.displayMethod === 'Fixed'">
            <?php require_once YAB_PLUGIN_DIR . 'admin/views/components/display-conditions.php'; ?>
        </div>

    </div>
</main>