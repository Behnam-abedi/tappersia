// tappersia/assets/js/admin/composables/banner-state/api-banner/useHotelApi.js
const { ref, reactive, computed, nextTick, watch } = Vue;
import { getRatingLabel, formatRating } from './utils.js';

export function useHotelApi(banner, showModal, ajax) {
    const isHotelModalOpen = ref(false);
    const isHotelLoading = ref(false);
    const isMoreHotelLoading = ref(false);
    const isHotelDetailsLoading = ref(false);
    const isHotelSelectionLoading = ref(false);
    const hotelResults = reactive([]);
    const hotelCurrentPage = ref(1);
    const canLoadMoreHotels = ref(true);
    const hotelModalListRef = ref(null);
    const tempSelectedHotels = ref([]); // FIX: Changed to array
    const isMultiSelect = ref(false);
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

    // --- START: MODAL LIST FIX (Copied from useTourApi) ---
    const sortedHotelResults = computed(() => {
        if (tempSelectedHotels.value.length === 0) return hotelResults;

        const selectedHotelsMap = new Map(tempSelectedHotels.value.map(h => [h.id, h]));
        
        // FIX: Ensure selected items are ordered as selected
        const selected = tempSelectedHotels.value; 
        
        // FIX: Ensure unselected items do not include selected ones
        const unselected = hotelResults.filter(h => !selectedHotelsMap.has(h.id));

        return [...selected, ...unselected];
    });
    // --- END: MODAL LIST FIX ---

    const selectedCityName = computed(() => {
        if (!filters.province) return 'All Cities';
        const city = cities.value.find(c => c.id === filters.province);
        return city ? city.name : 'All Cities';
    });

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
             if (banner.api.selectedHotel) {
                 banner.api.selectedHotel = tempSelectedHotels.value.length > 0 ? tempSelectedHotels.value[0] : null;
            }
        } finally {
            isHotelDetailsLoading.value = false;
        }
    };

    const fetchHotelsByIds = async (ids) => {
        if (!ids || ids.length === 0) return [];
        try {
             const hotelsData = await ajax.post('yab_fetch_hotel_details_by_ids', { hotel_ids: ids });
             return hotelsData.filter(Boolean);
        } catch (error) {
            showModal('Error', `Could not fetch details for selected hotels: ${error.message}`);
            return [];
        }
    };

    // --- START: MODAL LOGIC FIX (Copied from useTourApi) ---
    const openHotelModal = async (options = { multiSelect: false }) => {
        isMultiSelect.value = options.multiSelect;
        isHotelModalOpen.value = true;
        isHotelSelectionLoading.value = true;

        // Reset search/pagination state
        hotelCurrentPage.value = 1;
        canLoadMoreHotels.value = true;
        hotelResults.splice(0);

        const citiesPromise = cities.value.length === 0 ? fetchCities() : Promise.resolve();

        // Fetch selected hotels first
        let selectedHotelObjects = [];
        try {
            if (isMultiSelect.value) {
                const selectedIds = banner.hotel_carousel?.selectedHotels || [];
                if (selectedIds.length > 0) {
                    selectedHotelObjects = await fetchHotelsByIds(selectedIds);
                    // Ensure order is preserved
                    tempSelectedHotels.value = selectedIds.map(id => selectedHotelObjects.find(h => h.id === id)).filter(Boolean);
                } else {
                    tempSelectedHotels.value = [];
                }
            } else {
                tempSelectedHotels.value = banner.api.selectedHotel ? [banner.api.selectedHotel] : [];
                if (tempSelectedHotels.value.length > 0 && !tempSelectedHotels.value[0].coverImage) {
                    // If API banner only stored partial data, fetch full data
                    selectedHotelObjects = await fetchHotelsByIds(tempSelectedHotels.value.map(h => h.id));
                    tempSelectedHotels.value = selectedHotelObjects;
                }
            }
        } catch (error) {
            showModal('Error', `Could not load selected hotels: ${error.message}`);
            tempSelectedHotels.value = [];
        }
        
        // Add selected hotels to results list first
        hotelResults.push(...tempSelectedHotels.value);

        // Now fetch initial list of other hotels
        await citiesPromise;
        await searchHotels(true, true); // Pass flag to skip clearing
        
        isHotelSelectionLoading.value = false;

        nextTick(() => {
            hotelModalListRef.value?.addEventListener('scroll', handleHotelScroll);
        });
    };
    // --- END: MODAL LOGIC FIX ---

    const closeHotelModal = () => {
        isHotelModalOpen.value = false;
        hotelModalListRef.value?.removeEventListener('scroll', handleHotelScroll);
        // Clear filters on close
        resetFilters();
    };

    const confirmHotelSelection = () => {
        if (isMultiSelect.value) {
            if (!banner.hotel_carousel) banner.hotel_carousel = { selectedHotels: [], updateCounter: 0 };
            banner.hotel_carousel.selectedHotels = tempSelectedHotels.value.map(hotel => hotel.id);
            banner.hotel_carousel.updateCounter = (banner.hotel_carousel.updateCounter || 0) + 1;
        } else {
            banner.api.selectedTour = null;
            const selected = tempSelectedHotels.value.length > 0 ? tempSelectedHotels.value[0] : null;
            if (selected) {
                banner.api.selectedHotel = selected;
                // Fetch full details only if it wasn't fetched on open
                if (!selected.intro) { 
                    fetchFullHotelDetails(selected.id);
                }
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

     // --- START: MODAL LOGIC FIX (Search update) ---
     const searchHotels = async (isNewSearch = false, skipClear = false) => {
        if (isNewSearch) {
            hotelCurrentPage.value = 1;
            canLoadMoreHotels.value = true;
            if (!skipClear) { // Only clear if not skipping
                hotelResults.splice(0);
                 // Add selected back if clearing
                 hotelResults.push(...tempSelectedHotels.value);
            }
            isHotelLoading.value = true;
        } else {
            if (isHotelSelectionLoading.value || isHotelLoading.value) return;
            isMoreHotelLoading.value = true;
        }

        try {
            let starsParam = '';
            if (filters.stars > 0) {
                starsParam = Array.from({ length: filters.stars }, (_, i) => i + 1).join(',');
            }

            const params = {
                keyword: filters.keyword, page: hotelCurrentPage.value, size: 10,
                types: filters.types.join(','), minPrice: filters.minPrice, maxPrice: filters.maxPrice,
                province: filters.province, stars: starsParam, sort: filters.sort
            };

            const data = await ajax.post('yab_fetch_hotels_from_api', params);
            const newResults = data.data || [];
            
            // Add new results, avoiding duplicates (including selected ones)
            const existingIds = new Set(hotelResults.map(h => h.id));
            const uniqueNewResults = newResults.filter(h => !existingIds.has(h.id));
            hotelResults.push(...uniqueNewResults);

            if (uniqueNewResults.length > 0) { // Only increment if new items were added
                hotelCurrentPage.value++;
            } else if (newResults.length === 0) { // Stop if API returns empty
                 canLoadMoreHotels.value = false;
            }

        } catch (error) {
            showModal('API Error', error.message);
            canLoadMoreHotels.value = false;
        } finally {
            isHotelLoading.value = false;
            isMoreHotelLoading.value = false;
        }
    };
    // --- END: MODAL LOGIC FIX ---

    const debouncedHotelSearch = () => {
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(() => searchHotels(true), 500); // Pass true for new search
    };

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

    const selectHotel = (hotel) => {
        const index = tempSelectedHotels.value.findIndex(h => h.id === hotel.id);

        if (isMultiSelect.value) {
            if (index > -1) {
                tempSelectedHotels.value.splice(index, 1);
            } else {
                tempSelectedHotels.value.push(hotel);
            }
        } else {
            if (index > -1) {
                tempSelectedHotels.value = [];
            } else {
                tempSelectedHotels.value = [hotel];
            }
        }
    };

    const isHotelSelected = (hotel) => {
        return tempSelectedHotels.value.some(h => h.id === hotel.id);
    };

    const handleHotelScroll = () => {
        const el = hotelModalListRef.value;
        if (el && canLoadMoreHotels.value && !isMoreHotelLoading.value && !isHotelLoading.value && !isHotelSelectionLoading.value) {
            if (el.scrollTop + el.clientHeight >= el.scrollHeight - 200) {
                searchHotels(false); // Load next page
            }
        }
    };

    return {
        isHotelModalOpen, isHotelLoading, isMoreHotelLoading, sortedHotelResults,
        isHotelDetailsLoading, isHotelSelectionLoading,
        openHotelModal, closeHotelModal, selectHotel, hotelModalListRef,
        tempSelectedHotels,
        isHotelSelected,
        isMultiSelect,
        confirmHotelSelection,
        filters, cities, hotelTypes,
        debouncedHotelSearch, toggleType, setStarRating, resetFilters, toggleSort,
        isCityDropdownOpen, selectedCityName, selectCity,
        fetchFullHotelDetails,
        fetchHotelsByIds,
        getRatingLabel,
        formatRating,
    };
}