// tappersia/assets/js/admin/composables/banner-state/defaults/tourCarousel.js

export const createDefaultTourCarouselDesktopPart = () => ({
    slidesPerView: 3,
    loop: false,
    spaceBetween: 22,
    isDoubled: false,
    gridFill: 'column',
    direction: 'ltr',
    header: {
        text: 'Top Iran Tours',
        fontSize: 24,
        fontWeight: '700',
        color: '#ffffff',
        lineColor: '#00BAA4',
        marginTop: 28,
    },
    card: {
        height: 375,
        backgroundType: 'solid',
        bgColor: '#000000',
        gradientAngle: 90,
        gradientStops: [{ color: '#FFFFFF', stop: 0 }, { color: '#F9F9F9', stop: 100 }],
        borderWidth: 0,
        borderColor: '#E0E0E0',
        borderRadius: 14,
        padding: 9,
        imageHeight: 204,
        province: {
            fontSize: 14,
            fontWeight: '500',
            color: '#FFFFFF',
            bgColor: 'rgba(14,14,14,0.2)',
            blur: 3,
            bottom: 9,
            side: 11
        },
        title: { fontSize: 14, fontWeight: '600', color: '#000000ff', lineHeight: 1.5 },
        price: { fontSize: 14, fontWeight: '500', color: '#00BAA4' },
        duration: { fontSize: 12, fontWeight: '400', color: '#757575' },
        rating: { fontSize: 13, fontWeight: '700', color: '#333333' },
        reviews: { fontSize: 12, fontWeight: '400', color: '#757575' },
        button: { bgColor: '#00BAA4', fontSize: 13, fontWeight: '600', color: '#FFFFFF', arrowSize: 10 }
    },
    autoplay: { enabled: false, delay: 3000 },
    navigation: { enabled: true },
    pagination: { 
        enabled: true,
        paginationColor: 'rgba(0, 186, 164, 0.31)',
        paginationActiveColor: '#00BAA4',
    },
});

export const createDefaultTourCarouselMobilePart = () => {
    const mobileDefaults = createDefaultTourCarouselDesktopPart();
    mobileDefaults.slidesPerView = 1;
    mobileDefaults.card.height = 375;
    mobileDefaults.card.padding = 9;
    mobileDefaults.card.borderRadius = 14;
    mobileDefaults.card.borderWidth = 1;
    mobileDefaults.card.imageHeight = 204;
    return mobileDefaults;
};


export const createDefaultTourCarouselPart = () => ({
    selectedTours: [],
    updateCounter: 0,
    isMobileConfigured: false,
    settings: createDefaultTourCarouselDesktopPart(),
    settings_mobile: createDefaultTourCarouselMobilePart(),
});