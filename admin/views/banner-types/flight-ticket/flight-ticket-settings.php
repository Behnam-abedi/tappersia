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
            <span>{{ banner.flight_ticket.from ? `${banner.flight_ticket.from.city} (${banner.flight_ticket.from.iataCode})` : 'Not Selected' }}</span>
        </div>
        <hr class="border-gray-600 my-2">
        <div class="flex justify-between items-center">
            <span class="font-semibold">Destination:</span>
            <span>{{ banner.flight_ticket.to ? `${banner.flight_ticket.to.city} (${banner.flight_ticket.to.iataCode})` : 'Not Selected' }}</span>
        </div>
    </div>
    
    <div class="mt-4 p-3 bg-[#292929] rounded-lg text-sm text-gray-300">
        <h4 class="font-semibold text-base text-white mb-2">Cheapest Flight (Tomorrow)</h4>
        <div v-if="banner.flight_ticket.isLoadingFlights" class="flex items-center justify-center py-4">
             <div class="yab-spinner w-6 h-6"></div>
        </div>
        <div v-else-if="banner.flight_ticket.lastSearchError" class="text-center text-red-400">
            {{ banner.flight_ticket.lastSearchError }}
        </div>
         <div v-else-if="banner.flight_ticket.cheapestPrice !== null" class="text-center">
             <span class="text-2xl font-bold text-green-400">€{{ banner.flight_ticket.cheapestPrice.toFixed(2) }}</span>
        </div>
        <div v-else class="text-center text-gray-500">
            Select origin and destination to search for prices.
        </div>
    </div>
</div>