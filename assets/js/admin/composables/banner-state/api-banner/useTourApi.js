// tappersia/assets/js/admin/composables/banner-state/api-banner/useTourApi.js
const { ref, reactive, computed, nextTick, watch } = Vue;
import { getRatingLabel, formatRating } from './utils.js';

export function useTourApi(banner, showModal, ajax) {
    const isTourModalOpen = ref(false);
    const isTourLoading = ref(false);
    const isMoreTourLoading = ref(false);
    const isTourDetailsLoading = ref(false);
    const isTourSelectionLoading = ref(false); // State to control initial loading
    const tourResults = reactive([]);
    const tourCurrentPage = ref(1);
    const canLoadMoreTours = ref(true);
    const tourModalListRef = ref(null);
    const tempSelectedTours = ref([]);
    const isMultiSelect = ref(false);
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
            if (banner.api.selectedTour) {
                 banner.api.selectedTour = tempSelectedTours.value.length > 0 ? tempSelectedTours.value[0] : null;
            }
        } finally {
            isTourDetailsLoading.value = false;
        }
    };
    
    const fetchToursByIds = async (ids) => {
        if (!ids || ids.length === 0) return [];
        try {
            const toursData = await ajax.post('yab_fetch_tour_details_by_ids', { tour_ids: ids });
            return toursData;
        } catch (error) {
            showModal('Error', `Could not fetch details for selected tours: ${error.message}`);
            return [];
        }
    };

    // *** FIX START: Refactored function to prevent race conditions ***
    const openTourModal = async (options = { multiSelect: false }) => {
        isMultiSelect.value = options.multiSelect;
        isTourModalOpen.value = true;
        isTourSelectionLoading.value = true; // Activate loading state immediately

        // First, fetch static data and initial tour list if they don't exist
        const citiesPromise = tourCities.value.length === 0 ? fetchTourCities() : Promise.resolve();
        const toursPromise = tourResults.length === 0 ? searchTours(true) : Promise.resolve();
        
        await Promise.all([citiesPromise, toursPromise]);

        try {
            // Now, handle the selected tours
            if (isMultiSelect.value) {
                const selectedIds = banner.tour_carousel.selectedTours || [];
                if (selectedIds.length > 0) {
                    const selectedTourObjects = await fetchToursByIds(selectedIds);
                    tempSelectedTours.value = selectedTourObjects;

                    // Add tours to the main list if they are not already there
                    const existingIds = new Set(tourResults.map(t => t.id));
                    const missingTours = selectedTourObjects.filter(t => !existingIds.has(t.id));
                    if (missingTours.length > 0) {
                        tourResults.unshift(...missingTours);
                    }
                } else {
                    tempSelectedTours.value = [];
                }
            } else { // Single select mode for API banner
                banner.api.apiType = 'tour';
                tempSelectedTours.value = banner.api.selectedTour ? [banner.api.selectedTour] : [];
            }
        } catch (error) {
            showModal('Error', `Could not load selected tours: ${error.message}`);
        } finally {
            isTourSelectionLoading.value = false; // Deactivate loading state AFTER all data is ready
        }
        
        nextTick(() => {
            tourModalListRef.value?.addEventListener('scroll', handleTourScroll);
        });
    };
    // *** FIX END ***

    const closeTourModal = () => {
        isTourModalOpen.value = false;
        tourModalListRef.value?.removeEventListener('scroll', handleTourScroll);
    };

    const confirmTourSelection = () => {
        if (isMultiSelect.value) {
            const newTourIds = tempSelectedTours.value.map(tour => tour.id);
            banner.tour_carousel.selectedTours.length = 0;
            banner.tour_carousel.selectedTours.push(...newTourIds);
        } else {
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
            if (!isTourSelectionLoading.value) { // Prevent clearing list during initial selection load
                tourResults.splice(0);
            }
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
                const existingIds = new Set(tourResults.map(t => t.id));
                const uniqueNewResults = data.data.filter(t => !existingIds.has(t.id));
                tourResults.push(...uniqueNewResults);
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
    
    const selectTour = (tour) => {
        const index = tempSelectedTours.value.findIndex(t => t.id === tour.id);

        if (isMultiSelect.value) {
            if (index > -1) {
                tempSelectedTours.value.splice(index, 1);
            } else {
                tempSelectedTours.value.push(tour);
            }
        } else {
            if (index > -1) {
                tempSelectedTours.value = [];
            } else {
                tempSelectedTours.value = [tour];
            }
        }
    };

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
        isTourSelectionLoading, // Expose the new state
        openTourModal, closeTourModal, selectTour, tourModalListRef,
        tempSelectedTours,
        confirmTourSelection,
        tourFilters, tourCities, tourTypes,
        debouncedTourSearch, toggleTourType, resetTourFilters,
        isTourCityDropdownOpen, selectedTourCityName, selectTourCity,
        fetchFullTourDetails,
        getRatingLabel,
        formatRating,
        isTourSelected,
        isMultiSelect,
        ceil: Math.ceil
    };
}