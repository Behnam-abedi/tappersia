<div id="yab-app" class="bg-[#232323] text-white min-h-screen font-sans" v-cloak>

    <transition name="fade" mode="out-in">
        <div v-if="appState === 'selection'" key="selection" class="flex items-center justify-center h-screen">
            <div class="p-8 text-center ">
                <h1 class="text-3xl font-bold mb-8 text-gray-200 ">Create a New Element</h1>
                 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 ltr">
                    <div @click="selectElementType('single-banner')" class="cursor-pointer bg-[#656565] p-8 rounded-lg transform hover:-translate-y-1 transition-all duration-300 group flex justify-center items-center flex-col gap-2">
                        <span class="dashicons dashicons-format-image text-4xl mb-4 text-[#00baa4] group-hover:text-white transition-colors flex justify-center"></span>
                        <h3 class="font-semibold text-lg text-gray-200 group-hover:text-white">Single Banner</h3>
                    </div>
                    <div @click="selectElementType('double-banner')" class="cursor-pointer bg-[#656565] p-8 rounded-lg transform hover:-translate-y-1 transition-all duration-300 group flex justify-center items-center flex-col gap-2">
                        <span class="dashicons dashicons-columns text-4xl mb-4 text-[#00baa4] group-hover:text-white transition-colors flex justify-center"></span>
                        <h3 class="font-semibold text-lg text-gray-200 group-hover:text-white">Double Banner</h3>
                    </div>
                    <div @click="selectElementType('api-banner')" class="cursor-pointer bg-[#656565] p-8 rounded-lg transform hover:-translate-y-1 transition-all duration-300 group flex justify-center items-center flex-col gap-2">
                        <span class="dashicons dashicons-rest-api text-4xl mb-4 text-[#00baa4] group-hover:text-white transition-colors flex justify-center"></span>
                        <h3 class="font-semibold text-lg text-gray-200 group-hover:text-white">API Banner</h3>
                    </div>
                     <div @click="selectElementType('simple-banner')" class="cursor-pointer bg-[#656565] p-8 rounded-lg transform hover:-translate-y-1 transition-all duration-300 group flex justify-center items-center flex-col gap-2">
                        <span class="dashicons dashicons-text-page text-4xl mb-4 text-[#00baa4] group-hover:text-white transition-colors flex justify-center"></span>
                        <h3 class="font-semibold text-lg text-gray-200 group-hover:text-white">Simple Banner</h3>
                    </div>
                    <div @click="selectElementType('sticky-simple-banner')" class="cursor-pointer bg-[#656565] p-8 rounded-lg transform hover:-translate-y-1 transition-all duration-300 group flex justify-center items-center flex-col gap-2">
                        <span class="dashicons dashicons-sticky text-4xl mb-4 text-[#00baa4] group-hover:text-white transition-colors flex justify-center"></span>
                        <h3 class="font-semibold text-lg text-gray-200 group-hover:text-white">Sticky Simple Banner</h3>
                    </div>
                    <div @click="selectElementType('promotion-banner')" class="cursor-pointer bg-[#656565] p-8 rounded-lg transform hover:-translate-y-1 transition-all duration-300 group flex justify-center items-center flex-col gap-2">
                        <span class="dashicons dashicons-megaphone text-4xl mb-4 text-[#00baa4] group-hover:text-white transition-colors flex justify-center"></span>
                        <h3 class="font-semibold text-lg text-gray-200 group-hover:text-white">Promotion Banner</h3>
                    </div>
                    <div @click="selectElementType('content-html-banner')" class="cursor-pointer bg-[#656565] p-8 rounded-lg transform hover:-translate-y-1 transition-all duration-300 group flex justify-center items-center flex-col gap-2">
                        <span class="dashicons dashicons-editor-code text-4xl mb-4 text-[#00baa4] group-hover:text-white transition-colors flex justify-center"></span>
                        <h3 class="font-semibold text-lg text-gray-200 group-hover:text-white">Content HTML</h3>
                    </div>
                    <div @click="selectElementType('content-html-sidebar-banner')" class="cursor-pointer bg-[#656565] p-8 rounded-lg transform hover:-translate-y-1 transition-all duration-300 group flex justify-center items-center flex-col gap-2">
                        <span class="dashicons dashicons-align-pull-right text-4xl mb-4 text-[#00baa4] group-hover:text-white transition-colors flex justify-center"></span>
                        <h3 class="font-semibold text-lg text-gray-200 group-hover:text-white">Content HTML Sidebar</h3>
                    </div>
                    <div @click="selectElementType('tour-carousel')" class="cursor-pointer bg-[#656565] p-8 rounded-lg transform hover:-translate-y-1 transition-all duration-300 group flex justify-center items-center flex-col gap-2">
                        <span class="dashicons dashicons-slides text-4xl mb-4 text-[#00baa4] group-hover:text-white transition-colors flex justify-center"></span>
                        <h3 class="font-semibold text-lg text-gray-200 group-hover:text-white">Tour Carousel</h3>
                    </div>
                    <div @click="selectElementType('hotel-carousel')" class="cursor-pointer bg-[#656565] p-8 rounded-lg transform hover:-translate-y-1 transition-all duration-300 group flex justify-center items-center flex-col gap-2">
                        <span class="dashicons dashicons-building text-4xl mb-4 text-[#00baa4] group-hover:text-white transition-colors flex justify-center"></span>
                        <h3 class="font-semibold text-lg text-gray-200 group-hover:text-white">Hotel Carousel</h3>
                    </div>
                     <div @click="selectElementType('flight-ticket')" class="cursor-pointer bg-[#656565] p-8 rounded-lg transform hover:-translate-y-1 transition-all duration-300 group flex justify-center items-center flex-col gap-2">
                        <span class="dashicons dashicons-airplane text-4xl mb-4 text-[#00baa4] group-hover:text-white transition-colors flex justify-center"></span>
                        <h3 class="font-semibold text-lg text-gray-200 group-hover:text-white">Flight Ticket</h3>
                    </div>
                    </div>
            </div>
        </div>

        <div v-else-if="appState === 'editor'" key="editor">
            <?php
            // A map of banner types to their respective editor file paths.
            $banner_editors = [
                'double-banner'                 => 'double-banner/double-banner-editor.php',
                'single-banner'                 => 'single-banner/single-banner-editor.php',
                'api-banner'                    => 'api-banner/api-banner-editor.php',
                'simple-banner'                 => 'simple-banner/simple-banner-editor.php',
                'sticky-simple-banner'          => 'sticky-simple-banner/sticky-simple-banner-editor.php',
                'promotion-banner'              => 'promotion-banner/promotion-banner-editor.php',
                'content-html-banner'           => 'content-html-banner/content-html-editor.php',
                'content-html-sidebar-banner'   => 'content-html-sidebar-banner/content-html-sidebar-editor.php',
                'tour-carousel'                 => 'tour-carousel/tour-carousel-editor.php',
                'hotel-carousel'                => 'hotel-carousel/hotel-carousel-editor.php', // Added hotel carousel editor path
                'flight-ticket'                 => 'flight-ticket/flight-ticket-editor.php'
            ];

            foreach ($banner_editors as $type => $file_path) {
                // Generate the v-if directive dynamically
                echo "<div v-if=\"banner.type === '$type'\">";
                 // Construct the full path and include the file
                 $full_path = YAB_PLUGIN_DIR . "admin/views/banner-types/{$file_path}";
                 if (file_exists($full_path)) {
                    require_once $full_path;
                 } else {
                     // Optionally echo an error or log if the file is missing
                     echo "<p style='color:red;'>Error: Editor file not found for type '$type' at: $full_path</p>";
                 }
                echo "</div>";
            }
            ?>
        </div>

        <div v-else-if="appState === 'loading'" key="loading" class="flex items-center justify-center h-screen">
            <div class="yab-spinner w-12 h-12"></div>
        </div>
    </transition>

    <yab-modal ref="modalComponent"></yab-modal>

    <?php
    require_once YAB_PLUGIN_DIR . 'admin/views/components/hotel-modal.php';
    require_once YAB_PLUGIN_DIR . 'admin/views/components/tour-modal.php';
    require_once YAB_PLUGIN_DIR . 'admin/views/components/flight-ticket-modal.php';
    ?>

</div>

<style>
    /* ... (keep existing styles) ... */
     [v-cloak] { display: none; }
    #wpcontent { padding-left: 0; }
    .yab-color-picker { -webkit-appearance: none; -moz-appearance: none; appearance: none; width: 40px; height: 38px; background-color: transparent; border: none; cursor: pointer; padding: 0 }
    .yab-color-picker::-webkit-color-swatch { border-radius: 6px; border: 2px solid #656565; }
    .yab-color-picker::-moz-color-swatch { border-radius: 6px; border: 2px solid #656565; }
    .section-title { color: #fff; font-size: 15px; margin-bottom: 8px; background: #656565; padding: 5px 10px; border-radius: 7px; font-weight: 600; }
    .section-divider { height: 1px; margin: 12px 0; background-color: #656565; border: 0; }
    .text-input { width: 100%; background: #292929; border: 1px solid #292929; border-radius: 5px; padding: 8px; color: #fff; }
    .select-input { width: 100%; background: #292929; border: 1px solid #292929; border-radius: 5px; padding: 8px; color: #fff; }
    .tab-button { text-align: center; padding: 6px 0; cursor: pointer; color: #fff; transition: all 0.2s; border: none; background: transparent; } /* Ensure buttons are borderless */
    .active-tab { background: #00baa4; font-weight: bold; }
    .preview-title { font-weight: bold; font-size: 1.125rem; margin-bottom: 1rem; color: white; text-align: left; background: #656565; padding: 10px; border-radius: 7px; }
    .condition-box { background: #292929; padding: 12px; border-radius: 8px; }
    .condition-label { display: block; font-size: 0.875rem; font-weight: 500; color: #d1d5db; margin-bottom: 8px; text-align: left; }
    .search-input { width: 100%; background: #434343; border: 1px solid #656565; color: white; padding: 6px 10px; border-radius: 5px; margin-bottom: 8px; }
    .checkbox-label { display: flex; align-items: center; gap: 8px; padding: 4px; border-radius: 6px; cursor: pointer; transition: background-color 0.2s; }
    .checkbox-label:hover { background-color: #333; }
    .checkbox-custom { width: 20px; height: 20px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; border: 2px solid #888; border-radius: 6px; transition: all 0.2s; }
    .peer:checked ~ .checkbox-custom { background-color: #00baa4; border-color: #00baa4; }
    .checkbox-text { color: #d1d5db; transition: color 0.2s; }
    .checkbox-label:hover .checkbox-text { color: white; }

     /* Specific styles for better form elements */
     .yab-form-input { width: 100%; height: 40px; background: #292929; border: 1px solid #656565; border-radius: 6px; padding: 0 12px; color: #ffffff; transition: border-color 0.2s, box-shadow 0.2s; }
     .yab-form-input:focus { outline: none; border-color: #00baa4; box-shadow: 0 0 0 2px rgba(0, 186, 164, 0.5); }
     textarea.yab-form-input { height: auto; padding-top: 8px; padding-bottom: 8px; }
     .setting-label-sm { display: block; font-size: 0.75rem; font-weight: 500; color: #9ca3af; margin-bottom: 4px; }
     .yab-color-input-wrapper { display: flex; align-items: center; width: 100%; height: 40px; background: #292929; border: 1px solid #656565; color: #ffffff; border-radius: 6px; overflow: hidden; transition: border-color 0.2s, box-shadow 0.2s; }
     .yab-color-input-wrapper .yab-color-picker { -webkit-appearance: none; -moz-appearance: none; appearance: none; width: 40px; height: 40px; background-color: transparent; border: none; cursor: pointer; padding: 0; flex-shrink: 0; margin-left: 1px; border-radius: 0; }
     .yab-color-input-wrapper .yab-color-picker::-webkit-color-swatch { border-radius: 0; border: none; }
     .yab-color-input-wrapper .yab-color-picker::-moz-color-swatch { border-radius: 0; border: none; }
     .yab-color-input-wrapper .yab-hex-input { flex-grow: 1; background: transparent; border: none; color: #ffffff; padding-left: 12px; height: 100%; border-left: 1px solid #656565; border-top-left-radius: 0; border-bottom-left-radius: 0; margin-left: 1px; }
     .yab-color-input-wrapper .yab-hex-input:focus { outline: none !important; border: none !important; box-shadow: none !important; }
     .yab-spinner { border: 4px solid rgba(0, 186, 164, 0.2); border-top: 4px solid #00baa4; border-radius: 50%; animation: yab-spin 1s linear infinite; }
     @keyframes yab-spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
</style>