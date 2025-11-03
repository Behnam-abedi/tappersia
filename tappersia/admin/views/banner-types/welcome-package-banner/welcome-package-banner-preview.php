<?php
// tappersia/admin/views/banner-types/welcome-package-banner/welcome-package-banner-preview.php
?>
<div class="bg-[#434343] p-4 rounded-lg">
    <h3 class="preview-title">Live Preview</h3>


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