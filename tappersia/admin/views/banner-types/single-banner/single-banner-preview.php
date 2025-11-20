<?php
// tappersia/admin/views/banner-types/single-banner/single-banner-preview.php
?>
<div class="bg-[#434343] p-4 rounded-lg">
    <h3 class="preview-title">Live Preview</h3>
    
    <transition name="fade" mode="out-in">
        <div v-if="currentView === 'desktop'" class="flex flex-col items-center">
            <span class="text-xs text-gray-400 mb-2">Desktop View</span>
            <div class="flex justify-center w-full">
                <div class="relative overflow-hidden flex-shrink-0" :style="getBannerContainerStyles('desktop')">
                    
                    <?php // --- DESKTOP (No changes) --- ?>
                    <img v-if="banner.single.imageUrl" :src="banner.single.imageUrl" :style="{...imageStyleObject(banner.single), zIndex: banner.single.layerOrder === 'image-below-overlay' ? 1 : 2}" />
                    <div class="absolute inset-0" :style="{background: bannerStyles(banner.single), zIndex: banner.single.layerOrder === 'image-below-overlay' ? 2 : 1}"></div>
                    <div class="w-full h-full flex flex-col z-10 relative flex" :style="{...getContentStyles('desktop'), zIndex: 3}">
                        <div style="flex-grow: 1;">
                            <h4 :style="getTitleStyles('desktop')">{{ banner.single.titleText }}</h4>
                            <p :style="getDescriptionStyles('desktop')">{{ banner.single.descText }}</p>
                        </div>
                        <a v-if="banner.single.buttonText" :href="banner.single.buttonLink" target="_blank" :style="getButtonStyles('desktop')">{{ banner.single.buttonText }}</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div v-else-if="currentView === 'mobile'" class="flex flex-col items-center">
            <span class="text-xs text-gray-400 mb-2">Mobile View</span>
            <div class="w-[375px] h-auto bg-[#292922] rounded-2xl p-4 flex justify-center items-center mx-auto">
                <div class="relative overflow-hidden flex-shrink-0 w-full" :style="getBannerContainerStyles('mobile')">
                    
                    <?php // --- START: MOBILE CHANGES --- ?>
                    
                    <img v-if="banner.single.imageUrl" :src="banner.single.imageUrl" :style="{...imageStyleObject(banner.single_mobile), zIndex: banner.single.layerOrder === 'image-below-overlay' ? 1 : 2}" />
                    
                    <div class="absolute inset-0" :style="{background: bannerStyles(banner.single_mobile), zIndex: banner.single.layerOrder === 'image-below-overlay' ? 2 : 1}"></div>
                    
                    <div class="w-full h-full flex flex-col z-10 relative" :style="{...getContentStyles('mobile'), zIndex: 3}">
                        
                        <div style="flex-grow: 1;">
                            <h4 :style="getTitleStyles('mobile')">{{ banner.single.titleText }}</h4>
                            <p :style="getDescriptionStyles('mobile')">{{ banner.single.descText }}</p>
                        </div>
                        
                        <a v-if="banner.single.buttonText" :href="banner.single.buttonLink" target="_blank" :style="getButtonStyles('mobile')">{{ banner.single.buttonText }}</a>
                    </div>
                    <?php // --- END: MOBILE CHANGES --- ?>
                    
                </div>
            </div>
        </div>
    </transition>
</div>