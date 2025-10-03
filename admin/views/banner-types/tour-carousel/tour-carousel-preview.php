<?php
// tappersia/admin/views/banner-types/tour-carousel/tour-carousel-preview.php
?>
<div class="bg-[#434343] p-4 rounded-lg">
    <h3 class="preview-title">Live Preview</h3>

    <div v-if="!banner.tour_carousel.selectedTours || banner.tour_carousel.selectedTours.length === 0" class="flex items-center justify-center h-40 bg-[#292929] rounded-lg">
        <p class="text-gray-500">Please select tours to see the preview.</p>
    </div>

    <div v-else class="bg-[#292929] rounded-lg p-4">
        <h4 class="font-semibold text-lg text-white mb-3">Selected Tours ({{ banner.tour_carousel.selectedTours.length }})</h4>
        <ul class="list-disc list-inside text-gray-300 space-y-2 max-h-96 overflow-y-auto">
            <li v-for="tour in banner.tour_carousel.selectedTours" :key="tour.id">
                <span class="font-bold">{{ tour.title }}</span> (ID: {{ tour.id }})
            </li>
        </ul>
        <p class="text-xs text-gray-500 mt-4"><em>The actual carousel preview with Swiper.js will be implemented in the next steps.</em></p>
    </div>
</div>