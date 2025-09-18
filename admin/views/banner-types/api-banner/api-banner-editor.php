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
            <div class="flex mb-4 bg-[#292929] rounded-lg p-1">
                <button @click="currentView = 'desktop'" :class="{'active-tab': currentView === 'desktop'}" class="flex-1 tab-button rounded-md">Desktop</button>
                <button @click="currentView = 'mobile'" :class="{'active-tab': currentView === 'mobile'}" class="flex-1 tab-button rounded-md">Mobile</button>
            </div>

            <div v-if="banner.api.design" :key="currentView">
                <h3 class="font-bold text-xl text-white tracking-wide mb-4 capitalize">{{ currentView }} Settings</h3>
                <div>
                    <?php require 'api-banner-settings.php'; ?>
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
                 <div class="w-full rounded-lg bg-[#292929] flex items-stretch animate-pulse" 
                    :style="{ 
                        minHeight: (currentView === 'desktop' ? banner.api.design : banner.api.design_mobile).enableCustomDimensions ? `${(currentView === 'desktop' ? banner.api.design : banner.api.design_mobile).height}${(currentView === 'desktop' ? banner.api.design : banner.api.design_mobile).heightUnit}` : (currentView === 'desktop' ? '150px' : '80px'),
                        height: 'auto',
                        flexDirection: (currentView === 'desktop' ? banner.api.design : banner.api.design_mobile).layout === 'right' ? 'row-reverse' : 'row' 
                    }">
                    <div class="bg-[#656565]" :style="{ width: (currentView === 'desktop' ? banner.api.design : banner.api.design_mobile).imageContainerWidth + 'px' }"></div>
                    <div class="flex-grow p-4 flex flex-col justify-between space-y-2">
                        <div class="h-5 bg-[#656565] rounded w-3/4"></div>
                        <div class="h-4 bg-[#656565] rounded w-1/2"></div>
                        <div class="flex justify-between items-end pt-2">
                            <div class="h-4 bg-[#656565] rounded w-1/3"></div>
                            <div class="h-6 bg-[#656565] rounded w-1/4"></div>
                        </div>
                    </div>
                </div>
            </div>

            <transition v-else name="fade" mode="out-in">
                <div v-if="currentView === 'desktop'" class="flex flex-col items-center">
                    <span class="text-xs text-gray-400 mb-2">Desktop View</span>
                    <div class="flex justify-center w-full">
                        <div class="yab-api-banner-wrapper shadow-lg flex items-stretch font-sans" :style="getApiBannerStyles('desktop', banner)">
                            <div class="flex-shrink-0" :style="{ 
                                width: settings.imageContainerWidth + 'px', 
                                zIndex: 2,
                                backgroundImage: 'url(' + ((banner.api.selectedHotel || banner.api.selectedTour).coverImage?.url || (banner.api.selectedHotel || banner.api.selectedTour).bannerImage?.url) + ')',
                                backgroundSize: 'cover',
                                backgroundPosition: 'center center'
                            }">
                            </div>
                            <div class="flex-grow flex flex-col relative justify-between" :style="getApiContentStyles('desktop', banner)">
                                <?php include 'api-banner-preview-content.php'; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div v-else-if="currentView === 'mobile'" class="flex flex-col items-center">
                    <span class="text-xs text-gray-400 mb-2">Mobile View</span>
                    <div class="w-[375px] h-auto bg-[#292929] rounded-2xl p-4 flex justify-center items-center mx-auto">
                        <div class="yab-api-banner-wrapper shadow-lg flex items-stretch font-sans w-full" :style="getApiBannerStyles('mobile', banner)">
                            <div class="flex-shrink-0" :style="{ 
                                width: settings.imageContainerWidth + 'px', 
                                zIndex: 2,
                                backgroundImage: 'url(' + ((banner.api.selectedHotel || banner.api.selectedTour).coverImage?.url || (banner.api.selectedHotel || banner.api.selectedTour).bannerImage?.url) + ')',
                                backgroundSize: 'cover',
                                backgroundPosition: 'center center'
                            }">
                            </div>
                            <div class="flex-grow flex flex-col relative" :style="getApiContentStyles('mobile', banner)">
                                <?php include 'api-banner-preview-content.php'; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </transition>
        </div>
        
        <transition name="fade">
            <div v-if="banner.displayMethod === 'Fixed'">
                <?php require YAB_PLUGIN_DIR . 'admin/views/components/display-conditions.php'; ?>
            </div>
        </transition>
    </div>
</main>

<?php require_once YAB_PLUGIN_DIR . 'admin/views/components/hotel-modal.php'; ?>
<?php require_once YAB_PLUGIN_DIR . 'admin/views/components/tour-modal.php'; ?>