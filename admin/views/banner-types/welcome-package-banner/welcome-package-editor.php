<?php
// /admin/views/banner-types/welcome-package-banner/welcome-package-editor.php
require YAB_PLUGIN_DIR . 'admin/views/components/banner-editor-header.php';
?>

<main class="grid grid-cols-12 gap-6 p-6 ltr">
    <!-- Settings Column -->
    <div class="col-span-4 overflow-y-auto flex flex-col gap-6 [&>*:last-child]:mb-[40px]" style="max-height: calc(100vh - 120px);">
        <?php require YAB_PLUGIN_DIR . 'admin/views/banner-types/welcome-package-banner/welcome-package-settings.php'; ?>
    </div>

    <!-- Preview and Display Conditions Column -->
    <div class="col-span-8 sticky top-[120px] space-y-4">
        <?php require YAB_PLUGIN_DIR . 'admin/views/banner-types/welcome-package-banner/welcome-package-preview.php'; ?>

        <transition name="yab-modal-fade">
            <div v-if="banner.displayMethod === 'Fixed'">
                <?php require YAB_PLUGIN_DIR . 'admin/views/components/display-conditions.php'; ?>
            </div>
        </transition>
    </div>

    <!-- Welcome Package Modal (Loaded globally in view-add-new-banner.php now) -->
     <?php // require YAB_PLUGIN_DIR . 'admin/views/components/welcome-package-modal.php'; ?>

</main>
