// tappersia/assets/js/admin/composables/banner-state/defaults/apiBanner.js
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
        { color: '#F0F2F5FF', stop: 0 },
        { color: '#FFFFFFFF', stop: 100 }
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
    mobileDefaults.imageContainerWidth = 140;

    mobileDefaults.paddingTop = 12;
    mobileDefaults.paddingBottom = 12;
    mobileDefaults.paddingLeft = 24;
    mobileDefaults.paddingRight = 15;

    mobileDefaults.titleSize = 16;
    mobileDefaults.starSize = 11;
    mobileDefaults.citySize = 11;
    mobileDefaults.ratingBoxSize = 10;
    mobileDefaults.ratingTextSize = 10;
    mobileDefaults.reviewSize = 8;
    mobileDefaults.priceAmountSize = 12;
    mobileDefaults.priceFromSize = 9;

    return mobileDefaults;
};