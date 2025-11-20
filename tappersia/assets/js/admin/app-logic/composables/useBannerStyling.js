// tappersia/assets/js/admin/app-logic/composables/useBannerStyling.js
import { bannerStyles, contentAlignment, imageStyleObject, getApiBannerStyles, getApiContentStyles } from '../utils.js';

export function useBannerStyling(banner) {
    const getBannerContainerStyles = (view) => {
        const settings = view === 'desktop' ? banner.single : banner.single_mobile;
        if (!settings) return {};
        return {
            width: '100%',
            height: 'auto',
            minHeight: `${settings.minHeight}px`,
            border: settings.enableBorder ? `${settings.borderWidth}px solid ${banner.single.borderColor}` : 'none', // Color from desktop
            borderRadius: `${settings.borderRadius}px`,
            fontFamily: 'Roboto, sans-serif'
        };
    };
    
    // ***** START: REFACTORED FUNCTION *****
    const getDynamicStyles = (view) => {
        if (view === 'desktop') {
            const settings = banner.single;
            if (!settings) return {};
            return {
                contentStyles: {
                    alignItems: contentAlignment(settings.alignment),
                    textAlign: settings.alignment,
                    padding: `${settings.paddingY}px ${settings.paddingX}px`,
                    width: `${settings.contentWidth}${settings.contentWidthUnit}`,
                    minHeight: `${settings.minHeight}px`,
                    flexGrow: 1,
                    display: 'flex', 
                    flexDirection: 'column', 
                    zIndex: 3,
                    position: 'relative',
                    boxSizing: 'border-box'
                },
                titleStyles: {
                    color: settings.titleColor,
                    fontSize: `${settings.titleSize}px`,
                    fontWeight: settings.titleWeight,
                    lineHeight: 1,
                    margin: 0,
                },
                descriptionStyles: {
                    color: settings.descColor,
                    fontSize: `${settings.descSize}px`,
                    fontWeight: settings.descWeight,
                    whiteSpace: 'pre-wrap',
                    marginTop: `${settings.marginTopDescription}px`,
                    marginBottom: `0px`,
                    lineHeight: 1.5,
                    wordWrap: 'break-word'
                },
                buttonStyles: {
                    backgroundColor: settings.buttonBgColor,
                    color: settings.buttonTextColor,
                    fontSize: `${settings.buttonFontSize}px`,
                    fontWeight: settings.buttonFontWeight,
                    alignSelf: settings.alignment === 'center' ? 'center' : (settings.alignment === 'right' ? 'flex-end' : 'flex-start'),
                    borderRadius: `${settings.buttonBorderRadius}px`,
                    padding: `${settings.buttonPaddingY}px ${settings.buttonPaddingX}px`,
                    display: 'inline-flex',
                    alignItems: 'center',
                    justifyContent: 'center',
                    textDecoration: 'none',
                    lineHeight: 1,
                    marginTop: settings.buttonMarginTopAuto ? 'auto' : `${settings.buttonMarginTop}px`,
                    marginBottom: `${settings.buttonMarginBottom}px`
                }
            };
        } else {
            // --- Mobile View: Combine settings ---
            const mobileSettings = banner.single_mobile;
            const desktopSettings = banner.single; // Get desktop settings
            if (!mobileSettings || !desktopSettings) return {};

            const mobileAlignment = contentAlignment(desktopSettings.alignment); // Use desktop alignment

            return {
                contentStyles: {
                    alignItems: mobileAlignment,
                    textAlign: desktopSettings.alignment, // Use desktop alignment
                    padding: `${mobileSettings.paddingY}px ${mobileSettings.paddingX}px`, // Mobile padding
                    width: `${mobileSettings.contentWidth}${mobileSettings.contentWidthUnit}`, // Mobile width
                    minHeight: `${mobileSettings.minHeight}px`, // Mobile minHeight
                    flexGrow: 1,
                    display: 'flex', 
                    flexDirection: 'column', 
                    zIndex: 3,
                    position: 'relative',
                    boxSizing: 'border-box'
                },
                titleStyles: {
                    color: desktopSettings.titleColor, // Desktop color
                    fontSize: `${mobileSettings.titleSize}px`, // Mobile size
                    fontWeight: mobileSettings.titleWeight, // Mobile weight
                    lineHeight: 1,
                    margin: 0,
                },
                descriptionStyles: {
                    color: desktopSettings.descColor, // Desktop color
                    fontSize: `${mobileSettings.descSize}px`, // Mobile size
                    fontWeight: mobileSettings.descWeight, // Mobile weight
                    whiteSpace: 'pre-wrap',
                    marginTop: `${mobileSettings.marginTopDescription}px`, // Mobile margin
                    marginBottom: `0px`,
                    lineHeight: 1.5,
                    wordWrap: 'break-word'
                },
                buttonStyles: {
                    backgroundColor: desktopSettings.buttonBgColor, // Desktop color
                    color: desktopSettings.buttonTextColor, // Desktop color
                    fontSize: `${mobileSettings.buttonFontSize}px`, // Mobile size
                    fontWeight: desktopSettings.buttonFontWeight, // Desktop weight
                    alignSelf: desktopSettings.alignment === 'center' ? 'center' : (desktopSettings.alignment === 'right' ? 'flex-end' : 'flex-start'), // Desktop alignment
                    borderRadius: `${mobileSettings.buttonBorderRadius}px`, // Mobile radius
                    padding: `${mobileSettings.buttonPaddingY}px ${mobileSettings.buttonPaddingX}px`, // Mobile padding
                    display: 'inline-flex',
                    alignItems: 'center',
                    justifyContent: 'center',
                    textDecoration: 'none',
                    lineHeight: 1,
                    marginTop: mobileSettings.buttonMarginTopAuto ? 'auto' : `${mobileSettings.buttonMarginTop}px`, // Mobile margin
                    marginBottom: `${mobileSettings.buttonMarginBottom}px` // Mobile margin
                }
            };
        }
    };
    // ***** END: REFACTORED FUNCTION *****

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