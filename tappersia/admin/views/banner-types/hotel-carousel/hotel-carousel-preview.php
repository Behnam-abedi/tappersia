<?php
// tappersia/admin/views/banner-types/hotel-carousel/hotel-carousel-preview.php
?>
<div class="bg-[#434343] p-4 rounded-lg">
    <h3 class="preview-title">Live Preview</h3>

    <div v-if="!banner.hotel_carousel.selectedHotels || banner.hotel_carousel.selectedHotels.length === 0" class="flex items-center justify-center h-80 bg-[#292929] rounded-lg">
        <p class="text-gray-500">Please select hotels to see the preview.</p>
    </div>

    <div v-else>
        <transition name="fade" mode="out-in">
            <div v-if="currentView === 'desktop'" class="bg-[#292929] rounded-lg p-4 flex justify-center">
                 <hotel-carousel-logic
                    :hotel-ids="banner.hotel_carousel.selectedHotels" :ajax="ajax"
                    :settings="banner.hotel_carousel.settings" :key="'desktop_' + banner.hotel_carousel.updateCounter + '_' + banner.hotel_carousel.selectedHotels.join(',') + JSON.stringify(banner.hotel_carousel.settings)">
                </hotel-carousel-logic>
            </div>

            <div v-else-if="currentView === 'mobile'" class="flex flex-col items-center">
                <span class="text-xs text-gray-400 mb-2">Mobile View</span>
                 <div class="w-[375px] h-auto bg-[#292929] rounded-2xl p-4 flex justify-center items-center mx-auto">
                     <hotel-carousel-logic
                        :hotel-ids="banner.hotel_carousel.selectedHotels" :ajax="ajax"
                        :settings="banner.hotel_carousel.settings_mobile" :key="'mobile_' + banner.hotel_carousel.updateCounter + '_' + banner.hotel_carousel.selectedHotels.join(',') + JSON.stringify(banner.hotel_carousel.settings_mobile)">
                    </hotel-carousel-logic>
                 </div>
            </div>
        </transition>
    </div>
</div>