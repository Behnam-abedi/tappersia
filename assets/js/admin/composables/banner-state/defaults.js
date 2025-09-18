// tappersia/assets/js/admin/composables/banner-state/defaults.js
export const createDefaultPart = () => ({
    alignment: 'left',
    backgroundType: 'solid',
    bgColor: 'rgba(12, 165, 234, 0.85)',
    gradientAngle: 90,
    gradientStops: [
        { color: 'rgba(12, 165, 234, 0.8)', stop: 0 },
        { color: 'rgba(18, 76, 136, 0.8)', stop: 100 }
    ],
    titleText: 'Awesome Title',
    titleColor: '#ffffff',
    titleSize: 24,
    titleWeight: '700',
    titleLineHeight: 1,
    descText: 'A short and engaging description.',
    descColor: '#ffffff',
    descSize: 14,
    descWeight: '500',
    descLineHeight: 1.5,
    descWidth: 100, 
    descWidthUnit: '%',
    buttonText: 'Learn More',
    buttonLink: '#',
    buttonBgColor: '#124C88',
    buttonTextColor: '#ffffff',
    buttonFontSize: 14,
    buttonBgHoverColor: '#10447B',
    buttonFontWeight: '500',
    buttonLineHeight: 1,
    imageUrl: '',
    enableCustomImageSize: false,
    imageWidth: null,
    imageWidthUnit: 'px',
    imageHeight: null,
    imageHeightUnit: 'px',
    imagePosRight: 0,
    imagePosBottom: 0,
    enableCustomDimensions: true,
    width: 100,
    widthUnit: '%',
    height: 'auto',
    minHeight: 190,
    minHeightUnit: 'px',
    enableBorder: false,
    borderWidth: 1,
    borderColor: '#E0E0E0',
    borderRadius: 16,
    paddingTop: 34,
    paddingRight: 34,
    paddingBottom: 34,
    paddingLeft: 34,
    buttonBorderRadius: 8,
    marginTopDescription: 12,
    marginBottomDescription: 15, // New default for margin-bottom
    buttonPaddingTop: 12,
    buttonPaddingRight: 24,
    buttonPaddingBottom: 12,
    buttonPaddingLeft: 24,
});

export const createDefaultMobilePart = () => {
    const mobileDefaults = createDefaultPart();
    
    // Mobile specific overrides
    mobileDefaults.minHeight = 145;
    mobileDefaults.paddingTop = 20;
    mobileDefaults.paddingRight = 22;
    mobileDefaults.paddingBottom = 15;
    mobileDefaults.paddingLeft = 22;
    
    mobileDefaults.titleSize = 14;
    mobileDefaults.titleLineHeight = 1.4;

    mobileDefaults.descSize = 12;
    mobileDefaults.descLineHeight = 1.4;
    mobileDefaults.marginTopDescription = 12;

    mobileDefaults.buttonFontSize = 11;
    mobileDefaults.buttonPaddingTop = 10;
    mobileDefaults.buttonPaddingRight = 16;
    mobileDefaults.buttonPaddingBottom = 10;
    mobileDefaults.buttonPaddingLeft = 16;
    mobileDefaults.marginBottomDescription = 15;


    return mobileDefaults;
};


// START: NEW DEFAULTS FOR DOUBLE BANNER
export const createDefaultDoubleBannerPart = () => ({
    layerOrder: 'image-below-overlay', // 'image-below-overlay' or 'overlay-below-image'
    enableCustomDimensions: false,
    width: 50,
    widthUnit: '%',
    minHeight: 185,
    minHeightUnit: 'px',

    enableBorder: true,
    borderWidth: 0,
    borderColor: '#FFFFFF',
    borderRadius: 16,

    paddingTop: 35,
    paddingRight: 31,
    paddingBottom: 35,
    paddingLeft: 31,

    backgroundType: 'solid',
    bgColor: 'rgba(18, 76, 136, 0.8)',
    gradientAngle: 90,
    gradientStops: [
        { color: 'rgba(12, 165, 234, 0.8)', stop: 0 },
        { color: 'rgba(18, 76, 136, 0.8)', stop: 100 }
    ],

    imageUrl: '',
    enableCustomImageSize: false,
    imageWidth: null,
    imageWidthUnit: 'px',
    imageHeight: null,
    imageHeightUnit: 'px',
    imagePosRight: 0,
    imagePosBottom: 0,
    
    alignment: 'left',

    titleText: 'Banner Title',
    titleColor: '#FFFFFF',
    titleSize: 20,
    titleWeight: '700',

    descText: 'This is a description for the banner.',
    descColor: '#FFFFFF',
    descSize: 12,
    descWeight: '400',
    descWidth: 100,
    descWidthUnit: '%',

    buttonText: 'Click Me',
    buttonLink: '#',
    buttonBgColor: '#00BAA4',
    buttonTextColor: '#FFFFFF',
    buttonBgHoverColor: '#009a88',
    buttonFontSize: 14,
    buttonFontWeight: '600',
    buttonMinWidth: 143,
    buttonMinWidthUnit: 'px',
    buttonBorderRadius: 8,
});

export const createDefaultDoubleBannerMobilePart = () => {
    const mobileDefaults = createDefaultDoubleBannerPart();
    mobileDefaults.width = 100;
    mobileDefaults.widthUnit = '%';
    mobileDefaults.height = 'auto';
    mobileDefaults.minHeight = 110;
    mobileDefaults.paddingTop = 35;
    mobileDefaults.paddingRight = 30;
    mobileDefaults.paddingBottom = 35;
    mobileDefaults.paddingLeft = 30;
    mobileDefaults.imagePosRight = 0;
    mobileDefaults.imagePosBottom = 0;
    mobileDefaults.titleSize = 18;
    mobileDefaults.descSize = 12;
    mobileDefaults.descWidth = 100;
    mobileDefaults.descWidthUnit = '%';
    mobileDefaults.buttonFontSize = 12;
    return mobileDefaults;
};
// END: NEW DEFAULTS FOR DOUBLE BANNER


export const createDefaultApiDesign = () => ({
    layout: 'left',
    backgroundType: 'solid',
    bgColor: '#ffffff',
    gradientAngle: 90,
    gradientStops: [
        { color: 'rgba(240, 242, 245, 1)', stop: 0 },
        { color: 'rgba(255, 255, 255, 1)', stop: 100 }
    ],
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

export const createDefaultSimplePart = () => ({
    backgroundType: 'solid',
    bgColor: '#ffffff',
    gradientAngle: 90,
    gradientStops: [
        { color: '#F0F2F5', stop: 0 },
        { color: '#FFFFFF', stop: 100 }
    ],
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

export const createDefaultPromotionPart = () => ({
    borderWidth: 1,
    borderColor: '#ffad1e57',
    borderRadius: 12,
    direction: 'ltr',
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
    bodyBackgroundType: 'solid',
    bodyBgColor: '#f071001f',
    bodyGradientColor1: '#f071001f',
    bodyGradientColor2: '#FFFFFF',
    bodyGradientAngle: 90,
    bodyPaddingX: 20,
    bodyPaddingY: 5,
    bodyText: 'This is the main content of the promotion banner...',
    bodyTextColor: '#212121',
    bodyFontSize: 15,
    bodyFontWeight: '400',
    bodyLineHeight: 1.7,
    links: [],
});

export const createDefaultHtmlPart = () => ({
    html: '<div style="padding: 20px; text-align: center;">\n  <h2 style="color: #333;">Welcome!</h2>\n  <p style="color: #555;">This is your custom HTML banner.</p>\n</div>'
});

export const createDefaultHtmlSidebarPart = () => ({
    html: '<div style="padding: 15px; border: 1px solid #ddd; text-align: center;">\n  <h4 style="color: #333; margin-top: 0;">Sidebar Content</h4>\n</div>'
});