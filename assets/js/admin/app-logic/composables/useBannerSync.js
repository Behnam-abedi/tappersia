// tappersia/assets/js/admin/app-logic/composables/useBannerSync.js
const { watch } = Vue;

import { 
    createDefaultPart, createDefaultMobilePart ,
    createDefaultHotelCarouselMobilePart,createDefaultTourCarouselMobilePart,
    createDefaultSimplePart, createDefaultSimpleBannerMobilePart,
    createDefaultStickySimplePart, createDefaultStickySimpleMobilePart
} from '../../composables/banner-state/defaults/index.js';

export function useBannerSync(banner, currentView) {
    
    // --- 🚀 [DEBUGGER] INITIALIZATION ---
    console.log(`🚀 [DEBUGGER] useBannerSync INITIALIZED for banner type: ${banner.type}`);
    
    const singleDesktopDefaults = createDefaultPart();
    const singleMobileDefaults = createDefaultMobilePart();
    const simpleDesktopDefaults = createDefaultSimplePart();
    const simpleMobileDefaults = createDefaultSimpleBannerMobilePart();
    const stickyDesktopDefaults = createDefaultStickySimplePart();
    const stickyMobileDefaults = createDefaultStickySimpleMobilePart();

    // --- 1. INITIAL SYNC (When user clicks 'mobile' tab) ---
    watch(currentView, (newView) => {
        
        console.log(`🚀 [DEBUGGER] View changed to: ${newView}`);

        // --- Simple Banner: INITIAL Sync ---
        if (banner.type === 'simple-banner' && newView === 'mobile' && !banner.isMobileConfigured) {
            console.log('🚀 [DEBUGGER] Running INITIAL sync for simple-banner...');
            banner.isMobileConfigured = true;
            
            const desktop = banner.simple;
            const mobile = banner.simple_mobile;
            const propertiesToSync = [
                'backgroundType', 'bgColor', 'gradientAngle', 'gradientStops', 'text', 'textColor', 'textWeight',
                'buttonText', 'buttonLink', 'buttonBgColor', 'buttonTextColor', 'buttonBgHoverColor', 'buttonFontWeight',
                'enableBorder', 'borderColor', 'direction'
            ];
            
            for (const prop of propertiesToSync) {
                const desktopVal = desktop[prop];
                const desktopDefault = simpleDesktopDefaults[prop];
                
                let isDesktopDefault;
                if (typeof desktopVal === 'object' && desktopVal !== null) {
                    isDesktopDefault = JSON.stringify(desktopVal) === JSON.stringify(desktopDefault);
                } else {
                    isDesktopDefault = desktopVal === desktopDefault;
                }

                if (!isDesktopDefault) {
                    console.log(`  > INITIAL Syncing [${prop}]: ${desktopVal}`);
                    if (typeof desktopVal === 'object' && desktopVal !== null) {
                        mobile[prop] = JSON.parse(JSON.stringify(desktopVal));
                    } else {
                        mobile[prop] = desktopVal;
                    }
                }
            }
        }
        
        // --- Sticky Simple Banner: INITIAL Sync ---
         if (banner.type === 'sticky-simple-banner' && newView === 'mobile' && !banner.isMobileConfigured) {
            console.log('🚀 [DEBUGGER] Running INITIAL sync for sticky-simple-banner...');
            banner.isMobileConfigured = true;
            
            const desktop = banner.sticky_simple;
            const mobile = banner.sticky_simple_mobile;
            const propertiesToSync = [
                'backgroundType', 'bgColor', 'gradientAngle', 'gradientStops', 'text', 'textColor', 'textWeight',
                'buttonText', 'buttonLink', 'buttonBgColor', 'buttonTextColor', 'buttonBgHoverColor', 'buttonFontWeight',
                'enableBorder', 'borderColor', 'direction'
            ];
            
            for (const prop of propertiesToSync) {
                const desktopVal = desktop[prop];
                const desktopDefault = stickyDesktopDefaults[prop]; 
                
                let isDesktopDefault;
                if (typeof desktopVal === 'object' && desktopVal !== null) {
                    isDesktopDefault = JSON.stringify(desktopVal) === JSON.stringify(desktopDefault);
                } else {
                    isDesktopDefault = desktopVal === desktopDefault;
                }

                if (!isDesktopDefault) {
                    console.log(`  > INITIAL Syncing [${prop}]: ${desktopVal}`);
                    if (typeof desktopVal === 'object' && desktopVal !== null) {
                        mobile[prop] = JSON.parse(JSON.stringify(desktopVal));
                    } else {
                        mobile[prop] = desktopVal;
                    }
                }
            }
         }
         
         // ... (other banner types' initial sync logic remains here) ...
         // --- START: Other types ---
        if (banner.type === 'single-banner' && newView === 'mobile' && !banner.isMobileConfigured) {
            banner.isMobileConfigured = true;
            const desktop = banner.single;
            const mobile = banner.single_mobile;
            const propertiesToSync = [
                'layerOrder', 'alignment', 'backgroundType', 'bgColor', 'gradientAngle', 'gradientStops',
                'titleText', 'titleColor', 'titleWeight', 'descText', 'descColor', 'descWeight',
                'buttonText', 'buttonLink', 'buttonBgColor', 'buttonTextColor', 'buttonBgHoverColor', 'buttonFontWeight',
                'imageUrl', 'enableBorder', 'borderColor'
            ];
            for (const prop of propertiesToSync) {
                const desktopVal = desktop[prop];
                const desktopDefault = singleDesktopDefaults[prop];
                let isDesktopDefault;
                if (typeof desktopVal === 'object' && desktopVal !== null) {
                    isDesktopDefault = JSON.stringify(desktopVal) === JSON.stringify(desktopDefault);
                } else {
                    isDesktopDefault = desktopVal === desktopDefault;
                }
                if (!isDesktopDefault) {
                    if (typeof desktopVal === 'object' && desktopVal !== null) {
                        mobile[prop] = JSON.parse(JSON.stringify(desktopVal));
                    } else {
                        mobile[prop] = desktopVal;
                    }
                }
            }
        }
        if (banner.type === 'promotion-banner' && newView === 'mobile' && !banner.isMobileConfigured) {
            banner.promotion_mobile = JSON.parse(JSON.stringify(banner.promotion));
            banner.promotion_mobile.headerPaddingX = 15; banner.promotion_mobile.headerPaddingY = 10; banner.promotion_mobile.headerFontSize = 14;
            banner.promotion_mobile.iconSize = 24; banner.promotion_mobile.bodyPaddingX = 15; banner.promotion_mobile.bodyPaddingY = 15;
            banner.promotion_mobile.bodyFontSize = 12; banner.promotion_mobile.bodyLineHeight = '22px';
            banner.isMobileConfigured = true;
        }
        if (banner.type === 'double-banner' && newView === 'mobile' && !banner.double.isMobileConfigured) {
            banner.double.mobile.left = JSON.parse(JSON.stringify(banner.double.desktop.left));
            banner.double.mobile.right = JSON.parse(JSON.stringify(banner.double.desktop.right));
            banner.double.mobile.left.minHeight = 150; banner.double.mobile.left.paddingY = 20; banner.double.mobile.left.paddingX = 20;
            banner.double.mobile.left.titleSize = 14; banner.double.mobile.left.descSize = 12; banner.double.mobile.left.marginTopDescription = 8;
            banner.double.mobile.left.contentWidth = 100; banner.double.mobile.left.contentWidthUnit = '%';
            banner.double.mobile.left.buttonFontSize = 11; banner.double.mobile.left.buttonPaddingY = 10; banner.double.mobile.left.buttonPaddingX = 16;
            banner.double.mobile.right.minHeight = 150; banner.double.mobile.right.paddingY = 20; banner.double.mobile.right.paddingX = 20;
            banner.double.mobile.right.titleSize = 14; banner.double.mobile.right.descSize = 12; banner.double.mobile.right.marginTopDescription = 8;
            banner.double.mobile.right.contentWidth = 100; banner.double.mobile.right.contentWidthUnit = '%';
            banner.double.mobile.right.buttonFontSize = 11; banner.double.mobile.right.buttonPaddingY = 10; banner.double.mobile.right.buttonPaddingX = 16;
            banner.double.isMobileConfigured = true;
        }
        if (banner.type === 'api-banner' && newView === 'mobile' && !banner.api.isMobileConfigured) {
            banner.api.design_mobile = JSON.parse(JSON.stringify(banner.api.design));
            banner.api.design_mobile.minHeight = 80; banner.api.design_mobile.imageContainerWidth = 140; banner.api.design_mobile.imageContainerWidthUnit = 'px';
            banner.api.design_mobile.paddingY = 12; banner.api.design_mobile.paddingX = 20;
            delete banner.api.design_mobile.paddingTop; delete banner.api.design_mobile.paddingBottom; delete banner.api.design_mobile.paddingLeft; delete banner.api.design_mobile.paddingRight;
            banner.api.design_mobile.titleSize = 16; banner.api.design_mobile.starSize = 11; banner.api.design_mobile.citySize = 11;
            banner.api.design_mobile.ratingBoxSize = 10; banner.api.design_mobile.ratingTextSize = 10; banner.api.design_mobile.reviewSize = 8;
            banner.api.design_mobile.priceAmountSize = 12; banner.api.design_mobile.priceFromSize = 9;
            banner.api.isMobileConfigured = true;
        }
        if (banner.type === 'tour-carousel' && newView === 'mobile' && !banner.tour_carousel.isMobileConfigured) {
            const desktop = banner.tour_carousel.settings; const mobile = banner.tour_carousel.settings_mobile;
            const mobileDefaultCardWidth = createDefaultTourCarouselMobilePart().cardWidth;
            const desktopSettingsCopy = JSON.parse(JSON.stringify(desktop));
            Object.assign(mobile, desktopSettingsCopy);
            mobile.slidesPerView = 1; mobile.spaceBetween = 15; mobile.cardWidth = mobileDefaultCardWidth;
            mobile.card.height = 375; mobile.card.padding = 9; mobile.card.borderRadius = 14;
            mobile.card.borderWidth = 1; mobile.card.imageHeight = 204;
            banner.tour_carousel.isMobileConfigured = true;
        }
        if (banner.type === 'hotel-carousel' && newView === 'mobile' && !banner.hotel_carousel.isMobileConfigured) {
            const desktop = banner.hotel_carousel.settings; const mobile = banner.hotel_carousel.settings_mobile;
            const mobileDefaultCardWidth = createDefaultHotelCarouselMobilePart().cardWidth;
            const desktopSettingsCopy = JSON.parse(JSON.stringify(desktop));
            Object.assign(mobile, desktopSettingsCopy);
            mobile.slidesPerView = 1; mobile.spaceBetween = 15; mobile.cardWidth = mobileDefaultCardWidth;
            mobile.card.minHeight = desktop.card.minHeight; mobile.card.padding = desktop.card.padding;
            mobile.card.borderRadius = desktop.card.borderRadius; mobile.card.borderWidth = desktop.card.borderWidth;
            mobile.card.image.height = desktop.card.image.height; mobile.card.image.radius = desktop.card.image.radius;
            banner.hotel_carousel.isMobileConfigured = true;
        }
        if (banner.type === 'flight-ticket' && newView === 'mobile' && !banner.flight_ticket.isMobileConfigured) {
            const desktop = banner.flight_ticket.design; const mobile = banner.flight_ticket.design_mobile;
            mobile.layerOrder = desktop.layerOrder; mobile.backgroundType = desktop.backgroundType; mobile.bgColor = desktop.bgColor;
            mobile.gradientAngle = desktop.gradientAngle; mobile.gradientStops = JSON.parse(JSON.stringify(desktop.gradientStops));
            mobile.imageUrl = desktop.imageUrl;
            mobile.content1.text = desktop.content1.text; mobile.content1.color = desktop.content1.color;
            mobile.content2.text = desktop.content2.text; mobile.content2.color = desktop.content2.color;
            mobile.content3.text = desktop.content3.text; mobile.content3.color = desktop.content3.color;
            mobile.price.color = desktop.price.color; mobile.button.bgColor = desktop.button.bgColor;
            mobile.button.BgHoverColor = desktop.button.BgHoverColor; mobile.button.color = desktop.button.color;
            mobile.fromCity.color = desktop.fromCity.color; mobile.toCity.color = desktop.toCity.color;
            banner.flight_ticket.isMobileConfigured = true;
        }
         // --- END: Other types ---

    });

    // --- 2. CONTINUOUS SYNC (This is what we need to debug) ---
    
    /**
     * Helper function to create the core sync logic with detailed logging.
     * @param {string} bannerType - The banner type (e.g., 'simple-banner')
     * @param {object} mobileState - The mobile state object (e.g., banner.simple_mobile)
     * @param {object} mobileDefaults - The mobile defaults object (e.g., simpleMobileDefaults)
     * @param {object} newDesktop - The new desktop state
     * @param {object} oldDesktop - The old desktop state
     * @param {string[]} propertiesToSync - Array of property names to sync
     */
    const runContinuousSyncDebug = (bannerType, mobileState, mobileDefaults, newDesktop, oldDesktop, propertiesToSync) => {
        
        console.log(`🔥 [DEBUGGER] CONTINUOUS Sync Watcher FIRED for: ${bannerType}`);
    
        for (const propName of propertiesToSync) {
            const newDeskVal = newDesktop[propName];
            const oldDeskVal = oldDesktop ? oldDesktop[propName] : undefined; // *** FIX: Safe access for oldDesktop ***
    
            // 1. Check if desktop value actually changed
            let deskValChanged;
            if (typeof newDeskVal === 'object' && newDeskVal !== null) {
                deskValChanged = JSON.stringify(newDeskVal) !== JSON.stringify(oldDeskVal);
            } else {
                deskValChanged = newDeskVal !== oldDeskVal;
            }
    
            if (!deskValChanged) {
                continue; // This property didn't change, skip to the next property
            }
    
            // 2. OK, it changed. Log everything.
            console.log(`--- [${bannerType}] Property Changed: [${propName}] ---`);
            console.log(`  > Desktop Value:`, oldDeskVal, '->', newDeskVal);
    
            const mobileVal = mobileState[propName];
            const mobileDefaultVal = mobileDefaults[propName];
    
            // 3. Check if mobile is "untouched"
            let isMobileLikeOldDesktop;
            let isMobileLikeDefault;
    
            if (typeof mobileVal === 'object' && mobileVal !== null) {
                // *** FIX: Safe access for oldDeskVal ***
                isMobileLikeOldDesktop = JSON.stringify(mobileVal) === JSON.stringify(oldDeskVal);
                isMobileLikeDefault = JSON.stringify(mobileVal) === JSON.stringify(mobileDefaultVal);
            } else {
                // *** FIX: Safe access for oldDeskVal ***
                isMobileLikeOldDesktop = mobileVal === oldDeskVal;
                isMobileLikeDefault = mobileVal === mobileDefaultVal;
            }
    
            // Mobile is "untouched" if it still matches the *old* desktop value (it was following)
            // OR if it's still at its own default value (it was never touched at all).
            const isMobileUntouched = isMobileLikeOldDesktop || isMobileLikeDefault;
    
            console.log(`  > Current Mobile Value:`, mobileVal);
            console.log(`  > Mobile Default Value:`, mobileDefaultVal);
            console.log(`  > Was Mobile == Old Desktop?`, isMobileLikeOldDesktop);
            console.log(`  > Was Mobile == Mobile Default?`, isMobileLikeDefault);
            console.log(`  > Is Mobile "Untouched"?`, isMobileUntouched);
    
            // 4. Sync if untouched
            if (isMobileUntouched) {
                console.log(`  > ✅ [${propName}] SYNCING! Setting mobile to new desktop value.`);
                if (typeof newDeskVal === 'object' && newDeskVal !== null) {
                    mobileState[propName] = JSON.parse(JSON.stringify(newDeskVal));
                } else {
                    mobileState[propName] = newDeskVal;
                }
            } else {
                console.log(`  > ❌ [${propName}] NOT SYNCING. Mobile value appears manually changed.`);
            }
            console.log(`--- End [${propName}] ---`);
        }
    };
    
    // --- Single Banner: CONTINUOUS Sync (The working example) ---
    watch(() => banner.single, (newDesktop, oldDesktop) => {
        if (banner.type !== 'single-banner') return; 
        const propertiesToSync = [
            'layerOrder', 'alignment', 'backgroundType', 'bgColor', 'gradientAngle', 'gradientStops',
            'titleText', 'titleColor', 'titleWeight', 'descText', 'descColor', 'descWeight',
            'buttonText', 'buttonLink', 'buttonBgColor', 'buttonTextColor', 'buttonBgHoverColor', 'buttonFontWeight',
            'imageUrl', 'enableBorder', 'borderColor'
        ];
        runContinuousSyncDebug('single-banner', banner.single_mobile, singleMobileDefaults, newDesktop, oldDesktop, propertiesToSync);
    }, { deep: true });

    
    // --- Simple Banner: CONTINUOUS Sync (The broken one) ---
    watch(() => banner.simple, (newDesktop, oldDesktop) => {
        if (banner.type !== 'simple-banner') return;
        const propertiesToSync = [
            'backgroundType', 'bgColor', 'gradientAngle', 'gradientStops',
            'text', 'textColor', 'textWeight',
            'buttonText', 'buttonLink', 'buttonBgColor', 'buttonTextColor', 'buttonBgHoverColor', 'buttonFontWeight',
            'enableBorder', 'borderColor', 'direction'
        ];
        runContinuousSyncDebug('simple-banner', banner.simple_mobile, simpleMobileDefaults, newDesktop, oldDesktop, propertiesToSync);
     }, { deep: true });
     

    // --- Sticky Simple Banner: CONTINUOUS Sync (The broken one) ---
    watch(() => banner.sticky_simple, (newDesktop, oldDesktop) => {
        if (banner.type !== 'sticky-simple-banner') return;
        const propertiesToSync = [
            'backgroundType', 'bgColor', 'gradientAngle', 'gradientStops',
            'text', 'textColor', 'textWeight',
            'buttonText', 'buttonLink', 'buttonBgColor', 'buttonTextColor', 'buttonBgHoverColor', 'buttonFontWeight',
            'enableBorder', 'borderColor', 'direction'
        ];
        runContinuousSyncDebug('sticky-simple-banner', banner.sticky_simple_mobile, stickyMobileDefaults, newDesktop, oldDesktop, propertiesToSync);
     }, { deep: true });

     
     // ... (other banner types' continuous sync logic remains here) ...
     // --- START: Other types ---
     watch(() => banner.promotion, (newDesktop) => {
        if (banner.type !== 'promotion-banner' || !banner.isMobileConfigured) return;
        const mobile = banner.promotion_mobile;
         mobile.direction = newDesktop.direction; mobile.headerText = newDesktop.headerText; mobile.headerTextColor = newDesktop.headerTextColor;
         mobile.bodyText = newDesktop.bodyText; mobile.bodyTextColor = newDesktop.bodyTextColor;
         mobile.links = JSON.parse(JSON.stringify(newDesktop.links));
         mobile.borderColor = newDesktop.borderColor; mobile.iconUrl = newDesktop.iconUrl; mobile.enableBorder = newDesktop.enableBorder;
     }, { deep: true });
    watch(() => banner.double.desktop, (newDesktop) => {
        if (banner.type !== 'double-banner' || !banner.double.isMobileConfigured) return;
        ['left', 'right'].forEach(position => {
            const desk = newDesktop[position]; const mob = banner.double.mobile[position];
            mob.imageUrl = desk.imageUrl; mob.alignment = desk.alignment; mob.contentWidth = desk.contentWidth; mob.contentWidthUnit = desk.contentWidthUnit;
            mob.paddingX = desk.paddingX; mob.paddingY = desk.paddingY; mob.titleText = desk.titleText; mob.titleColor = desk.titleColor;
            mob.descText = desk.descText; mob.descColor = desk.descColor; mob.buttonText = desk.buttonText; mob.buttonLink = desk.buttonLink;
            mob.buttonBgColor = desk.buttonBgColor; mob.buttonTextColor = desk.buttonTextColor; mob.buttonBgHoverColor = desk.buttonBgHoverColor;
            mob.buttonPaddingX = desk.buttonPaddingX; mob.buttonPaddingY = desk.buttonPaddingY; mob.borderColor = desk.borderColor; mob.layerOrder = desk.layerOrder;
         });
    }, { deep: true });
    watch(() => banner.tour_carousel.settings, (newDesktopSettings) => {
        if (banner.type !== 'tour-carousel' || !banner.tour_carousel.isMobileConfigured) return;
        const mobile = banner.tour_carousel.settings_mobile;
        mobile.direction = newDesktopSettings.direction; mobile.autoplay = JSON.parse(JSON.stringify(newDesktopSettings.autoplay));
        mobile.header.text = newDesktopSettings.header.text; mobile.header.color = newDesktopSettings.header.color; mobile.header.lineColor = newDesktopSettings.header.lineColor;
        mobile.card.backgroundType = newDesktopSettings.card.backgroundType; mobile.card.bgColor = newDesktopSettings.card.bgColor;
        mobile.card.gradientAngle = newDesktopSettings.card.gradientAngle; mobile.card.gradientStops = JSON.parse(JSON.stringify(newDesktopSettings.card.gradientStops));
        mobile.card.borderColor = newDesktopSettings.card.borderColor; mobile.card.province.color = newDesktopSettings.card.province.color;
        mobile.card.province.bgColor = newDesktopSettings.card.province.bgColor; mobile.card.title.color = newDesktopSettings.card.title.color;
        mobile.card.price.color = newDesktopSettings.card.price.color; mobile.card.duration.color = newDesktopSettings.card.duration.color;
        mobile.card.rating.color = newDesktopSettings.card.rating.color; mobile.card.reviews.color = newDesktopSettings.card.reviews.color;
        mobile.card.button.color = newDesktopSettings.card.button.color; mobile.card.button.bgColor = newDesktopSettings.card.button.bgColor;
        mobile.card.button.BgHoverColor = newDesktopSettings.card.button.BgHoverColor;
    }, { deep: true });
    watch(() => banner.hotel_carousel.settings, (newDesktopSettings) => {
        if (banner.type !== 'hotel-carousel' || !banner.hotel_carousel.isMobileConfigured) return;
        const mobile = banner.hotel_carousel.settings_mobile;
        mobile.direction = newDesktopSettings.direction; mobile.autoplay = JSON.parse(JSON.stringify(newDesktopSettings.autoplay));
        mobile.header.text = newDesktopSettings.header.text; mobile.header.color = newDesktopSettings.header.color; mobile.header.lineColor = newDesktopSettings.header.lineColor;
        mobile.card.bgColor = newDesktopSettings.card.bgColor; mobile.card.borderColor = newDesktopSettings.card.borderColor;
        mobile.card.imageOverlay.gradientStartColor = newDesktopSettings.card.imageOverlay.gradientStartColor; mobile.card.imageOverlay.gradientEndColor = newDesktopSettings.card.imageOverlay.gradientEndColor;
        mobile.card.badges.bestSeller.textColor = newDesktopSettings.card.badges.bestSeller.textColor; mobile.card.badges.bestSeller.bgColor = newDesktopSettings.card.badges.bestSeller.bgColor;
        mobile.card.badges.discount.textColor = newDesktopSettings.card.badges.discount.textColor; mobile.card.badges.discount.bgColor = newDesktopSettings.card.badges.discount.bgColor;
        mobile.card.stars.shapeColor = newDesktopSettings.card.stars.shapeColor; mobile.card.stars.textColor = newDesktopSettings.card.stars.textColor;
        mobile.card.bodyContent.textColor = newDesktopSettings.card.bodyContent.textColor; mobile.card.title.color = newDesktopSettings.card.title.color;
        mobile.card.rating.boxBgColor = newDesktopSettings.card.rating.boxBgColor; mobile.card.rating.boxColor = newDesktopSettings.card.rating.boxColor;
        mobile.card.rating.labelColor = newDesktopSettings.card.rating.labelColor; mobile.card.rating.countColor = newDesktopSettings.card.rating.countColor;
        mobile.card.divider.color = newDesktopSettings.card.divider.color; mobile.card.price.fromColor = newDesktopSettings.card.price.fromColor;
        mobile.card.price.amountColor = newDesktopSettings.card.price.amountColor; mobile.card.price.nightColor = newDesktopSettings.card.price.nightColor;
        mobile.card.price.originalColor = newDesktopSettings.card.price.originalColor;
    }, { deep: true });
    watch(() => banner.flight_ticket.design, (newDesktopSettings) => {
        if (banner.type !== 'flight-ticket' || !banner.isMobileConfigured) return;
        const mobile = banner.flight_ticket.design_mobile;
        mobile.layerOrder = newDesktopSettings.layerOrder; mobile.backgroundType = newDesktopSettings.backgroundType; mobile.bgColor = newDesktopSettings.bgColor;
        if (newDesktopSettings.gradientStops && Array.isArray(newDesktopSettings.gradientStops)) {
            const newMobileStops = [];
            newDesktopSettings.gradientStops.forEach((desktopStop, index) => {
                const existingMobileStop = (mobile.gradientStops && mobile.gradientStops[index]) ? mobile.gradientStops[index] : null;
                newMobileStops.push({
                    color: desktopStop.color,
                    stop: existingMobileStop ? existingMobileStop.stop : desktopStop.stop
                });
            });
            mobile.gradientStops = newMobileStops;
        }
        mobile.imageUrl = newDesktopSettings.imageUrl;
        mobile.content1.text = newDesktopSettings.content1.text; mobile.content1.color = newDesktopSettings.content1.color;
        mobile.content2.text = newDesktopSettings.content2.text; mobile.content2.color = newDesktopSettings.content2.color;
        mobile.content3.text = newDesktopSettings.content3.text; mobile.content3.color = newDesktopSettings.content3.color;
        mobile.price.color = newDesktopSettings.price.color; mobile.button.bgColor = newDesktopSettings.button.bgColor;
        mobile.button.BgHoverColor = newDesktopSettings.button.BgHoverColor; mobile.button.color = newDesktopSettings.button.color;
        mobile.fromCity.color = newDesktopSettings.fromCity.color; mobile.toCity.color = newDesktopSettings.toCity.color;
    }, { deep: true });
     // --- END: Other types ---
}