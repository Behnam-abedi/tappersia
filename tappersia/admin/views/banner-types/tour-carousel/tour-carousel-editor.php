<?php
// tappersia/admin/views/banner-types/tour-carousel/tour-carousel-editor.php
require YAB_PLUGIN_DIR . 'admin/views/components/banner-editor-header.php';
?>

<main class="p-6 ltr">
    
    <div class="w-full mb-6">
        <?php require YAB_PLUGIN_DIR . 'admin/views/banner-types/tour-carousel/tour-carousel-preview.php'; ?>
    </div>
    
    <div class="grid grid-cols-12 gap-6">
        
        <div class="col-span-4 overflow-y-auto flex flex-col gap-6 [&>*:last-child]:mb-[40px] mr-2" style="max-height: calc(100vh - 150px);">
            <?php require YAB_PLUGIN_DIR . 'admin/views/banner-types/tour-carousel/tour-carousel-settings.php'; ?>
        </div>
        
        <div class="col-span-8 sticky top-[120px] space-y-6">
            
            <div v-if="banner.tour_carousel.selectedTours && banner.tour_carousel.selectedTours.length > 0" class="bg-[#434343] p-4 rounded-lg">
                <h3 class="preview-title">Slide Order (Drag to reorder)</h3>
                <div class="relative bg-[#292929] rounded-lg min-h-[116px] p-2">
                    <div v-if="isLoadingThumbnails" class="absolute inset-0 flex items-center justify-center bg-[#292929]/80 z-10">
                        <div class="yab-spinner w-8 h-8"></div>
                    </div>
                    <div ref="thumbnailContainerRef" class="flex gap-3 overflow-x-auto pb-[8px]">
                        <div v-for="tour in thumbnailTours" :key="tour.id" :data-id="tour.id" class="cursor-move flex-shrink-0">
                            <img :src="tour.bannerImage.url" class="w-24 h-24 object-cover rounded-md border-2 border-transparent hover:border-[#00baa4]" :alt="tour.title" />
                        </div>
                    </div>
                </div>
            </div>

            <transition name="fade">
                <div v-if="banner.displayMethod === 'Fixed'">
                    <?php require YAB_PLUGIN_DIR . 'admin/views/components/display-conditions.php'; ?>
                </div>
            </transition>

        </div>

    </div>

</main>