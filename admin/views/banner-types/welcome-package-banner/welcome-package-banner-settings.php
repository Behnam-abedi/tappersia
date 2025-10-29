<?php
// tappersia/admin/views/banner-types/welcome-package-banner/welcome-package-banner-settings.php
?>
<div class="bg-[#434343] p-5 rounded-lg shadow-xl mr-2">
    <h3 class="font-bold text-xl text-white tracking-wide mb-5">Package Selection</h3>
    <button @click="openWelcomePackageModal" class="w-full flex gap-2 justify-center items-center bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 mb-4">
        <span class="dashicons dashicons-products"></span>
        <span>{{ banner.welcome_package.selectedKey ? 'Change Package' : 'Select Welcome Package ' }}</span>
    </button>
    <div v-if="banner.welcome_package.selectedKey" class="text-sm bg-[#292929] p-3 rounded-lg text-gray-300">
        <p><strong>Selected Package:</strong> {{ banner.welcome_package.selectedKey }}</p>
        <p><strong>Current Price:</strong> €{{ banner.welcome_package.selectedPrice?.toFixed(2) ?? 'N/A' }}</p>
        <p v-if="banner.welcome_package.selectedOriginalPrice && banner.welcome_package.selectedOriginalPrice !== banner.welcome_package.selectedPrice">
            <strong>Original Price:</strong> <span style="text-decoration: line-through;">€{{ banner.welcome_package.selectedOriginalPrice?.toFixed(2) ?? 'N/A' }}</span>
        </p>
    </div>


    <div class="mt-4 pt-4 border-t border-gray-600">
        <h4 class="font-semibold text-lg text-white mb-3">Available Placeholders</h4>
        <p class="text-xs text-gray-400 mb-3">Click on a box to copy the placeholder to your clipboard.</p>
        <div class="grid grid-cols-2 gap-3">
            
            <div @click="copyPlaceholder('{{price}}')" 
                 class="bg-[#292929] p-3 rounded text-center cursor-pointer transition-colors hover:bg-[#555] active:bg-[#4a4a4a]" 
                 title="Click to copy {{price}}">
                <span class="text-sm text-yellow-300 font-mono">price</span>
            </div>

            <div @click="copyPlaceholder('{{originalPrice}}')" 
                 class="bg-[#292929] p-3 rounded text-center cursor-pointer transition-colors hover:bg-[#555] active:bg-[#4a4a4a]" 
                 title="Click to copy {{originalPrice}}">
                <span class="text-sm text-yellow-300 font-mono">originalPrice</span>
            </div>

            <div @click="copyPlaceholder('{{selectedKey}}')" 
                 class="bg-[#292929] p-3 rounded text-center cursor-pointer transition-colors hover:bg-[#555] active:bg-[#4a4a4a]" 
                 title="Click to copy {{selectedKey}}">
                <span class="text-sm text-yellow-300 font-mono">selectedKey</span>
            </div>

            <div @click="copyPlaceholder('{{discountPercentage}}')" 
                 class="bg-[#292929] p-3 rounded text-center cursor-pointer transition-colors hover:bg-[#555] active:bg-[#4a4a4a]" 
                 title="Click to copy {{discountPercentage}}">
                <span class="text-sm text-yellow-300 font-mono">discountPercentage</span>
            </div>

        </div>
    </div>
    </div>

<div class="bg-[#434343] p-5 rounded-lg shadow-xl mr-2">
    <h3 class="font-bold text-xl text-white tracking-wide mb-5">Banner HTML Template</h3>

    <textarea
        v-model="banner.welcome_package.html"
        rows="15"
        class="w-full p-3 font-mono text-sm bg-[#292929] border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#00baa4] text-white"
        placeholder="e.g., <div style='...'>Get {{selectedKey}} for only €{{price}}! (was €{{originalPrice}}, {{discountPercentage}}% off)</div>"
    ></textarea>
</div>