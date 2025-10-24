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
    createDefaultWelcomePackagePart, // Added import for Welcome Package
} from './defaults/index.js';

export function useBannerState() {
    const createDefaultBanner = () => ({
        id: null, name: '', displayMethod: 'Fixed', isActive: true, type: null,
        isMobileConfigured: false,
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
                                .replace('welcomepackagebanner', 'welcomepackagebanner'); // Add welcome package

        if (banner.displayMethod === 'Embeddable') {
            return banner.id ? `[${base} id="${banner.id}"]` : `[${base} id="..."]`;
        }
        return `[${base}_fixed]`;
    });

    const mergeWithExisting = (existingData) => {
        const deepMerge = (target, source) => {
            for (const key in source) {
                if (source.hasOwnProperty(key)) {
                    if (source[key] instanceof Object && key in target && target[key] instanceof Object && !(source[key] instanceof Array) && key !== 'gradientStops') {
                         deepMerge(target[key], source[key]);
                    } else {
                        // Directly assign arrays or primitives
                        target[key] = source[key];
                    }
                }
            }
        };


        // Set mobile configured flags if editing an existing banner
        if (existingData.id) {
             if (existingData.single) existingData.isMobileConfigured = true; // Use banner level flag for single
             if (existingData.double) existingData.double.isMobileConfigured = true;
             if (existingData.api) existingData.api.isMobileConfigured = true;
             if (existingData.tour_carousel) existingData.tour_carousel.isMobileConfigured = true;
             if (existingData.hotel_carousel) existingData.hotel_carousel.isMobileConfigured = true;
             if (existingData.simple) existingData.isMobileConfigured = true; // Use banner level flag
             if (existingData.sticky_simple) existingData.isMobileConfigured = true; // Use banner level flag
             if (existingData.promotion) existingData.isMobileConfigured = true; // Use banner level flag
             // Welcome package banner doesn't have separate mobile config currently
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

        // Ensure arrays exist for specific types
        if (banner.type === 'promotion-banner' && !Array.isArray(banner.promotion.links)) {
            banner.promotion.links = [];
        }
        if (banner.type === 'hotel-carousel' && !Array.isArray(banner.hotel_carousel.selectedHotels)) {
            banner.hotel_carousel.selectedHotels = [];
        }
        if (banner.type === 'tour-carousel' && !Array.isArray(banner.tour_carousel.selectedTours)) {
            banner.tour_carousel.selectedTours = [];
        }
         // Ensure welcome package object exists
         if (banner.type === 'welcome-package-banner' && typeof banner.welcome_package !== 'object') {
             banner.welcome_package = createDefaultWelcomePackagePart();
         }

    };

    const resetBannerState = () => {
        Object.assign(banner, createDefaultBanner());
    };

    return { banner, shortcode, mergeWithExisting, resetBannerState };
}
