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
    paddingY: 24,
    paddingX: 40,
    paddingXUnit: 'px',
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
    buttonPaddingY: 8,
    buttonPaddingX: 15,
    buttonMinWidth: 72,
});

export const createDefaultSimpleBannerMobilePart = () => {
    const mobileDefaults = createDefaultSimplePart();
    mobileDefaults.minHeight = 7;
    mobileDefaults.paddingY = 24;
    mobileDefaults.paddingX = 20;
    mobileDefaults.textSize = 17;
    mobileDefaults.buttonFontSize = 8;
    mobileDefaults.buttonPaddingY = 8;
    mobileDefaults.buttonPaddingX = 12;
    mobileDefaults.buttonMinWidth = 0;
    return mobileDefaults;
};