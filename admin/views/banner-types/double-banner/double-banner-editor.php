<?php require YAB_PLUGIN_DIR . 'admin/views/components/banner-editor-header.php'; ?>

<main class="grid grid-cols-12 gap-6 p-6 ltr">
    <div class="col-span-4 overflow-y-auto flex flex-col gap-6 [&>*:last-child]:mb-[40px]" style="max-height: calc(100vh - 120px);">
        <div class="bg-[#434343] p-5 rounded-lg shadow-xl">
            <div class="flex mb-4 bg-[#292929] rounded-lg p-1">
                <button @click="currentView = 'desktop'" :class="{'active-tab': currentView === 'desktop'}" class="flex-1 tab-button rounded-md">Desktop</button>
                <button @click="currentView = 'mobile'" :class="{'active-tab': currentView === 'mobile'}" class="flex-1 tab-button rounded-md">Mobile</button>
            </div>
            <div v-if="banner.left && banner.right && banner.left_mobile && banner.right_mobile" :key="currentView">
                <h3 class="font-bold text-xl text-white tracking-wide mb-4 capitalize">{{ currentView }} Settings</h3>
                <div class="flex flex-col gap-5">
                    <div v-for="side in ['left','right']" :key="side" class="bg-[#434343] p-5 rounded-lg shadow-xl">
                        <h3 class="font-bold text-xl text-white capitalize tracking-wide mb-5">{{ side }} Banner Settings</h3>
                        <div class="flex flex-col gap-5">
                            <?php require 'double-banner-settings.php'; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-span-8 sticky top-[120px] space-y-4">
        <div class="bg-[#434343] p-4 rounded-lg">
            <h3 class="preview-title">Live Preview</h3>
            <transition name="fade" mode="out-in">
                <div v-if="currentView === 'desktop'" class="flex flex-row gap-[20px] justify-center">
                    <div v-for="side in ['left','right']" :key="`preview-desktop-${side}`" :style="getPartContainerStyles(side, 'desktop')" class="relative overflow-hidden flex flex-shrink-0">
                        <template v-if="banner[side].layerOrder === 'overlay-top'">
                            <img v-if="banner[side].imageUrl" :src="banner[side].imageUrl" :style="{...imageStyleObject(banner[side]), zIndex: 1}" />
                            <div class="absolute inset-0" :style="{background: bannerStyles(banner[side]), zIndex: 2}"></div>
                        </template>
                        <template v-else>
                            <div class="absolute inset-0" :style="{background: bannerStyles(banner[side]), zIndex: 1}"></div>
                            <img v-if="banner[side].imageUrl" :src="banner[side].imageUrl" :style="{...imageStyleObject(banner[side]), zIndex: 2}" />
                        </template>
                        <div class="w-full h-full flex flex-col z-10 relative" :style="getPartDynamicStyles(side, 'desktop').contentStyles">
                            <h4 :style="getPartDynamicStyles(side, 'desktop').titleStyles">{{ banner[side].titleText }}</h4>
                            <p :style="getPartDynamicStyles(side, 'desktop').descriptionStyles">{{ banner[side].descText }}</p>
                            <a v-if="banner[side].buttonText" :href="banner[side].buttonLink" target="_blank" :style="getPartDynamicStyles(side, 'desktop').buttonStyles">{{ banner[side].buttonText }}</a>
                        </div>
                    </div>
                </div>
                <div v-else-if="currentView === 'mobile'" class="flex flex-col gap-[20px] items-center">
                    <div v-for="side in ['left','right']" :key="`preview-mobile-${side}`" :style="getPartContainerStyles(side, 'mobile')" class="relative overflow-hidden w-full">
                        <template v-if="banner[side + '_mobile'].layerOrder === 'overlay-top'">
                            <img v-if="banner[side + '_mobile'].imageUrl" :src="banner[side + '_mobile'].imageUrl" :style="{...imageStyleObject(banner[side + '_mobile']), zIndex: 1}" />
                            <div class="absolute inset-0" :style="{background: bannerStyles(banner[side + '_mobile']), zIndex: 2}"></div>
                        </template>
                        <template v-else>
                            <div class="absolute inset-0" :style="{background: bannerStyles(banner[side + '_mobile']), zIndex: 1}"></div>
                            <img v-if="banner[side + '_mobile'].imageUrl" :src="banner[side + '_mobile'].imageUrl" :style="{...imageStyleObject(banner[side + '_mobile']), zIndex: 2}" />
                        </template>
                        <div class="w-full h-full flex flex-col z-10 relative" :style="getPartDynamicStyles(side, 'mobile').contentStyles">
                            <h4 :style="getPartDynamicStyles(side, 'mobile').titleStyles">{{ banner[side + '_mobile'].titleText }}</h4>
                            <p :style="getPartDynamicStyles(side, 'mobile').descriptionStyles">{{ banner[side + '_mobile'].descText }}</p>
                            <a v-if="banner[side + '_mobile'].buttonText" :href="banner[side + '_mobile'].buttonLink" target="_blank" :style="getPartDynamicStyles(side, 'mobile').buttonStyles">{{ banner[side + '_mobile'].buttonText }}</a>
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
