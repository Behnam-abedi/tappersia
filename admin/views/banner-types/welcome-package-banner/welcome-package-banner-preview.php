<?php
// tappersia/admin/views/banner-types/welcome-package-banner/welcome-package-banner-preview.php
?>
<div class="bg-[#434343] p-4 rounded-lg">
    <h3 class="preview-title">Live Preview (Admin Editor)</h3>
    <p class="text-xs text-gray-400 mb-3">
        This preview shows the HTML structure you entered. Placeholders like <code class="bg-[#292929] px-1 rounded">{{price}}</code> will be replaced with live data on the website frontend.
    </p>
    <div
        class="w-full h-auto bg-white rounded-lg p-4 overflow-auto"
        style="min-height: 200px;"
        v-html="banner.welcome_package.html"
    >
        <div v-if="!banner.welcome_package.html" class="text-center text-gray-500 py-10">
            Enter HTML content in the settings to see a preview.
        </div>
    </div>
</div>