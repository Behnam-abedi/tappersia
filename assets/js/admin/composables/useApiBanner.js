const { ref, reactive, computed, nextTick, watch } = Vue;

/**
 * Manages all logic for the API Banner functionality (modal, search, selection, filtering).
 */
export function useApiBanner(banner, showModal, ajax) {
    const isHotelModalOpen = ref(false);
    const isHotelLoading = ref(false);
    const isMoreHotelLoading = ref(false);
    const isHotelDetailsLoading = ref(false); // <-- New state
    const hotelResults = reactive([]);
    const hotelCurrentPage = ref(1);
    const canLoadMoreHotels = ref(true);
    const modalListRef = ref(null);
    let debounceTimeout = null;
    
    const tempSelectedHotel = ref(null);
    const cities = ref([]);
    const isCityDropdownOpen = ref(false);

    const filters = reactive({
        keyword: '',
        types: [],
        minPrice: 0,
        maxPrice: 1000,
        province: '',
        stars: 0
    });

    const hotelTypes = [
        { key: 'Hotel', label: 'Hotel' }, { key: 'TraditionalHouse', label: 'Traditional House' },
        { key: 'BoutiqueHotel', label: 'Boutique Hotel' }, { key: 'Apartments', label: 'Apartments' },
        { key: 'EcoLodge', label: 'Eco Lodge' }, { key: 'GuestHouse', label: 'Guest House' }, { key: 'Hostel', label: 'Hostel' }
    ];

    const sortedHotelResults = computed(() => {
        if (!tempSelectedHotel.value) return hotelResults;
        const selectedId = tempSelectedHotel.value.id;
        const selectedHotel = hotelResults.find(h => h.id === selectedId);
        if (!selectedHotel) return hotelResults;
        const otherHotels = hotelResults.filter(h => h.id !== selectedId);
        return [selectedHotel, ...otherHotels];
    });

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
        isHotelDetailsLoading.value = true; // <-- Set loading to true
        try {
            const hotelDetails = await ajax.post('yab_fetch_hotel_details_from_api', { hotel_id: hotelId });
            banner.api.selectedHotel = hotelDetails;
        } catch (error) {
            showModal('Error', `Could not fetch hotel details: ${error.message}`);
            banner.api.selectedHotel = tempSelectedHotel.value; // Fallback to partial data
        } finally {
            isHotelDetailsLoading.value = false; // <-- Set loading to false
        }
    };

    const openHotelModal = () => {
        banner.api.apiType = 'hotel';
        isHotelModalOpen.value = true;
        tempSelectedHotel.value = banner.api.selectedHotel; 
        
        if (cities.value.length === 0) fetchCities();
        if (hotelResults.length === 0) searchHotels(true);

        nextTick(() => {
            modalListRef.value?.addEventListener('scroll', handleHotelScroll);
        });
    };
    
    const closeHotelModal = () => {
        isHotelModalOpen.value = false;
        modalListRef.value?.removeEventListener('scroll', handleHotelScroll);
    };

    const confirmHotelSelection = () => {
        if (tempSelectedHotel.value) {
            fetchFullHotelDetails(tempSelectedHotel.value.id);
        } else {
            banner.api.selectedHotel = null;
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
            hotelResults.splice(0); 
            isHotelLoading.value = true;
        } else {
            isMoreHotelLoading.value = true;
        }

        try {
            let starsParam = '';
            if (filters.stars > 0) {
                starsParam = Array.from({ length: filters.stars }, (_, i) => i + 1).join(',');
            }
            
            const params = {
                keyword: filters.keyword,
                page: hotelCurrentPage.value,
                size: 10,
                types: filters.types.join(','),
                minPrice: filters.minPrice,
                maxPrice: filters.maxPrice,
                province: filters.province,
                stars: starsParam
            };
            const data = await ajax.post('yab_fetch_hotels_from_api', params);

            if (data.data && data.data.length > 0) {
                hotelResults.push(...data.data);
                hotelCurrentPage.value++;
            } else {
                canLoadMoreHotels.value = false; 
            }
        } catch (error) {
            showModal('API Error', error.message);
        } finally {
            isHotelLoading.value = false;
            isMoreHotelLoading.value = false;
        }
    };

    const debouncedHotelSearch = () => {
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(() => searchHotels(true), 500);
    };
    
    watch(() => filters.minPrice, (newMin) => {
        if (newMin > filters.maxPrice) {
            filters.maxPrice = newMin;
        }
    });
     watch(() => filters.maxPrice, (newMax) => {
        if (newMax < filters.minPrice) {
            filters.minPrice = newMax;
        }
    });

    watch(() => ({...filters}), (newFilters, oldFilters) => {
        if (newFilters.keyword !== oldFilters.keyword) return;
        if (newFilters.minPrice !== oldFilters.minPrice || newFilters.maxPrice !== oldFilters.maxPrice) {
             clearTimeout(debounceTimeout);
             debounceTimeout = setTimeout(() => searchHotels(true), 500);
        } else {
            searchHotels(true);
        }
    }, { deep: true });
    
    const toggleType = (typeKey) => {
        const index = filters.types.indexOf(typeKey);
        if (index > -1) filters.types.splice(index, 1);
        else filters.types.push(typeKey);
    };

    const setStarRating = (star) => {
        filters.stars = filters.stars === star ? 0 : star;
    };
    
    const selectCity = (cityId) => {
        filters.province = cityId;
        isCityDropdownOpen.value = false;
    };

    const resetFilters = () => {
        Object.assign(filters, {
            keyword: '', types: [], minPrice: 0, maxPrice: 1000, province: '', stars: 0
        });
    };

    const selectHotel = (hotel) => {
        if (tempSelectedHotel.value && tempSelectedHotel.value.id === hotel.id) {
            tempSelectedHotel.value = null;
        } else {
            tempSelectedHotel.value = hotel;
        }
    };

    const handleHotelScroll = () => {
        const el = modalListRef.value;
        if (el && canLoadMoreHotels.value && !isMoreHotelLoading.value) {
            if (el.scrollTop + el.clientHeight >= el.scrollHeight - 200) {
                searchHotels(false);
            }
        }
    };

    return { 
        isHotelModalOpen, isHotelLoading, isMoreHotelLoading, sortedHotelResults,
        isHotelDetailsLoading, // <-- Expose new state
        openHotelModal, closeHotelModal, selectHotel, modalListRef, 
        tempSelectedHotel, confirmHotelSelection,
        filters, cities, hotelTypes,
        debouncedHotelSearch, toggleType, setStarRating, resetFilters,
        isCityDropdownOpen, selectedCityName, selectCity,
        fetchFullHotelDetails,
    };
}