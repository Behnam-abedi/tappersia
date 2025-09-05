// tappersia/assets/js/admin/composables/banner-state/bannerState.js
const { reactive, computed } = Vue;
import { 
    createDefaultPart, 
    createDefaultMobilePart, 
    createDefaultApiDesign, 
    createDefaultSimplePart, 
    createDefaultPromotionPart, 
    createDefaultHtmlPart, 
    createDefaultHtmlSidebarPart 
} from './defaults.js';

export function useBannerState() {
    const createDefaultBanner = () => {
        const left = createDefaultPart();
        left.width = 50; left.widthUnit = '%'; left.minHeight = 185; left.paddingTop = 35; left.paddingBottom = 35; left.paddingRight = 31; left.paddingLeft = 31; left.buttonMinWidth = 143; left.marginTopDescription = 12;

        const right = createDefaultPart();
        right.width = 50; right.widthUnit = '%'; right.minHeight = 185; right.paddingTop = 35; right.paddingBottom = 35; right.paddingRight = 31; right.paddingLeft = 31; right.buttonMinWidth = 143; right.marginTopDescription = 12;

        const left_mobile = createDefaultMobilePart();
        left_mobile.paddingTop = 35; left_mobile.paddingBottom = 35; left_mobile.paddingRight = 31; left_mobile.paddingLeft = 31; left_mobile.buttonMinWidth = 143; left_mobile.marginTopDescription = 12;

        const right_mobile = createDefaultMobilePart();
        right_mobile.paddingTop = 35; right_mobile.paddingBottom = 35; right_mobile.paddingRight = 31; right_mobile.paddingLeft = 31; right_mobile.buttonMinWidth = 143; right_mobile.marginTopDescription = 12;

        return {
            id: null, name: '', displayMethod: 'Fixed', isActive: true, type: null,
            isMobileConfigured: false, // Flag to track if mobile has been configured once
            displayOn: { posts: [], pages: [], categories: [] },
            left, right, left_mobile, right_mobile,
            single: createDefaultPart(),
            single_mobile: createDefaultMobilePart(),
            simple: createDefaultSimplePart(),
            sticky_simple: createDefaultSimplePart(),
            promotion: createDefaultPromotionPart(),
            content_html: createDefaultHtmlPart(),
            content_html_sidebar: createDefaultHtmlSidebarPart(),
            api: {
                apiType: null,
                selectedHotel: null,
                selectedTour: null,
                design: createDefaultApiDesign(),
            },
        };
    };

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
        if (!existingData.single_mobile) {
            existingData.single_mobile = createDefaultMobilePart();
        }
        if (!existingData.left_mobile) {
            existingData.left_mobile = createDefaultMobilePart();
        }
        if (!existingData.right_mobile) {
            existingData.right_mobile = createDefaultMobilePart();
        }

        // If loading an existing banner, mark mobile as "configured" to prevent auto-copying
        if (existingData.id) {
            existingData.isMobileConfigured = true;
        }

        for (const key in existingData) {
            if (Object.prototype.hasOwnProperty.call(existingData, key)) {
                if (typeof existingData[key] === 'object' && existingData[key] !== null && !Array.isArray(existingData[key]) && banner[key]) {
                     if (key === 'api' && existingData[key].design) {
                        if (!banner.api.design) {
                            banner.api.design = {};
                        }
                        Object.assign(banner.api.design, existingData[key].design);
                        Object.assign(banner.api, { ...existingData.api, design: banner.api.design });
                    } else {
                        Object.assign(banner[key], existingData[key]);
                    }
                } else {
                    banner[key] = existingData[key];
                }
            }
            if (key === 'promotion' && typeof existingData[key].direction === 'undefined') {
                existingData[key].direction = 'ltr';
            }
        }
        
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