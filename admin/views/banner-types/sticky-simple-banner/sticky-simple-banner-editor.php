<header class="sticky top-7 bg-[#434343]/60 backdrop-blur-md p-4 z-20 flex items-center justify-between shadow-lg ltr">
    <div class="flex items-center gap-4">
        <button @click="goBackToSelection" class="text-gray-400 hover:text-white bg-transparent border-none p-0 cursor-pointer flex items-center">&larr; Back</button>
        <span class="text-gray-600">|</span>
        <span class="text-sm">Title:</span>
        <input type="text" v-model="banner.name" placeholder="Enter Banner Name..." class="bg-[#656565] text-white border border-gray-600 rounded px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-[#00baa4] w-64">
    </div>
    
    <div class="flex items-center gap-5">
        <div class="flex items-center gap-2">
            <span class="text-sm">Display Method:</span>
            <div class="flex rounded-lg bg-[#656565] overflow-hidden">
                 <button class="bg-[#00baa4] text-white px-4 py-1.5 text-sm flex-1 cursor-default">Fixed</button>
            </div>
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
    <div class="col-span-4 overflow-y-auto flex flex-col gap-6 [&>*:last-child]:mb-[40px] mr-2" style="max-height: calc(100vh - 120px);">
        <div class="bg-[#434343] p-5 rounded-lg shadow-xl mr-2">
            <h3 class="font-bold text-xl text-white tracking-wide mb-4 capitalize">Device</h3>
            <div class="flex  bg-[#292929] rounded-lg p-1">
                <button @click="currentView = 'desktop'" :class="{'active-tab': currentView === 'desktop'}" class="flex-1 tab-button rounded-md">Desktop</button>
                <button @click="currentView = 'mobile'" :class="{'active-tab': currentView === 'mobile'}" class="flex-1 tab-button rounded-md">Mobile</button>
            </div>
        </div>
        <div class="bg-[#434343] p-5 rounded-lg shadow-xl mr-2"v-if="currentView === 'desktop'">
            <div >
                <h3 class="font-bold text-xl text-white tracking-wide mb-4 capitalize">Alignment</h3>
                <div class="flex  bg-[#292929] rounded-lg p-1">
                    <button @click="settings.direction = 'ltr'" :class="settings.direction === 'ltr' ? 'active-tab' : ''" class="flex-1 tab-button rounded-md">Left to Right</button>
                    <button @click="settings.direction = 'rtl'" :class="settings.direction === 'rtl' ? 'active-tab' : ''" class="flex-1 tab-button rounded-md">Right to Left</button>
                </div>
            </div>
        </div>
        <div class="bg-[#434343] p-5 rounded-lg shadow-xl mr-2">


            <div v-if="banner.sticky_simple && banner.sticky_simple_mobile" :key="currentView">
                <h3 class="font-bold text-xl text-white tracking-wide mb-4 capitalize">{{ currentView }} Settings</h3>
            
                <div class="flex flex-col gap-5">
                    <?php require 'sticky-simple-banner-settings.php'; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-span-8 sticky top-[120px] space-y-4">
        <?php require YAB_PLUGIN_DIR . 'admin/views/banner-types/sticky-simple-banner/sticky-simple-banner-preview.php'; ?>
        
        <transition name="fade">
            <div v-if="banner.displayMethod === 'Fixed'">
                <?php require YAB_PLUGIN_DIR . 'admin/views/components/display-conditions.php'; ?>
            </div>
        </transition>
    </div>
</main>