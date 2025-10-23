// tappersia/assets/js/admin/composables/banner-state/defaults/hotelCarousel.js

// Keep settings identical to Tour Carousel for now, but add card styling specifics
export const createDefaultHotelCarouselDesktopPart = () => ({
    slidesPerView: 3,
    loop: false,
    spaceBetween: 18, // Default space for new card design
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
        // backgroundType: 'solid', // Keep background simple for now
        bgColor: '#ffffff',
        // gradientAngle: 90,
        // gradientStops: [{ color: '#FFFFFF', stop: 0 }, { color: '#F9F9F9', stop: 100 }],
        borderWidth: 1,
        borderColor: '#E5E5E5',
        borderRadius: 16,
        padding: 9, // Overall card padding

        imageContainer: { // Padding inside the image div
            paddingX: 13,
            paddingY: 13,
        },
        image: {
            height: 176, // Fixed height
            radius: 14,
        },
        imageOverlay: { // Settings for the black gradient highlight
            gradientStartColor: 'rgba(0,0,0,0)', // Top color
            gradientEndColor: 'rgba(0,0,0,0.83)',   // Bottom color
            gradientStartPercent: 38, // Where the gradient starts fading from top (percentage)
            gradientEndPercent: 0, // Where the bottom color starts (percentage from bottom)
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
        stars: { // Combined star settings
            shapeSize: 17,
            shapeColor: '#FCC13B',
            textSize: 12,
            textColor: '#ffffff',
        },
        bodyContent: { // Container below image
            marginTop: 14,
            marginX: 19, // Left/Right margin/padding for the content block
            textColor: '#333333', // Default text color in this area
        },
        title: {
            fontSize: 14,
            fontWeight: '600',
            color: '#333333',
            lineHeight: 1.2, // Approx 17px / 14px
            minHeight: 34, // For 2 lines
        },
        rating: { // Combined rating elements
            marginTop: 7,
            gap: 6,
            boxBgColor: '#5191FA',
            boxColor: '#ffffff',
            boxFontSize: 11,
            // boxFontWeight: 'normal', // Not needed, implied
            boxPaddingX: 6,
            boxPaddingY: 2,
            boxRadius: 3,
            labelColor: '#333333',
            labelFontSize: 12,
            // labelFontWeight: 'normal', // Implied
            countColor: '#999999',
            countFontSize: 10,
        },
        tags: {
            marginTop: 7,
            gap: 5,
            fontSize: 11, // Base font size for tags
            paddingX: 6,
            paddingY: 2,
            radius: 3,
            // Colors are determined dynamically by getTagClass
        },
        divider: {
            marginTop: 9.5,
            marginBottom: 7.5,
            color: '#EEEEEE',
        },
        price: { // Combined price elements
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
        // 'button' settings are removed as the new card is just a link
    },
    // --- END: Added Detailed Card Styling ---
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
    mobileDefaults.spaceBetween = 15; // Slightly less space for mobile maybe

    // Mobile Card Overrides (Only if different from desktop)
    // Most card styles likely remain the same due to fixed card design.
    // If specific mobile overrides are needed, add them here:
    // e.g., mobileDefaults.card.title.fontSize = 13;

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
