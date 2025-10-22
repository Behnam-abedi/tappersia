// tappersia/assets/js/admin/app-logic/main.js
const { createApp, ref, onMounted, watch } = Vue;

// ... other imports ...
import { useHotelCarousel } from './composables/useHotelCarousel.js'; // Import the new composable
import { useHotelCarouselValidation } from './composables/useHotelCarouselValidation.js'; // Import validation
import { useHotelThumbnails } from './composables/useHotelThumbnails.js'; // Import thumbnails logic

// Keep existing imports
import { useAjax } from '../composables/useAjax.js';
import { useBannerState } from '../composables/banner-state/bannerState.js';
import { useApiBanner } from '../composables/useApiBanner.js';
import { useDisplayConditions } from '../composables/useDisplayConditions.js';
import { ImageLoader } from './components.js';
import { useAppSetup } from './composables/useAppSetup.js';
import { useBannerActions } from './composables/useBannerActions.js';
import { useBannerSync } from './composables/useBannerSync.js';
import { useBannerStyling } from './composables/useBannerStyling.js';
import { useComputedProperties } from './composables/useComputedProperties.js';
import { usePromotionBanner } from './composables/usePromotionBanner.js';
import { useTourCarousel } from './composables/useTourCarousel.js'; // Keep tour carousel
import { useTourCarouselValidation } from './composables/useTourCarouselValidation.js';
import { useTourThumbnails } from './composables/useTourThumbnails.js';
import { useFlightTicket } from '../composables/useFlightTicket.js';


export function initializeApp(yabData) {
    const app = createApp({
        setup() {
            // --- Core State & Composables ---
            const { banner, shortcode, mergeWithExisting, resetBannerState } = useBannerState();
            const ajax = useAjax(yabData.ajax_url, yabData.nonce);

            // --- UI State ---
            const currentView = ref('desktop');
            const selectedDoubleBanner = ref('left');

            // --- Modular Composables ---
            const { appState, isSaving, modalComponent, showModal, selectElementType, goBackToSelection, goToListPage } = useAppSetup(yabData, resetBannerState);
            const bannerActions = useBannerActions(banner, isSaving, showModal, ajax);
            const displayConditionsLogic = useDisplayConditions(banner, ajax);
            // useApiBanner includes both hotel and tour API logic now
            const apiBannerLogic = useApiBanner(banner, showModal, ajax);
            const promotionBannerLogic = usePromotionBanner(banner, showModal);
            const bannerStyling = useBannerStyling(banner);
            const computedProperties = useComputedProperties(banner, currentView, selectedDoubleBanner);
            const flightTicketLogic = useFlightTicket(banner, showModal, ajax);

            useBannerSync(banner, currentView); // Handles syncing desktop to mobile

            // --- Tour Carousel Specific Logic ---
            useTourCarouselValidation(banner, showModal);
            const tourThumbnailContainerRef = ref(null); // Keep separate ref for tour
            const { thumbnailTours, isLoadingThumbnails: isLoadingTourThumbnails } = useTourThumbnails(banner, ajax, tourThumbnailContainerRef);

            // --- NEW: Hotel Carousel Specific Logic ---
            useHotelCarouselValidation(banner, showModal); // Use hotel validation
            const hotelThumbnailContainerRef = ref(null); // New ref for hotel thumbnails
            const { thumbnailHotels, isLoadingThumbnails: isLoadingHotelThumbnails } = useHotelThumbnails(banner, ajax, hotelThumbnailContainerRef); // Use hotel thumbnail logic


            // --- Lifecycle Hooks ---
            onMounted(() => {
                 // ... (existing siteData setup) ...
                displayConditionsLogic.siteData.posts = yabData.posts || [];
                displayConditionsLogic.siteData.pages = yabData.pages || [];
                displayConditionsLogic.siteData.categories = yabData.categories || [];


                if (yabData.existing_banner) {
                    // Use a deep copy to avoid reactivity issues during merge
                     const existingBannerCopy = JSON.parse(JSON.stringify(yabData.existing_banner));
                     mergeWithExisting(existingBannerCopy);

                    // Fetch details for API banner if needed (no change here)
                    if (banner.type === 'api-banner' && banner.api.selectedHotel?.id) {
                        apiBannerLogic.fetchFullHotelDetails(banner.api.selectedHotel.id);
                    }
                     if (banner.type === 'api-banner' && banner.api.selectedTour?.id) {
                        apiBannerLogic.fetchFullTourDetails(banner.api.selectedTour.id);
                    }
                    // No specific fetch needed here for carousels on mount, thumbnails/preview handle it

                    appState.value = 'editor';
                } else {
                    appState.value = 'selection';
                }
            });

            // --- Helper Methods ---
            const addGradientStop = (settings) => { /* ... (no changes) ... */
                 if (!settings.gradientStops) {
                    settings.gradientStops = [];
                 }
                // Add new stop slightly offset from the last one if possible
                 const lastStop = settings.gradientStops.length > 0 ? settings.gradientStops[settings.gradientStops.length - 1].stop : 0;
                 const newStopPosition = Math.min(100, lastStop + 10); // Add 10% or cap at 100%
                 settings.gradientStops.push({ color: 'rgba(255, 255, 255, 0.5)', stop: newStopPosition });
                 // Optionally re-sort stops after adding
                 settings.gradientStops.sort((a, b) => a.stop - b.stop);
             };
            const removeGradientStop = (settings, index) => { /* ... (no changes) ... */
                if (settings.gradientStops.length > 1) {
                    settings.gradientStops.splice(index, 1);
                } else {
                    showModal('Info', 'A gradient must have at least one color stop.');
                }
             };

            return {
                // Core state & methods
                appState, isSaving, banner, shortcode, modalComponent, currentView, selectedDoubleBanner,
                selectElementType: (type) => { banner.type = selectElementType(type) },
                goBackToSelection, goToListPage,
                ...bannerActions,
                ajax,
                 // Composables (spread syntax)
                ...apiBannerLogic, // Includes hotel & tour modal logic
                ...displayConditionsLogic,
                ...promotionBannerLogic,
                ...bannerStyling,
                ...computedProperties,
                ...flightTicketLogic,
                 // Helpers
                addGradientStop, removeGradientStop,
                // Tour Carousel refs & data
                tourThumbnailContainerRef, // Keep separate tour ref
                thumbnailTours,
                isLoadingTourThumbnails,
                // NEW: Hotel Carousel refs & data
                hotelThumbnailContainerRef, // Add hotel ref
                thumbnailHotels,
                isLoadingHotelThumbnails,
            };
        },
        components: {
            'yab-modal': YabModal,
            'image-loader': ImageLoader,
            // Register TourCarouselLogic (assuming useTourCarousel returns a component options object)
             'TourCarouselLogic': useTourCarousel(),
            // Register HotelCarouselLogic
             'HotelCarouselLogic': useHotelCarousel(), // Register new component
        }
    });

     // Mount the app
    app.mount('#yab-app');
}