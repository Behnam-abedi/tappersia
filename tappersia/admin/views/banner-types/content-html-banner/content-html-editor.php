<?php
// tappersia/admin/views/banner-types/content-html-editor.php
require YAB_PLUGIN_DIR . 'admin/views/components/banner-editor-header.php'; 
?>

<main class="grid grid-cols-12 gap-6 p-6 ltr">
    <div class="col-span-1"></div>

    <div class="col-span-10 sticky top-[120px] space-y-4">
        <div class="bg-[#434343] p-4 rounded-lg">
            <h3 class="preview-title">HTML Box</h3>
            <textarea 
                v-model="banner.content_html.html"
                rows="10"
                class="w-full p-3 font-mono text-sm bg-[#292929] border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#00baa4] text-white"
                placeholder="Enter your HTML, CSS, and JavaScript code here..."
            ></textarea>
        </div>

        <div class="bg-[#434343] p-4 rounded-lg">
            <h3 class="preview-title">Live Preview</h3>
            <div 
                class="w-full h-auto bg-white rounded-lg p-4 overflow-auto"
                style="min-height: 200px;"
                v-html="banner.content_html.html"
            >
            </div>
        </div>

        <transition name="yab-modal-fade">
            <div v-if="banner.displayMethod === 'Fixed'">
                <?php require YAB_PLUGIN_DIR . 'admin/views/components/display-conditions.php'; ?>
            </div>
        </transition>

    </div>
    
    <div class="col-span-1"></div>
</main>