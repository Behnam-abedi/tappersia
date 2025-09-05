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
    titleSize: 20,
    titleWeight: '700',
    descText: 'A short and engaging description.',
    descColor: '#ffffff',
    descSize: 12,
    descWeight: '500',
    descLineHeight: 1.1,
    descWidth: 100, // *** ADDED: Default description width ***
    descWidthUnit: '%', // *** ADDED: Default description width unit ***
    buttonText: 'Learn More',
    buttonLink: '#',
    buttonBgColor: '#124C88',
    buttonTextColor: '#ffffff',
    buttonFontSize: 14,
    buttonBgHoverColor: '#10447B',
    buttonFontWeight: '600',
    imageUrl: '',
    imageFit: 'cover',
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
    height: 183,
    heightUnit: 'px',
    enableBorder: false,
    borderWidth: 1,
    borderColor: '#E0E0E0',
    borderRadius: 16,
    paddingTop: 41.8,
    paddingRight: 50,
    paddingBottom: 26,
    paddingLeft: 50,
    buttonWidth: null,
    buttonWidthUnit: 'px',
    buttonHeight: null,
    buttonHeightUnit: 'px',
    buttonMinWidth: 118,
    buttonMinWidthUnit: 'px',
    buttonBorderRadius: 5,
    marginTopDescription: 10,
    buttonPaddingX: 23,
    buttonPaddingY: 9,
});

export const createDefaultMobilePart = () => {
    const mobileDefaults = createDefaultPart();
    
    // Custom defaults for mobile as per user request
    mobileDefaults.width = 100;
    mobileDefaults.widthUnit = '%';
    mobileDefaults.height = 110;
    mobileDefaults.heightUnit = 'px';
    mobileDefaults.borderRadius = 16;
    mobileDefaults.paddingTop = 15;
    mobileDefaults.paddingRight = 12;
    mobileDefaults.paddingBottom = 15;
    mobileDefaults.paddingLeft = 12;
    mobileDefaults.marginTopDescription = 2;
    mobileDefaults.titleSize = 14;
    mobileDefaults.descSize = 9;
    mobileDefaults.descLineHeight = 1.2;
    mobileDefaults.descWidth = 100; // Default width for mobile description
    mobileDefaults.descWidthUnit = '%';
    mobileDefaults.buttonPaddingX = 18;
    mobileDefaults.buttonPaddingY = 9;
    mobileDefaults.buttonFontSize = 9; // *** ADDED: Default button font size for mobile ***
    mobileDefaults.buttonMinWidth = 80; // *** ADDED: Default button min-width for mobile ***

    return mobileDefaults;
};

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