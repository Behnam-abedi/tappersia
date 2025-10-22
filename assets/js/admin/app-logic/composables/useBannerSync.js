// tappersia/assets/js/admin/app-logic/composables/useBannerSync.js
const { watch } = Vue;

export function useBannerSync(banner, currentView) {
    // Sync logic when switching from desktop to mobile view for the first time
    watch(currentView, (newView) => {
        
        // ... (تمام if های قبلی برای single, simple, sticky, promotion, double, api ... ) ...

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

        // --- START: ADDED HOTEL CAROUSEL SYNC ---
        if (banner.type === 'hotel-carousel' && newView === 'mobile' && !banner.hotel_carousel.isMobileConfigured) {
            const desktop = banner.hotel_carousel.settings;
            const mobile = banner.hotel_carousel.settings_mobile;

            // 1. Deep copy all desktop settings to mobile on first switch
            const desktopSettingsCopy = JSON.parse(JSON.stringify(desktop));
            Object.assign(mobile, desktopSettingsCopy);

            // 2. Apply specific mobile overrides (mirroring tour carousel)
            mobile.slidesPerView = 1;

            banner.hotel_carousel.isMobileConfigured = true;
        }
        // --- END: ADDED HOTEL CAROUSEL SYNC ---
    });

    // Continuous sync for shared properties from desktop to mobile
    // ... (تمام watch های قبلی برای single, simple, sticky, double ... ) ...

    // ... (watch برای tour_carousel.settings) ...
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

    // --- START: ADDED HOTEL CAROUSEL CONTINUOUS SYNC ---
    watch(() => banner.hotel_carousel.settings, (newDesktopSettings) => {
        if (banner.type !== 'hotel-carousel') return;
        
        const mobile = banner.hotel_carousel.settings_mobile;

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
    // --- END: ADDED HOTEL CAROUSEL CONTINUOUS SYNC ---
}