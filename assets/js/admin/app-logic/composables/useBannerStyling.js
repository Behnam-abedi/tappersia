// tappersia/assets/js/admin/app-logic/composables/useBannerStyling.js
import { bannerStyles, contentAlignment, imageStyleObject, getApiBannerStyles, getApiContentStyles } from '../utils.js';

export function useBannerStyling(banner) {
    const getBannerContainerStyles = (view) => {
        const settings = view === 'desktop' ? banner.single : banner.single_mobile;
        if (!settings) return {};
        return {
            // --- تغییرات ---
            width: '100%', // <--- همیشه 100%
            height: 'auto',
            minHeight: `${settings.minHeight}px`, // <--- ساده‌سازی شد
            // --- پایان تغییرات ---
            border: settings.enableBorder ? `${settings.borderWidth}px solid ${settings.borderColor}` : 'none',
            borderRadius: `${settings.borderRadius}px`,
            fontFamily: 'Roboto, sans-serif'
        };
    };
    
    const getDynamicStyles = (view) => {
        const settings = view === 'desktop' ? banner.single : banner.single_mobile;
        if (!settings) return {};

        return {
            contentStyles: {
                alignItems: contentAlignment(settings.alignment),
                textAlign: settings.alignment,
                // --- تغییرات ---
                padding: `${settings.paddingY}px ${settings.paddingX}px`, // <--- پدینگ Y/X
                width: `${settings.contentWidth}${settings.contentWidthUnit}`, // <--- عرض محتوا
                // --- پایان تغییرات ---
                flexGrow: 1,
            },
            titleStyles: {
                color: settings.titleColor,
                fontSize: `${settings.titleSize}px`,
                fontWeight: settings.titleWeight,
                lineHeight: 1, // <--- ثابت شد
                margin: 0,
            },
            descriptionStyles: {
                color: settings.descColor,
                fontSize: `${settings.descSize}px`,
                fontWeight: settings.descWeight,
                whiteSpace: 'pre-wrap',
                marginTop: `${settings.marginTopDescription}px`,
                marginBottom: `0px`,
                lineHeight: 1.5, // <--- ثابت شد (یا هر مقدار دیفالت مناسب دیگر)
                // width: `${settings.descWidth}${settings.descWidthUnit}`, // <--- حذف شد
                wordWrap: 'break-word'
            },
            buttonStyles: {
                backgroundColor: settings.buttonBgColor,
                color: settings.buttonTextColor,
                fontSize: `${settings.buttonFontSize}px`,
                fontWeight: settings.buttonFontWeight,
                alignSelf: settings.alignment === 'center' ? 'center' : (settings.alignment === 'right' ? 'flex-end' : 'flex-start'),
                borderRadius: `${settings.buttonBorderRadius}px`,
                // --- تغییرات ---
                padding: `${settings.buttonPaddingY}px ${settings.buttonPaddingX}px`, // <--- پدینگ Y/X
                // --- پایان تغییرات ---
                display: 'inline-flex',
                alignItems: 'center',
                justifyContent: 'center',
                textDecoration: 'none',
                lineHeight: 1, // <--- ثابت شد
                
                // --- START: MODIFIED MARGIN LOGIC ---
                marginTop: settings.buttonMarginTopAuto ? 'auto' : `${settings.buttonMarginTop}px`
                // --- END: MODIFIED MARGIN LOGIC ---
            }
        };
    };

    const getContentStyles = (view) => getDynamicStyles(view).contentStyles;
    const getTitleStyles = (view) => getDynamicStyles(view).titleStyles;
    const getDescriptionStyles = (view) => getDynamicStyles(view).descriptionStyles;
    const getButtonStyles = (view) => getDynamicStyles(view).buttonStyles;

    // +++ START: MODIFIED FUNCTION +++
    // This function now correctly handles the new gradient object structure
    const getPromoBackgroundStyle = (promo, section) => {
        if (!promo) return 'transparent'; // Safety check

        const prefix = section; // 'header' or 'body'
        const typeKey = `${prefix}BackgroundType`;
        const stopsKey = `${prefix}GradientStops`;
        const angleKey = `${prefix}GradientAngle`;
        const colorKey = `${prefix}BgColor`;

        // Check if gradient
        if (promo[typeKey] === 'gradient') {
             if (!promo[stopsKey] || promo[stopsKey].length === 0) {
                return 'transparent';
            }
            const sortedStops = [...promo[stopsKey]].sort((a, b) => a.stop - b.stop);
            const stopsString = sortedStops.map(s => `${s.color} ${s.stop}%`).join(', ');
            return `linear-gradient(${promo[angleKey] || 90}deg, ${stopsString})`;
        }

        // Fallback to solid
        return promo[colorKey] || '#ffffff';
    };
    // +++ END: MODIFIED FUNCTION +++

    return {
        bannerStyles,
        contentAlignment,
        imageStyleObject,
        getApiBannerStyles,
        getApiContentStyles,
        getBannerContainerStyles,
        getContentStyles,
        getTitleStyles,
        getDescriptionStyles,
        getButtonStyles,
        getPromoBackgroundStyle,
    };
}