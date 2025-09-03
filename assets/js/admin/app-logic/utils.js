// tappersia/assets/js/admin/app-logic/utils.js
export const bannerStyles = (b, section = null) => {
    if (!b) return '';
    let typeKey = 'backgroundType', gradAngleKey = 'gradientAngle', gradColor1Key = 'gradientColor1', gradColor2Key = 'gradientColor2', bgColorKey = 'bgColor';
    if (section) { 
        typeKey = section + 'BackgroundType';
        bgColorKey = section + 'BgColor';
        gradColor1Key = section + 'GradientColor1';
        gradColor2Key = section + 'GradientColor2';
        gradAngleKey = section + 'GradientAngle';
    }
    return b[typeKey] === 'gradient' 
        ? `linear-gradient(${b[gradAngleKey]||90}deg, ${b[gradColor1Key]}, ${b[gradColor2Key]})` 
        : b[bgColorKey];
};

export const contentAlignment = (align) => align === 'right' ? 'flex-end' : (align === 'center' ? 'center' : 'flex-start');

export const imageStyleObject = (b) => {
    const style = { position: 'absolute', right: `${b.imagePosRight||0}px`, bottom: `${b.imagePosBottom||0}px` };
    if (b.enableCustomImageSize) {
        style.width = b.imageWidth ? `${b.imageWidth}px` : 'auto';
        style.height = b.imageHeight ? `${b.imageHeight}px` : 'auto';
    } else {
        style.objectFit = b.imageFit;
    }
    return style;
};