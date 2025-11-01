const { watch, nextTick } = Vue;

/**
 * Handles all validation logic for the tour carousel settings, including smart adjustments.
 * @param {object} banner - The reactive banner state object.
 * @param {function} showModal - Function to display a modal notification.
 */
export function useTourCarouselValidation(banner, showModal) {
    
    // Watcher for all tour carousel settings to perform validations
    watch(
        () => ({
            loop: banner.tour_carousel.settings.loop,
            isDoubled: banner.tour_carousel.settings.isDoubled,
            slidesPerView: banner.tour_carousel.settings.slidesPerView,
            tourCount: banner.tour_carousel.selectedTours.length
        }),
        (newVal, oldVal) => {
            // Destructure for easier access
            const { loop, isDoubled, slidesPerView, tourCount } = newVal;
            
            // Exit if not a tour carousel or no tours are selected
            if (banner.type !== 'tour-carousel' || tourCount === 0) {
                return;
            }

            // --- Main validation for Double Carousel (isDoubled is true) ---
            if (isDoubled) {
                // 1. Check if tour count is even. This rule applies regardless of loop status.
                if (tourCount % 2 !== 0) {
                    nextTick(() => {
                        banner.tour_carousel.settings.isDoubled = false;
                        showModal(
                            'Double Carousel Disabled',
                            'The number of selected tours must be an even number to use the Double Carousel. Please add or remove a tour.'
                        );
                    });
                    return; // Stop further validation as state is corrected
                }
                
                // 2. Additional validation for NON-LOOPED double carousel
                if (!loop) {
                    let maxSlides = 4; // Default for 8+ tours
                    if (tourCount < 8) {
                        maxSlides = tourCount / 2;
                    }

                    if (slidesPerView > maxSlides) {
                        nextTick(() => {
                            banner.tour_carousel.settings.slidesPerView = maxSlides;
                            showModal(
                                'Adjustment',
                                `With ${tourCount} tours in a Double Carousel, the maximum 'Slides Per View' is ${maxSlides}. It has been adjusted automatically.`
                            );
                        });
                        return;
                    }
                }
            }

            // --- Validation for SINGLE ROW Carousel (loop disabled) ---
            if (!isDoubled && !loop) {
                if (tourCount < slidesPerView) {
                    // If the user *just* changed slidesPerView to an invalid number
                    if (newVal.slidesPerView !== oldVal.slidesPerView) {
                         nextTick(() => {
                            banner.tour_carousel.settings.slidesPerView = tourCount;
                            showModal(
                                'Adjustment',
                                `Slides Per View was adjusted to ${tourCount} because you only have ${tourCount} tour(s) selected.`
                            );
                        });
                    }
                }
            }
        },
        { deep: true, immediate: true } // immediate: true runs validation on component load
    );
}