// tappersia/assets/js/admin/composables/banner-state/defaults/promotionBanner.js
export const createDefaultPromotionPart = () => ({
    borderWidth: 1,
    borderColor: '#ff731b3d',
    borderRadius: 16,
    direction: 'ltr',
    headerBackgroundType: 'solid',
    headerBgColor: '#FF731B',
    headerGradientColor1: '#FF731B',
    headerGradientColor2: '#F07100',
    headerGradientAngle: 90,
    iconUrl: '',
    iconSize: 27,
    headerPaddingX: 20,
    headerPaddingY: 12,
    headerText: 'Major Update for Iran Visa Regulations – August 2025: What Travelers Need to Know',
    headerTextColor: '#FFFFFF',
    headerFontSize: 16,
    headerFontWeight: '700',
    bodyBackgroundType: 'solid',
    bodyBgColor: '#FDEEE0',
    bodyGradientColor1: '#FFF0E5',
    bodyGradientColor2: '#FFFFFF',
    bodyGradientAngle: 90,
    bodyPaddingX: 23,
    bodyPaddingY: 23,
    bodyText: 'Please be aware that, due to new regulations, the only way to travel to Iran and obtain a visa is through an Iranian travel agency with an organized tour package. Visa applications for independent travel without a tour or guide are no longer accepted. To avoid any delays or complications, please contact us on WhatsApp +98 910 300 4875 directly to choose the tour package that suits your preferences and budget before applying for your visa. Refunds are not possible if the visa is applied for without finalizing your tour package. The approximate visa processing time is about 2 weeks. Read all the details here.',
    bodyTextColor: '#333333',
    bodyFontSize: 14,
    bodyFontWeight: '400',
    bodyLineHeight: '26px',
    links: [],
});

// START: ADDED MOBILE DEFAULTS
export const createDefaultPromotionMobilePart = () => {
    const mobileDefaults = createDefaultPromotionPart();
    
    // Mobile specific overrides
    mobileDefaults.headerPaddingX = 15;
    mobileDefaults.headerPaddingY = 10;
    mobileDefaults.headerFontSize = 14;
    mobileDefaults.iconSize = 24;

    mobileDefaults.bodyPaddingX = 15;
    mobileDefaults.bodyPaddingY = 15;
    mobileDefaults.bodyFontSize = 12;
    mobileDefaults.bodyLineHeight = '22px';

    return mobileDefaults;
};
// END: ADDED MOBILE DEFAULTS