// tappersia/assets/js/admin/app-logic/main.js
const { createApp, ref, onMounted, watch, nextTick } = Vue;

// Import کامپوننت‌ها به جای composables
import { HotelCarouselLogic } from './composables/useHotelCarousel.js';
import { TourCarouselLogic } from './composables/useTourCarousel.js';

// Existing imports...
import { useHotelCarouselValidation } from './composables/useHotelCarouselValidation.js';
import { useHotelThumbnails } from './composables/useHotelThumbnails.js';
import { useAjax } from '../composables/useAjax.js';
import { useBannerState } from '../composables/banner-state/bannerState.js';
import { useApiBanner } from '../composables/useApiBanner.js';
import { useDisplayConditions } from '../composables/useDisplayConditions.js';
import { ImageLoader } from './components.js'; // Keep import
import { useAppSetup } from './composables/useAppSetup.js';
import { useBannerActions } from './composables/useBannerActions.js';
import { useBannerSync } from './composables/useBannerSync.js';
import { useBannerStyling } from './composables/useBannerStyling.js';
import { useComputedProperties } from './composables/useComputedProperties.js';
import { usePromotionBanner } from './composables/usePromotionBanner.js';
import { useTourCarouselValidation } from './composables/useTourCarouselValidation.js';
import { useTourThumbnails } from './composables/useTourThumbnails.js';
import { useFlightTicket } from '../composables/useFlightTicket.js';
import { useWelcomePackageBanner } from '../composables/useWelcomePackageBanner.js';

// Assuming YabModal is globally available via admin-modal-component.js
// If not, you'd need to import it as well.
// import { YabModal } from '../path/to/admin-modal-component.js'; // Example path

export function initializeApp(yabData) {
    const app = createApp({
        // ثبت کامپوننت‌ها
        components: {
            'yab-modal': YabModal,
            'image-loader': ImageLoader,
            'tour-carousel-logic': TourCarouselLogic,    // ثبت شد
            'hotel-carousel-logic': HotelCarouselLogic   // ثبت شد
        },
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
            const computedProps = useComputedProperties(banner, currentView, selectedDoubleBanner);
            const flightTicketLogic = useFlightTicket(banner, showModal, ajax);
            const welcomePackageLogic = useWelcomePackageBanner(banner, showModal, ajax);
            useBannerSync(banner, currentView);

            // --- Tour Carousel ---
            useTourCarouselValidation(banner, showModal);
            const tourThumbnailContainerRef = ref(null);
            const { thumbnailTours, isLoadingThumbnails: isLoadingTourThumbnails } = useTourThumbnails(banner, ajax, tourThumbnailContainerRef);

            // --- Hotel Carousel ---
            useHotelCarouselValidation(banner, showModal);
            const hotelThumbnailContainerRef = ref(null);
            const { thumbnailHotels, isLoadingThumbnails: isLoadingHotelThumbnails } = useHotelThumbnails(banner, ajax, hotelThumbnailContainerRef);


            // --- START: Removed Flight Ticket Clip-Path Script ---
            /*
            // This function and its watch calls are now obsolete as the shape is done via CSS/SVG/HTML structure.
            function updateClipPath() { ... }
            watch(() => [banner.flight_ticket.from, banner.flight_ticket.to], ([newFrom, newTo]) => { ... });
            */
            // --- END: Removed Flight Ticket Clip-Path Script ---


            // --- Lifecycle Hooks ---
            onMounted(() => {
                // ... (keep existing onMounted logic) ...
                 displayConditionsLogic.siteData.posts = yabData.posts || [];
                displayConditionsLogic.siteData.pages = yabData.pages || [];
                displayConditionsLogic.siteData.categories = yabData.categories || [];

                if (yabData.existing_banner) {
                     const existingBannerCopy = JSON.parse(JSON.stringify(yabData.existing_banner));
                     mergeWithExisting(existingBannerCopy);

                    if (banner.type === 'api-banner' && banner.api.selectedHotel?.id) {
                        apiBannerLogic.fetchFullHotelDetails(banner.api.selectedHotel.id);
                    }
                     if (banner.type === 'api-banner' && banner.api.selectedTour?.id) {
                        apiBannerLogic.fetchFullTourDetails(banner.api.selectedTour.id);
                    }

                    // START: Added logic for flight ticket
                    if (banner.type === 'flight-ticket' && banner.flight_ticket.from && banner.flight_ticket.to) {
                        // Fetch flight price on load if airports are already selected
                        flightTicketLogic.fetchCheapestFlight();
                        // REMOVED: Clip-path initialization
                    }
                    // END: Added logic

                    appState.value = 'editor';
                } else {
                    appState.value = 'selection';
                }
            });

            // Helper function to initialize/reinitialize Coloris
            const reinitializeColoris = () => {
                 if (typeof Coloris !== 'undefined') {
                     // FIX: Explicit 50ms delay added here to resolve the Coloris race condition
                     setTimeout(() => { 
                        Coloris({
                            theme: 'pill',
                            themeMode: 'dark',
                            format: 'mixed',
                            alpha: true,
                            onChange: (color, inputEl) => {
                                if (inputEl) {
                                    const event = new Event('input', { bubbles: true });
                                    inputEl.value = color;
                                    inputEl.dispatchEvent(event);

                                    const preview = inputEl.previousElementSibling;
                                    if (preview && preview.style) {
                                        preview.style.backgroundColor = color;
                                    }
                                }
                            }
                        });
                     }, 50); // FIX: Explicit 50ms delay added here
                 } else {
                     console.error("Coloris library is not loaded.");
                 }
            };


            // Watchers for view and state changes
            watch(currentView, async (newView, oldView) => {
                 if (newView !== oldView) {
                     await nextTick();
                     reinitializeColoris(); 
                 }
            });
            watch(selectedDoubleBanner, async (newSelection, oldSelection) => {
                 if (newSelection !== oldSelection) {
                     await nextTick();
                      reinitializeColoris(); 
                 }
            });

             watch(appState, async (newState, oldState) => {
                if (newState === 'editor' && oldState !== 'editor') {
                    await nextTick();
                    reinitializeColoris();
                }
             }, { immediate: true }); 


            // --- Helper Methods ---
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

             const copyPlaceholder = (placeholder) => {
                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(placeholder).then(() => {
                        showModal('Copied!', `Placeholder "${placeholder}" copied to clipboard.`);
                    }).catch(err => {
                        console.error('Failed to copy placeholder: ', err);
                        showModal('Error', 'Could not copy placeholder.');
                    });
                } else {
                    try {
                        const textArea = document.createElement("textarea");
                        textArea.value = placeholder;
                        textArea.style.position = "absolute"; textArea.style.left = "-9999px";
                        document.body.appendChild(textArea);
                        textArea.select(); document.execCommand('copy');
                        document.body.removeChild(textArea);
                        showModal('Copied!', `Placeholder "${placeholder}" copied to clipboard.`);
                    } catch (err) {
                        console.error('Fallback copy failed: ', err);
                        showModal('Error', 'Could not copy placeholder.');
                    }
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
                // --- START FIX: Manually spread computedProps and rename settings ---
                previewBodyText: computedProps.previewBodyText,
                welcomePackagePreviewHtml: computedProps.welcomePackagePreviewHtml,
                apiItem: computedProps.apiItem,
                isApiHotel: computedProps.isApiHotel,
                computedSettings: computedProps.settings, // <--- Renamed here
                // --- END FIX ---
                ...flightTicketLogic,
                ...welcomePackageLogic,
                 // Helpers
                addGradientStop, removeGradientStop,
                copyPlaceholder,
                // Tour Carousel refs & data
                tourThumbnailContainerRef,
                thumbnailTours,
                isLoadingTourThumbnails,
                // Hotel Carousel refs & data
                hotelThumbnailContainerRef,
                thumbnailHotels,
                isLoadingHotelThumbnails,
                // *** حذف کامپوننت‌های منطقی از return ***
            };
        }
    });

    app.mount('#yab-app');
}