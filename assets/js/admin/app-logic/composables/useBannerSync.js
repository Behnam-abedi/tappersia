// tappersia/assets/js/admin/app-logic/composables/useBannerSync.js
const { watch } = Vue;

export function useBannerSync(banner, currentView) {
    // Sync logic when switching from desktop to mobile view for the first time
    watch(currentView, (newView) => {
        if (banner.type === 'single-banner' && newView === 'mobile' && !banner.isMobileConfigured) {
            const desktop = banner.single;
            const mobile = banner.single_mobile;
            
            mobile.gradientAngle = desktop.gradientAngle;
            mobile.gradientStops = JSON.parse(JSON.stringify(desktop.gradientStops));
            mobile.enableCustomImageSize = desktop.enableCustomImageSize;
            mobile.imageWidth = desktop.imageWidth;
            mobile.imageWidthUnit = desktop.imageWidthUnit;
            mobile.imageHeight = desktop.imageHeight;
            mobile.imageHeightUnit = desktop.imageHeightUnit;
            mobile.imagePosRight = desktop.imagePosRight;
            mobile.imagePosBottom = desktop.imagePosBottom;
            mobile.titleWeight = desktop.titleWeight;
            mobile.titleLineHeight = desktop.titleLineHeight;
            mobile.descWeight = desktop.descWeight;
            mobile.descLineHeight = desktop.descLineHeight;
            mobile.buttonFontWeight = desktop.buttonFontWeight;
            mobile.buttonLineHeight = desktop.buttonLineHeight;
            mobile.buttonBorderRadius = desktop.buttonBorderRadius;

            banner.isMobileConfigured = true;
        }

        if (banner.type === 'simple-banner' && newView === 'mobile' && !banner.isMobileConfigured) {
            const desktop = banner.simple;
            const mobile = banner.simple_mobile;

            mobile.backgroundType = desktop.backgroundType;
            mobile.bgColor = desktop.bgColor;
            mobile.gradientStops = JSON.parse(JSON.stringify(desktop.gradientStops));
            mobile.gradientAngle = desktop.gradientAngle;
            mobile.borderRadius = desktop.borderRadius;
            mobile.direction = desktop.direction;
            mobile.text = desktop.text;
            mobile.textColor = desktop.textColor;
            mobile.textWeight = desktop.textWeight;
            mobile.buttonText = desktop.buttonText;
            mobile.buttonLink = desktop.buttonLink;
            mobile.buttonBgColor = desktop.buttonBgColor;
            mobile.buttonTextColor = desktop.buttonTextColor;
            mobile.buttonBorderRadius = desktop.buttonBorderRadius;
            mobile.buttonFontWeight = desktop.buttonFontWeight;

            banner.isMobileConfigured = true;
        }

        if (banner.type === 'sticky-simple-banner' && newView === 'mobile' && !banner.isMobileConfigured) {
            const desktop = banner.sticky_simple;
            const mobile = banner.sticky_simple_mobile;

            mobile.backgroundType = desktop.backgroundType;
            mobile.bgColor = desktop.bgColor;
            mobile.gradientStops = JSON.parse(JSON.stringify(desktop.gradientStops));
            mobile.gradientAngle = desktop.gradientAngle;
            mobile.borderRadius = desktop.borderRadius;
            mobile.direction = desktop.direction;
            mobile.text = desktop.text;
            mobile.textColor = desktop.textColor;
            mobile.textWeight = desktop.textWeight;
            mobile.buttonText = desktop.buttonText;
            mobile.buttonLink = desktop.buttonLink;
            mobile.buttonBgColor = desktop.buttonBgColor;
            mobile.buttonTextColor = desktop.buttonTextColor;
            mobile.buttonBorderRadius = desktop.buttonBorderRadius;
            mobile.buttonFontWeight = desktop.buttonFontWeight;

            banner.isMobileConfigured = true;
        }
        
        if (banner.type === 'promotion-banner' && newView === 'mobile' && !banner.isMobileConfigured) {
            const desktop = banner.promotion;
            const mobile = banner.promotion_mobile;

            Object.keys(desktop).forEach(key => {
                if (key !== 'links') {
                     mobile[key] = JSON.parse(JSON.stringify(desktop[key]));
                }
            });
             mobile.links = JSON.parse(JSON.stringify(desktop.links));


            banner.isMobileConfigured = true;
        }

        if (banner.type === 'double-banner' && newView === 'mobile' && !banner.double.isMobileConfigured) {
            ['left', 'right'].forEach(key => {
                const desktop = banner.double.desktop[key];
                const mobile = banner.double.mobile[key];

                mobile.borderColor = desktop.borderColor;
                mobile.layerOrder = desktop.layerOrder;
                mobile.backgroundType = desktop.backgroundType;
                mobile.bgColor = desktop.bgColor;
                mobile.gradientStops = JSON.parse(JSON.stringify(desktop.gradientStops));
                mobile.imageUrl = desktop.imageUrl;
                mobile.alignment = desktop.alignment;
                mobile.titleText = desktop.titleText;
                mobile.titleColor = desktop.titleColor;
                mobile.titleWeight = desktop.titleWeight;
                mobile.descText = desktop.descText;
                mobile.descColor = desktop.descColor;
                mobile.descWeight = desktop.descWeight;
                mobile.buttonText = desktop.buttonText;
                mobile.buttonLink = desktop.buttonLink;
                mobile.buttonBgColor = desktop.buttonBgColor;
                mobile.buttonBgHoverColor = desktop.buttonBgHoverColor;
                mobile.buttonTextColor = desktop.buttonTextColor;
                mobile.buttonFontWeight = desktop.buttonFontWeight;
                mobile.buttonBorderRadius = desktop.buttonBorderRadius;
                mobile.enableCustomImageSize = desktop.enableCustomImageSize;
            });
            banner.double.isMobileConfigured = true;
        }

        if (banner.type === 'api-banner' && newView === 'mobile' && !banner.api.isMobileConfigured) {
            const desktop = banner.api.design;
            const mobile = banner.api.design_mobile;

            mobile.layout = desktop.layout;
            mobile.backgroundType = desktop.backgroundType;
            mobile.bgColor = desktop.bgColor;
            mobile.gradientStops = JSON.parse(JSON.stringify(desktop.gradientStops));
            mobile.gradientAngle = desktop.gradientAngle;
            mobile.enableBorder = desktop.enableBorder;
            mobile.borderColor = desktop.borderColor;
            mobile.titleColor = desktop.titleColor;
            mobile.cityColor = desktop.cityColor;
            mobile.ratingBoxBgColor = desktop.ratingBoxBgColor;
            mobile.ratingBoxColor = desktop.ratingBoxColor;
            mobile.ratingTextColor = desktop.ratingTextColor;
            mobile.reviewColor = desktop.reviewColor;
            mobile.priceFromColor = desktop.priceFromColor;
            mobile.priceAmountColor = desktop.priceAmountColor;
            mobile.priceNightColor = desktop.priceNightColor;
            
            banner.api.isMobileConfigured = true;
        }

        if (banner.type === 'tour-carousel' && newView === 'mobile' && !banner.tour_carousel.isMobileConfigured) {
            const desktop = banner.tour_carousel.settings;
            const mobile = banner.tour_carousel.settings_mobile;

            // 1. Deep copy all desktop settings to mobile on first switch
            const desktopSettingsCopy = JSON.parse(JSON.stringify(desktop));
            Object.assign(mobile, desktopSettingsCopy);

            // 2. Apply specific mobile overrides
            mobile.slidesPerView = 1;

            banner.tour_carousel.isMobileConfigured = true;
        }
    });

    // Continuous sync for shared properties from desktop to mobile
    watch(() => banner.single, (newDesktopSettings) => {
        if (banner.type !== 'single-banner') return;
        const mobile = banner.single_mobile;
        mobile.borderColor = newDesktopSettings.borderColor;
        mobile.backgroundType = newDesktopSettings.backgroundType;
        mobile.bgColor = newDesktopSettings.bgColor;
        mobile.imageUrl = newDesktopSettings.imageUrl;
        mobile.alignment = newDesktopSettings.alignment;
        mobile.titleText = newDesktopSettings.titleText;
        mobile.titleColor = newDesktopSettings.titleColor;
        mobile.descText = newDesktopSettings.descText;
        mobile.descColor = newDesktopSettings.descColor;
        mobile.buttonText = newDesktopSettings.buttonText;
        mobile.buttonLink = newDesktopSettings.buttonLink;
        mobile.buttonBgColor = newDesktopSettings.buttonBgColor;
        mobile.buttonBgHoverColor = newDesktopSettings.buttonBgHoverColor;
        mobile.buttonTextColor = newDesktopSettings.buttonTextColor;
    }, { deep: true });

    watch(() => banner.simple, (newDesktopSettings) => {
        if (banner.type !== 'simple-banner') return;
        const mobile = banner.simple_mobile;
        mobile.text = newDesktopSettings.text;
        mobile.buttonText = newDesktopSettings.buttonText;
        mobile.buttonLink = newDesktopSettings.buttonLink;
        mobile.direction = newDesktopSettings.direction;
        mobile.backgroundType = newDesktopSettings.backgroundType;
        mobile.bgColor = newDesktopSettings.bgColor;
        mobile.gradientStops = JSON.parse(JSON.stringify(newDesktopSettings.gradientStops));
        mobile.textColor = newDesktopSettings.textColor;
        mobile.buttonBgColor = newDesktopSettings.buttonBgColor;
        mobile.buttonTextColor = newDesktopSettings.buttonTextColor;
    }, { deep: true });

     watch(() => banner.sticky_simple, (newDesktopSettings) => {
        if (banner.type !== 'sticky-simple-banner') return;
        const mobile = banner.sticky_simple_mobile;
        mobile.text = newDesktopSettings.text;
        mobile.buttonText = newDesktopSettings.buttonText;
        mobile.buttonLink = newDesktopSettings.buttonLink;
        mobile.direction = newDesktopSettings.direction;
        mobile.backgroundType = newDesktopSettings.backgroundType;
        mobile.bgColor = newDesktopSettings.bgColor;
        mobile.gradientStops = JSON.parse(JSON.stringify(newDesktopSettings.gradientStops));
        mobile.textColor = newDesktopSettings.textColor;
        mobile.buttonBgColor = newDesktopSettings.buttonBgColor;
        mobile.buttonTextColor = newDesktopSettings.buttonTextColor;
    }, { deep: true });

    watch(() => banner.double.desktop, (newDesktopSettings) => {
        if (banner.type !== 'double-banner') return;
        ['left', 'right'].forEach(key => {
            const desktop = newDesktopSettings[key];
            const mobile = banner.double.mobile[key];
            mobile.imageUrl = desktop.imageUrl;
            mobile.titleText = desktop.titleText;
            mobile.descText = desktop.descText;
            mobile.buttonText = desktop.buttonText;
            mobile.buttonLink = desktop.buttonLink;
            mobile.backgroundType = desktop.backgroundType;
            mobile.bgColor = desktop.bgColor;
            mobile.gradientStops = JSON.parse(JSON.stringify(desktop.gradientStops));
            mobile.titleColor = desktop.titleColor;
            mobile.descColor = desktop.descColor;
            mobile.buttonBgColor = desktop.buttonBgColor;
            mobile.buttonTextColor = desktop.buttonTextColor;
            mobile.buttonBgHoverColor = desktop.buttonBgHoverColor;
        });
    }, { deep: true });

    // NEW & CORRECTED: Continuous sync for tour-carousel desktop-only properties
    watch(() => banner.tour_carousel.settings, (newDesktopSettings) => {
        if (banner.type !== 'tour-carousel') return;
        
        const mobile = banner.tour_carousel.settings_mobile;

        // Sync properties that are only configured on desktop view
        mobile.direction = newDesktopSettings.direction;
        mobile.autoplay = JSON.parse(JSON.stringify(newDesktopSettings.autoplay));
        
        // Sync header text and colors
        mobile.header.text = newDesktopSettings.header.text;
        mobile.header.color = newDesktopSettings.header.color;
        mobile.header.lineColor = newDesktopSettings.header.lineColor;

        // Sync desktop-only card styles
        mobile.card.backgroundType = newDesktopSettings.card.backgroundType;
        mobile.card.bgColor = newDesktopSettings.card.bgColor;
        mobile.card.gradientAngle = newDesktopSettings.card.gradientAngle;
        mobile.card.gradientStops = JSON.parse(JSON.stringify(newDesktopSettings.card.gradientStops));
        mobile.card.borderColor = newDesktopSettings.card.borderColor;
        mobile.card.province.color = newDesktopSettings.card.province.color;
        mobile.card.province.bgColor = newDesktopSettings.card.province.bgColor;
        mobile.card.title.color = newDesktopSettings.card.title.color;
        mobile.card.price.color = newDesktopSettings.card.price.color;
        mobile.card.duration.color = newDesktopSettings.card.duration.color;
        mobile.card.rating.color = newDesktopSettings.card.rating.color;
        mobile.card.reviews.color = newDesktopSettings.card.reviews.color;
        mobile.card.button.color = newDesktopSettings.card.button.color;
        mobile.card.button.bgColor = newDesktopSettings.card.button.bgColor;

    }, { deep: true });
}