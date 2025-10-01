// tappersia/assets/js/admin/app-logic/composables/useBannerStyling.js
import { bannerStyles, contentAlignment, imageStyleObject, getApiBannerStyles, getApiContentStyles } from '../utils.js';

export function useBannerStyling(banner) {
    const getBannerContainerStyles = (view) => {
        const settings = view === 'desktop' ? banner.single : banner.single_mobile;
        if (!settings) return {};
        return {
            width: settings.enableCustomDimensions ? `${settings.width}${settings.widthUnit}` : '100%',
            height: 'auto',
            minHeight: settings.enableCustomDimensions ? `${settings.minHeight}${settings.minHeightUnit}` : (view === 'desktop' ? '190px' : '145px'),
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
                padding: `${settings.paddingTop}px ${settings.paddingRight}px ${settings.paddingBottom}px ${settings.paddingLeft}px`,
                flexGrow: 1,
            },
            titleStyles: {
                color: settings.titleColor,
                fontSize: `${settings.titleSize}px`,
                fontWeight: settings.titleWeight,
                lineHeight: settings.titleLineHeight,
                margin: 0,
            },
            descriptionStyles: {
                color: settings.descColor,
                fontSize: `${settings.descSize}px`,
                fontWeight: settings.descWeight,
                whiteSpace: 'pre-wrap',
                marginTop: `${settings.marginTopDescription}px`,
                marginBottom: `0px`,
                lineHeight: settings.descLineHeight,
                width: `${settings.descWidth}${settings.descWidthUnit}`,
                wordWrap: 'break-word'
            },
            buttonStyles: {
                backgroundColor: settings.buttonBgColor,
                color: settings.buttonTextColor,
                fontSize: `${settings.buttonFontSize}px`,
                fontWeight: settings.buttonFontWeight,
                alignSelf: settings.alignment === 'center' ? 'center' : (settings.alignment === 'right' ? 'flex-end' : 'flex-start'),
                borderRadius: `${settings.buttonBorderRadius}px`,
                padding: `${settings.buttonPaddingTop}px ${settings.buttonPaddingRight}px ${settings.buttonPaddingBottom}px ${settings.buttonPaddingLeft}px`,
                display: 'inline-flex',
                alignItems: 'center',
                justifyContent: 'center',
                textDecoration: 'none',
                lineHeight: settings.buttonLineHeight,
                marginTop: `${settings.marginBottomDescription}px`
            }
        };
    };

    const getContentStyles = (view) => getDynamicStyles(view).contentStyles;
    const getTitleStyles = (view) => getDynamicStyles(view).titleStyles;
    const getDescriptionStyles = (view) => getDynamicStyles(view).descriptionStyles;
    const getButtonStyles = (view) => getDynamicStyles(view).buttonStyles;

    const getPromoBackgroundStyle = (promo, section) => {
        const prefix = section;
        const typeKey = `${prefix}BackgroundType`;
        const colorKey = `${prefix}BgColor`;
        const grad1Key = `${prefix}GradientColor1`;
        const grad2Key = `${prefix}GradientColor2`;
        const angleKey = `${prefix}GradientAngle`;

        if (promo[typeKey] === 'gradient') {
            const angle = promo[angleKey] || 90;
            const color1 = promo[grad1Key] || '#ffffff';
            const color2 = promo[grad2Key] || '#ffffff';
            return `linear-gradient(${angle}deg, ${color1}, ${color2})`;
        }
        return promo[colorKey] || '#ffffff';
    };

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