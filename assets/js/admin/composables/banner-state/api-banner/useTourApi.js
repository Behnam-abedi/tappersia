// tappersia/assets/js/admin/composables/api-banner/useTourApi.js
const { ref, reactive, computed, nextTick, watch } = Vue;
import { getRatingLabel, formatRating } from './utils.js';

export function useTourApi(banner, showModal, ajax) {
    const isTourModalOpen = ref(false);
    const isTourLoading = ref(false);
    const isMoreTourLoading = ref(false);
    const isTourDetailsLoading = ref(false);
    const tourResults = reactive([]);
    const tourCurrentPage = ref(1);
    const canLoadMoreTours = ref(true);
    const tourModalListRef = ref(null);
    const tempSelectedTours = ref([]); // Changed from single object to array
    const isMultiSelect = ref(false); // New state to control modal behavior
    const tourCities = ref([]);
    const isTourCityDropdownOpen = ref(false);
    let debounceTimeout = null;

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

    const sortedTourResults = computed(() => {
        if (tempSelectedTours.value.length === 0) return tourResults;
        const selectedIds = new Set(tempSelectedTours.value.map(t => t.id));
        const selected = tourResults.filter(t => selectedIds.has(t.id));
        const unselected = tourResults.filter(t => !selectedIds.has(t.id));
        return [...selected, ...unselected];
    });


    const selectedTourCityName = computed(() => {
        if (!tourFilters.province) return 'All Cities';
        const city = tourCities.value.find(c => c.id === tourFilters.province);
        return city ? city.name : 'All Cities';
    });

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
            // In case of error, use the partial data
            if (banner.api.selectedTour) {
                 banner.api.selectedTour = tempSelectedTours.value.length > 0 ? tempSelectedTours.value[0] : null;
            }
        } finally {
            isTourDetailsLoading.value = false;
        }
    };
    
    // Updated to accept options object
    const openTourModal = (options = { multiSelect: false }) => {
        isMultiSelect.value = options.multiSelect;

        if (isMultiSelect.value) {
            // For Carousel: clone the existing array to avoid direct mutation
            tempSelectedTours.value = banner.tour_carousel.selectedTours ? [...banner.tour_carousel.selectedTours] : [];
        } else {
            // For API Banner: put the single tour into an array for the modal
            banner.api.apiType = 'tour';
            tempSelectedTours.value = banner.api.selectedTour ? [banner.api.selectedTour] : [];
        }
        
        isTourModalOpen.value = true;

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

    const confirmTourSelection = () => {
        if (isMultiSelect.value) {
            // For Carousel: assign the array of selected tour IDs
            banner.tour_carousel.selectedTours = tempSelectedTours.value.map(tour => tour.id);
        } else {
            // For API Banner: assign the first selected tour, or null
            banner.api.selectedHotel = null; 
            const selected = tempSelectedTours.value.length > 0 ? tempSelectedTours.value[0] : null;
            if (selected) {
                banner.api.selectedTour = selected;
                fetchFullTourDetails(selected.id);
            } else {
                banner.api.selectedTour = null;
            }
        }
        closeTourModal();
    };

    const fetchTourCities = async () => {
        try {
            tourCities.value = await ajax.post('yab_fetch_tour_cities_from_api');
        } catch (error) {
            showModal('Error fetching tour cities', error.message);
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

    const debouncedTourSearch = () => {
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(() => searchTours(true), 500);
    };

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

    watch(() => tourFilters.keyword, debouncedTourSearch);
    watch(() => [tourFilters.types, tourFilters.province], () => searchTours(true), { deep: true });
    watch(() => [tourFilters.minPrice, tourFilters.maxPrice], () => {
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(() => searchTours(true), 500);
    });

    const toggleTourType = (typeKey) => {
        const index = tourFilters.types.indexOf(typeKey);
        if (index > -1) tourFilters.types.splice(index, 1);
        else tourFilters.types.push(typeKey);
    };

    const selectTourCity = (cityId) => {
        tourFilters.province = cityId;
        isTourCityDropdownOpen.value = false;
    };

    const resetTourFilters = () => {
        Object.assign(tourFilters, {
            keyword: '', types: [], minPrice: 0, maxPrice: 1000, province: ''
        });
    };
    
    // Updated to handle both single and multi select
    const selectTour = (tour) => {
        const index = tempSelectedTours.value.findIndex(t => t.id === tour.id);

        if (isMultiSelect.value) {
            // Multi-select logic (add/remove from array)
            if (index > -1) {
                tempSelectedTours.value.splice(index, 1);
            } else {
                tempSelectedTours.value.push(tour);
            }
        } else {
            // Single-select logic (replace the array)
            if (index > -1) {
                tempSelectedTours.value = [];
            } else {
                tempSelectedTours.value = [tour];
            }
        }
    };

    // New computed property to check if a tour is selected
    const isTourSelected = (tour) => {
        return tempSelectedTours.value.some(t => t.id === tour.id);
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
        isTourModalOpen, isTourLoading, isMoreTourLoading, sortedTourResults,
        isTourDetailsLoading,
        openTourModal, closeTourModal, selectTour, tourModalListRef,
        tempSelectedTours, // Changed name for clarity
        confirmTourSelection,
        tourFilters, tourCities, tourTypes,
        debouncedTourSearch, toggleTourType, resetTourFilters,
        isTourCityDropdownOpen, selectedTourCityName, selectTourCity,
        fetchFullTourDetails,
        getRatingLabel,
        formatRating,
        isTourSelected, // Expose new computed
        isMultiSelect, // Expose for use in template
    };
}