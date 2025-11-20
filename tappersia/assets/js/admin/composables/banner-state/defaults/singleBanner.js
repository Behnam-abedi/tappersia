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
    descText: 'A short and engaging description.',
    descColor: '#ffffff',
    descSize: 14,
    descWeight: '500',
    buttonText: 'Learn More',
    buttonLink: '#',
    buttonBgColor: '#124C88',
    buttonTextColor: '#ffffff',
    buttonFontSize: 14,
    buttonBgHoverColor: '#10447B',
    buttonFontWeight: '500',
    imageUrl: '',
    enableCustomImageSize: false,
    imageWidth: null,
    imageWidthUnit: 'px',
    imageHeight: null,
    imageHeightUnit: 'px',
    imagePosRight: 0,
    imagePosBottom: 0,
    
    // --- تغییرات چیدمان ---
    minHeight: 190,
    contentWidth: 100,
    contentWidthUnit: '%',
    
    enableBorder: false,
    borderWidth: 1,
    borderColor: '#E0E0E0',
    borderRadius: 16,
    
    // --- تغییرات پدینگ ---
    paddingY: 34,
    paddingX: 34,
    
    buttonBorderRadius: 8,
    marginTopDescription: 12,
    
    // --- START: Refactored Button Margin ---
    buttonMarginTopAuto: true,
    buttonMarginTop: 15,
    buttonMarginBottom: 0, // <-- ADDED
    // --- END: Refactored Button Margin ---
    
    // --- تغییرات پدینگ دکمه ---
    buttonPaddingY: 12,
    buttonPaddingX: 24,
});

export const createDefaultMobilePart = () => {
    const mobileDefaults = createDefaultPart();
    
    // Mobile specific overrides
    mobileDefaults.layerOrder = 'image-below-overlay';
    mobileDefaults.minHeight = 145;
    
    // --- تغییرات پدینگ ---
    mobileDefaults.paddingY = 20;
    mobileDefaults.paddingX = 22;
    
    mobileDefaults.titleSize = 14;

    mobileDefaults.descSize = 12;
    mobileDefaults.marginTopDescription = 12;

    mobileDefaults.buttonFontSize = 11;
    
    // --- تغییرات پدینگ دکمه ---
    mobileDefaults.buttonPaddingY = 10;
    mobileDefaults.buttonPaddingX = 16;
    
    // --- START: Refactored Button Margin ---
    mobileDefaults.buttonMarginTopAuto = true;
    mobileDefaults.buttonMarginTop = 15;
    mobileDefaults.buttonMarginBottom = 0; // <-- ADDED
    // --- END: Refactored Button Margin ---


    return mobileDefaults;
};