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
    createDefaultPromotionPart, 
    createDefaultHtmlPart, 
    createDefaultHtmlSidebarPart 
} from './defaults/index.js'; // مسير مستقيم به پوشه مقادير پيش‌فرض

export function useBannerState() {
    const createDefaultBanner = () => ({
        id: null, name: '', displayMethod: 'Fixed', isActive: true, type: null,
        isMobileConfigured: false, 
        displayOn: { posts: [], pages: [], categories: [] },
        
        // Single Banner State
        single: createDefaultPart(),
        single_mobile: createDefaultMobilePart(),

        // START: NEW DOUBLE BANNER STATE
        double: {
            isMobileConfigured: false,
            desktop: {
                left: createDefaultDoubleBannerPart(),
                right: createDefaultDoubleBannerPart()
            },
            mobile: {
                left: createDefaultDoubleBannerMobilePart(),
                right: createDefaultDoubleBannerMobilePart()
            }
        },
        // END: NEW DOUBLE BANNER STATE

        // START: ADDED simple_mobile state
        simple: createDefaultSimplePart(),
        simple_mobile: createDefaultSimpleBannerMobilePart(),
        // END: ADDED simple_mobile state
        sticky_simple: createDefaultSimplePart(),
        promotion: createDefaultPromotionPart(),
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
    });

    const banner = reactive(createDefaultBanner());

    const shortcode = computed(() => {
        if (!banner.type) return '';
        const base = banner.type.replace(/-/g, '')
                                .replace('contenthtmlbanner', 'contenthtml')
                                .replace('contenthtmlsidebarbanner', 'contenthtmlsidebar');
        if (banner.displayMethod === 'Embeddable') {
            return banner.id ? `[${base} id="${banner.id}"]` : `[${base} id="..."]`;
        }
        return `[${base}_fixed]`;
    });
    
    const mergeWithExisting = (existingData) => {
        // --- Deep merge helper ---
        const deepMerge = (target, source) => {
            for (const key in source) {
                if (source.hasOwnProperty(key)) {
                    if (source[key] instanceof Object && key in target && target[key] instanceof Object) {
                        deepMerge(target[key], source[key]);
                    } else {
                        target[key] = source[key];
                    }
                }
            }
        };

        // If loading an existing banner, mark mobile as "configured" to prevent auto-copying
        if (existingData.id) {
            existingData.isMobileConfigured = true;
            if (existingData.double) {
                existingData.double.isMobileConfigured = true;
            }
            if (existingData.api) {
                existingData.api.isMobileConfigured = true;
            }
        }
        
        deepMerge(banner, existingData);
        
        // --- Final checks and sanitization after merge ---
        if (!banner.displayOn) {
            banner.displayOn = { posts: [], pages: [], categories: [] };
        } else {
            if (!Array.isArray(banner.displayOn.posts)) banner.displayOn.posts = [];
            if (!Array.isArray(banner.displayOn.pages)) banner.displayOn.pages = [];
            if (!Array.isArray(banner.displayOn.categories)) banner.displayOn.categories = [];
        }

        if (banner.type === 'promotion-banner' && !Array.isArray(banner.promotion.links)) {
            banner.promotion.links = [];
        }
    };

    const resetBannerState = () => {
        Object.assign(banner, createDefaultBanner());
    };

    return { banner, shortcode, mergeWithExisting, resetBannerState };
}