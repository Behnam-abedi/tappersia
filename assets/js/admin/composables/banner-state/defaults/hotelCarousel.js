// tappersia/assets/js/admin/composables/banner-state/defaults/hotelCarousel.js

// Keep settings identical to Tour Carousel for now
export const createDefaultHotelCarouselDesktopPart = () => ({
    slidesPerView: 3,
    loop: false,
    spaceBetween: 22,
    isDoubled: false,
    gridFill: 'column',
    direction: 'ltr',
    header: {
        text: 'Top Rated Hotel in Isfahan', // Changed default text
        fontSize: 24,
        fontWeight: '700',
        color: '#ffffff', // Default to white for dark admin theme
        lineColor: '#00BAA4',
        marginTop: 28,
    },
    card: { // Keep card structure same as tour for now, adjust later
        height: 375,
        backgroundType: 'solid',
        bgColor: '#ffffff',
        gradientAngle: 90,
        gradientStops: [{ color: '#FFFFFF', stop: 0 }, { color: '#F9F9F9', stop: 100 }],
        borderWidth: 1,
        borderColor: '#E5E5E5',
        borderRadius: 14,
        padding: 9,
        imageHeight: 204,
        province: { fontSize: 14, fontWeight: '500', color: '#FFFFFF', bgColor: 'rgba(14,14,14,0.2)', blur: 3, bottom: 9, side: 11 }, // Re-purpose for city/location
        title: { fontSize: 14, fontWeight: '600', color: '#000000ff', lineHeight: 1.5 },
        price: { fontSize: 14, fontWeight: '500', color: '#00BAA4' },
        duration: { fontSize: 12, fontWeight: '400', color: '#757575' }, // Re-purpose for '/ night' text maybe
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

export const createDefaultHotelCarouselMobilePart = () => {
    const mobileDefaults = createDefaultHotelCarouselDesktopPart();
    mobileDefaults.slidesPerView = 1;
    // Keep other mobile defaults same as tour for now
    mobileDefaults.card.height = 375;
    mobileDefaults.card.padding = 9;
    mobileDefaults.card.borderRadius = 14;
    mobileDefaults.card.borderWidth = 1;
    mobileDefaults.card.imageHeight = 204;
    return mobileDefaults;
};

// Main export for the hotel carousel state part
export const createDefaultHotelCarouselPart = () => ({
    selectedHotels: [], // Changed from selectedTours
    updateCounter: 0,
    isMobileConfigured: false,
    settings: createDefaultHotelCarouselDesktopPart(),
    settings_mobile: createDefaultHotelCarouselMobilePart(),
});