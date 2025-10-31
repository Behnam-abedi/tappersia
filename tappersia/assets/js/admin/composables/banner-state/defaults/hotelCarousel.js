// tappersia/assets/js/admin/composables/banner-state/defaults/hotelCarousel.js

// Keep settings identical to Tour Carousel for now, but add card styling specifics
export const createDefaultHotelCarouselDesktopPart = () => ({
    slidesPerView: 3,
    loop: false,
    spaceBetween: 20, // Default space for new card design
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
    // --- START: Added Detailed Card Styling ---
    card: {
        height: 357, // Fixed height based on new design
        bgColor: '#ffffff',
        borderWidth: 1,
        borderColor: '#E5E5E5',
        borderRadius: 16,
        padding: 9, // Overall card padding

        imageContainer: {
            paddingX: 13,
            paddingY: 13,
        },
        image: {
            height: 176,
            radius: 14,
        },
        imageOverlay: {
            // rgba(0,0,0,0) → #00000000
            gradientStartColor: '#00000000',
            // rgba(0,0,0,0.83) → #000000D4 (0.83 × 255 ≈ 212 → D4)
            gradientEndColor: '#000000D4',
            gradientStartPercent: 38,
            gradientEndPercent: 0,
        },
        badges: {
            bestSeller: {
                textColor: '#ffffff',
                fontSize: 12,
                bgColor: '#F66A05',
                paddingX: 7,
                paddingY: 5,
                radius: 20,
            },
            discount: {
                textColor: '#ffffff',
                fontSize: 12,
                bgColor: '#FB2D51',
                paddingX: 10,
                paddingY: 5,
                radius: 20,
            },
        },
        stars: {
            shapeSize: 17,
            shapeColor: '#FCC13B',
            textSize: 12,
            textColor: '#ffffff',
        },
        bodyContent: {
            marginTop: 14,
            marginX: 19,
            textColor: '#333333',
        },
        title: {
            fontSize: 14,
            fontWeight: '600',
            color: '#333333',
            lineHeight: 1.2,
            minHeight: 34,
        },
        rating: {
            marginTop: 7,
            gap: 6,
            boxBgColor: '#5191FA',
            boxColor: '#ffffff',
            boxFontSize: 11,
            boxPaddingX: 6,
            boxPaddingY: 2,
            boxRadius: 3,
            labelColor: '#333333',
            labelFontSize: 12,
            countColor: '#999999',
            countFontSize: 10,
        },
        tags: {
            marginTop: 7,
            gap: 5,
            fontSize: 11,
            paddingX: 6,
            paddingY: 2,
            radius: 3,
        },
        divider: {
            marginTop: 9.5,
            marginBottom: 7.5,
            color: '#EEEEEE',
        },
        price: {
            fromColor: '#999999',
            fromSize: 12,
            amountColor: '#00BAA4',
            amountSize: 16,
            amountWeight: '700',
            nightColor: '#555555',
            nightSize: 13,
            originalColor: '#999999',
            originalSize: 12,
        },
    },
    // --- END: Added Detailed Card Styling ---
    autoplay: { enabled: false, delay: 3000 },
    navigation: { enabled: true },
    pagination: {
        enabled: true,
        // rgba(0, 186, 164, 0.31) → #00BAA44F (0.31 × 255 ≈ 79 → 4F)
        paginationColor: '#00BAA44F',
        paginationActiveColor: '#00BAA4',
    },
});

export const createDefaultHotelCarouselMobilePart = () => {
    const mobileDefaults = createDefaultHotelCarouselDesktopPart();
    mobileDefaults.slidesPerView = 1;
    mobileDefaults.spaceBetween = 15;

    return mobileDefaults;
};

export const createDefaultHotelCarouselPart = () => ({
    selectedHotels: [],
    updateCounter: 0,
    isMobileConfigured: false,
    settings: createDefaultHotelCarouselDesktopPart(),
    settings_mobile: createDefaultHotelCarouselMobilePart(),
});
