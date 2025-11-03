// tappersia/assets/js/admin/app-logic/composables/useBannerSync.js
const { watch } = Vue;

export function useBannerSync(banner, currentView) {
    // Sync logic when switching from desktop to mobile view for the first time
    watch(currentView, (newView) => {

        // --- Keep existing sync logic for other types ---
        if (banner.type === 'single-banner' && newView === 'mobile' && !banner.isMobileConfigured) {
            banner.single_mobile = JSON.parse(JSON.stringify(banner.single));
            // Apply mobile overrides AFTER copying
            banner.single_mobile.layerOrder = banner.single.layerOrder; // <<< ADDED
            banner.single_mobile.minHeight = 145;
            banner.single_mobile.paddingY = 20; // Changed
            banner.single_mobile.paddingX = 22; // Changed
            banner.single_mobile.titleSize = 14;
            // banner.single_mobile.titleLineHeight = 1.4; // Removed
            banner.single_mobile.descSize = 12;
            // banner.single_mobile.descLineHeight = 1.4; // Removed
            banner.single_mobile.marginTopDescription = 12;
            banner.single_mobile.marginBottomDescription = 15;
            banner.single_mobile.buttonFontSize = 11;
            banner.single_mobile.buttonPaddingY = 10; // Changed
            banner.single_mobile.buttonPaddingX = 16; // Changed
            banner.isMobileConfigured = true;
        }

        if (banner.type === 'simple-banner' && newView === 'mobile' && !banner.isMobileConfigured) {
             banner.simple_mobile = JSON.parse(JSON.stringify(banner.simple));
             // Apply mobile overrides
            banner.simple_mobile.minHeight = 7;
            banner.simple_mobile.paddingY = 24;
            banner.simple_mobile.paddingX = 20;
            banner.simple_mobile.textSize = 17;
            banner.simple_mobile.buttonFontSize = 8;
            banner.simple_mobile.buttonPaddingY = 8;
            banner.simple_mobile.buttonPaddingX = 12;
            banner.simple_mobile.buttonMinWidth = 0;
            banner.isMobileConfigured = true;
        }
         if (banner.type === 'sticky-simple-banner' && newView === 'mobile' && !banner.isMobileConfigured) {
             banner.sticky_simple_mobile = JSON.parse(JSON.stringify(banner.sticky_simple));
             // Apply mobile overrides
             banner.sticky_simple_mobile.minHeight = 7;
             banner.sticky_simple_mobile.paddingY = 24;
             banner.sticky_simple_mobile.paddingX = 20;
             banner.sticky_simple_mobile.textSize = 17;
             banner.sticky_simple_mobile.buttonFontSize = 8;
             banner.sticky_simple_mobile.buttonPaddingY = 8;
             banner.sticky_simple_mobile.buttonPaddingX = 12;
             banner.sticky_simple_mobile.buttonMinWidth = 0;
            banner.isMobileConfigured = true;
         }

        if (banner.type === 'promotion-banner' && newView === 'mobile' && !banner.isMobileConfigured) {
            banner.promotion_mobile = JSON.parse(JSON.stringify(banner.promotion));
             // Apply mobile overrides
            banner.promotion_mobile.headerPaddingX = 15;
            banner.promotion_mobile.headerPaddingY = 10;
            banner.promotion_mobile.headerFontSize = 14;
            banner.promotion_mobile.iconSize = 24;
            banner.promotion_mobile.bodyPaddingX = 15;
            banner.promotion_mobile.bodyPaddingY = 15;
            banner.promotion_mobile.bodyFontSize = 12;
            banner.promotion_mobile.bodyLineHeight = '22px';
            banner.isMobileConfigured = true;
        }

        if (banner.type === 'double-banner' && newView === 'mobile' && !banner.double.isMobileConfigured) {
            banner.double.mobile.left = JSON.parse(JSON.stringify(banner.double.desktop.left));
            banner.double.mobile.right = JSON.parse(JSON.stringify(banner.double.desktop.right));
            // Apply mobile overrides for left
            banner.double.mobile.left.minHeight = 150;
            banner.double.mobile.left.paddingY = 20;
            banner.double.mobile.left.paddingX = 20;
            banner.double.mobile.left.titleSize = 14;
            banner.double.mobile.left.descSize = 12;
            banner.double.mobile.left.marginTopDescription = 8;
            banner.double.mobile.left.contentWidth = 100;
            banner.double.mobile.left.contentWidthUnit = '%';
            banner.double.mobile.left.buttonFontSize = 11;
            banner.double.mobile.left.buttonPaddingY = 10;
            banner.double.mobile.left.buttonPaddingX = 16;
            // Apply mobile overrides for right
            banner.double.mobile.right.minHeight = 150;
            banner.double.mobile.right.paddingY = 20;
            banner.double.mobile.right.paddingX = 20;
            banner.double.mobile.right.titleSize = 14;
            banner.double.mobile.right.descSize = 12;
            banner.double.mobile.right.marginTopDescription = 8;
            banner.double.mobile.right.contentWidth = 100;
            banner.double.mobile.right.contentWidthUnit = '%';
            banner.double.mobile.right.buttonFontSize = 11;
            banner.double.mobile.right.buttonPaddingY = 10;
            banner.double.mobile.right.buttonPaddingX = 16;

            banner.double.isMobileConfigured = true;
        }

        if (banner.type === 'api-banner' && newView === 'mobile' && !banner.api.isMobileConfigured) {
            banner.api.design_mobile = JSON.parse(JSON.stringify(banner.api.design));
             // Apply mobile overrides
            banner.api.design_mobile.height = 80;
            banner.api.design_mobile.imageContainerWidth = 140;
            banner.api.design_mobile.paddingTop = 12; banner.api.design_mobile.paddingBottom = 12;
            banner.api.design_mobile.paddingLeft = 24; banner.api.design_mobile.paddingRight = 15;
            banner.api.design_mobile.titleSize = 16; banner.api.design_mobile.starSize = 11;
            banner.api.design_mobile.citySize = 11; banner.api.design_mobile.ratingBoxSize = 10;
            banner.api.design_mobile.ratingTextSize = 10;
            banner.api.design_mobile.reviewSize = 8;
            banner.api.design_mobile.priceAmountSize = 12;
            banner.api.design_mobile.priceFromSize = 9;
            banner.api.isMobileConfigured = true;
        }

        if (banner.type === 'tour-carousel' && newView === 'mobile' && !banner.tour_carousel.isMobileConfigured) {
            const desktop = banner.tour_carousel.settings;
            const mobile = banner.tour_carousel.settings_mobile;
            const desktopSettingsCopy = JSON.parse(JSON.stringify(desktop));
            Object.assign(mobile, desktopSettingsCopy);
            mobile.slidesPerView = 1;
             mobile.card.height = 375; // Keep card settings consistent unless explicitly overridden
             mobile.card.padding = 9;
             mobile.card.borderRadius = 14;
             mobile.card.borderWidth = 1;
             mobile.card.imageHeight = 204;
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
            mobile.spaceBetween = 20; // Example override

            // Explicitly ensure mobile card settings match desktop initially if needed
             mobile.card.height = desktop.card.height;
             mobile.card.padding = desktop.card.padding;
             mobile.card.borderRadius = desktop.card.borderRadius;
             mobile.card.borderWidth = desktop.card.borderWidth;
             mobile.card.image.height = desktop.card.image.height;
             mobile.card.image.radius = desktop.card.image.radius;
             // ... copy other relevant card settings if needed, but deep copy should handle it ...

            banner.hotel_carousel.isMobileConfigured = true;
        }
        // --- END: ADDED HOTEL CAROUSEL SYNC ---
        
        // +++ START: --- FIX for Flight Ticket Sync --- +++
        if (banner.type === 'flight-ticket' && newView === 'mobile' && !banner.flight_ticket.isMobileConfigured) {
            const desktop = banner.flight_ticket.design;
            const mobile = banner.flight_ticket.design_mobile;

            // به جای کپی کامل آبجکت، فقط مقادیر مشترک (متن، رنگ، عکس) را همگام‌سازی می‌کنیم
            // این کار باعث حفظ دیفالت‌های مخصوص موبایل (مثل minHeight, padding, fontSize) می‌شود
            // که از قبل در bannerState.js ست شده‌اند.

            // همگام‌سازی مقادیر مشترک (متن‌ها، رنگ‌ها، تصویر)
            mobile.layerOrder = desktop.layerOrder;
            mobile.backgroundType = desktop.backgroundType;
            mobile.bgColor = desktop.bgColor;
            mobile.gradientAngle = desktop.gradientAngle;
            mobile.gradientStops = JSON.parse(JSON.stringify(desktop.gradientStops));
            
            mobile.imageUrl = desktop.imageUrl;

            mobile.content1.text = desktop.content1.text;
            mobile.content1.color = desktop.content1.color;
            mobile.content2.text = desktop.content2.text;
            mobile.content2.color = desktop.content2.color;
            mobile.content3.text = desktop.content3.text;
            mobile.content3.color = desktop.content3.color;

            mobile.price.color = desktop.price.color;
            mobile.button.bgColor = desktop.button.bgColor;
            mobile.button.BgHoverColor = desktop.button.BgHoverColor;
            mobile.button.color = desktop.button.color;
            mobile.fromCity.color = desktop.fromCity.color;
            mobile.toCity.color = desktop.toCity.color;

            // مقادیر چیدمان مخصوص موبایل (minHeight, padding, fontSizes و...)
            // که از createDefaultFlightTicketMobilePart آمده‌اند، دست نخورده باقی می‌مانند.
            
            banner.flight_ticket.isMobileConfigured = true;
        }
        // +++ END: --- FIX for Flight Ticket Sync --- +++

    });

    // Continuous sync for shared properties from desktop to mobile
    // --- Keep existing watches for other types ---
    watch(() => banner.single, (newDesktop) => {
        if (banner.type !== 'single-banner' || !banner.isMobileConfigured) return;
        const mobile = banner.single_mobile;
        mobile.imageUrl = newDesktop.imageUrl;
        mobile.alignment = newDesktop.alignment;
        mobile.titleText = newDesktop.titleText;
        mobile.titleColor = newDesktop.titleColor;
        mobile.descText = newDesktop.descText;
        mobile.descColor = newDesktop.descColor;
        mobile.buttonText = newDesktop.buttonText;
        mobile.buttonLink = newDesktop.buttonLink;
        mobile.buttonBgColor = newDesktop.buttonBgColor;
        mobile.buttonTextColor = newDesktop.buttonTextColor;
        mobile.buttonBgHoverColor = newDesktop.buttonBgHoverColor;
        mobile.borderColor = newDesktop.borderColor; // Sync border color
        mobile.layerOrder = newDesktop.layerOrder; // <<< ADDED
     }, { deep: true });

    watch(() => banner.simple, (newDesktop) => {
        if (banner.type !== 'simple-banner' || !banner.isMobileConfigured) return;
        const mobile = banner.simple_mobile;
        mobile.text = newDesktop.text;
        mobile.textColor = newDesktop.textColor;
        mobile.buttonText = newDesktop.buttonText;
        mobile.buttonLink = newDesktop.buttonLink;
        mobile.buttonBgColor = newDesktop.buttonBgColor;
        mobile.buttonBgHoverColor = newDesktop.buttonBgHoverColor; // <<< افزوده شد
        mobile.buttonTextColor = newDesktop.buttonTextColor;
        mobile.direction = newDesktop.direction; // Sync direction
     }, { deep: true });

    watch(() => banner.sticky_simple, (newDesktop) => {
        if (banner.type !== 'sticky-simple-banner' || !banner.isMobileConfigured) return;
        const mobile = banner.sticky_simple_mobile;
        mobile.text = newDesktop.text;
        mobile.textColor = newDesktop.textColor;
        mobile.buttonText = newDesktop.buttonText;
        mobile.buttonLink = newDesktop.buttonLink;
        mobile.buttonBgColor = newDesktop.buttonBgColor;
        mobile.buttonBgHoverColor = newDesktop.buttonBgHoverColor; // <<< افزوده شد
        mobile.buttonTextColor = newDesktop.buttonTextColor;
        mobile.direction = newDesktop.direction; // Sync direction
     }, { deep: true });

     watch(() => banner.promotion, (newDesktop) => {
        if (banner.type !== 'promotion-banner' || !banner.isMobileConfigured) return;
        const mobile = banner.promotion_mobile;
         mobile.direction = newDesktop.direction;
         mobile.headerText = newDesktop.headerText;
         mobile.headerTextColor = newDesktop.headerTextColor;
         mobile.bodyText = newDesktop.bodyText;
         mobile.bodyTextColor = newDesktop.bodyTextColor;
         mobile.links = JSON.parse(JSON.stringify(newDesktop.links)); // Deep copy links
         mobile.borderColor = newDesktop.borderColor;
         mobile.iconUrl = newDesktop.iconUrl; // Sync icon URL
     }, { deep: true });


    watch(() => banner.double.desktop, (newDesktop) => {
        if (banner.type !== 'double-banner' || !banner.double.isMobileConfigured) return;
        ['left', 'right'].forEach(position => {
            const desk = newDesktop[position];
            const mob = banner.double.mobile[position];
            mob.imageUrl = desk.imageUrl;
            mob.alignment = desk.alignment;
            mob.contentWidth = desk.contentWidth;
            mob.contentWidthUnit = desk.contentWidthUnit;
            mob.paddingX = desk.paddingX;
            mob.paddingY = desk.paddingY;
            mob.titleText = desk.titleText;
            mob.titleColor = desk.titleColor;
            mob.descText = desk.descText;
            mob.descColor = desk.descColor;
            mob.buttonText = desk.buttonText;
            mob.buttonLink = desk.buttonLink;
            mob.buttonBgColor = desk.buttonBgColor;
            mob.buttonTextColor = desk.buttonTextColor;
            mob.buttonBgHoverColor = desk.buttonBgHoverColor;
            mob.buttonPaddingX = desk.buttonPaddingX;
            mob.buttonPaddingY = desk.buttonPaddingY;
            mob.borderColor = desk.borderColor; // Sync border color
            mob.layerOrder = desk.layerOrder; // Sync layer order
         });
    }, { deep: true });

    // --- Tour Carousel Continuous Sync ---
    watch(() => banner.tour_carousel.settings, (newDesktopSettings) => {
        if (banner.type !== 'tour-carousel' || !banner.tour_carousel.isMobileConfigured) return;
        const mobile = banner.tour_carousel.settings_mobile;
        mobile.direction = newDesktopSettings.direction;
        mobile.autoplay = JSON.parse(JSON.stringify(newDesktopSettings.autoplay));
        mobile.header.text = newDesktopSettings.header.text;
        mobile.header.color = newDesktopSettings.header.color;
        mobile.header.lineColor = newDesktopSettings.header.lineColor;
        // Sync desktop-only card styles (colors, backgrounds)
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
        mobile.card.button.BgHoverColor = newDesktopSettings.card.button.BgHoverColor;
    }, { deep: true });

    // --- START: ADDED HOTEL CAROUSEL CONTINUOUS SYNC ---
    watch(() => banner.hotel_carousel.settings, (newDesktopSettings) => {
        if (banner.type !== 'hotel-carousel' || !banner.hotel_carousel.isMobileConfigured) return;

        const mobile = banner.hotel_carousel.settings_mobile;

        // Sync Carousel level settings (Desktop only)
        mobile.direction = newDesktopSettings.direction;
        mobile.autoplay = JSON.parse(JSON.stringify(newDesktopSettings.autoplay));
        mobile.header.text = newDesktopSettings.header.text;
        mobile.header.color = newDesktopSettings.header.color;
        mobile.header.lineColor = newDesktopSettings.header.lineColor;

        // Sync Card level settings (Desktop only colors/styles, NOT sizes/padding/radius)
        mobile.card.bgColor = newDesktopSettings.card.bgColor;
        mobile.card.borderColor = newDesktopSettings.card.borderColor;
        mobile.card.imageOverlay.gradientStartColor = newDesktopSettings.card.imageOverlay.gradientStartColor;
        mobile.card.imageOverlay.gradientEndColor = newDesktopSettings.card.imageOverlay.gradientEndColor;
        mobile.card.badges.bestSeller.textColor = newDesktopSettings.card.badges.bestSeller.textColor;
        mobile.card.badges.bestSeller.bgColor = newDesktopSettings.card.badges.bestSeller.bgColor;
        mobile.card.badges.discount.textColor = newDesktopSettings.card.badges.discount.textColor;
        mobile.card.badges.discount.bgColor = newDesktopSettings.card.badges.discount.bgColor;
        mobile.card.stars.shapeColor = newDesktopSettings.card.stars.shapeColor;
        mobile.card.stars.textColor = newDesktopSettings.card.stars.textColor;
        mobile.card.bodyContent.textColor = newDesktopSettings.card.bodyContent.textColor;
        mobile.card.title.color = newDesktopSettings.card.title.color;
        mobile.card.rating.boxBgColor = newDesktopSettings.card.rating.boxBgColor;
        mobile.card.rating.boxColor = newDesktopSettings.card.rating.boxColor;
        mobile.card.rating.labelColor = newDesktopSettings.card.rating.labelColor;
        mobile.card.rating.countColor = newDesktopSettings.card.rating.countColor;
        mobile.card.divider.color = newDesktopSettings.card.divider.color;
        mobile.card.price.fromColor = newDesktopSettings.card.price.fromColor;
        mobile.card.price.amountColor = newDesktopSettings.card.price.amountColor;
        mobile.card.price.nightColor = newDesktopSettings.card.price.nightColor;
        mobile.card.price.originalColor = newDesktopSettings.card.price.originalColor;

    }, { deep: true });
    // --- END: ADDED HOTEL CAROUSEL CONTINUOUS SYNC ---

    // +++ START: ADDED FLIGHT TICKET CONTINUOUS SYNC +++
// +++ START: ADDED FLIGHT TICKET CONTINUOUS SYNC +++
    watch(() => banner.flight_ticket.design, (newDesktopSettings) => {
        if (banner.type !== 'flight-ticket' || !banner.flight_ticket.isMobileConfigured) return;

        const mobile = banner.flight_ticket.design_mobile;

        // Sync shared properties (texts, colors, image)
        mobile.layerOrder = newDesktopSettings.layerOrder;
        mobile.backgroundType = newDesktopSettings.backgroundType;
        mobile.bgColor = newDesktopSettings.bgColor;
        
        // mobile.gradientAngle = newDesktopSettings.gradientAngle; // <<< حذف شد تا زاویه موبایل مستقل باشد

        // --- START: همگام سازی هوشمند رنگ گرادینت ---
        // این بخش رنگ‌ها را سینک می‌کند اما موقعیت‌ها (stops) را مستقل نگه می‌دارد
        if (newDesktopSettings.gradientStops && Array.isArray(newDesktopSettings.gradientStops)) {
            const newMobileStops = [];
            newDesktopSettings.gradientStops.forEach((desktopStop, index) => {
                // چک می‌کند آیا آیتم متناظری در آرایه موبایل وجود دارد
                const existingMobileStop = (mobile.gradientStops && mobile.gradientStops[index]) ? mobile.gradientStops[index] : null;
                
                newMobileStops.push({
                    color: desktopStop.color, // رنگ همیشه از دسکتاپ می‌آید
                    stop: existingMobileStop ? existingMobileStop.stop : desktopStop.stop // پوزیشن موبایل حفظ می‌شود، اگر وجود نداشت از دسکتاپ می‌آید
                });
            });
            mobile.gradientStops = newMobileStops; // این کار حذف و اضافه شدن آیتم‌ها را هم مدیریت می‌کند
        }
        // --- END: همگام سازی هوشمند رنگ گرادینت ---
        
        mobile.imageUrl = newDesktopSettings.imageUrl;

        mobile.content1.text = newDesktopSettings.content1.text;
        mobile.content1.color = newDesktopSettings.content1.color;
        mobile.content2.text = newDesktopSettings.content2.text;
        mobile.content2.color = newDesktopSettings.content2.color;
        mobile.content3.text = newDesktopSettings.content3.text;
        mobile.content3.color = newDesktopSettings.content3.color;

        mobile.price.color = newDesktopSettings.price.color;
        mobile.button.bgColor = newDesktopSettings.button.bgColor;
        mobile.button.BgHoverColor = newDesktopSettings.button.BgHoverColor;
        mobile.button.color = newDesktopSettings.button.color;
        mobile.fromCity.color = newDesktopSettings.fromCity.color;
        mobile.toCity.color = newDesktopSettings.toCity.color;

    }, { deep: true });
    // +++ END: ADDED FLIGHT TICKET CONTINUOUS SYNC +++
    // +++ END: ADDED FLIGHT TICKET CONTINUOUS SYNC +++
}