const { ref, reactive, computed, nextTick, watch } = Vue;

/**
 * Manages all logic for the API Banner functionality (modal, search, selection, filtering).
 */
export function useApiBanner(banner, showModal, ajax) {
    const isHotelModalOpen = ref(false);
    const isHotelLoading = ref(false);
    const isMoreHotelLoading = ref(false);
    const isHotelDetailsLoading = ref(false);
    const hotelResults = reactive([]);
    const hotelCurrentPage = ref(1);
    const canLoadMoreHotels = ref(true);
    const modalListRef = ref(null);
    let debounceTimeout = null;
    
    const tempSelectedHotel = ref(null);
    const cities = ref([]);
    const isCityDropdownOpen = ref(false);
    
    const isTourModalOpen = ref(false);
    const isTourLoading = ref(false);
    const isMoreTourLoading = ref(false);
    const isTourDetailsLoading = ref(false);
    const tourResults = reactive([]);
    const tourCurrentPage = ref(1);
    const canLoadMoreTours = ref(true);
    const tourModalListRef = ref(null);
    const tempSelectedTour = ref(null);
    const tourCities = ref([]);
    const isTourCityDropdownOpen = ref(false);

    const tourFilters = reactive({
        keyword: '',
        types: [],
        minPrice: 0,
        maxPrice: 1000,
        province: '',
    });

    const tourTypes = [
        { key: 'Daily', label: 'Daily' }, { key: 'Package', label: 'Package' },
        { key: 'Pickup', label: 'Pickup' }, { key: 'Experience', label: 'Experience' }
    ];

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

    // *** START: ADDED FUNCTIONS ***
    const getRatingLabel = (score) => {
        if (score === null || score === undefined || score === 0) return 'New';
        if (score >= 4.6) return 'Excellent';
        if (score >= 4.1) return 'Very Good';
        if (score >= 3.6) return 'Good';
        if (score >= 3.0) return 'Average';
        return 'Poor';
    };

    const formatRating = (score) => {
        if (score === null || score === undefined) return '';
        if (Math.floor(score) === score) {
            return parseInt(score, 10);
        }
        return Math.floor(score * 10) / 10;
    };
    // *** END: ADDED FUNCTIONS ***

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
    
    const sortedTourResults = computed(() => {
        if (!tempSelectedTour.value) return tourResults;
        const selectedId = tempSelectedTour.value.id;
        const selectedTour = tourResults.find(t => t.id === selectedId);
        if (!selectedTour) return tourResults;
        const otherTours = tourResults.filter(t => t.id !== selectedId);
        return [selectedTour, ...otherTours];
    });

    const selectedTourCityName = computed(() => {
        if (!tourFilters.province) return 'All Cities';
        const city = tourCities.value.find(c => c.id === tourFilters.province);
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
            banner.api.selectedHotel = tempSelectedHotel.value;
        } finally {
            isHotelDetailsLoading.value = false;
        }
    };

    const fetchFullTourDetails = async (tourId) => {
        if (!tourId) {
            banner.api.selectedTour = null;
            return;
        }
        isTourDetailsLoading.value = true;
        try {
            await new Promise(resolve => setTimeout(resolve, 100));
            const tourDetails = await ajax.post('yab_fetch_tour_details_from_api', { tour_id: tourId });
            banner.api.selectedTour = tourDetails;
        } catch (error) {
            showModal('Error', `Could not fetch tour details: ${error.message}`);
            banner.api.selectedTour = tempSelectedTour.value;
        } finally {
            isTourDetailsLoading.value = false;
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

    const openTourModal = () => {
        banner.api.apiType = 'tour';
        isTourModalOpen.value = true;
        tempSelectedTour.value = banner.api.selectedTour;

        if (tourCities.value.length === 0) fetchTourCities();
        if (tourResults.length === 0) searchTours(true);

        nextTick(() => {
            tourModalListRef.value?.addEventListener('scroll', handleTourScroll);
        });
    };

    const closeTourModal = () => {
        isTourModalOpen.value = false;
        tourModalListRef.value?.removeEventListener('scroll', handleTourScroll);
    };

    const confirmHotelSelection = () => {
        banner.api.selectedTour = null; // Clear tour selection
        if (tempSelectedHotel.value) {
            banner.api.selectedHotel = tempSelectedHotel.value;
            fetchFullHotelDetails(tempSelectedHotel.value.id);
        } else {
            banner.api.selectedHotel = null;
        }
        closeHotelModal();
    };
    
    const confirmTourSelection = () => {
        banner.api.selectedHotel = null; // Clear hotel selection
        if (tempSelectedTour.value) {
            banner.api.selectedTour = tempSelectedTour.value;
            fetchFullTourDetails(tempSelectedTour.value.id);
        } else {
            banner.api.selectedTour = null;
        }
        closeTourModal();
    };

    const fetchCities = async () => {
        try {
            cities.value = await ajax.post('yab_fetch_cities_from_api');
        } catch (error) {
            showModal('Error fetching cities', error.message);
        }
    };

    const fetchTourCities = async () => {
        try {
            tourCities.value = await ajax.post('yab_fetch_tour_cities_from_api');
        } catch (error) {
            showModal('Error fetching tour cities', error.message);
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
                stars: starsParam,
                sort: filters.sort
            };
            const data = await ajax.post('yab_fetch_hotels_from_api', params);

            const newResults = data.data || [];

            if (isNewSearch && tempSelectedHotel.value) {
                const isSelectedInResults = newResults.some(h => h.id === tempSelectedHotel.value.id);
                if (!isSelectedInResults) {
                    hotelResults.push(tempSelectedHotel.value);
                }
            }

            const existingIds = new Set(hotelResults.map(h => h.id));
            const uniqueNewResults = newResults.filter(h => !existingIds.has(h.id));
            hotelResults.push(...uniqueNewResults);

            if (data.data && data.data.length > 0) {
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

    const searchTours = async (isNewSearch = false) => {
        if (isNewSearch) {
            tourCurrentPage.value = 1;
            canLoadMoreTours.value = true;
            tourResults.splice(0);
            isTourLoading.value = true;
        } else {
            isMoreTourLoading.value = true;
        }

        try {
            const params = {
                keyword: tourFilters.keyword,
                page: tourCurrentPage.value,
                size: 10,
                types: tourFilters.types.join(','),
                minPrice: tourFilters.minPrice,
                maxPrice: tourFilters.maxPrice,
                province: tourFilters.province,
            };
            const data = await ajax.post('yab_fetch_tours_from_api', params);

            if (data.data && data.data.length > 0) {
                tourResults.push(...data.data);
                tourCurrentPage.value++;
            } else {
                canLoadMoreTours.value = false;
            }
        } catch (error) {
            showModal('API Error', error.message);
        } finally {
            isTourLoading.value = false;
            isMoreTourLoading.value = false;
        }
    };

    const debouncedHotelSearch = () => {
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(() => searchHotels(true), 500);
    };

    const debouncedTourSearch = () => {
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(() => searchTours(true), 500);
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

    watch(() => tourFilters.minPrice, (newMin) => {
        if (newMin > tourFilters.maxPrice) {
            tourFilters.maxPrice = newMin;
        }
    });
     watch(() => tourFilters.maxPrice, (newMax) => {
        if (newMax < tourFilters.minPrice) {
            tourFilters.minPrice = newMax;
        }
    });

    watch(() => filters.keyword, debouncedHotelSearch);
    watch(() => [filters.types, filters.province, filters.stars, filters.sort], () => searchHotels(true), { deep: true });
    watch(() => [filters.minPrice, filters.maxPrice], () => {
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(() => searchHotels(true), 500);
    });

    watch(() => tourFilters.keyword, debouncedTourSearch);
    watch(() => [tourFilters.types, tourFilters.province], () => searchTours(true), { deep: true });
    watch(() => [tourFilters.minPrice, tourFilters.maxPrice], () => {
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(() => searchTours(true), 500);
    });

    const toggleType = (typeKey) => {
        const index = filters.types.indexOf(typeKey);
        if (index > -1) filters.types.splice(index, 1);
        else filters.types.push(typeKey);
    };
    
    const toggleSort = (sortKey) => {
        if (filters.sort === sortKey) {
            filters.sort = '';
        } else {
            filters.sort = sortKey;
        }
    };

    const toggleTourType = (typeKey) => {
        const index = tourFilters.types.indexOf(typeKey);
        if (index > -1) tourFilters.types.splice(index, 1);
        else tourFilters.types.push(typeKey);
    };

    const setStarRating = (star) => {
        filters.stars = filters.stars === star ? 0 : star;
    };
    
    const selectCity = (cityId) => {
        filters.province = cityId;
        isCityDropdownOpen.value = false;
    };

    const selectTourCity = (cityId) => {
        tourFilters.province = cityId;
        isTourCityDropdownOpen.value = false;
    };

    const resetFilters = () => {
        Object.assign(filters, {
            keyword: '', types: [], minPrice: 0, maxPrice: 1000, province: '', stars: 0, sort: ''
        });
    };
    
    const resetTourFilters = () => {
        Object.assign(tourFilters, {
            keyword: '', types: [], minPrice: 0, maxPrice: 1000, province: ''
        });
    };

    const selectHotel = (hotel) => {
        if (tempSelectedHotel.value && tempSelectedHotel.value.id === hotel.id) {
            tempSelectedHotel.value = null;
        } else {
            tempSelectedHotel.value = hotel;
        }
    };
    
    const selectTour = (tour) => {
        if (tempSelectedTour.value && tempSelectedTour.value.id === tour.id) {
            tempSelectedTour.value = null;
        } else {
            tempSelectedTour.value = tour;
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
    
    const handleTourScroll = () => {
        const el = tourModalListRef.value;
        if (el && canLoadMoreTours.value && !isMoreTourLoading.value) {
            if (el.scrollTop + el.clientHeight >= el.scrollHeight - 200) {
                searchTours(false);
            }
        }
    };

    return { 
        isHotelModalOpen, isHotelLoading, isMoreHotelLoading, sortedHotelResults,
        isHotelDetailsLoading,
        openHotelModal, closeHotelModal, selectHotel, modalListRef, 
        tempSelectedHotel, confirmHotelSelection,
        filters, cities, hotelTypes,
        debouncedHotelSearch, toggleType, setStarRating, resetFilters, toggleSort,
        isCityDropdownOpen, selectedCityName, selectCity,
        fetchFullHotelDetails,
        
        isTourModalOpen, isTourLoading, isMoreTourLoading, sortedTourResults,
        isTourDetailsLoading,
        openTourModal, closeTourModal, selectTour, tourModalListRef,
        tempSelectedTour, confirmTourSelection,
        tourFilters, tourCities, tourTypes,
        debouncedTourSearch, toggleTourType, resetTourFilters,
        isCityDropdownOpen, selectedTourCityName, selectTourCity,
        fetchFullTourDetails,

        // *** START: EXPOSE FUNCTIONS TO TEMPLATE ***
        getRatingLabel,
        formatRating,
        ceil: Math.ceil,
        // *** END: EXPOSE FUNCTIONS TO TEMPLATE ***
    };
}