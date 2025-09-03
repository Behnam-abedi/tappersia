// tappersia/assets/js/admin/composables/banner-state/defaults.js
export const createDefaultPart = () => ({
    alignment: 'left', backgroundType: 'solid', bgColor: '#232323',
    gradientColor1: '#232323', gradientColor2: '#1A2B48', gradientAngle: 90,
    titleText: 'Awesome Title', titleColor: '#ffffff', titleSize: 15, titleWeight: '700',
    descText: 'A short and engaging description.', descColor: '#dddddd', descSize: 10, descWeight: '400',
    buttonText: 'Learn More', buttonLink: '#', buttonBgColor: '#00baa4',
    buttonTextColor: '#ffffff', buttonFontSize: 10, buttonBgHoverColor: '#008a7b',
    imageUrl: '', imageFit: 'none', enableCustomImageSize: false,
    imageWidth: null, imageHeight: null, imagePosRight: 0, imagePosBottom: 0,
    
    enableCustomDimensions: false,
    width: 886,
    widthUnit: 'px',
    height: 178,
    heightUnit: 'px',
    enableBorder: false,
    borderWidth: 1,
    borderColor: '#E0E0E0',
    borderRadius: 8,
    paddingTop: 32,
    paddingRight: 32,
    paddingBottom: 32,
    paddingLeft: 32,
    buttonWidth: null,
    buttonWidthUnit: 'px',
    buttonHeight: null,
    buttonHeightUnit: 'px',
    buttonMinWidth: null,
    buttonMinWidthUnit: 'px',
    buttonBorderRadius: 4,
    marginTopDescription: 8,
    marginBottomDescription: 24,
});

export const createDefaultMobilePart = () => {
    const mobileDefaults = createDefaultPart();
    mobileDefaults.width = 100;
    mobileDefaults.widthUnit = '%';
    mobileDefaults.height = 250;
    mobileDefaults.heightUnit = 'px';
    return mobileDefaults;
};

export const createDefaultApiDesign = () => ({
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

export const createDefaultSimplePart = () => ({
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