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
    minHeight: 190,
    minHeightUnit: 'px',

    enableBorder: false,
    borderWidth: 0,
    borderColor: '#FFFFFF',
    borderRadius: 16,

    paddingTop: 31,
    paddingRight: 24,
    paddingBottom: 31,
    paddingLeft: 24,

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
    titleSize: 19,
    titleWeight: '700',
    titleLineHeight: 1,

    descText: 'This is a description for the banner.',
    descColor: '#FFFFFF',
    descSize: 13,
    descWeight: '400',
    descLineHeight: 1.5,
    descWidth: 100,
    descWidthUnit: '%',
    marginTopDescription: 12,

    buttonText: 'Click Me',
    buttonLink: '#',
    buttonBgColor: '#00BAA4',
    buttonTextColor: '#FFFFFF',
    buttonBgHoverColor: '#009a88',
    buttonFontSize: 13,
    buttonFontWeight: '500',
    buttonBorderRadius: 8,
    buttonPaddingTop: 12,
    buttonPaddingRight: 24,
    buttonPaddingBottom: 12,
    buttonPaddingLeft: 24,
    buttonMarginTop: 10,
    buttonMarginBottom: 0,
    buttonLineHeight: 1,
});

export const createDefaultDoubleBannerMobilePart = () => {
    const mobileDefaults = createDefaultDoubleBannerPart();
    mobileDefaults.width = 100;
    mobileDefaults.widthUnit = '%';
    mobileDefaults.minHeight = 150;
    mobileDefaults.paddingTop = 20;
    mobileDefaults.paddingRight = 20;
    mobileDefaults.paddingBottom = 20;
    mobileDefaults.paddingLeft = 20;
    mobileDefaults.titleSize = 14;
    mobileDefaults.titleLineHeight = 1.4;
    mobileDefaults.descSize = 12;
    mobileDefaults.descLineHeight = 1.4;
    mobileDefaults.marginTopDescription = 8;
    mobileDefaults.buttonFontSize = 11;
    mobileDefaults.buttonMarginTop = 10;
    mobileDefaults.buttonMarginBottom = 0;
    mobileDefaults.buttonPaddingTop = 12;
    mobileDefaults.buttonPaddingRight = 24;
    mobileDefaults.buttonPaddingBottom = 12;
    mobileDefaults.buttonPaddingLeft = 24;
    return mobileDefaults;
};
// END: NEW DEFAULTS FOR DOUBLE BANNER


export const createDefaultApiDesign = () => ({
    imageContainerWidth: 360,
    enableCustomDimensions: false,
    width: 100,
    widthUnit: '%',
    height: 150,
    heightUnit: 'px',
    layout: 'left',
    backgroundType: 'solid',
    bgColor: '#ffffff',
    gradientAngle: 90,
    gradientStops: [
        { color: 'rgba(240, 242, 245, 1)', stop: 0 },
        { color: 'rgba(255, 255, 255, 1)', stop: 100 }
    ],
    enableBorder: false,
    borderWidth: 1,
    borderColor: '#E0E0E0',
    borderRadius: 16,
    paddingTop: 24,
    paddingBottom: 24,
    paddingLeft: 35,
    paddingRight: 35,
    titleColor: '#000000', 
    titleSize: 20, 
    titleWeight: '700',
    starSize: 16,
    cityColor: '#000000',
    citySize: 12,
    ratingBoxBgColor: '#5191FA',
    ratingBoxColor: '#FFFFFF',
    ratingBoxSize: 14,
    ratingBoxWeight: '500',
    ratingTextColor: '#5191FA',
    ratingTextSize: 14,
    ratingTextWeight: '700',
    reviewColor: '#999999',
    reviewSize: 11,
    priceFromColor: '#999999',
    priceFromSize: 12,
    priceAmountColor: '#00BAA4',
    priceAmountSize: 16,
    priceAmountWeight: '700',
    priceNightColor: '#999999',
    priceNightSize: 10,
});

export const createDefaultApiMobileDesign = () => {
    const mobileDefaults = createDefaultApiDesign();
    
    // Mobile specific overrides
    mobileDefaults.height = 80;
    mobileDefaults.imageContainerWidth = 200;

    mobileDefaults.paddingTop = 12;
    mobileDefaults.paddingBottom = 12;
    mobileDefaults.paddingLeft = 15;
    mobileDefaults.paddingRight = 15;

    mobileDefaults.titleSize = 8;
    mobileDefaults.starSize = 10;
    mobileDefaults.citySize = 9;
    mobileDefaults.ratingBoxSize = 10;
    mobileDefaults.ratingTextSize = 10;
    mobileDefaults.reviewSize = 8;
    mobileDefaults.priceAmountSize = 12;
    mobileDefaults.priceFromSize = 9;

    return mobileDefaults;
};

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

// START: NEW DEFAULT FOR SIMPLE BANNER MOBILE
export const createDefaultSimpleBannerMobilePart = () => {
    const mobileDefaults = createDefaultSimplePart();
    mobileDefaults.height = 'auto';
    mobileDefaults.paddingY = 15;
    mobileDefaults.paddingX = 15;
    mobileDefaults.textSize = 14;
    mobileDefaults.buttonFontSize = 12;
    mobileDefaults.buttonPaddingY = 8;
    mobileDefaults.buttonPaddingX = 12;
    mobileDefaults.buttonMinWidth = 0;
    return mobileDefaults;
};
// END: NEW DEFAULT FOR SIMPLE BANNER MOBILE

export const createDefaultPromotionPart = () => ({
    borderWidth: 1,
    borderColor: '#FFAD1E',
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
    bodyBgColor: '#FFF0E5',
    bodyGradientColor1: '#FFF0E5',
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