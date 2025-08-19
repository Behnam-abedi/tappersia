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
            <h3 class="font-bold text-xl text-white tracking-wide mb-5">Content Source</h3>
            <div class="flex flex-col gap-5 text-left">
                <div>
                    <div class="flex gap-4">
                        <button @click="openHotelModal" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 flex items-center gap-2">
                            <span class="dashicons dashicons-building"></span>
                            {{ banner.api.selectedHotel ? 'Change Hotel' : 'Select Hotel' }}
                        </button>
                        <button class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700 flex items-center gap-2" disabled>
                            <span class="dashicons dashicons-palmtree"></span>
                            Select Tour (Coming Soon)
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="bg-[#434343] p-5 rounded-lg shadow-xl">
            <h3 class="font-bold text-xl text-white tracking-wide mb-5">Banner Settings</h3>
             <div class="flex flex-col gap-5">
                <div>
                    <h4 class="section-title">Layout</h4>
                    <div class="flex rounded-lg bg-[#292929] overflow-hidden">
                        <button @click="banner.api.design.layout = 'left'" :class="banner.api.design.layout === 'left' ? 'active-tab' : ''" class="flex-1 tab-button">Image Left</button>
                        <button @click="banner.api.design.layout = 'right'" :class="banner.api.design.layout === 'right' ? 'active-tab' : ''" class="flex-1 tab-button">Image Right</button>
                    </div>
                </div>
                <hr class="section-divider">
                 <div>
                    <h4 class="section-title">Appearance</h4>
                    <div class="flex items-center gap-2 mb-2">
                        <label class="text-sm text-gray-300 w-24">Background</label>
                        <input type="color" v-model="banner.api.design.bgColor" class="yab-color-picker">
                        <input type="text" v-model="banner.api.design.bgColor" class="flex-1 text-input" placeholder="#hexcode">
                    </div>
                    <div class="flex items-center justify-between bg-[#292929] p-2 rounded-md mb-2">
                        <label class="text-sm text-gray-300">Enable Border</label>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" v-model="banner.api.design.enableBorder" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-600 rounded-full peer peer-focus:ring-2 peer-focus:ring-blue-500 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div>
                        </label>
                    </div>
                    <div v-if="banner.api.design.enableBorder">
                        <div class="flex items-center gap-2 mb-2">
                            <label class="text-sm text-gray-300 w-24">Border Color</label>
                            <input type="color" v-model="banner.api.design.borderColor" class="yab-color-picker">
                            <input type="text" v-model="banner.api.design.borderColor" class="flex-1 text-input" placeholder="#hexcode">
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div class="flex items-center gap-2">
                                <label class="text-sm text-gray-300">Width</label>
                                <input type="number" v-model.number="banner.api.design.borderWidth" class="text-input" placeholder="Width (px)">
                            </div>
                            <div class="flex items-center gap-2">
                                <label class="text-sm text-gray-300">Radius</label>
                                <input type="number" v-model.number="banner.api.design.borderRadius" class="text-input" placeholder="Radius (px)">
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="section-divider">
                <div>
                    <h4 class="section-title">Content Padding</h4>
                    <div class="flex items-center justify-between bg-[#292929] p-2 rounded-md mb-2">
                        <label class="text-sm text-gray-300">Enable Custom Padding</label>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" v-model="banner.api.design.enableCustomPadding" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-600 rounded-full peer peer-focus:ring-2 peer-focus:ring-blue-500 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div>
                        </label>
                    </div>
                    <div v-if="banner.api.design.enableCustomPadding" class="grid grid-cols-2 gap-2">
                        <input type="number" v-model.number="banner.api.design.paddingTop" class="text-input" placeholder="Top (px)">
                        <input type="number" v-model.number="banner.api.design.paddingBottom" class="text-input" placeholder="Bottom (px)">
                        <input type="number" v-model.number="banner.api.design.paddingLeft" class="text-input" placeholder="Left (px)">
                        <input type="number" v-model.number="banner.api.design.paddingRight" class="text-input" placeholder="Right (px)">
                    </div>
                </div>
                <hr class="section-divider">
                <div>
                    <h4 class="section-title">Title</h4>
                    <div class="grid grid-cols-3 gap-2">
                        <input type="color" v-model="banner.api.design.titleColor" class="yab-color-picker !w-full">
                        <input type="number" v-model.number="banner.api.design.titleSize" class="text-input" placeholder="Size (px)">
                        <select v-model="banner.api.design.titleWeight" class="select-input">
                            <option value="400">Normal</option><option value="500">Medium</option><option value="600">Semi-Bold</option><option value="700">Bold</option><option value="800">Extra Bold</option>
                        </select>
                    </div>
                </div>
                <hr class="section-divider">
                 <div>
                    <h4 class="section-title">Stars & City</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                             <label class="text-xs text-gray-400 block mb-1">Star Size (px)</label>
                             <input type="number" v-model.number="banner.api.design.starSize" class="text-input w-full">
                        </div>
                        <div>
                            <label class="text-xs text-gray-400 block mb-1">City Color</label>
                            <input type="color" v-model="banner.api.design.cityColor" class="yab-color-picker w-full h-10">
                        </div>
                         <div class="col-span-2">
                             <label class="text-xs text-gray-400 block mb-1">City Font Size (px)</label>
                             <input type="number" v-model.number="banner.api.design.citySize" class="text-input w-full">
                         </div>
                    </div>
                </div>
                <hr class="section-divider">
                <div>
                    <h4 class="section-title">Rating & Reviews</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                             <label class="text-xs text-gray-400 block mb-1">Rating Box BG</label>
                             <input type="color" v-model="banner.api.design.ratingBoxBgColor" class="yab-color-picker w-full h-10">
                        </div>
                         <div>
                             <label class="text-xs text-gray-400 block mb-1">Rating Box Text</label>
                             <input type="color" v-model="banner.api.design.ratingBoxColor" class="yab-color-picker w-full h-10">
                         </div>
                         <div class="col-span-2">
                              <label class="text-xs text-gray-400 block mb-1">Rating Box Font Size (px)</label>
                             <input type="number" v-model.number="banner.api.design.ratingBoxSize" class="text-input w-full">
                         </div>
                         <div>
                             <label class="text-xs text-gray-400 block mb-1">"Very Good" Color</label>
                             <input type="color" v-model="banner.api.design.ratingTextColor" class="yab-color-picker w-full h-10">
                         </div>
                          <div>
                              <label class="text-xs text-gray-400 block mb-1">"Very Good" Size (px)</label>
                             <input type="number" v-model.number="banner.api.design.ratingTextSize" class="text-input w-full">
                         </div>
                           <div>
                             <label class="text-xs text-gray-400 block mb-1">Review Color</label>
                             <input type="color" v-model="banner.api.design.reviewColor" class="yab-color-picker w-full h-10">
                         </div>
                          <div>
                              <label class="text-xs text-gray-400 block mb-1">Review Size (px)</label>
                             <input type="number" v-model.number="banner.api.design.reviewSize" class="text-input w-full">
                         </div>
                    </div>
                </div>
                <hr class="section-divider">
                <div>
                    <h4 class="section-title">Price Details</h4>
                    <div class="grid grid-cols-2 gap-4">
                         <div>
                             <label class="text-xs text-gray-400 block mb-1">"From" Color</label>
                             <input type="color" v-model="banner.api.design.priceFromColor" class="yab-color-picker w-full h-10">
                         </div>
                          <div>
                              <label class="text-xs text-gray-400 block mb-1">"From" Size (px)</label>
                             <input type="number" v-model.number="banner.api.design.priceFromSize" class="text-input w-full">
                         </div>
                         <div>
                             <label class="text-xs text-gray-400 block mb-1">Amount Color</label>
                             <input type="color" v-model="banner.api.design.priceAmountColor" class="yab-color-picker w-full h-10">
                         </div>
                          <div>
                              <label class="text-xs text-gray-400 block mb-1">Amount Size (px)</label>
                             <input type="number" v-model.number="banner.api.design.priceAmountSize" class="text-input w-full">
                         </div>
                         <div>
                             <label class="text-xs text-gray-400 block mb-1">"/ night" Color</label>
                             <input type="color" v-model="banner.api.design.priceNightColor" class="yab-color-picker w-full h-10">
                         </div>
                          <div>
                              <label class="text-xs text-gray-400 block mb-1">"/ night" Size (px)</label>
                             <input type="number" v-model.number="banner.api.design.priceNightSize" class="text-input w-full">
                         </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-span-8 sticky top-[120px] space-y-4">
        <div class="bg-[#434343] p-4 rounded-lg">
            <h3 class="preview-title">Live Preview</h3>
            <div v-if="!banner.api.selectedHotel" class="flex items-center justify-center h-72 bg-[#292929] rounded-lg">
                <p class="text-gray-500">Please select a hotel to see the preview.</p>
            </div>
             <div v-else-if="isHotelDetailsLoading" class="flex justify-center">
                <div class="yab-api-banner-wrapper shadow-lg flex items-stretch font-sans animate-pulse" style="width: 864px; height: 128px; background-color: #292929; border-radius: 15px;">
                    <div class="flex-shrink-0 bg-[#434343]" style="width: 360px; height: 100%;"></div>
                    <div class="flex-grow p-6 flex flex-col justify-between">
                        <div class="h-5 bg-[#434343] rounded w-3/4"></div>
                        <div class="flex items-center">
                            <div class="h-4 bg-[#434343] rounded w-1/4"></div>
                            <div class="h-4 bg-[#434343] rounded w-1/4 ml-4"></div>
                        </div>
                        <div class="flex justify-between items-center">
                             <div class="h-4 bg-[#434343] rounded w-1/2"></div>
                             <div class="h-6 bg-[#434343] rounded w-1/4"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else class="flex justify-center">
                <div 
                    class="yab-api-banner-wrapper shadow-lg flex items-stretch font-sans" 
                    :style="{ 
                        backgroundColor: banner.api.design.bgColor,
                        border: `${banner.api.design.enableBorder ? banner.api.design.borderWidth : 0}px solid ${banner.api.design.borderColor}`,
                        borderRadius: `${banner.api.design.borderRadius}px`,
                        width: '864px', 
                        height: '128px',
                        flexDirection: banner.api.design.layout === 'right' ? 'row-reverse' : 'row',
                        overflow: 'hidden'
                    }">
                    <div class="flex-shrink-0" style="width: 360px; height: 100%;">
                        <img :src="banner.api.selectedHotel.coverImage.url" :alt="banner.api.selectedHotel.title" class="w-full h-full object-cover block">
                    </div>

                    <div 
                        class="flex-grow flex flex-col relative"
                        :style="apiContentStyles"
                    >
                        <h3 class="font-bold leading-[18px]" :style="{ 
                            color: banner.api.design.titleColor, 
                            fontSize: banner.api.design.titleSize + 'px', 
                            fontWeight: banner.api.design.titleWeight,
                            margin: '0'
                        }">{{ banner.api.selectedHotel.title }}</h3>
                        
                        <div class="flex items-center mt-[9px]" :style="{ justifyContent: banner.api.design.layout === 'right' ? 'flex-end' : 'flex-start' }">
                            <div class="text-yellow-400 flex items-center">
                                <span v-for="n in 5" :key="n" :style="{ fontSize: banner.api.design.starSize + 'px', width: banner.api.design.starSize + 'px', height: banner.api.design.starSize + 'px', lineHeight: 1 }">{{ n <= banner.api.selectedHotel.star ? '★' : '☆' }}</span>
                            </div>
                            <div class="border-l border-gray-600 h-4 mx-[13px]"></div>
                            <span :style="{ color: banner.api.design.cityColor, fontSize: banner.api.design.citySize + 'px' }">
                                {{ banner.api.selectedHotel.province.name }}
                            </span>
                        </div>
                        
                        <div class="mt-auto flex items-center justify-between">
                            <div class="flex items-center">
                                <div v-if="banner.api.selectedHotel.avgRating > 0" 
                                    class="flex items-center justify-center rounded" 
                                    :style="{ 
                                        width: '35px', height: '15px', 
                                        backgroundColor: banner.api.design.ratingBoxBgColor,
                                    }">
                                    <span class="font-bold" :style="{ color: banner.api.design.ratingBoxColor, fontSize: banner.api.design.ratingBoxSize + 'px' }">{{ banner.api.selectedHotel.avgRating }}</span>
                                </div>
                                <span class="mx-[7px]" :style="{ color: banner.api.design.ratingTextColor, fontSize: banner.api.design.ratingTextSize + 'px' }">verygood</span>
                                <span v-if="banner.api.selectedHotel.reviewCount > 0" :style="{ color: banner.api.design.reviewColor, fontSize: banner.api.design.reviewSize + 'px' }">({{ banner.api.selectedHotel.reviewCount }} reviews)</span>
                            </div>

                            <div>
                                <div class="flex items-baseline gap-1.5" :style="{ flexDirection: banner.api.design.layout === 'right' ? 'row-reverse' : 'row' }">
                                    <span :style="{ color: banner.api.design.priceFromColor, fontSize: banner.api.design.priceFromSize + 'px' }">from</span>
                                    <span :style="{ color: banner.api.design.priceAmountColor, fontSize: banner.api.design.priceAmountSize + 'px', fontWeight: banner.api.design.priceAmountWeight }">€{{ banner.api.selectedHotel.minPrice.toFixed(2) }}</span>
                                    <span :style="{ color: banner.api.design.priceNightColor, fontSize: banner.api.design.priceNightSize + 'px' }">/ night</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div v-if="banner.displayMethod === 'Fixed'">
            <?php require_once YAB_PLUGIN_DIR . 'admin/views/components/display-conditions.php'; ?>
        </div>

    </div>
</main>

<?php require_once YAB_PLUGIN_DIR . 'admin/views/components/hotel-modal.php'; ?>