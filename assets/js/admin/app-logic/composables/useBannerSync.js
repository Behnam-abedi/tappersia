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
        
        // START: ADDED PROMOTION BANNER SYNC
        if (banner.type === 'promotion-banner' && newView === 'mobile' && !banner.isMobileConfigured) {
            const desktop = banner.promotion;
            const mobile = banner.promotion_mobile;

            // Sync all relevant properties
            Object.keys(desktop).forEach(key => {
                if (key !== 'links') { // Avoid deep copying arrays of objects if not needed, or handle it carefully
                     mobile[key] = JSON.parse(JSON.stringify(desktop[key]));
                }
            });
             mobile.links = JSON.parse(JSON.stringify(desktop.links));


            banner.isMobileConfigured = true;
        }
        // END: ADDED PROMOTION BANNER SYNC

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
    });

    // Continuous sync for shared properties from desktop to mobile
    watch(() => ({
        borderColor: banner.single.borderColor,
        backgroundType: banner.single.backgroundType,
        bgColor: banner.single.bgColor,
        imageUrl: banner.single.imageUrl,
        alignment: banner.single.alignment,
        titleText: banner.single.titleText,
        titleColor: banner.single.titleColor,
        descText: banner.single.descText,
        descColor: banner.single.descColor,
        buttonText: banner.single.buttonText,
        buttonLink: banner.single.buttonLink,
        buttonBgColor: banner.single.buttonBgColor,
        buttonBgHoverColor: banner.single.buttonBgHoverColor,
        buttonTextColor: banner.single.buttonTextColor,
    }), (newDesktopSettings) => {
         if (banner.type !== 'single-banner') return;
        Object.assign(banner.single_mobile, newDesktopSettings);
    }, { deep: true });

    watch(() => ({
        text: banner.simple.text,
        buttonText: banner.simple.buttonText,
        buttonLink: banner.simple.buttonLink,
        direction: banner.simple.direction,
        backgroundType: banner.simple.backgroundType,
        bgColor: banner.simple.bgColor,
        gradientStops: banner.simple.gradientStops,
        textColor: banner.simple.textColor,
        buttonBgColor: banner.simple.buttonBgColor,
        buttonTextColor: banner.simple.buttonTextColor,
    }), (newDesktopSettings) => {
        if (banner.type !== 'simple-banner') return;
        const desktopStops = JSON.parse(JSON.stringify(newDesktopSettings.gradientStops));
        Object.assign(banner.simple_mobile, { ...newDesktopSettings, gradientStops: desktopStops });
    }, { deep: true });

     watch(() => ({
        text: banner.sticky_simple.text,
        buttonText: banner.sticky_simple.buttonText,
        buttonLink: banner.sticky_simple.buttonLink,
        direction: banner.sticky_simple.direction,
        backgroundType: banner.sticky_simple.backgroundType,
        bgColor: banner.sticky_simple.bgColor,
        gradientStops: banner.sticky_simple.gradientStops,
        textColor: banner.sticky_simple.textColor,
        buttonBgColor: banner.sticky_simple.buttonBgColor,
        buttonTextColor: banner.sticky_simple.buttonTextColor,
    }), (newDesktopSettings) => {
        if (banner.type !== 'sticky-simple-banner') return;
        const desktopStops = JSON.parse(JSON.stringify(newDesktopSettings.gradientStops));
        Object.assign(banner.sticky_simple_mobile, { ...newDesktopSettings, gradientStops: desktopStops });
    }, { deep: true });

    watch(() => banner.double.desktop, (newDesktopSettings) => {
        if (banner.type !== 'double-banner') return;

        ['left', 'right'].forEach(key => {
            const desktop = newDesktopSettings[key];
            const mobile = banner.double.mobile[key];

            const sharedProperties = {
                imageUrl: desktop.imageUrl,
                titleText: desktop.titleText,
                descText: desktop.descText,
                buttonText: desktop.buttonText,
                buttonLink: desktop.buttonLink,
                backgroundType: desktop.backgroundType,
                bgColor: desktop.bgColor,
                gradientStops: JSON.parse(JSON.stringify(desktop.gradientStops)),
                titleColor: desktop.titleColor,
                descColor: desktop.descColor,
                buttonBgColor: desktop.buttonBgColor,
                buttonTextColor: desktop.buttonTextColor,
                buttonBgHoverColor: desktop.buttonBgHoverColor,
            };

            Object.assign(mobile, sharedProperties);
        });
    }, { deep: true });
}