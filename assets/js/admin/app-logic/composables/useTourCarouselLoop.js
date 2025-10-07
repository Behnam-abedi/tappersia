// tappersia/assets/js/admin/app-logic/composables/useTourCarouselLoop.js
const { watch } = Vue;

/**
 * Handles validation for the tour carousel when loop mode is disabled.
 * @param {object} banner - The reactive banner state object.
 * @param {function} showModal - Function to display a modal notification.
 */
export function useTourCarouselLoop(banner, showModal) {
    watch(
        () => [banner.tour_carousel.settings.loop, banner.tour_carousel.settings.slidesPerView, banner.tour_carousel.selectedTours.length],
        ([loop, slidesPerView, tourCount]) => {
            // This validation only runs when loop is disabled.
            if (banner.type !== 'tour-carousel' || loop || tourCount === 0) {
                return;
            }

            // Scenario: Loop is off, and there are not enough tours to fill the view.
            if (tourCount < slidesPerView) {
                showModal(
                    'Validation Warning',
                    `You need at least ${slidesPerView} tours to match 'Slides Per View' when loop is disabled. You currently have ${tourCount}.`
                );
                // Automatically adjust slidesPerView to a valid number.
                banner.tour_carousel.settings.slidesPerView = tourCount;
            }
        },
        { deep: true }
    );
}