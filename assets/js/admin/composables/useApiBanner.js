// tappersia/assets/js/admin/composables/useApiBanner.js
import { useHotelApi } from './banner-state/api-banner/useHotelApi.js';
import { useTourApi } from './banner-state/api-banner/useTourApi.js';
import { getRatingLabel, formatRating } from './banner-state/api-banner/utils.js';

export function useApiBanner(banner, showModal, ajax) {
    const hotelApi = useHotelApi(banner, showModal, ajax);
    const tourApi = useTourApi(banner, showModal, ajax);

    return {
        ...hotelApi,
        ...tourApi,
        // Provide a unified getRatingLabel and formatRating if they are generic enough
        // If they have different logic for hotel/tour, they should stay within their respective composables
        getRatingLabel,
        formatRating,
        ceil: Math.ceil, // Keep utility functions here
    };
}