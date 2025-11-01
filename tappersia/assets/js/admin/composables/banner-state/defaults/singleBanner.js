// tappersia/assets/js/admin/composables/banner-state/defaults/singleBanner.js
export const createDefaultPart = () => ({
    layerOrder: 'image-below-overlay', // 'image-below-overlay' or 'overlay-below-image'
    alignment: 'left',
    backgroundType: 'solid',
    bgColor: '#0facf0',
    gradientAngle: 90,
    gradientStops: [
        { color: '#0CA5EACC', stop: 0 },
        { color: '#124C88CC', stop: 100 }
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
    mobileDefaults.layerOrder = 'image-below-overlay'; // Add this
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