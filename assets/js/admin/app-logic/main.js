// tappersia/assets/js/admin/app-logic/main.js
const { createApp, ref, onMounted } = Vue;

import { useAjax } from '../composables/useAjax.js';
// مسیر مستقیم به فایل اصلی
import { useBannerState } from '../composables/banner-state/bannerState.js'; 
import { useApiBanner } from '../composables/useApiBanner.js';
import { useDisplayConditions } from '../composables/useDisplayConditions.js';
import { ImageLoader } from './components.js';

// Import new modular composables
import { useAppSetup } from './composables/useAppSetup.js';
import { useBannerActions } from './composables/useBannerActions.js';
import { useBannerSync } from './composables/useBannerSync.js';
import { useBannerStyling } from './composables/useBannerStyling.js';
import { useComputedProperties } from './composables/useComputedProperties.js';
import { usePromotionBanner } from './composables/usePromotionBanner.js';

export function initializeApp(yabData) {
    createApp({
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
            
            useBannerSync(banner, currentView); // Handles all watchers

            // --- Lifecycle Hooks ---
            onMounted(() => {
                displayConditionsLogic.siteData.posts = yabData.posts || [];
                displayConditionsLogic.siteData.pages = yabData.pages || [];
                displayConditionsLogic.siteData.categories = yabData.categories || [];
    
                if (yabData.existing_banner) {
                    mergeWithExisting(JSON.parse(JSON.stringify(yabData.existing_banner)));
                     if (banner.type === 'api-banner' && banner.api.selectedHotel?.id) {
                        apiBannerLogic.fetchFullHotelDetails(banner.api.selectedHotel.id);
                    }
                     if (banner.type === 'api-banner' && banner.api.selectedTour?.id) {
                        apiBannerLogic.fetchFullTourDetails(banner.api.selectedTour.id);
                    }
                    appState.value = 'editor';
                } else {
                    appState.value = 'selection';
                }
            });

            // --- Helper Methods (for template) ---
            const addGradientStop = (settings) => {
                if (!settings.gradientStops) {
                    settings.gradientStops = [];
                }
                settings.gradientStops.push({ color: 'rgba(255, 255, 255, 0.5)', stop: 100 });
            };

            const removeGradientStop = (settings, index) => {
                if (settings.gradientStops.length > 1) {
                    settings.gradientStops.splice(index, 1);
                } else {
                    showModal('Info', 'A gradient must have at least one color stop.');
                }
            };
            
            return {
                // App State
                appState, isSaving, banner, shortcode, modalComponent, currentView, selectedDoubleBanner,

                // App Logic & Actions
                selectElementType: (type) => { banner.type = selectElementType(type) },
                goBackToSelection, goToListPage,
                ...bannerActions,
                
                // Composables
                ...apiBannerLogic,
                ...displayConditionsLogic,
                ...promotionBannerLogic,
                ...bannerStyling,
                ...computedProperties,

                // Helpers
                addGradientStop, removeGradientStop,
            };
        },
        components: { 
            'yab-modal': YabModal,
            'image-loader': ImageLoader,
        }
    }).mount('#yab-app');
}