// tappersia/assets/js/admin/composables/banner-state/defaults/tourCarousel.js

export const createDefaultTourCarouselPart = () => ({
    selectedTours: [],
    settings: {
        slidesPerView: 3,
        loop: false, // <-- Added this line
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