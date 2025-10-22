// tappersia/assets/js/admin/composables/banner-state/bannerState.js
const { reactive, computed } = Vue;
import {
    createDefaultPart,
    createDefaultMobilePart,
    createDefaultDoubleBannerPart,
    createDefaultDoubleBannerMobilePart,
    createDefaultApiDesign,
    createDefaultApiMobileDesign,
    createDefaultSimplePart,
    createDefaultSimpleBannerMobilePart,
    createDefaultStickySimplePart,
    createDefaultStickySimpleMobilePart,
    createDefaultPromotionPart,
    createDefaultPromotionMobilePart,
    createDefaultHtmlPart,
    createDefaultHtmlSidebarPart,
    createDefaultTourCarouselPart,
    createDefaultHotelCarouselPart, // Added import
    createDefaultFlightTicketPart
} from './defaults/index.js'; // Ensure index.js exports the new hotel default

export function useBannerState() {
    const createDefaultBanner = () => ({
        id: null, name: '', displayMethod: 'Fixed', isActive: true, type: null,
        isMobileConfigured: false, // General mobile config flag (used by some types)
        displayOn: { posts: [], pages: [], categories: [] },

        single: createDefaultPart(),
        single_mobile: createDefaultMobilePart(),

        double: {
            isMobileConfigured: false,
            desktop: { left: createDefaultDoubleBannerPart(), right: createDefaultDoubleBannerPart() },
            mobile: { left: createDefaultDoubleBannerMobilePart(), right: createDefaultDoubleBannerMobilePart() }
        },

        simple: createDefaultSimplePart(),
        simple_mobile: createDefaultSimpleBannerMobilePart(),

        sticky_simple: createDefaultStickySimplePart(),
        sticky_simple_mobile: createDefaultStickySimpleMobilePart(),

        promotion: createDefaultPromotionPart(),
        promotion_mobile: createDefaultPromotionMobilePart(),

        content_html: createDefaultHtmlPart(),
        content_html_sidebar: createDefaultHtmlSidebarPart(),

        api: {
            apiType: null,
            selectedHotel: null,
            selectedTour: null,
            design: createDefaultApiDesign(),
            design_mobile: createDefaultApiMobileDesign(),
            isMobileConfigured: false,
        },

        tour_carousel: createDefaultTourCarouselPart(),
        hotel_carousel: createDefaultHotelCarouselPart(), // Added hotel carousel state
        flight_ticket: createDefaultFlightTicketPart(),
    });

    const banner = reactive(createDefaultBanner());

    const shortcode = computed(() => {
        if (!banner.type) return '';
        // Add hotelcarousel replacement logic
        const base = banner.type.replace(/-/g, '')
                                .replace('contenthtmlbanner', 'contenthtml')
                                .replace('contenthtmlsidebarbanner', 'contenthtmlsidebar')
                                .replace('tourcarousel', 'tourcarousel') // Keep explicit tour
                                .replace('hotelcarousel', 'hotelcarousel'); // Add hotel

        if (banner.displayMethod === 'Embeddable') {
            return banner.id ? `[${base} id="${banner.id}"]` : `[${base} id="..."]`;
        }
        return `[${base}_fixed]`;
    });

    const mergeWithExisting = (existingData) => {
        const deepMerge = (target, source) => {
             // ... (deepMerge function remains the same) ...
            for (const key in source) {
                if (source.hasOwnProperty(key)) {
                    if (source[key] instanceof Object && key in target && target[key] instanceof Object && !(source[key] instanceof Array)) { // Avoid merging arrays like gradientStops incorrectly
                         deepMerge(target[key], source[key]);
                    } else if (key === 'gradientStops' && Array.isArray(source[key])) {
                         // Explicitly handle gradientStops array overwrite/copy
                         target[key] = JSON.parse(JSON.stringify(source[key]));
                     } else {
                        target[key] = source[key];
                    }
                }
            }
        };


        // Set mobile configured flags if editing an existing banner
        if (existingData.id) {
             // Check and set flag for each type that has separate mobile config
             // existingData.isMobileConfigured = true; // Maybe remove this general one?
            if (existingData.single) existingData.isMobileConfigured = true; // Use banner level flag for single
            if (existingData.double) existingData.double.isMobileConfigured = true;
            if (existingData.api) existingData.api.isMobileConfigured = true;
            if (existingData.tour_carousel) existingData.tour_carousel.isMobileConfigured = true;
            if (existingData.hotel_carousel) existingData.hotel_carousel.isMobileConfigured = true; // Set flag for hotel
            if (existingData.simple) existingData.isMobileConfigured = true; // Use banner level flag
            if (existingData.sticky_simple) existingData.isMobileConfigured = true; // Use banner level flag
            if (existingData.promotion) existingData.isMobileConfigured = true; // Use banner level flag

        }

        deepMerge(banner, existingData);

        // Ensure displayOn exists and its arrays are valid
        if (!banner.displayOn) {
            banner.displayOn = { posts: [], pages: [], categories: [] };
        } else {
            if (!Array.isArray(banner.displayOn.posts)) banner.displayOn.posts = [];
            if (!Array.isArray(banner.displayOn.pages)) banner.displayOn.pages = [];
            if (!Array.isArray(banner.displayOn.categories)) banner.displayOn.categories = [];
        }

        // Ensure links array exists for promotion banner
        if (banner.type === 'promotion-banner' && !Array.isArray(banner.promotion.links)) {
            banner.promotion.links = [];
        }

         // Ensure selectedHotels array exists for hotel carousel
         if (banner.type === 'hotel-carousel' && !Array.isArray(banner.hotel_carousel.selectedHotels)) {
            banner.hotel_carousel.selectedHotels = [];
         }
         // Ensure selectedTours array exists for tour carousel
         if (banner.type === 'tour-carousel' && !Array.isArray(banner.tour_carousel.selectedTours)) {
            banner.tour_carousel.selectedTours = [];
         }
    };


    const resetBannerState = () => {
        Object.assign(banner, createDefaultBanner());
    };

    return { banner, shortcode, mergeWithExisting, resetBannerState };
}