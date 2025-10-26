<?php
// /admin/views/banner-types/welcome-package-banner/welcome-package-preview.php
?>
<div class="bg-[#434343] p-4 rounded-lg">
    <h3 class="preview-title">Live Preview</h3>

    <div v-if="!banner.welcome_package.selectedPackageKey" class="text-center text-gray-500 p-8 bg-white rounded-lg border border-gray-500" style="min-height: 200px;">
         Select a package and add HTML content to see the preview.
     </div>
     <div v-else-if="!banner.welcome_package.htmlContent" class="text-center text-gray-500 p-8 bg-white rounded-lg border border-gray-500" style="min-height: 200px;">
         Enter HTML content to see the preview.
     </div>
     <div
        v-else
        class="w-full h-auto bg-white rounded-lg p-4 overflow-auto border border-gray-500"
        style="min-height: 200px;"
        v-html="welcomePackagePreviewHtml"
    >
        </div>
    <p class="text-xs text-gray-400 mt-2">
        Note: Prices shown here are based on the time of selection. The live banner will always fetch the most current prices from the API.
    </p>
</div>