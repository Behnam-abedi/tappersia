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
        // Layout & Background
        layout: 'left',
        backgroundType: 'solid',
        bgColor: '#ffffff',
        gradientColor1: '#F0F2F5',
        gradientColor2: '#FFFFFF',
        gradientAngle: 90,
        // Border
        enableBorder: true,
        borderWidth: 1,
        borderColor: '#E0E0E0',
        borderRadius: 15,
        // Content Padding
        enableCustomPadding: false,
        paddingTop: 23,
        paddingBottom: 23,
        paddingLeft: 55,
        paddingRight: 30,
        // Title
        titleColor: '#000000', 
        titleSize: 18, 
        titleWeight: '700',
        // Stars & City
        starSize: 13,
        cityColor: '#000000',
        citySize: 10,
        // Rating Box
        ratingBoxBgColor: '#5191FA',
        ratingBoxColor: '#FFFFFF',
        ratingBoxSize: 10,
        // Rating Text ("Very Good")
        ratingTextColor: '#5191FA',
        ratingTextSize: 10,
        // Review Count
        reviewColor: '#999999',
        reviewSize: 10,
        // Price
        priceFromColor: '#999999',
        priceFromSize: 10,
        priceAmountColor: '#00BAA4',
        priceAmountSize: 16,
        priceAmountWeight: '700',
        priceNightColor: '#999999',
        priceNightSize: 10, // Added this missing property
    });

    const banner = reactive({
        id: null, name: '', displayMethod: 'Fixed', isActive: true, type: null,
        displayOn: { posts: [], pages: [], categories: [] },
        left: createDefaultPart(), right: createDefaultPart(), single: createDefaultPart(),
        api: { 
            apiType: null, 
            selectedHotel: null, 
            selectedTour: null,
            design: createDefaultApiDesign(),
        },
    });

    const shortcode = computed(() => {
        if (!banner.type) return '';
        const base = banner.type.replace('-', '');
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
        }
    };

    return { banner, shortcode, mergeWithExisting };
}
