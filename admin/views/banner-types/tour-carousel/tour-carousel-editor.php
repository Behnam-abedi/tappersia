<?php
// tappersia/admin/views/banner-types/tour-carousel/tour-carousel-editor.php
require YAB_PLUGIN_DIR . 'admin/views/components/banner-editor-header.php';
?>

<main class="grid grid-cols-12 gap-6 p-6 ltr">
    
    <div class="col-span-12 col-start-1 flex flex-col gap-6">
        
        <div class="w-full">
            <?php require YAB_PLUGIN_DIR . 'admin/views/banner-types/tour-carousel/tour-carousel-preview.php'; ?>
        </div>

        <div class="grid grid-cols-12 gap-6 ">
            
            <div class="flex flex-col gap-6 col-span-4">
                <?php require YAB_PLUGIN_DIR . 'admin/views/banner-types/tour-carousel/tour-carousel-settings.php'; ?>
            </div>
            
            <transition name="fade">
                <div v-if="banner.displayMethod === 'Fixed'" class="col-span-8">
                    <?php require YAB_PLUGIN_DIR . 'admin/views/components/display-conditions.php'; ?>
                </div>
            </transition>

        </div>

    </div>

</main>