<?php require YAB_PLUGIN_DIR . 'admin/views/components/banner-editor-header.php'; ?>

<main class="grid grid-cols-12 gap-6 p-6 ltr">
    <div class="col-span-4 overflow-y-auto flex flex-col gap-6 [&>*:last-child]:mb-[40px]" style="max-height: calc(100vh - 120px);">
        <div class="bg-[#434343] p-5 rounded-lg shadow-xl mr-2">
            <h3 class="font-bold text-xl text-white tracking-wide mb-5">Content Source</h3>
            <div class="flex gap-4">
                <button @click="openHotelModal" class=" w-1/2 flex gap-2 justify-center items-center bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                    <span class="dashicons dashicons-building"></span>
                    {{ banner.api.selectedHotel ? 'Change Hotel' : 'Select Hotel' }}
                </button>
                <button @click="openTourModal" class="w-1/2 flex gap-2 justify-center items-center bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700">
                    <span class="dashicons dashicons-palmtree"></span>
                    {{ banner.api.selectedTour ? 'Change Tour' : 'Select Tour' }}
                </button>
            </div>
        </div>

        <div class="bg-[#434343] p-5 rounded-lg shadow-xl mr-2">
            <h3 class="font-bold text-xl text-white tracking-wide mb-5">Banner Settings</h3>
            <div class="flex flex-col gap-5">

                <div>
                    <h4 class="section-title">Background</h4>
                    <div class="flex mb-2 bg-[#292929] rounded-lg border-none">
                        <button @click="banner.api.design.backgroundType = 'solid'" :class="{'active-tab': banner.api.design.backgroundType === 'solid'}" class="flex-1 tab-button rounded-l-lg border-none">Solid Color</button>
                        <button @click="banner.api.design.backgroundType = 'gradient'" :class="{'active-tab': banner.api.design.backgroundType === 'gradient'}" class="flex-1 tab-button rounded-r-lg border-none">Gradient</button>
                    </div>
                    <div v-if="banner.api.design.backgroundType === 'solid'" class="space-y-2">
                        <label class="setting-label-sm">Background Color</label>
                        <div class="yab-color-input-wrapper">
                            <input type="color" v-model="banner.api.design.bgColor" class="yab-color-picker">
                            <input type="text" v-model="banner.api.design.bgColor" class="yab-hex-input" placeholder="#hexcode">
                        </div>
                    </div>
                    <div v-else class="space-y-2">
                        <div>
                            <label class="setting-label-sm">Gradient Colors</label>
                            <div class="grid grid-cols-2 gap-2">
                                <div class="yab-color-input-wrapper">
                                    <input type="color" v-model="banner.api.design.gradientColor1" class="yab-color-picker">
                                    <input type="text" v-model="banner.api.design.gradientColor1" class="yab-hex-input" placeholder="#hexcode">
                                </div>
                                <div class="yab-color-input-wrapper">
                                    <input type="color" v-model="banner.api.design.gradientColor2" class="yab-color-picker">
                                    <input type="text" v-model="banner.api.design.gradientColor2" class="yab-hex-input" placeholder="#hexcode">
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="setting-label-sm">Gradient Angle</label>
                            <div class="flex items-center gap-2">
                                <input type="number" v-model.number="banner.api.design.gradientAngle" class="yab-form-input w-full" placeholder="e.g., 90">
                                <span class="text-sm text-gray-400">deg</span>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="section-divider">

                <div>
                    <h4 class="section-title">Layout</h4>
                    <label class="setting-label-sm">Image Position</label>
                    <div class="flex overflow-hidden bg-[#292929] rounded-lg">
                        <button @click="banner.api.design.layout = 'left'" :class="banner.api.design.layout === 'left' ? 'active-tab' : ''" class="flex-1 tab-button rounded-l-lg">Image Left</button>
                        <button @click="banner.api.design.layout = 'right'" :class="banner.api.design.layout === 'right' ? 'active-tab' : ''" class="flex-1 tab-button rounded-r-lg">Image Right</button>
                    </div>
                </div>
                <hr class="section-divider">

                <div>
                    <h4 class="section-title">Border</h4>
                    <div class="flex items-center justify-between bg-[#292929] p-2 rounded-md mb-2">
                        <label class="text-sm font-medium text-gray-300">Enable Border</label>
                        <label class="relative inline-flex items-center cursor-pointer"><input type="checkbox" v-model="banner.api.design.enableBorder" class="sr-only peer"><div class="w-11 h-6 bg-gray-600 rounded-full peer peer-checked:after:translate-x-full after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div></label>
                    </div>
                     <div v-if="banner.api.design.enableBorder" class="grid grid-cols-3 gap-2">
                        <div>
                            <label class="setting-label-sm">Color</label>
                            <div class="yab-color-input-wrapper">
                                <input type="color" v-model="banner.api.design.borderColor" class="yab-color-picker">
                                 <input type="text" v-model="banner.api.design.borderColor" class="yab-hex-input" placeholder="Color">
                            </div>
                        </div>
                        <div>
                            <label class="setting-label-sm">Width (px)</label>
                            <input type="number" v-model.number="banner.api.design.borderWidth" class="yab-form-input" placeholder="e.g., 1">
                        </div>
                        <div>
                            <label class="setting-label-sm">Radius (px)</label>
                            <input type="number" v-model.number="banner.api.design.borderRadius" class="yab-form-input" placeholder="e.g., 15">
                        </div>
                    </div>
                </div>
                <hr class="section-divider">

                <div>
                    <h4 class="section-title">Title</h4>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="col-span-2">
                            <label class="setting-label-sm">Color</label>
                             <div class="yab-color-input-wrapper">
                                <input type="color" v-model="banner.api.design.titleColor" class="yab-color-picker">
                                <input type="text" v-model="banner.api.design.titleColor" class="yab-hex-input" placeholder="#hexcode">
                            </div>
                        </div>
                        <div>
                            <label class="setting-label-sm">Font Size (px)</label>
                            <input type="number" v-model.number="banner.api.design.titleSize" class="yab-form-input" placeholder="e.g., 18">
                        </div>
                        <div>
                            <label class="setting-label-sm">Font Weight</label>
                            <select v-model="banner.api.design.titleWeight" class="yab-form-input">
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
                            <input type="number" v-model.number="banner.api.design.starSize" class="yab-form-input w-full">
                        </div>
                        <div>
                            <label class="setting-label-sm">City Font Size (px)</label>
                            <input type="number" v-model.number="banner.api.design.citySize" class="yab-form-input w-full">
                        </div>
                        <div class="col-span-2">
                             <label class="setting-label-sm">City Color</label>
                             <div class="yab-color-input-wrapper">
                                 <input type="color" v-model="banner.api.design.cityColor" class="yab-color-picker">
                                 <input type="text" v-model="banner.api.design.cityColor" class="yab-hex-input" placeholder="City Color">
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
                            <div class="yab-color-input-wrapper">
                                <input type="color" v-model="banner.api.design.ratingBoxBgColor" class="yab-color-picker">
                                 <input type="text" v-model="banner.api.design.ratingBoxBgColor" class="yab-hex-input" placeholder="Rating BG">
                            </div>
                        </div>
                        <div>
                            <label class="setting-label-sm">Rating Box Text</label>
                            <div class="yab-color-input-wrapper">
                                <input type="color" v-model="banner.api.design.ratingBoxColor" class="yab-color-picker">
                                <input type="text" v-model="banner.api.design.ratingBoxColor" class="yab-hex-input" placeholder="Rating Text">
                            </div>
                        </div>
                        <div class="col-span-2">
                            <label class="setting-label-sm">Rating Box Font Size (px)</label>
                            <input type="number" v-model.number="banner.api.design.ratingBoxSize" class="yab-form-input w-full">
                        </div>
                        <div>
                            <label class="setting-label-sm">Rating Text Color</label>
                            <div class="yab-color-input-wrapper">
                                <input type="color" v-model="banner.api.design.ratingTextColor" class="yab-color-picker">
                                 <input type="text" v-model="banner.api.design.ratingTextColor" class="yab-hex-input" placeholder="Rating Text">
                            </div>
                        </div>
                         <div>
                            <label class="setting-label-sm">Rating Text Size (px)</label>
                            <input type="number" v-model.number="banner.api.design.ratingTextSize" class="yab-form-input flex-1">
                        </div>
                        <div>
                            <label class="setting-label-sm">Review Count Color</label>
                             <div class="yab-color-input-wrapper">
                                 <input type="color" v-model="banner.api.design.reviewColor" class="yab-color-picker">
                                 <input type="text" v-model="banner.api.design.reviewColor" class="yab-hex-input" placeholder="Review Text">
                            </div>
                        </div>
                         <div>
                            <label class="setting-label-sm">Review Count Size (px)</label>
                            <input type="number" v-model.number="banner.api.design.reviewSize" class="yab-form-input flex-1">
                        </div>
                    </div>
                </div>
                <hr class="section-divider">

                <div>
                    <h4 class="section-title">Price</h4>
                     <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="setting-label-sm">Amount Color</label>
                             <div class="yab-color-input-wrapper">
                                <input type="color" v-model="banner.api.design.priceAmountColor" class="yab-color-picker">
                                <input type="text" v-model="banner.api.design.priceAmountColor" class="yab-hex-input" placeholder="Amount Color">
                            </div>
                        </div>
                        <div>
                            <label class="setting-label-sm">Amount Size (px)</label>
                            <input type="number" v-model.number="banner.api.design.priceAmountSize" class="yab-form-input flex-1">
                        </div>
                        <div>
                            <label class="setting-label-sm">"From" & "/ night" Color</label>
                             <div class="yab-color-input-wrapper">
                                 <input type="color" v-model="banner.api.design.priceFromColor" class="yab-color-picker">
                                 <input type="text" v-model="banner.api.design.priceFromColor" class="yab-hex-input" placeholder="Text Color">
                            </div>
                        </div>
                        <div>
                            <label class="setting-label-sm">"From" & "/ night" Size (px)</label>
                            <input type="number" v-model.number="banner.api.design.priceFromSize" class="yab-form-input flex-1">
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    
    <div class="col-span-8 sticky top-[120px] space-y-4">
        <div class="bg-[#434343] p-4 rounded-lg">
            <h3 class="preview-title">Live Preview</h3>
            <div v-if="!banner.api.selectedHotel && !banner.api.selectedTour" class="flex items-center justify-center h-40 bg-[#292929] rounded-lg">
                <p class="text-gray-500">Please select a hotel or tour to see the preview.</p>
            </div>
             <div v-else-if="isHotelDetailsLoading || isTourDetailsLoading" class="flex justify-center">
                 <div class="w-[864px] h-[128px] rounded-lg bg-[#292929] flex items-stretch animate-pulse" :style="{ flexDirection: banner.api.design.layout === 'right' ? 'row-reverse' : 'row' }">
                    <div class="w-[360px] h-full bg-[#656565] rounded-l-lg"></div>
                    <div class="flex-grow p-4 flex flex-col justify-between">
                        <div class="h-5 bg-[#656565] rounded w-3/4"></div>
                        <div class="h-4 bg-[#656565] rounded w-1/2"></div>
                        <div class="flex justify-between">
                            <div class="h-4 bg-[#656565] rounded w-1/3"></div>
                            <div class="h-6 bg-[#656565] rounded w-1/4"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else class="flex justify-center">
                <div v-if="banner.api.selectedHotel"
                    class="yab-api-banner-wrapper shadow-lg flex items-stretch font-sans" 
                    :style="{ 
                        background: bannerStyles(banner.api.design),
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
                            margin: 0
                        }">{{ banner.api.selectedHotel.title }}</h3>
                        
                        <div class="flex items-center mt-[9px] flex-start" :style="{  flexDirection: banner.api.design.layout === 'right' ? 'row-reverse' : 'row' }">
                            <div class="text-yellow-400 flex items-center" :style="{ flexDirection: banner.api.design.layout === 'right' ? 'row-reverse' : 'row' }">
                                <span v-for="n in 5" :key="n" :style="{ fontSize: banner.api.design.starSize + 'px', width: banner.api.design.starSize + 'px', height: banner.api.design.starSize + 'px', lineHeight: 1 }">{{ n <= banner.api.selectedHotel.star ? '★' : '☆' }}</span>
                            </div>
                            <div class="border-l border-gray-600 h-4 mx-[13px]"></div>
                            <span :style="{ color: banner.api.design.cityColor, fontSize: banner.api.design.citySize + 'px' }">
                                {{ banner.api.selectedHotel.province.name }}
                            </span>
                        </div>
                        
                        <div class="mt-auto flex items-center justify-between" :class="banner.api.design.layout === 'right' ? 'flex-row-reverse' : 'flex-row' ">
                            <div class="flex items-center" :style="{flexDirection: banner.api.design.layout === 'right' ? 'row-reverse' : 'row'}">
                                <div v-if="banner.api.selectedHotel.avgRating != null" 
                                    class="flex items-center justify-center rounded" 
                                    :style="{ 
                                        minWidth: '35px', padding: '0 6px', height: '15px', 
                                        backgroundColor: banner.api.design.ratingBoxBgColor,
                                    }">
                                    <span class="font-bold" :style="{ color: banner.api.design.ratingBoxColor, fontSize: banner.api.design.ratingBoxSize + 'px' }">{{ formatRating(banner.api.selectedHotel.avgRating) }}</span>
                                </div>
                                <span class="mx-[7px]" :style="{ color: banner.api.design.ratingTextColor, fontSize: banner.api.design.ratingTextSize + 'px' }">{{ getRatingLabel(banner.api.selectedHotel.avgRating) }}</span>
                                <span v-if="banner.api.selectedHotel.reviewCount != null" :style="{ color: banner.api.design.reviewColor, fontSize: banner.api.design.reviewSize + 'px' }">({{ banner.api.selectedHotel.reviewCount }} reviews)</span>
                            </div>

                            <div>
                                <div class="flex items-baseline gap-1.5" >
                                    <span :style="{ color: banner.api.design.priceFromColor, fontSize: banner.api.design.priceFromSize + 'px' }">from</span>
                                    <span :style="{ color: banner.api.design.priceAmountColor, fontSize: banner.api.design.priceAmountSize + 'px', fontWeight: banner.api.design.priceAmountWeight }">€{{ banner.api.selectedHotel.minPrice.toFixed(2) }}</span>
                                    <span :style="{ color: banner.api.design.priceNightColor, fontSize: banner.api.design.priceFromSize + 'px' }">/ night</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div v-if="banner.api.selectedTour"
                    class="yab-api-banner-wrapper shadow-lg flex items-stretch font-sans" 
                    :style="{ 
                        background: bannerStyles(banner.api.design),
                        border: `${banner.api.design.enableBorder ? banner.api.design.borderWidth : 0}px solid ${banner.api.design.borderColor}`,
                        borderRadius: `${banner.api.design.borderRadius}px`,
                        width: '864px', 
                        height: '128px',
                        flexDirection: banner.api.design.layout === 'right' ? 'row-reverse' : 'row',
                        overflow: 'hidden'
                    }">
                    <div class="flex-shrink-0" style="width: 360px; height: 100%;">
                        <img :src="banner.api.selectedTour.bannerImage.url" :alt="banner.api.selectedTour.title" class="w-full h-full object-cover block">
                    </div>

                    <div 
                        class="flex-grow flex flex-col relative"
                        :style="apiContentStyles"
                    >
                        <h3 class="font-bold leading-[18px]" :style="{ 
                            color: banner.api.design.titleColor, 
                            fontSize: banner.api.design.titleSize + 'px', 
                            fontWeight: banner.api.design.titleWeight,
                            margin: 0
                        }">{{ banner.api.selectedTour.title }}</h3>
                        
                        <div class="flex items-center mt-[9px] justify-start" :class="banner.api.design.layout === 'right' ? 'flex-row-reverse' : 'flex-row' ">
                             <div class="text-yellow-400 flex items-center" >
                                <span v-for="n in 5" :key="n" :style="{ fontSize: banner.api.design.starSize + 'px', width: banner.api.design.starSize + 'px', height: banner.api.design.starSize + 'px', lineHeight: 1 }">{{ n <= ceil(banner.api.selectedTour.rate) ? '★' : '☆' }}</span>
                            </div>
                            <div class="border-l border-gray-600 h-4 mx-[13px]"></div>
                            <span :style="{ color: banner.api.design.cityColor, fontSize: banner.api.design.citySize + 'px' }">
                                {{ banner.api.selectedTour.startProvince.name }}
                            </span>
                        </div>
                        
                        <div class="mt-auto flex items-center justify-between " :class="banner.api.design.layout === 'right' ? 'flex-row-reverse' : 'row' ">
                            <div class="flex items-center gap-[7px]" :style="{flexDirection: banner.api.design.layout === 'right' ? 'row-reverse' : 'row'}">
                                 <div v-if="banner.api.selectedTour.rate != null" 
                                    class="flex items-center justify-center rounded" 
                                    :style="{ 
                                        minWidth: '35px', padding: '0 6px', height: '15px', 
                                        backgroundColor: banner.api.design.ratingBoxBgColor,
                                    }">
                                    <span class="font-bold" :style="{ color: banner.api.design.ratingBoxColor, fontSize: banner.api.design.ratingBoxSize + 'px' }">{{ formatRating(banner.api.selectedTour.rate) }}</span>
                                </div>
                                <span v-if="banner.api.selectedTour.rateCount != null" :style="{ color: banner.api.design.reviewColor, fontSize: banner.api.design.reviewSize + 'px' }">({{ banner.api.selectedTour.rateCount }} reviews)</span>
                            </div>

                            <div>
                                <div class="flex items-baseline gap-1.5" >
                                    <span :style="{ color: banner.api.design.priceFromColor, fontSize: banner.api.design.priceFromSize + 'px' }">from</span>
                                    <span :style="{ color: banner.api.design.priceAmountColor, fontSize: banner.api.design.priceAmountSize + 'px', fontWeight: banner.api.design.priceAmountWeight }">€{{ banner.api.selectedTour.price.toFixed(2) }}</span>
                                </div>
                            </div>
                        </div>
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

<?php require_once YAB_PLUGIN_DIR . 'admin/views/components/hotel-modal.php'; ?>
<?php require_once YAB_PLUGIN_DIR . 'admin/views/components/tour-modal.php'; ?>