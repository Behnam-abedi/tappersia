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
    // titleLineHeight: 1, // <--- حذف شد
    descText: 'A short and engaging description.',
    descColor: '#ffffff',
    descSize: 14,
    descWeight: '500',
    // descLineHeight: 1.5, // <--- حذف شد
    // descWidth: 100, // <--- حذف شد
    // descWidthUnit: '%', // <--- حذف شد
    buttonText: 'Learn More',
    buttonLink: '#',
    buttonBgColor: '#124C88',
    buttonTextColor: '#ffffff',
    buttonFontSize: 14,
    buttonBgHoverColor: '#10447B',
    buttonFontWeight: '500',
    // buttonLineHeight: 1, // <--- حذف شد
    imageUrl: '',
    enableCustomImageSize: false,
    imageWidth: null,
    imageWidthUnit: 'px',
    imageHeight: null,
    imageHeightUnit: 'px',
    imagePosRight: 0,
    imagePosBottom: 0,
    
    // --- تغییرات چیدمان ---
    // enableCustomDimensions: true, // <--- حذف شد
    // width: 100, // <--- حذف شد
    // widthUnit: '%', // <--- حذف شد
    // height: 'auto', // <--- حذف شد
    minHeight: 190, // <--- ساده‌سازی شد
    // minHeightUnit: 'px', // <--- حذف شد
    contentWidth: 100, // <--- جدید
    contentWidthUnit: '%', // <--- جدید
    
    enableBorder: false,
    borderWidth: 1,
    borderColor: '#E0E0E0',
    borderRadius: 16,
    
    // --- تغییرات پدینگ ---
    paddingY: 34, // <--- جدید (جایگزین Top/Bottom)
    paddingX: 34, // <--- جدید (جایگزین Left/Right)
    // paddingTop: 34, // <--- حذف شد
    // paddingRight: 34, // <--- حذف شد
    // paddingBottom: 34, // <--- حذف شد
    // paddingLeft: 34, // <--- حذف شد
    
    buttonBorderRadius: 8,
    marginTopDescription: 12,
    marginBottomDescription: 15, // New default for margin-bottom
    
    // --- تغییرات پدینگ دکمه ---
    buttonPaddingY: 12, // <--- جدید
    buttonPaddingX: 24, // <--- جدید
    // buttonPaddingTop: 12, // <--- حذف شد
    // buttonPaddingRight: 24, // <--- حذف شد
    // buttonPaddingBottom: 12, // <--- حذف شد
    // buttonPaddingLeft: 24, // <--- حذف شد
});

export const createDefaultMobilePart = () => {
    const mobileDefaults = createDefaultPart();
    
    // Mobile specific overrides
    mobileDefaults.layerOrder = 'image-below-overlay';
    mobileDefaults.minHeight = 145;
    
    // --- تغییرات پدینگ ---
    mobileDefaults.paddingY = 20; // <--- جدید
    mobileDefaults.paddingX = 22; // <--- جدید
    // mobileDefaults.paddingTop = 20; // <--- حذف شد
    // mobileDefaults.paddingRight = 22; // <--- حذف شد
    // mobileDefaults.paddingBottom = 15; // <--- حذف شد
    // mobileDefaults.paddingLeft = 22; // <--- حذف شد
    
    mobileDefaults.titleSize = 14;
    // mobileDefaults.titleLineHeight = 1.4; // <--- حذف شد

    mobileDefaults.descSize = 12;
    // mobileDefaults.descLineHeight = 1.4; // <--- حذف شد
    mobileDefaults.marginTopDescription = 12;

    mobileDefaults.buttonFontSize = 11;
    
    // --- تغییرات پدینگ دکمه ---
    mobileDefaults.buttonPaddingY = 10; // <--- جدید
    mobileDefaults.buttonPaddingX = 16; // <--- جدید
    // mobileDefaults.buttonPaddingTop = 10; // <--- حذف شد
    // mobileDefaults.buttonPaddingRight = 16; // <--- حذف شد
    // mobileDefaults.buttonPaddingBottom = 10; // <--- حذف شد
    // mobileDefaults.buttonPaddingLeft = 16; // <--- حذف شد
    
    mobileDefaults.marginBottomDescription = 15;


    return mobileDefaults;
};