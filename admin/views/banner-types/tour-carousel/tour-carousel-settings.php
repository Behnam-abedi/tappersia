<?php
// tappersia/admin/views/banner-types/tour-carousel/tour-carousel-settings.php
?>
<div class="bg-[#434343] p-5 rounded-lg shadow-xl mr-2">
    <h3 class="font-bold text-xl text-white tracking-wide mb-5">Content Source</h3>
    <div class="flex gap-4">
        <button @click="openTourModal({ multiSelect: true })" class="w-full flex gap-2 justify-center items-center bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700">
            <span class="dashicons dashicons-palmtree"></span>
            {{ banner.tour_carousel.selectedTours && banner.tour_carousel.selectedTours.length > 0 ? 'Edit Selected Tours' : 'Select Tours' }}
        </button>
    </div>
</div>

<div class="bg-[#434343] p-5 rounded-lg shadow-xl mr-2">
    <h3 class="font-bold text-xl text-white tracking-wide mb-5">Carousel Settings</h3>
    <div class="space-y-4">
        <p class="text-gray-400 text-sm">Carousel layout and design settings will be added here in the next steps.</p>
        </div>
</div>