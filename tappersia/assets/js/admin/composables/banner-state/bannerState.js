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
    createDefaultHotelCarouselPart,
    createDefaultFlightTicketPart,
    createDefaultWelcomePackagePart, // Added import
} from './defaults/index.js';

export function useBannerState() {
    const createDefaultBanner = () => ({
        id: null, name: '', displayMethod: 'Fixed', isActive: true, type: null,
        isMobileConfigured: false, // General flag, might be overridden by specific types
        displayOn: { posts: [], pages: [], categories: [] },

        // Existing types...
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
        hotel_carousel: createDefaultHotelCarouselPart(),
        flight_ticket: createDefaultFlightTicketPart(),

        // New Welcome Package type
        welcome_package: createDefaultWelcomePackagePart(),
    });

    const banner = reactive(createDefaultBanner());

    const shortcode = computed(() => {
        if (!banner.type) return '';
        // Add welcomepackagebanner replacement logic
        const base = banner.type.replace(/-/g, '')
                                .replace('contenthtmlbanner', 'contenthtml')
                                .replace('contenthtmlsidebarbanner', 'contenthtmlsidebar')
                                .replace('tourcarousel', 'tourcarousel')
                                .replace('hotelcarousel', 'hotelcarousel')
                                .replace('welcomepackagebanner', 'welcomepackage'); // Added

        if (banner.displayMethod === 'Embeddable') {
            return banner.id ? `[${base} id="${banner.id}"]` : `[${base} id="..."]`;
        }
        return `[${base}_fixed]`;
    });

    const mergeWithExisting = (existingData) => {
        const deepMerge = (target, source) => {
            for (const key in source) {
                if (source.hasOwnProperty(key)) {
                    // Check if target has the key and both are objects (but not arrays, except specific cases)
                    if (key in target && target[key] !== null && typeof target[key] === 'object' && !Array.isArray(target[key]) &&
                        source[key] !== null && typeof source[key] === 'object' && !Array.isArray(source[key])) {
                        deepMerge(target[key], source[key]);
                    } else if (Array.isArray(source[key]) && key in target && Array.isArray(target[key])) {
                        // Smart array merge (replace or simple concat depending on context)
                        // For displayOn and selected items, replace is usually desired.
                        if (['posts', 'pages', 'categories', 'selectedTours', 'selectedHotels'].includes(key)) {
                            target[key] = [...source[key]];
                        } else if (key === 'gradientStops') {
                             target[key] = source[key].map(stop => ({...stop})); // Deep copy stops
                        }
                         else {
                            // Default: simple concat (might need refinement for other array types)
                            // target[key] = [...target[key], ...source[key]];
                             target[key] = [...source[key]]; // Default to replacing arrays for simplicity for now
                        }
                    } else {
                         // Assign primitives, nulls, or replace if types mismatch or target key doesn't exist
                        target[key] = source[key];
                    }
                }
            }
        };


        // Set mobile configured flags if editing an existing banner
        if (existingData.id) {
             if (existingData.single) existingData.isMobileConfigured = true;
             if (existingData.double) existingData.double.isMobileConfigured = true;
             if (existingData.api) existingData.api.isMobileConfigured = true;
             if (existingData.tour_carousel) existingData.tour_carousel.isMobileConfigured = true;
             if (existingData.hotel_carousel) existingData.hotel_carousel.isMobileConfigured = true;
             if (existingData.simple) existingData.isMobileConfigured = true;
             if (existingData.sticky_simple) existingData.isMobileConfigured = true;
             if (existingData.promotion) existingData.isMobileConfigured = true;
             // No mobile config for Welcome Package
        }

        deepMerge(banner, existingData);

        // Ensure displayOn exists and its arrays are valid AFTER merging
        if (!banner.displayOn) {
            banner.displayOn = { posts: [], pages: [], categories: [] };
        } else {
            if (!Array.isArray(banner.displayOn.posts)) banner.displayOn.posts = [];
            if (!Array.isArray(banner.displayOn.pages)) banner.displayOn.pages = [];
            if (!Array.isArray(banner.displayOn.categories)) banner.displayOn.categories = [];
        }

        // Ensure arrays/objects exist for specific types AFTER merging
        if (banner.type === 'promotion-banner') {
            if (!banner.promotion) banner.promotion = createDefaultPromotionPart();
            if (!Array.isArray(banner.promotion.links)) banner.promotion.links = [];
        }
        if (banner.type === 'hotel-carousel') {
             if (!banner.hotel_carousel) banner.hotel_carousel = createDefaultHotelCarouselPart();
            if (!Array.isArray(banner.hotel_carousel.selectedHotels)) banner.hotel_carousel.selectedHotels = [];
        }
        if (banner.type === 'tour-carousel') {
            if (!banner.tour_carousel) banner.tour_carousel = createDefaultTourCarouselPart();
            if (!Array.isArray(banner.tour_carousel.selectedTours)) banner.tour_carousel.selectedTours = [];
        }
         if (banner.type === 'welcome-package-banner') {
             if (!banner.welcome_package) banner.welcome_package = createDefaultWelcomePackagePart();
             // Ensure properties exist even if loading minimal data
             if (typeof banner.welcome_package.selectedKey === 'undefined') banner.welcome_package.selectedKey = null;
             if (typeof banner.welcome_package.selectedPrice === 'undefined') banner.welcome_package.selectedPrice = null;
             if (typeof banner.welcome_package.selectedOriginalPrice === 'undefined') banner.welcome_package.selectedOriginalPrice = null;
             if (typeof banner.welcome_package.html === 'undefined') banner.welcome_package.html = '';
         }
         // Add similar checks for other types if needed

    };


    const resetBannerState = () => {
        Object.assign(banner, createDefaultBanner());
    };

    return { banner, shortcode, mergeWithExisting, resetBannerState };
}