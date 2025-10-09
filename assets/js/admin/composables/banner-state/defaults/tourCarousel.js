// tappersia/assets/js/admin/composables/banner-state/defaults/tourCarousel.js

export const createDefaultTourCarouselPart = () => ({
    selectedTours: [],
    updateCounter: 0,
    settings: {
        slidesPerView: 3,
        loop: false,
        spaceBetween: 22,
        isDoubled: false,
        gridFill: 'column',
        direction: 'ltr',
        
        // Header settings
        header: {
            text: 'Top Iran Tours',
            fontSize: 24,
            fontWeight: '700',
            color: '#ffffffff',
            lineColor: '#00BAA4',
            marginTop: 28, // Space between header and carousel
        },

        autoplay: {
            enabled: false,
            delay: 3000,
        },
        navigation: {
            enabled: true,
        },
        pagination: {
            enabled: true,
        },
    }
});