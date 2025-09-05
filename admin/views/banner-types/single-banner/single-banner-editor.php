<?php require YAB_PLUGIN_DIR . 'admin/views/components/banner-editor-header.php'; ?>

<main class="grid grid-cols-12 gap-6 p-6 ltr">
    <div class="col-span-4 overflow-y-auto flex flex-col gap-6 [&>*:last-child]:mb-[40px] mr-2" style="max-height: calc(100vh - 120px);">
        <div class="bg-[#434343] p-5 rounded-lg shadow-xl mr-2">
            <div class="flex mb-4 bg-[#292929] rounded-lg p-1">
                <button @click="currentView = 'desktop'" :class="{'active-tab': currentView === 'desktop'}" class="flex-1 tab-button rounded-md">Desktop</button>
                <button @click="currentView = 'mobile'" :class="{'active-tab': currentView === 'mobile'}" class="flex-1 tab-button rounded-md">Mobile</button>
            </div>

            <div v-if="banner.single && banner.single_mobile" :key="currentView">
                <h3 class="font-bold text-xl text-white tracking-wide mb-4 capitalize">{{ currentView }} Settings</h3>
            
                <div class="flex flex-col gap-5">
                    <?php require 'single-banner-settings.php'; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-span-8 sticky top-[120px] space-y-4">
        <div class="bg-[#434343] p-4 rounded-lg">
            <h3 class="preview-title">Live Preview</h3>
            
            <transition name="fade" mode="out-in">
                <div v-if="currentView === 'desktop'" class="flex flex-col items-center">
                    <span class="text-xs text-gray-400 mb-2">Desktop View</span>
                    <div class="flex justify-center w-full">
                        <div class="relative overflow-hidden flex-shrink-0" :style="getBannerContainerStyles('desktop')">
                            <img v-if="banner.single.imageUrl" :src="banner.single.imageUrl" :style="{...imageStyleObject(banner.single), zIndex: 1}" />
                            <div class="absolute inset-0" :style="{background: bannerStyles(banner.single), zIndex: 2}"></div>
                            <div class="w-full h-full flex flex-col z-10 relative" :style="{...getContentStyles('desktop'), zIndex: 3}">
                                <h4 :style="getTitleStyles('desktop')">{{ banner.single.titleText }}</h4>
                                <p :style="getDescriptionStyles('desktop')">{{ banner.single.descText }}</p>
                                <a v-if="banner.single.buttonText" :href="banner.single.buttonLink" target="_blank" :style="getButtonStyles('desktop')">{{ banner.single.buttonText }}</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div v-else-if="currentView === 'mobile'" class="flex flex-col items-center">
                    <span class="text-xs text-gray-400 mb-2">Mobile View</span>
                    <div class="w-[375px] h-auto bg-[#292929] rounded-2xl p-4 flex justify-center items-center mx-auto">
                        <div class="relative overflow-hidden flex-shrink-0 w-full" :style="getBannerContainerStyles('mobile')">
                            <img v-if="banner.single_mobile.imageUrl" :src="banner.single_mobile.imageUrl" :style="{...imageStyleObject(banner.single_mobile), zIndex: 1}" />
                            <div class="absolute inset-0" :style="{background: bannerStyles(banner.single_mobile), zIndex: 2}"></div>
                            <div class="w-full h-full flex flex-col z-10 relative" :style="{...getContentStyles('mobile'), zIndex: 3}">
                                <h4 :style="getTitleStyles('mobile')">{{ banner.single_mobile.titleText }}</h4>
                                <p :style="getDescriptionStyles('mobile')">{{ banner.single_mobile.descText }}</p>
                                <a v-if="banner.single_mobile.buttonText" :href="banner.single_mobile.buttonLink" target="_blank" :style="getButtonStyles('mobile')">{{ banner.single_mobile.buttonText }}</a>
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