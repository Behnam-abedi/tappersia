// tappersia/assets/js/admin/app-logic/main.js
const { createApp, ref, onMounted, watch } = Vue;

// Existing imports...
import { useHotelCarousel } from './composables/useHotelCarousel.js';
import { useHotelCarouselValidation } from './composables/useHotelCarouselValidation.js';
import { useHotelThumbnails } from './composables/useHotelThumbnails.js';
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
import { useTourCarousel } from './composables/useTourCarousel.js';
import { useTourCarouselValidation } from './composables/useTourCarouselValidation.js';
import { useTourThumbnails } from './composables/useTourThumbnails.js';
import { useFlightTicket } from '../composables/useFlightTicket.js';

// New import for Welcome Package
import { useWelcomePackageBanner } from '../composables/useWelcomePackageBanner.js';


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
            const apiBannerLogic = useApiBanner(banner, showModal, ajax);
            const promotionBannerLogic = usePromotionBanner(banner, showModal);
            const bannerStyling = useBannerStyling(banner);
            const computedProperties = useComputedProperties(banner, currentView, selectedDoubleBanner);
            const flightTicketLogic = useFlightTicket(banner, showModal, ajax);
            useBannerSync(banner, currentView);

            // --- Tour Carousel ---
            useTourCarouselValidation(banner, showModal);
            const tourThumbnailContainerRef = ref(null);
            const { thumbnailTours, isLoadingThumbnails: isLoadingTourThumbnails } = useTourThumbnails(banner, ajax, tourThumbnailContainerRef);

            // --- Hotel Carousel ---
            useHotelCarouselValidation(banner, showModal);
            const hotelThumbnailContainerRef = ref(null);
            const { thumbnailHotels, isLoadingThumbnails: isLoadingHotelThumbnails } = useHotelThumbnails(banner, ajax, hotelThumbnailContainerRef);

            // --- Welcome Package ---
            const welcomePackageLogic = useWelcomePackageBanner(banner, showModal, ajax); // Instantiate the new composable


            // --- Lifecycle Hooks ---
            onMounted(() => {
                displayConditionsLogic.siteData.posts = yabData.posts || [];
                displayConditionsLogic.siteData.pages = yabData.pages || [];
                displayConditionsLogic.siteData.categories = yabData.categories || [];

                if (yabData.existing_banner) {
                     const existingBannerCopy = JSON.parse(JSON.stringify(yabData.existing_banner));
                     mergeWithExisting(existingBannerCopy);

                    // Fetch details for API banner if needed
                    if (banner.type === 'api-banner' && banner.api.selectedHotel?.id) {
                        apiBannerLogic.fetchFullHotelDetails(banner.api.selectedHotel.id);
                    }
                     if (banner.type === 'api-banner' && banner.api.selectedTour?.id) {
                        apiBannerLogic.fetchFullTourDetails(banner.api.selectedTour.id);
                    }
                    // Welcome package fetches prices live on frontend, initial fetch needed for admin preview/selection
                    if (banner.type === 'welcome-package-banner' && banner.welcome_package.selectedPackageKey) {
                        // We might want to fetch the package details again on load
                        // to ensure the stored initial prices are reasonably up-to-date for preview.
                        // Or rely on the data saved during the last selection. Let's rely on saved data for now.
                    }

                    appState.value = 'editor';
                } else {
                    appState.value = 'selection';
                }
            });

            // --- Helper Methods (Gradient Stops) ---
            const addGradientStop = (settings) => {
                 if (!settings.gradientStops) settings.gradientStops = [];
                 const lastStop = settings.gradientStops.length > 0 ? settings.gradientStops[settings.gradientStops.length - 1].stop : 0;
                 const newStopPosition = Math.min(100, lastStop + 10);
                 settings.gradientStops.push({ color: 'rgba(255, 255, 255, 0.5)', stop: newStopPosition });
                 settings.gradientStops.sort((a, b) => a.stop - b.stop);
             };
            const removeGradientStop = (settings, index) => {
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
                ...apiBannerLogic,
                ...displayConditionsLogic,
                ...promotionBannerLogic,
                ...bannerStyling,
                ...computedProperties,
                ...flightTicketLogic,
                ...welcomePackageLogic, // Expose Welcome Package logic
                 // Helpers
                addGradientStop, removeGradientStop,
                // Tour Carousel refs & data
                tourThumbnailContainerRef,
                thumbnailTours,
                isLoadingTourThumbnails,
                // Hotel Carousel refs & data
                hotelThumbnailContainerRef,
                thumbnailHotels,
                isLoadingHotelThumbnails,
            };
        },
        components: {
            'yab-modal': YabModal,
            'image-loader': ImageLoader,
            'TourCarouselLogic': useTourCarousel(),
            'HotelCarouselLogic': useHotelCarousel(),
        }
    });

    app.mount('#yab-app');
}
