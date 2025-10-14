<?php
// tappersia/admin/views/banner-types/flight-ticket/flight-ticket-settings.php
?>
<div class="bg-[#434343] p-5 rounded-lg shadow-xl mr-2">
    <h3 class="font-bold text-xl text-white tracking-wide mb-5">Flight Settings</h3>
    <div class="flex gap-4">
        <button @click="openFlightModal" class="w-full flex gap-2 justify-center items-center bg-sky-600 text-white px-4 py-2 rounded-md hover:bg-sky-700">
            <span class="dashicons dashicons-airplane"></span>
            <span>Select Airports</span>
        </button>
    </div>
    <div class="mt-4 p-3 bg-[#292929] rounded-lg text-sm text-gray-300">
        <div class="flex justify-between items-center">
            <span class="font-semibold">Origin:</span>
            <span>{{ banner.flight_ticket.fromAirportCode || 'Not Selected' }}</span>
        </div>
        <hr class="border-gray-600 my-2">
        <div class="flex justify-between items-center">
            <span class="font-semibold">Destination:</span>
            <span>{{ banner.flight_ticket.toAirportCode || 'Not Selected' }}</span>
        </div>
    </div>
</div>