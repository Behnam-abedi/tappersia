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