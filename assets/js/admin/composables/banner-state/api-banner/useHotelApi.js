// tappersia/assets/js/admin/composables/banner-state/api-banner/useHotelApi.js
const { ref, reactive, computed, nextTick, watch } = Vue;
import { getRatingLabel, formatRating } from './utils.js';

export function useHotelApi(banner, showModal, ajax) {
    const isHotelModalOpen = ref(false);
    const isHotelLoading = ref(false);
    const isMoreHotelLoading = ref(false);
    const isHotelDetailsLoading = ref(false);
    const isHotelSelectionLoading = ref(false); // New state for loading selected hotels
    const hotelResults = reactive([]);
    const hotelCurrentPage = ref(1);
    const canLoadMoreHotels = ref(true);
    const hotelModalListRef = ref(null); // Renamed for clarity
    const tempSelectedHotels = ref([]); // Changed from tempSelectedHotel to array
    const isMultiSelect = ref(false); // New state to track multi-select mode
    const cities = ref([]);
    const isCityDropdownOpen = ref(false);
    let debounceTimeout = null;

    const filters = reactive({
        keyword: '',
        types: [],
        minPrice: 0,
        maxPrice: 1000,
        province: '',
        stars: 0,
        sort: ''
    });

    const hotelTypes = [
        { key: 'Hotel', label: 'Hotel' }, { key: 'TraditionalHouse', label: 'Traditional House' },
        { key: 'BoutiqueHotel', label: 'Boutique Hotel' }, { key: 'Apartments', label: 'Apartments' },
        { key: 'EcoLodge', label: 'Eco Lodge' }, { key: 'GuestHouse', label: 'Guest House' }, { key: 'Hostel', label: 'Hostel' }
    ];

    // Updated computed property for sorting based on multiple selections
    const sortedHotelResults = computed(() => {
        if (tempSelectedHotels.value.length === 0) return hotelResults;

        const selectedHotelsMap = new Map(tempSelectedHotels.value.map(h => [h.id, h]));
        // Ensure selected hotels appear first, maintaining their selection order if needed, followed by others
        const selected = tempSelectedHotels.value;
        const unselected = hotelResults.filter(h => !selectedHotelsMap.has(h.id));

        return [...selected, ...unselected];
    });


    const selectedCityName = computed(() => {
        if (!filters.province) return 'All Cities';
        const city = cities.value.find(c => c.id === filters.province);
        return city ? city.name : 'All Cities';
    });

    // Kept for single selection use case (e.g., API Banner)
    const fetchFullHotelDetails = async (hotelId) => {
        if (!hotelId) {
            banner.api.selectedHotel = null;
            return;
        }
        isHotelDetailsLoading.value = true;
        try {
            await new Promise(resolve => setTimeout(resolve, 100));
            const hotelDetails = await ajax.post('yab_fetch_hotel_details_from_api', { hotel_id: hotelId });
            banner.api.selectedHotel = hotelDetails;
        } catch (error) {
            showModal('Error', `Could not fetch hotel details: ${error.message}`);
            // Fallback for single select
             if (banner.api.selectedHotel) {
                 banner.api.selectedHotel = tempSelectedHotels.value.length > 0 ? tempSelectedHotels.value[0] : null;
            }
        } finally {
            isHotelDetailsLoading.value = false;
        }
    };

    // New function similar to fetchToursByIds
    const fetchHotelsByIds = async (ids) => {
        if (!ids || ids.length === 0) return [];
        try {
            // Using the 'yab_fetch_hotel_details_by_ids' endpoint created in PHP
             const hotelsData = await ajax.post('yab_fetch_hotel_details_by_ids', { hotel_ids: ids });
             return hotelsData.filter(Boolean); // Filter out nulls from failed fetches
        } catch (error) {
            showModal('Error', `Could not fetch details for selected hotels: ${error.message}`);
            return [];
        }
    };


    // Updated to handle multiSelect option
    const openHotelModal = async (options = { multiSelect: false }) => {
        isMultiSelect.value = options.multiSelect;
        isHotelModalOpen.value = true;
        isHotelSelectionLoading.value = true; // Start loading indicator

        // Fetch cities and initial hotels if not already loaded
        const citiesPromise = cities.value.length === 0 ? fetchCities() : Promise.resolve();
        const hotelsPromise = hotelResults.length === 0 ? searchHotels(true) : Promise.resolve();

        // Wait for basic data
        await Promise.all([citiesPromise, hotelsPromise]);

        // Now handle fetching details of pre-selected hotels
        try {
            if (isMultiSelect.value) {
                // Fetch details for Hotel Carousel pre-selected IDs
                const selectedIds = banner.hotel_carousel?.selectedHotels || []; // Use optional chaining
                if (selectedIds.length > 0) {
                    const selectedHotelObjects = await fetchHotelsByIds(selectedIds);
                     // Ensure the order matches the saved order
                    const orderedSelectedHotels = selectedIds.map(id => selectedHotelObjects.find(h => h.id === id)).filter(Boolean);
                    tempSelectedHotels.value = orderedSelectedHotels;

                    // Check if selected hotels are already in the main list, if not, add them
                    const existingIds = new Set(hotelResults.map(h => h.id));
                    const missingHotels = selectedHotelObjects.filter(h => !existingIds.has(h.id));
                    if (missingHotels.length > 0) {
                         // Add missing selected hotels to the beginning of the results list
                        hotelResults.unshift(...missingHotels);
                    }
                } else {
                    tempSelectedHotels.value = []; // Clear selection if no IDs are saved
                }
            } else {
                // Handle single select for API Banner
                banner.api.apiType = 'hotel'; // Keep this for API Banner context
                tempSelectedHotels.value = banner.api.selectedHotel ? [banner.api.selectedHotel] : [];
            }
        } catch (error) {
            showModal('Error', `Could not load selected hotels: ${error.message}`);
            tempSelectedHotels.value = []; // Reset on error
        } finally {
            isHotelSelectionLoading.value = false; // Stop loading indicator
        }


        // Add scroll listener
        nextTick(() => {
            hotelModalListRef.value?.addEventListener('scroll', handleHotelScroll);
        });
    };

    const closeHotelModal = () => {
        isHotelModalOpen.value = false;
        hotelModalListRef.value?.removeEventListener('scroll', handleHotelScroll);
    };

    // Updated to handle both single and multi-select confirmation
    const confirmHotelSelection = () => {
        if (isMultiSelect.value) {
            // Save to hotel_carousel
            if (!banner.hotel_carousel) banner.hotel_carousel = { selectedHotels: [], updateCounter: 0 }; // Initialize if not present
            banner.hotel_carousel.selectedHotels = tempSelectedHotels.value.map(hotel => hotel.id);
            banner.hotel_carousel.updateCounter = (banner.hotel_carousel.updateCounter || 0) + 1; // Force preview refresh
        } else {
            // Single select logic (for API Banner)
            banner.api.selectedTour = null; // Deselect tour if a hotel is chosen
            const selected = tempSelectedHotels.value.length > 0 ? tempSelectedHotels.value[0] : null;
            if (selected) {
                banner.api.selectedHotel = selected;
                fetchFullHotelDetails(selected.id);
            } else {
                banner.api.selectedHotel = null;
            }
        }
        closeHotelModal();
    };

    const fetchCities = async () => {
        try {
            cities.value = await ajax.post('yab_fetch_cities_from_api');
        } catch (error) {
            showModal('Error fetching cities', error.message);
        }
    };

     const searchHotels = async (isNewSearch = false) => {
        if (isNewSearch) {
            hotelCurrentPage.value = 1;
            canLoadMoreHotels.value = true;
            if (!isHotelSelectionLoading.value) {
                hotelResults.splice(0);
            }
            isHotelLoading.value = true;
        } else {
            if (isHotelSelectionLoading.value || isHotelLoading.value) return; // Prevent parallel loads
            isMoreHotelLoading.value = true;
        }

        try {
            let starsParam = '';
            if (filters.stars > 0) {
                // Was sending '1,2,3' if 3 stars selected. API might just want '3'.
                // Sticking to original logic from single-select file for now.
                starsParam = Array.from({ length: filters.stars }, (_, i) => i + 1).join(',');
            }

            const params = {
                keyword: filters.keyword, page: hotelCurrentPage.value, size: 10,
                types: filters.types.join(','), minPrice: filters.minPrice, maxPrice: filters.maxPrice,
                province: filters.province, stars: starsParam, sort: filters.sort
            };
            
            const data = await ajax.post('yab_fetch_hotels_from_api', params);
            const newResults = data.data || [];

            // Add new results, avoiding duplicates
            const existingIds = new Set(hotelResults.map(h => h.id));
            const uniqueNewResults = newResults.filter(h => !existingIds.has(h.id));
            hotelResults.push(...uniqueNewResults);

            if (newResults.length > 0) {
                hotelCurrentPage.value++;
            } else {
                 canLoadMoreHotels.value = false;
            }
            
            // After search, ensure selected items are present (in case filter changed)
            if (isNewSearch && isMultiSelect.value && tempSelectedHotels.value.length > 0) {
                const currentResultIds = new Set(hotelResults.map(h => h.id));
                const missingSelected = tempSelectedHotels.value.filter(h => !currentResultIds.has(h.id));
                if (missingSelected.length > 0) {
                    hotelResults.unshift(...missingSelected);
                }
            }


        } catch (error) {
            showModal('API Error', error.message);
            canLoadMoreHotels.value = false; // Stop trying on error
        } finally {
            isHotelLoading.value = false;
            isMoreHotelLoading.value = false;
        }
    };


    const debouncedHotelSearch = () => {
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(() => searchHotels(true), 500);
    };

    // Watchers remain mostly the same
    watch(() => filters.minPrice, (newMin) => { if (newMin > filters.maxPrice) filters.maxPrice = newMin; });
    watch(() => filters.maxPrice, (newMax) => { if (newMax < filters.minPrice) filters.minPrice = newMax; });
    watch(() => filters.keyword, debouncedHotelSearch);
    watch(() => [filters.types, filters.province, filters.stars, filters.sort], () => searchHotels(true), { deep: true });
    watch(() => [filters.minPrice, filters.maxPrice], () => {
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(() => searchHotels(true), 500);
    });

    const toggleType = (typeKey) => {
        const index = filters.types.indexOf(typeKey);
        if (index > -1) filters.types.splice(index, 1);
        else filters.types.push(typeKey);
    };

    const toggleSort = (sortKey) => { filters.sort = filters.sort === sortKey ? '' : sortKey; };
    const setStarRating = (star) => { filters.stars = filters.stars === star ? 0 : star; };
    const selectCity = (cityId) => { filters.province = cityId; isCityDropdownOpen.value = false; };
    const resetFilters = () => { Object.assign(filters, { keyword: '', types: [], minPrice: 0, maxPrice: 1000, province: '', stars: 0, sort: '' }); };

    // Updated selection logic
    const selectHotel = (hotel) => {
        const index = tempSelectedHotels.value.findIndex(h => h.id === hotel.id);

        if (isMultiSelect.value) {
            if (index > -1) {
                tempSelectedHotels.value.splice(index, 1); // Remove if already selected
            } else {
                tempSelectedHotels.value.push(hotel); // Add if not selected
            }
        } else {
            // Single select mode
            if (index > -1) {
                tempSelectedHotels.value = []; // Deselect if clicking the selected one
            } else {
                tempSelectedHotels.value = [hotel]; // Select the new one
            }
        }
    };

    // New helper to check if a hotel is selected (works for both modes)
    const isHotelSelected = (hotel) => {
        return tempSelectedHotels.value.some(h => h.id === hotel.id);
    };


    const handleHotelScroll = () => {
        const el = hotelModalListRef.value;
         // Prevent scroll loading if initial search/selection load is happening
        if (el && canLoadMoreHotels.value && !isMoreHotelLoading.value && !isHotelLoading.value && !isHotelSelectionLoading.value) {
            if (el.scrollTop + el.clientHeight >= el.scrollHeight - 200) {
                searchHotels(false); // Load next page
            }
        }
    };


    return {
        isHotelModalOpen, isHotelLoading, isMoreHotelLoading, sortedHotelResults,
        isHotelDetailsLoading, isHotelSelectionLoading, // Expose new loading state
        openHotelModal, closeHotelModal, selectHotel, hotelModalListRef, // Renamed ref
        tempSelectedHotels, // Expose array
        isHotelSelected, // Expose checker function
        isMultiSelect, // Expose mode flag
        confirmHotelSelection,
        filters, cities, hotelTypes,
        debouncedHotelSearch, toggleType, setStarRating, resetFilters, toggleSort,
        isCityDropdownOpen, selectedCityName, selectCity,
        fetchFullHotelDetails, // Keep for single display
        fetchHotelsByIds, // Expose bulk fetcher
        getRatingLabel,
        formatRating,
    };
}