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
                </div>
            </div>
        </div>

        <div v-else-if="appState === 'editor'" key="editor">
            <div v-if="banner.type === 'double-banner'">
                <?php require_once YAB_PLUGIN_DIR . 'admin/views/banner-types/double-banner-editor.php'; ?>
            </div>
            <div v-if="banner.type === 'single-banner'">
                <?php require_once YAB_PLUGIN_DIR . 'admin/views/banner-types/single-banner-editor.php'; ?>
            </div>
            <div v-if="banner.type === 'api-banner'">
                <?php require_once YAB_PLUGIN_DIR . 'admin/views/banner-types/api-banner-editor.php'; ?>
            </div>
            <div v-if="banner.type === 'simple-banner'">
                <?php require_once YAB_PLUGIN_DIR . 'admin/views/banner-types/simple-banner-editor.php'; ?>
            </div>
            <div v-if="banner.type === 'sticky-simple-banner'">
                <?php require_once YAB_PLUGIN_DIR . 'admin/views/banner-types/sticky-simple-banner-editor.php'; ?>
            </div>
        </div>

        <div v-else-if="appState === 'loading'" key="loading" class="flex items-center justify-center h-screen">
            <div class="yab-spinner w-12 h-12"></div>
        </div>
    </transition>

    <yab-modal ref="modalComponent"></yab-modal>

</div>

<style>
    [v-cloak] { display: none; }
    #wpcontent { padding-left: 0; }
    .yab-color-picker { -webkit-appearance: none; -moz-appearance: none; appearance: none; width: 40px; height: 38px; background-color: transparent; border: none; cursor: pointer; padding: 0 }
    .yab-color-picker::-webkit-color-swatch { border-radius: 6px; border: 2px solid #656565; }
    .yab-color-picker::-moz-color-swatch { border-radius: 6px; border: 2px solid #656565; }
    .section-title { color: #fff; font-size: 15px; margin-bottom: 8px; background: #656565; padding: 5px 10px; border-radius: 7px; font-weight: 600; }
    .section-divider { height: 1px; margin: 12px 0; background-color: #656565; border: 0; }
    .text-input { width: 100%; background: #292929; border: 1px solid #292929; border-radius: 5px; padding: 8px; color: #fff; }
    .select-input { width: 100%; background: #292929; border: 1px solid #292929; border-radius: 5px; padding: 8px; color: #fff; }
    .tab-button { text-align: center; padding: 6px 0; cursor: pointer; color: #fff; transition: all 0.2s; }
    .active-tab { background: #00baa4;  font-weight: bold; }
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
</style>