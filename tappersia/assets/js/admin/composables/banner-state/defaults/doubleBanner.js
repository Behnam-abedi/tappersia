// tappersia/assets/js/admin/composables/banner-state/defaults/doubleBanner.js
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