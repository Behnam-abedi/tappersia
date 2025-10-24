<?php
// /admin/views/banner-types/welcome-package-banner/welcome-package-settings.php
?>
<div class="bg-[#434343] p-5 rounded-lg shadow-xl mr-2">
    <h3 class="font-bold text-xl text-white tracking-wide mb-5">Welcome Package Selection</h3>
    <div class="space-y-4">
        <button @click="openWelcomePackageModal" class="w-full flex gap-2 justify-center items-center bg-teal-600 text-white px-4 py-2 rounded-md hover:bg-teal-700">
            <span class="dashicons dashicons-cart"></span>
            <span>{{ banner.welcome_package.selectedPackageKey ? 'Change Package' : 'Select Welcome Package Price' }}</span>
        </button>

        <div v-if="banner.welcome_package.selectedPackageKey" class="mt-4 p-3 bg-[#292929] rounded-lg text-sm text-gray-300">
            <p><strong>Selected Package:</strong> {{ banner.welcome_package.selectedPackageKey }}</p>
            <p><strong>Original Price:</strong> {{ banner.welcome_package.originalPrice || '...' }}</p>
            <p><strong>Discounted Price:</strong> {{ banner.welcome_package.discountedPrice || '...' }}</p>
        </div>
         <div v-else class="mt-4 p-3 bg-[#292929] rounded-lg text-sm text-gray-400 text-center">
             No package selected yet.
         </div>
    </div>
</div>

<div class="bg-[#434343] p-5 rounded-lg shadow-xl mr-2">
    <h3 class="font-bold text-xl text-white tracking-wide mb-5">HTML Content</h3>
    <div class="space-y-2">
         <label class="setting-label-sm">HTML Template</label>
        <textarea
            v-model="banner.welcome_package.htmlContent"
            rows="15"
            class="w-full p-3 font-mono text-sm bg-[#292929] border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#00baa4] text-white"
            placeholder="Enter your HTML template here. Use {{originalPrice}} and {{discountedPrice}} for placeholders."
        ></textarea>
         <p class="text-xs text-gray-400 mt-1">
             Use placeholders: <code>{{originalPrice}}</code> for original price, <code>{{discountedPrice}}</code> for the discounted/current price, and <code>{{key}}</code> for the package key. These will be replaced with live data on the frontend.
         </p>
    </div>
</div>
