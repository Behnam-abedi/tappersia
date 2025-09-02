// import { reactive, computed } from 'vue'; // <--- This line was removed
const { reactive, computed } = Vue; // <--- This line was substituted

/**
 * Manages the core state, defaults, and computed properties of the banner.
 */
export function useBannerState() {
    const createDefaultPart = () => ({
        alignment: 'left', backgroundType: 'solid', bgColor: '#232323',
        gradientColor1: '#232323', gradientColor2: '#1A2B48', gradientAngle: 90,
        titleText: 'Awesome Title', titleColor: '#ffffff', titleSize: 15, titleWeight: '700',
        descText: 'A short and engaging description.', descColor: '#dddddd', descSize: 10, descWeight: '400',
        buttonText: 'Learn More', buttonLink: '#', buttonBgColor: '#00baa4',
        buttonTextColor: '#ffffff', buttonFontSize: 10, buttonBgHoverColor: '#008a7b',
        imageUrl: '', imageFit: 'none', enableCustomImageSize: false,
        imageWidth: null, imageHeight: null, imagePosRight: 0, imagePosBottom: 0,
    });
    
    const createDefaultApiDesign = () => ({
        layout: 'left',
        backgroundType: 'solid',
        bgColor: '#ffffff',
        gradientColor1: '#F0F2F5',
        gradientColor2: '#FFFFFF',
        gradientAngle: 90,
        enableBorder: true,
        borderWidth: 1,
        borderColor: '#E0E0E0',
        borderRadius: 15,
        enableCustomPadding: false,
        paddingTop: 23,
        paddingBottom: 23,
        paddingLeft: 55,
        paddingRight: 30,
        titleColor: '#000000', 
        titleSize: 18, 
        titleWeight: '700',
        starSize: 13,
        cityColor: '#000000',
        citySize: 10,
        ratingBoxBgColor: '#5191FA',
        ratingBoxColor: '#FFFFFF',
        ratingBoxSize: 10,
        ratingTextColor: '#5191FA',
        ratingTextSize: 10,
        reviewColor: '#999999',
        reviewSize: 10,
        priceFromColor: '#999999',
        priceFromSize: 10,
        priceAmountColor: '#00BAA4',
        priceAmountSize: 16,
        priceAmountWeight: '700',
        priceNightColor: '#999999',
        priceNightSize: 10,
    });

    const createDefaultSimplePart = () => ({
        backgroundType: 'solid',
        bgColor: '#ffffff',
        gradientColor1: '#F0F2F5',
        gradientColor2: '#FFFFFF',
        gradientAngle: 90,
        height: 74,
        borderRadius: 10,
        paddingY: 26,
        paddingX: 40,
        direction: 'ltr',
        text: 'This is a simple banner text.',
        textColor: '#000000',
        textSize: 17,
        textWeight: '700',
        buttonText: 'Click Here',
        buttonLink: '#',
        buttonBgColor: '#1EC2AF',
        buttonTextColor: '#ffffff',
        buttonBorderRadius: 3,
        buttonFontSize: 8,
        buttonFontWeight: '600',
        buttonPaddingY: 7,
        buttonPaddingX: 15,
        buttonMinWidth: 72,
    });

    const createDefaultPromotionPart = () => ({
        // General
        borderWidth: 1,
        borderColor: '#ffad1e57',
        borderRadius: 12,
        direction: 'ltr',
        // Header
        headerBackgroundType: 'solid',
        headerBgColor: '#FF731B',
        headerGradientColor1: '#FF731B',
        headerGradientColor2: '#F07100',
        headerGradientAngle: 90,
        iconUrl: '',
        iconSize: 24,
        headerPaddingX: 20,
        headerPaddingY: 12,
        headerText: 'Promotion Header!',
        headerTextColor: '#FFFFFF',
        headerFontSize: 18,
        headerFontWeight: '700',
        // Body
        bodyBackgroundType: 'solid',
        bodyBgColor: '#f071001f',
        bodyGradientColor1: '#f071001f',
        bodyGradientColor2: '#FFFFFF',
        bodyGradientAngle: 90,
        bodyPaddingX: 20,
        bodyPaddingY: 5,
        bodyText: 'This is the main content of the promotion banner. You can write a detailed paragraph here. To link a part of this text, use placeholders like [[this text]].',
        bodyTextColor: '#212121',
        bodyFontSize: 15,
        bodyFontWeight: '400',
        bodyLineHeight: 1.7,
        // Links
        links: [],
    });

    const createDefaultHtmlPart = () => ({
        html: '<div style="padding: 20px; text-align: center;">\n  <h2 style="color: #333;">Welcome!</h2>\n  <p style="color: #555;">This is your custom HTML banner.</p>\n  <button style="background-color: #00baa4; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">Click Me</button>\n</div>'
    });
    
    // START: ADDED SECTION
    const createDefaultHtmlSidebarPart = () => ({
        html: '<div style="padding: 15px; border: 1px solid #ddd; text-align: center;">\n  <h4 style="color: #333; margin-top: 0;">Sidebar Content</h4>\n  <p style="color: #555; font-size: 14px;">Your custom HTML for sidebar.</p>\n</div>'
    });
    // END: ADDED SECTION

    const createDefaultBanner = () => ({
        id: null, name: '', displayMethod: 'Fixed', isActive: true, type: null,
        displayOn: { posts: [], pages: [], categories: [] },
        left: createDefaultPart(), right: createDefaultPart(), single: createDefaultPart(),
        simple: createDefaultSimplePart(),
        sticky_simple: createDefaultSimplePart(),
        promotion: createDefaultPromotionPart(),
        content_html: createDefaultHtmlPart(),
        // START: ADDED SECTION
        content_html_sidebar: createDefaultHtmlSidebarPart(),
        // END: ADDED SECTION
        api: { 
            apiType: null, 
            selectedHotel: null, 
            selectedTour: null,
            design: createDefaultApiDesign(),
        },
    });

    const banner = reactive(createDefaultBanner());

    const shortcode = computed(() => {
        if (!banner.type) return '';
        // START: MODIFIED SECTION
        const base = banner.type.replace(/-/g, '')
                                .replace('contenthtmlbanner', 'contenthtml')
                                .replace('contenthtmlsidebarbanner', 'contenthtmlsidebar');
        // END: MODIFIED SECTION
        if (banner.displayMethod === 'Embeddable') {
            return banner.id ? `[${base} id="${banner.id}"]` : `[${base} id="..."]`;
        }
        return `[${base}_fixed]`;
    });
    
    const mergeWithExisting = (existingData) => {
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