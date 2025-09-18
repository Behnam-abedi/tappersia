// tappersia/assets/js/admin/app-logic/utils.js
export const bannerStyles = (b) => {
    if (!b) return '';
    if (b.backgroundType === 'gradient') {
        if (!b.gradientStops || b.gradientStops.length === 0) {
            return 'transparent';
        }
        const sortedStops = [...b.gradientStops].sort((a, b) => a.stop - b.stop);
        const stopsString = sortedStops.map(s => `${s.color} ${s.stop}%`).join(', ');
        return `linear-gradient(${b.gradientAngle || 90}deg, ${stopsString})`;
    }
    return b.bgColor;
};

export const contentAlignment = (align) => align === 'right' ? 'flex-end' : (align === 'center' ? 'center' : 'flex-start');

export const imageStyleObject = (b) => {
    const style = { 
        position: 'absolute', 
        objectFit: 'cover', // Always cover
        right: `${b.imagePosRight||0}px`, 
        bottom: `${b.imagePosBottom||0}px` 
    };

    if (b.enableCustomImageSize) {
        // Use custom values if they exist, otherwise fallback to default
        style.width = (b.imageWidth !== null && b.imageWidth !== '') ? `${b.imageWidth}${b.imageWidthUnit}` : 'auto';
        style.height = (b.imageHeight !== null && b.imageHeight !== '') ? `${b.imageHeight}${b.imageHeightUnit}` : '100%';
    } else {
        // Default behavior
        style.width = 'auto';
        style.height = '100%';
    }
    return style;
};

// START: NEW FUNCTIONS FOR API BANNER
export const getApiBannerStyles = (view, banner) => {
    const settings = view === 'desktop' ? banner.api.design : banner.api.design_mobile;
    const defaultHeight = view === 'desktop' ? '150px' : '80px';
    
    return {
        background: bannerStyles(settings),
        border: `${settings.enableBorder ? settings.borderWidth : 0}px solid ${settings.borderColor}`,
        borderRadius: `${settings.borderRadius}px`,
        width: settings.enableCustomDimensions ? `${settings.width}${settings.widthUnit}` : '100%',
        minHeight: settings.enableCustomDimensions ? `${settings.height}${settings.heightUnit}` : defaultHeight,
        height: 'auto',
        flexDirection: settings.layout === 'right' ? 'row-reverse' : 'row',
        overflow: 'hidden',
        position: 'relative',
    };
};

export const getApiContentStyles = (view, banner) => {
    const settings = view === 'desktop' ? banner.api.design : banner.api.design_mobile;
    
    return {
        padding: `${settings.paddingTop}px ${settings.paddingRight}px ${settings.paddingBottom}px ${settings.paddingLeft}px`,
        textAlign: settings.layout === 'right' ? 'right' : 'left',
        justifyContent: 'space-between'
    };
};
// END: NEW FUNCTIONS FOR API BANNER