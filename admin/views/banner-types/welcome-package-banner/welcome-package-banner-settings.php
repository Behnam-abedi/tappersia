<?php
// tappersia/admin/views/banner-types/welcome-package-banner/welcome-package-banner-settings.php
?>
<div class="bg-[#434343] p-5 rounded-lg shadow-xl mr-2">
    <h3 class="font-bold text-xl text-white tracking-wide mb-5">Package Selection</h3>
    <button @click="openWelcomePackageModal" class="w-full flex gap-2 justify-center items-center bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 mb-4">
        <span class="dashicons dashicons-products"></span>
        <span>{{ banner.welcome_package.selectedKey ? 'Change Package' : 'Select Welcome Package Price' }}</span>
    </button>
    <div v-if="banner.welcome_package.selectedKey" class="text-sm bg-[#292929] p-3 rounded-lg text-gray-300">
        <p><strong>Selected Package:</strong> {{ banner.welcome_package.selectedKey }}</p>
        <p><strong>Current Price:</strong> €{{ banner.welcome_package.selectedPrice }}</p>
        <p v-if="banner.welcome_package.selectedOriginalPrice && banner.welcome_package.selectedOriginalPrice !== banner.welcome_package.selectedPrice">
            <strong>Original Price:</strong> <span style="text-decoration: line-through;">€{{ banner.welcome_package.selectedOriginalPrice }}</span>
        </p>
         <p class="text-xs text-gray-500 mt-2">Note: Prices shown are from the time of selection. The banner will always display the latest price from the API on the frontend.</p>
    </div>
     <div v-else class="text-sm text-gray-400 text-center py-2">
        No package selected. Click the button above.
    </div>
</div>

<div class="bg-[#434343] p-5 rounded-lg shadow-xl mr-2">
    <h3 class="font-bold text-xl text-white tracking-wide mb-5">Banner HTML Template</h3>
     <p class="text-xs text-gray-400 mb-3">
         Enter your custom HTML below. Use <code class="bg-[#292929] px-1 rounded">{{price}}</code>, <code class="bg-[#292929] px-1 rounded">{{originalPrice}}</code>, and <code class="bg-[#292929] px-1 rounded">{{selectedKey}}</code> as placeholders.
         These will be replaced with the latest API values when the banner is displayed.
    </p>
    <textarea
        v-model="banner.welcome_package.html"
        rows="15"
        class="w-full p-3 font-mono text-sm bg-[#292929] border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#00baa4] text-white"
        placeholder="e.g., <div style='...'>Get {{selectedKey}} for only {{price}}! (was {{originalPrice}})</div>"
    ></textarea>
</div>