<?php
// tappersia/admin/views/banner-types/double-banner/double-banner-editor.php
require YAB_PLUGIN_DIR . 'admin/views/components/banner-editor-header.php'; 
?>

<main class="grid grid-cols-12 gap-6 p-6 ltr">
    <div class="col-span-4 overflow-y-auto flex flex-col gap-6 [&>*:last-child]:mb-[40px] mr-2" style="max-height: calc(100vh - 120px);">
        <?php require YAB_PLUGIN_DIR . 'admin/views/banner-types/double-banner/double-banner-settings.php'; ?>
    </div>

    <div class="col-span-8 sticky top-[120px] space-y-4">
        <?php require YAB_PLUGIN_DIR . 'admin/views/banner-types/double-banner/double-banner-preview.php'; ?>
        
        <transition name="fade">
            <div v-if="banner.displayMethod === 'Fixed'">
                <?php require YAB_PLUGIN_DIR . 'admin/views/components/display-conditions.php'; ?>
            </div>
        </transition>
    </div>
</main>