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
            <div class="flex  bg-[#292929] rounded-lg p-1 w-[200px]">
                <button @click="banner.displayMethod = 'Fixed'" :class="banner.displayMethod === 'Fixed' ? ' active-tab' : ''" class="flex-1 tab-button rounded-md">Fixed</button>
                <button @click="banner.displayMethod = 'Embeddable'" :class="banner.displayMethod === 'Embeddable' ? ' active-tab' : ''" class="flex-1 tab-button rounded-md">Embeddable</button>
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