<?php
// tappersia/admin/views/components/flight-ticket-modal.php
?>
<transition name="yab-modal-fade">
    <div v-if="isFlightModalOpen" dir="ltr" class="fixed inset-0 bg-black bg-opacity-80 z-[99999] flex items-center justify-center p-4" @keydown.esc="closeFlightModal">
        <div class="bg-[#2d2d2d] w-full max-w-4xl h-[80vh] rounded-xl shadow-2xl flex flex-col overflow-hidden">
            <header class="bg-[#434343] p-4 flex items-center justify-between flex-shrink-0">
                <h2 class="text-xl font-bold text-white">Select Origin & Destination</h2>
                <button @click="closeFlightModal" class="text-gray-400 hover:text-white text-3xl leading-none">&times;</button>
            </header>
            <div class="p-4 border-b border-gray-700">
                <div class="flex items-center gap-3">
                    <svg class="w-7 h-7 text-gray-500 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" /></svg>
                    <div class="relative flex-grow">
                        <input type="search" v-model="airportSearchQuery" placeholder="Search by airport name, city, or IATA code..."
                               class="w-full bg-[#232323] text-white border-2 border-gray-600 rounded-lg h-14 px-4 text-base focus:outline-none focus:ring-2 focus:ring-[#00baa4] focus:border-[#00baa4] transition-colors">
                    </div>
                </div>
            </div>
            <main class="flex-grow relative overflow-y-auto" ref="airportModalListRef">
                <div v-if="isAirportsLoading" class="absolute inset-0 flex items-center justify-center bg-[#2d2d2d]/80 z-10">
                    <div class="yab-spinner w-12 h-12"></div>
                </div>
                <div v-else-if="filteredAirports.length === 0" class="text-center text-gray-400 py-16">
                    <p class="text-lg">No airports found.</p>
                </div>
                <ul v-else class="p-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    <li v-for="airport in filteredAirports" :key="airport.iataCode" @click="selectAirport(airport)" 
                        class="p-3 bg-[#434343] rounded-lg cursor-pointer border-2 transition-all duration-200"
                        :class="{
                            'border-green-500 shadow-lg': tempSelectedAirports.from && tempSelectedAirports.from.iataCode === airport.iataCode,
                            'border-blue-500 shadow-lg': tempSelectedAirports.to && tempSelectedAirports.to.iataCode === airport.iataCode,
                            'border-transparent hover:border-gray-600': !isAirportSelected(airport)
                        }">
                        <div class="flex flex-col text-left">
                            <span class="font-bold text-base text-white">{{ airport.city }} ({{ airport.iataCode }})</span>
                            <span class="text-sm text-gray-400">{{ airport.name }}</span>
                        </div>
                    </li>
                </ul>
            </main>
            <footer class="bg-[#434343] p-4 flex-shrink-0 flex justify-between items-center">
                <div class="text-sm text-gray-300 flex gap-4">
                    <span><span class="font-semibold text-green-400">Origin:</span> {{ tempSelectedAirports.from ? tempSelectedAirports.from.iataCode : '...' }}</span>
                    <span><span class="font-semibold text-blue-400">Destination:</span> {{ tempSelectedAirports.to ? tempSelectedAirports.to.iataCode : '...' }}</span>
                </div>
                <button @click="confirmAirportSelection" :disabled="!tempSelectedAirports.from || !tempSelectedAirports.to" class="bg-[#00baa4] text-white font-bold px-6 py-2 rounded-lg hover:bg-opacity-80 transition-all disabled:bg-gray-500 disabled:cursor-not-allowed">
                    Confirm Selection
                </button>
            </footer>
        </div>
    </div>
</transition>