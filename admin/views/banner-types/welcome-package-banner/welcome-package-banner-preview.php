<?php
// tappersia/admin/views/banner-types/welcome-package-banner/welcome-package-banner-preview.php
?>
<div class="bg-[#434343] p-4 rounded-lg">
    <h3 class="preview-title">Live Preview (Admin Editor)</h3>
    <p class="text-xs text-gray-400 mb-3">
        This preview shows the HTML structure with <strong class="text-yellow-300">saved prices</strong> (from the time of selection). Placeholders will be replaced with <strong class="text-yellow-300">live API data</strong> on the website frontend.
    </p>

    <div v-if="welcomePackagePreviewHtml"
        class="w-full h-auto bg-white rounded-lg p-4 overflow-auto"
        style="min-height: 200px;"
        v-html="welcomePackagePreviewHtml">
        </div>
    <div v-else
        class="w-full h-auto bg-white rounded-lg p-4 overflow-auto text-center text-gray-500 py-10"
        style="min-height: 200px;">
            Select a package and enter HTML content in the settings to see a preview.
    </div>
    </div>