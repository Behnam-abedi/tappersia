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
    const tempSelectedHotels = ref([]); // FIX: Was tempSelectedHotel (single)
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
        const selected = tempSelectedHotels.value;
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
             if (banner.api.selectedHotel) { // Fallback if API fails
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

        const citiesPromise = cities.value.length === 0 ? fetchCities() : Promise.resolve();
        const hotelsPromise = hotelResults.length === 0 ? searchHotels(true) : Promise.resolve();

        await Promise.all([citiesPromise, hotelsPromise]);

        try {
            if (isMultiSelect.value) {
                const selectedIds = banner.hotel_carousel?.selectedHotels || [];
                if (selectedIds.length > 0) {
                    const selectedHotelObjects = await fetchHotelsByIds(selectedIds);
                    const orderedSelectedHotels = selectedIds.map(id => selectedHotelObjects.find(h => h.id === id)).filter(Boolean);
                    tempSelectedHotels.value = orderedSelectedHotels;

                    const existingIds = new Set(hotelResults.map(h => h.id));
                    const missingHotels = selectedHotelObjects.filter(h => !existingIds.has(h.id));
                    if (missingHotels.length > 0) {
                        hotelResults.unshift(...missingHotels);
                    }
                } else {
                    tempSelectedHotels.value = [];
                }
            } else {
                banner.api.apiType = 'hotel';
                tempSelectedHotels.value = banner.api.selectedHotel ? [banner.api.selectedHotel] : [];
            }
        } catch (error) {
            showModal('Error', `Could not load selected hotels: ${error.message}`);
            tempSelectedHotels.value = [];
        } finally {
            isHotelSelectionLoading.value = false;
        }

        nextTick(() => {
            hotelModalListRef.value?.addEventListener('scroll', handleHotelScroll);
        });
    };
    // --- END: MODAL LOGIC FIX ---

    const closeHotelModal = () => {
        isHotelModalOpen.value = false;
        hotelModalListRef.value?.removeEventListener('scroll', handleHotelScroll);
    };

    // --- START: MODAL LOGIC FIX (Copied from useTourApi) ---
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
                fetchFullHotelDetails(selected.id);
            } else {
                banner.api.selectedHotel = null;
            }
        }
        closeHotelModal();
    };
    // --- END: MODAL LOGIC FIX ---

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

            const existingIds = new Set(hotelResults.map(h => h.id));
            const uniqueNewResults = newResults.filter(h => !existingIds.has(h.id));
            hotelResults.push(...uniqueNewResults);

            if (newResults.length > 0) {
                hotelCurrentPage.value++;
            } else {
                 canLoadMoreHotels.value = false;
            }

            // --- START: MODAL LIST FIX (Copied from useTourApi) ---
            if (isNewSearch && tempSelectedHotels.value.length > 0) {
                const currentResultIds = new Set(hotelResults.map(h => h.id));
                const missingSelected = tempSelectedHotels.value.filter(h => !currentResultIds.has(h.id));
                if (missingSelected.length > 0) {
                    hotelResults.unshift(...missingSelected);
                }
            }
            // --- END: MODAL LIST FIX ---

        } catch (error) {
            showModal('API Error', error.message);
            canLoadMoreHotels.value = false;
        } finally {
            isHotelLoading.value = false;
            isMoreHotelLoading.value = false;
        }
    };

    const debouncedHotelSearch = () => {
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(() => searchHotels(true), 500);
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

    // --- START: MODAL LOGIC FIX (Copied from useTourApi) ---
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
    // --- END: MODAL LOGIC FIX ---

    const handleHotelScroll = () => {
        const el = hotelModalListRef.value;
        if (el && canLoadMoreHotels.value && !isMoreHotelLoading.value && !isHotelLoading.value && !isHotelSelectionLoading.value) {
            if (el.scrollTop + el.clientHeight >= el.scrollHeight - 200) {
                searchHotels(false);
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