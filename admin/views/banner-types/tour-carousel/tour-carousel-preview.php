<?php
// tappersia/admin/views/banner-types/tour-carousel/tour-carousel-preview.php
?>
<div class="bg-[#434343] p-4 rounded-lg">
    <h3 class="preview-title">Live Preview</h3>

    <div v-if="!banner.tour_carousel.selectedTours || banner.tour_carousel.selectedTours.length === 0" class="flex items-center justify-center h-80 bg-[#292929] rounded-lg">
        <p class="text-gray-500">Please select tours to see the preview.</p>
    </div>

    <div v-else class="bg-[#292929] rounded-lg p-4">
        <tour-carousel-logic 
            :tour-ids="banner.tour_carousel.selectedTours" 
            :ajax="ajax" 
            :key="banner.tour_carousel.selectedTours.join(',')">
        </tour-carousel-logic>
    </div>
</div>