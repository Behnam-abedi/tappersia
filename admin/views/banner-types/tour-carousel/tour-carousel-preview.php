<?php
// tappersia/admin/views/banner-types/tour-carousel/tour-carousel-preview.php
?>
<div class="bg-[#434343] p-4 rounded-lg">
    <h3 class="preview-title">Live Preview</h3>

    <div v-if="!banner.tour_carousel.selectedTours || banner.tour_carousel.selectedTours.length === 0" class="flex items-center justify-center h-80 bg-[#292929] rounded-lg">
        <p class="text-gray-500">Please select tours to see the preview.</p>
    </div>

    <div v-else>
        <transition name="fade" mode="out-in">
            <div v-if="currentView === 'desktop'" class="bg-[#292929] rounded-lg p-4 flex justify-center">
                <tour-carousel-logic 
                    :tour-ids="banner.tour_carousel.selectedTours" 
                    :ajax="ajax"
                    :settings="banner.tour_carousel.settings"
                    :key="'desktop_' + banner.tour_carousel.updateCounter + '_' + banner.tour_carousel.selectedTours.join(',') + JSON.stringify(banner.tour_carousel.settings)">
                </tour-carousel-logic>
            </div>
            
            <div v-else-if="currentView === 'mobile'" class="flex flex-col items-center">
                <span class="text-xs text-gray-400 mb-2">Mobile View</span>
                 <div class="w-[375px] h-auto bg-[#292929] rounded-2xl p-4 flex justify-center items-center mx-auto">
                    <tour-carousel-logic 
                        :tour-ids="banner.tour_carousel.selectedTours" 
                        :ajax="ajax"
                        :settings="banner.tour_carousel.settings_mobile"
                        :key="'mobile_' + banner.tour_carousel.updateCounter + '_' + banner.tour_carousel.selectedTours.join(',') + JSON.stringify(banner.tour_carousel.settings_mobile)">
                    </tour-carousel-logic>
                 </div>
            </div>
        </transition>
    </div>
</div>