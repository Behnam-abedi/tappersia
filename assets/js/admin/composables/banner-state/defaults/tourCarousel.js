// tappersia/assets/js/admin/composables/banner-state/defaults/tourCarousel.js

export const createDefaultTourCarouselPart = () => ({
    // This will hold the full objects of selected tours
    selectedTours: [],
    // Swiper.js and other design settings will be added here later
    settings: {
    slidesPerView: 3,
    spaceBetween: 22,
    pagination: {
        el: '.swiper-pagination',
        clickable: true,
    },
    navigation: {
        nextEl: '.tappersia-carusel-next',
        prevEl: '.tappersia-carusel-perv',
    },
    }
});