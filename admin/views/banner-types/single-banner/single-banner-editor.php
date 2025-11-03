<?php require YAB_PLUGIN_DIR . 'admin/views/components/banner-editor-header.php'; ?>

<main class="grid grid-cols-12 gap-6 p-6 ltr">
    <div class="col-span-4 overflow-y-auto flex flex-col gap-6 [&>*:last-child]:mb-[40px] mr-2" style="max-height: calc(100vh - 120px);">
        <div class="bg-[#434343] p-5 rounded-lg shadow-xl mr-2">
                            <h3 class="font-bold text-xl text-white tracking-wide mb-4 capitalize">Device</h3>
            <div class="flex bg-[#292929] rounded-lg p-1">
                <button @click="currentView = 'desktop'" :class="{'active-tab': currentView === 'desktop'}" class="flex-1 tab-button rounded-md">Desktop</button>
                <button @click="currentView = 'mobile'" :class="{'active-tab': currentView === 'mobile'}" class="flex-1 tab-button rounded-md">Mobile</button>
            </div>
        </div>     
        <div class="bg-[#434343] p-5 rounded-lg shadow-xl mr-2" v-if="currentView === 'desktop'">
            <div >
                <h3 class="font-bold text-xl text-white tracking-wide mb-4 capitalize">Alignment</h3>
                <div class="flex rounded-lg bg-[#292929] overflow-hidden p-1">
                    <button @click="settings.alignment = 'left'" :class="settings.alignment === 'left' ? 'active-tab' : ''" class="flex-1 tab-button rounded-md">Left</button>
                    <button @click="settings.alignment = 'center'" :class="settings.alignment === 'center' ? 'active-tab' : ''" class="flex-1 tab-button rounded-md">Center</button>
                    <button @click="settings.alignment = 'right'" :class="settings.alignment === 'right' ? 'active-tab' : ''" class="flex-1 tab-button rounded-md">Right</button>
                </div>
            </div>
        </div>

        <div class="bg-[#434343] p-5 rounded-lg shadow-xl mr-2">
            <div v-if="banner.single && banner.single_mobile" :key="currentView">
                <h3 class="font-bold text-xl text-white tracking-wide mb-4 capitalize">{{ currentView }} Settings</h3>
            
                <div class="flex flex-col gap-5">
                    <?php require 'single-banner-settings.php'; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-span-8 sticky top-[120px] space-y-4">
        <?php require YAB_PLUGIN_DIR . 'admin/views/banner-types/single-banner/single-banner-preview.php'; ?>
        
        <transition name="fade">
            <div v-if="banner.displayMethod === 'Fixed'">
                <?php require YAB_PLUGIN_DIR . 'admin/views/components/display-conditions.php'; ?>
            </div>
        </transition>
    </div>
</main>