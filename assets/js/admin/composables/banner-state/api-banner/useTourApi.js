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
    const tempSelectedTour = ref(null);
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

    const confirmTourSelection = () => {
        banner.api.selectedHotel = null; 
        if (tempSelectedTour.value) {
            banner.api.selectedTour = tempSelectedTour.value;
            fetchFullTourDetails(tempSelectedTour.value.id);
        } else {
            banner.api.selectedTour = null;
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
    
    const selectTour = (tour) => {
        if (tempSelectedTour.value && tempSelectedTour.value.id === tour.id) {
            tempSelectedTour.value = null;
        } else {
            tempSelectedTour.value = tour;
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
        isTourModalOpen, isTourLoading, isMoreTourLoading, sortedTourResults,
        isTourDetailsLoading,
        openTourModal, closeTourModal, selectTour, tourModalListRef,
        tempSelectedTour, confirmTourSelection,
        tourFilters, tourCities, tourTypes,
        debouncedTourSearch, toggleTourType, resetTourFilters,
        isTourCityDropdownOpen, selectedTourCityName, selectTourCity,
        fetchFullTourDetails,
        getRatingLabel,
        formatRating,
    };
}