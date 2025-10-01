// tappersia/assets/js/admin/composables/banner-state/defaults/simpleBanner.js
export const createDefaultSimplePart = () => ({
    backgroundType: 'solid',
    bgColor: '#ffffff',
    gradientAngle: 90,
    gradientStops: [
        { color: '#F0F2F5', stop: 0 },
        { color: '#FFFFFF', stop: 100 }
    ],
    height: 'auto',
    minHeight: 74,
    borderRadius: 10,
    paddingY: 26,
    paddingX: 40,
    paddingXUnit: 'px',
    direction: 'ltr',
    text: 'This is a simple banner text.',
    textColor: '#000000',
    textSize: 17,
    textWeight: '700',
    textWidth: 100,
    textWidthUnit: '%',
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

export const createDefaultSimpleBannerMobilePart = () => {
    const mobileDefaults = createDefaultSimplePart();
    mobileDefaults.minHeight = 0;
    mobileDefaults.paddingY = 15;
    mobileDefaults.paddingX = 15;
    mobileDefaults.textSize = 14;
    mobileDefaults.textWidth = 60;
    mobileDefaults.buttonFontSize = 12;
    mobileDefaults.buttonPaddingY = 8;
    mobileDefaults.buttonPaddingX = 12;
    mobileDefaults.buttonMinWidth = 0;
    return mobileDefaults;
};