<?php
// tappersia/admin/views/banner-types/flight-ticket/flight-ticket-preview.php
?>
<div class="bg-[#434343] p-4 rounded-lg">
    <h3 class="preview-title">Live Preview</h3>
    <div class="flex flex-col items-center justify-center h-80 bg-[#292929] rounded-lg p-6">
        
        <div v-if="!banner.flight_ticket.from || !banner.flight_ticket.to" class="text-center">
            <span class="dashicons dashicons-airplane text-6xl text-gray-600 mb-4"></span>
            <p class="text-gray-500">Please select an origin and destination.</p>
        </div>
        
        <div v-else-if="banner.flight_ticket.isLoadingFlights" class="text-center">
            <div class="yab-spinner w-12 h-12 mb-4"></div>
            <p class="text-gray-400 text-lg">Searching for flights from {{ banner.flight_ticket.from.iataCode }} to {{ banner.flight_ticket.to.iataCode }}...</p>
        </div>
        
         <div v-else-if="banner.flight_ticket.lastSearchError" class="text-center">
             <span class="dashicons dashicons-warning text-6xl text-red-500 mb-4"></span>
            <p class="text-red-400 text-lg">Error: {{ banner.flight_ticket.lastSearchError }}</p>
        </div>
        
        <div v-else-if="banner.flight_ticket.cheapestPrice !== null" class="text-center">
            <span class="text-gray-400 text-lg">Cheapest Flight (Tomorrow)</span>
             <div class="flex items-center justify-center gap-4 my-4">
                <span class="text-2xl text-white">{{ banner.flight_ticket.from.city }} ({{ banner.flight_ticket.from.iataCode }})</span>
                <span class="dashicons dashicons-arrow-right-alt text-4xl text-gray-500"></span>
                <span class="text-2xl text-white">{{ banner.flight_ticket.to.city }} ({{ banner.flight_ticket.to.iataCode }})</span>
            </div>
            <span class="text-5xl font-bold text-green-400">€{{ banner.flight_ticket.cheapestPrice.toFixed(2) }}</span>
        </div>

        <div v-else class="text-center">
             <span class="dashicons dashicons-info text-6xl text-gray-600 mb-4"></span>
            <p class="text-gray-500">Click "Select Airports" to begin.</p>
        </div>

    </div>
</div>