// tappersia/assets/js/admin/composables/banner-state/defaults/doubleBanner.js
export const createDefaultDoubleBannerPart = () => ({
    layerOrder: 'image-below-overlay', // 'image-below-overlay' or 'overlay-below-image'
    minHeight: 190,

    enableBorder: false,
    borderWidth: 0,
    borderColor: '#FFFFFF',
    borderRadius: 16,

    paddingY: 31,
    paddingX: 24,

    backgroundType: 'solid',
    bgColor: '#124c88',
    gradientAngle: 90,
    gradientStops: [
        { color: '#0ca5ea', stop: 0 },
        { color: '#124c88', stop: 100 }
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
    contentWidth: 100,
    contentWidthUnit: '%',

    titleText: 'Banner Title',
    titleColor: '#FFFFFF',
    titleSize: 19,
    titleWeight: '700',

    descText: 'This is a description for the banner.',
    descColor: '#FFFFFF',
    descSize: 13,
    descWeight: '400',
    marginTopDescription: 12,

    buttonText: 'Click Me',
    buttonLink: '#',
    buttonBgColor: '#00BAA4',
    buttonTextColor: '#FFFFFF',
    buttonBgHoverColor: '#009a88',
    buttonFontSize: 13,
    buttonFontWeight: '500',
    buttonBorderRadius: 8,
    buttonPaddingY: 12,
    buttonPaddingX: 24,
    buttonMarginTop: 10,
    buttonMarginBottom: 0,
});

export const createDefaultDoubleBannerMobilePart = () => {
    const mobileDefaults = createDefaultDoubleBannerPart();
    mobileDefaults.minHeight = 150;
    mobileDefaults.paddingY = 20;
    mobileDefaults.paddingX = 20;
    
    mobileDefaults.titleSize = 14;

    mobileDefaults.descSize = 12;
    mobileDefaults.marginTopDescription = 8;
    
    mobileDefaults.contentWidth = 100;
    mobileDefaults.contentWidthUnit = '%';

    mobileDefaults.buttonFontSize = 11;
    mobileDefaults.buttonMarginTop = 10;
    mobileDefaults.buttonMarginBottom = 0;
    mobileDefaults.buttonPaddingY = 10;
    mobileDefaults.buttonPaddingX = 16;
    return mobileDefaults;
};