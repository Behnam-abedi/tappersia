<?php
// tappersia/admin/views/components/hotel-modal.php
?>
<transition name="yab-modal-fade">
    <div v-if="isHotelModalOpen" dir="ltr" class="fixed inset-0 bg-black bg-opacity-80 z-[99999] flex items-center justify-center p-4" @keydown.esc="closeHotelModal">
        <div class="bg-[#2d2d2d] w-full max-w-6xl h-[90vh] rounded-xl shadow-2xl flex flex-row overflow-hidden">

            <aside class="w-1/4 bg-[#292929] p-4 flex flex-col text-left overflow-y-auto">
                <div class="flex-grow">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-white">Filters</h3>
                        <button @click="resetFilters" class="text-sm text-white bg-red-600 hover:bg-red-700 px-3 py-1 rounded-md">Reset</button>
                    </div>
                    <div class="mb-6">
                        <label class="filter-label">Type</label>
                        <div class="flex flex-wrap gap-2">
                            <button v-for="type in hotelTypes" :key="type.key" @click="toggleType(type.key)"
                                    :class="filters.types.includes(type.key) ? 'bg-[#00baa4] text-white' : 'bg-[#434343] text-gray-300'"
                                    class="px-2 py-1 text-xs rounded-md transition-colors">
                                {{ type.label }}
                            </button>
                        </div>
                    </div>
                    <div class="mb-6 relative">
                        <label class="filter-label">City</label>
                        <button @click="isCityDropdownOpen = !isCityDropdownOpen" class="w-full filter-input text-left flex justify-between items-center">
                            <span>{{ selectedCityName }}</span>
                            <svg class="w-4 h-4 text-gray-400 transition-transform" :class="{ 'rotate-180': isCityDropdownOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div v-if="isCityDropdownOpen" class="absolute z-10 w-full mt-1 bg-[#434343] border border-gray-600 rounded-md shadow-lg max-h-60 overflow-y-auto">
                            <ul class="py-1">
                                <li @click="selectCity('')" class="px-4 py-2 text-sm text-gray-300 hover:bg-[#656565] cursor-pointer">All Cities</li>
                                <li v-for="city in cities" :key="city.id" @click="selectCity(city.id)"
                                    class="px-4 py-2 text-sm text-gray-300 hover:bg-[#656565] cursor-pointer flex justify-between items-center"
                                    :class="{ 'bg-[#00baa4] text-white': filters.province === city.id }">
                                    <span>{{ city.name }}</span>
                                    <span class="text-xs text-gray-400">{{ city.hotelCount }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="mb-6">
                        <label class="filter-label">Stars</label>
                        <div class="flex justify-around bg-[#434343] p-2 rounded-md">
                            <span v-for="star in 5" :key="star" @click="setStarRating(star)"
                                class="cursor-pointer text-2xl transition-all transform hover:scale-125"
                                :class="star <= filters.stars ? 'text-yellow-400' : 'text-gray-600'">★</span>
                        </div>
                    </div>
                    <div class="mb-6">
                        <label class="filter-label">Price Range: €{{ filters.minPrice }} - €{{ filters.maxPrice }}</label>
                        <div class="space-y-3">
                            <div>
                                <label class="text-xs text-gray-400">Min Price</label>
                                <input type="range" v-model.number="filters.minPrice" min="0" max="1000" step="10" class="w-full">
                            </div>
                            <div>
                                <label class="text-xs text-gray-400">Max Price</label>
                                <input type="range" v-model.number="filters.maxPrice" min="0" max="1000" step="10" class="w-full">
                            </div>
                        </div>
                    </div>
                    <div class="mb-6">
                        <label class="filter-label">Sort By</label>
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <button @click="toggleSort('rate')" :class="filters.sort === 'rate' ? 'bg-[#00baa4] text-white' : 'bg-[#434343] text-gray-300'" class="px-2 py-2 rounded-md transition-colors">Rating</button>
                            <button @click="toggleSort('price_low_high')" :class="filters.sort === 'price_low_high' ? 'bg-[#00baa4] text-white' : 'bg-[#434343] text-gray-300'" class="px-2 py-2 rounded-md transition-colors">Price (Low-High)</button>
                            <button @click="toggleSort('price_high_low')" :class="filters.sort === 'price_high_low' ? 'bg-[#00baa4] text-white' : 'bg-[#434343] text-gray-300'" class="px-2 py-2 rounded-md transition-colors">Price (High-Low)</button>
                            <button @click="toggleSort('star_high_low')" :class="filters.sort === 'star_high_low' ? 'bg-[#00baa4] text-white' : 'bg-[#434343] text-gray-300'" class="px-2 py-2 rounded-md transition-colors">Stars (High-Low)</button>
                        </div>
                    </div>
                </div>
                <div class="mt-auto pt-4">
                     <button @click="confirmHotelSelection" :disabled="tempSelectedHotels.length === 0" class="w-full bg-[#00baa4] text-white font-bold px-4 py-3 rounded-lg hover:bg-opacity-80 transition-all disabled:bg-gray-500 disabled:cursor-not-allowed">
                        Confirm Selection{{ isMultiSelect ? ' (' + tempSelectedHotels.length + ')' : '' }}
                    </button>
                </div>
            </aside>

            <div class="w-3/4 flex flex-col relative">
                <header class="bg-[#434343] p-4 flex items-center justify-between flex-shrink-0">
                     <div>
                        <h2 class="text-xl font-bold text-white">Select a Hotel</h2>
                        <p v-if="isMultiSelect" class="text-xs text-gray-400">You can select multiple hotels.</p>
                    </div>
                    <button @click="closeHotelModal" class="text-gray-400 hover:text-white text-3xl leading-none">&times;</button>
                </header>
                <div class="p-4 border-b border-gray-700">
                    <div class="flex items-center gap-3">
                        <svg class="w-7 h-7 text-gray-500 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" /></svg>
                        <div class="relative flex-grow">
                            <input type="search" v-model="filters.keyword" @input="debouncedHotelSearch" placeholder="Search for hotels by name..."
                                class="w-full bg-[#232323] text-white border-2 border-gray-600 rounded-lg h-14 px-4 text-base focus:outline-none focus:ring-2 focus:ring-[#00baa4] focus:border-[#00baa4] transition-colors">
                        </div>
                    </div>
                </div>
                <main class="flex-grow relative overflow-y-auto" ref="hotelModalListRef">
                    <div v-if="isHotelLoading || isHotelSelectionLoading" class="absolute inset-0 flex items-center justify-center bg-[#2d2d2d]/80 z-10">
                        <div class="yab-spinner w-12 h-12"></div>
                    </div>

                    <div v-else-if="sortedHotelResults.length === 0" class="text-center text-gray-400 py-16">
                        <p class="text-lg">No hotels found matching your criteria.</p>
                    </div>
                    
                    <template v-else>
                        <ul class="p-4 space-y-3">
                            <li v-for="hotel in sortedHotelResults" :key="hotel.id" @click="selectHotel(hotel)"
                                class="p-3 bg-[#434343] rounded-lg flex items-center gap-4 cursor-pointer border-2 transition-all duration-200"
                                 :class="isHotelSelected(hotel) ? 'border-[#00baa4] shadow-lg' : 'border-transparent hover:border-gray-600'">
                                <image-loader
                                    :src="hotel.coverImage ? hotel.coverImage.url : 'https://placehold.co/100x100/292929/434343?text=No+Image'"
                                    :alt="hotel.title"
                                    img-class="w-full h-full object-cover rounded-md"
                                ></image-loader>
                                <div class="flex-grow text-left flex flex-col gap-[21px]">
                                    <div>
                                        <h4 class="font-bold text-lg text-white mb-2">{{ hotel.title }}</h4>
                                        <div class="flex items-center">
                                            <div class="flex">
                                                <span v-for="n in 5" :key="n" class="text-yellow-400" :style="{ width: '15px', height: '16px', fontSize: '16px', lineHeight: '1' }">{{ n <= hotel.star ? '★' : '☆' }}</span>
                                            </div>
                                            <div class="flex items-center ml-[15px] pl-[15px] border-l border-gray-600">
                                                <span class="text-sm text-gray-400">{{ hotel.province.name }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div v-if="hotel.avgRating != null" class="flex items-center justify-center rounded" style="min-width: 28px; padding: 0 6px; height: 15px; background-color: #5191FA;"><span class="text-white font-bold text-[10px]">{{ formatRating(hotel.avgRating) }}</span></div>
                                            <span class="ml-[7px] text-[10px] font-semibold" style="color: #5191FA;">{{ getRatingLabel(hotel.avgRating) }}</span>
                                            <span v-if="hotel.reviewCount != null" class="ml-[7px] text-[10px]" style="color: #999999;">({{ hotel.reviewCount }} reviews)</span>
                                        </div>
                                        <div class="flex items-baseline gap-1.5">
                                            <span class="text-[10px]" style="color: #666666;">from</span>
                                            <span class="text-base font-bold" style="color: #00BAA4;">€{{ hotel.minPrice.toFixed(2) }}</span>
                                            <span class="text-[10px]" style="color: #666666;">/ night</span>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                        <div v-if="isMoreHotelLoading" class="flex justify-center py-4">
                            <div class="yab-spinner w-8 h-8"></div>
                        </div>
                    </template>
                </main>
            </div>
        </div>
    </div>
</transition>