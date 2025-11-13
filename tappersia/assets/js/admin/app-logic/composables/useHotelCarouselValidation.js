// assets/js/admin/app-logic/composables/useHotelCarouselValidation.js
const { watch, nextTick } = Vue;

/**
 * Handles validation logic for the hotel carousel settings.
 * @param {object} banner - The reactive banner state object.
 * @param {function} showModal - Function to display a modal notification.
 */
export function useHotelCarouselValidation(banner, showModal) {

    watch(
        () => ({
            loop: banner.hotel_carousel?.settings?.loop, // Use optional chaining
            isDoubled: banner.hotel_carousel?.settings?.isDoubled,
            slidesPerView: banner.hotel_carousel?.settings?.slidesPerView,
            hotelCount: banner.hotel_carousel?.selectedHotels?.length ?? 0 // Use selectedHotels and nullish coalescing
        }),
        (newVal, oldVal) => {
            // Destructure for easier access - check if newVal exists
             if (!newVal || newVal.loop === undefined) return; // Exit if hotel_carousel part not initialized yet

            const { loop, isDoubled, slidesPerView, hotelCount } = newVal;

            // Exit if not a hotel carousel or no hotels are selected
            if (banner.type !== 'hotel-carousel' || hotelCount === 0) {
                return;
            }

            // --- Validation logic (mirrors tour carousel for now) ---
             if (isDoubled) {
                if (hotelCount % 2 !== 0) {
                    nextTick(() => {
                        banner.hotel_carousel.settings.isDoubled = false;
                        showModal(
                            'Double Carousel Disabled',
                            'The number of selected hotels must be an even number to use the Double Carousel.'
                        );
                    });
                    return;
                }
                if (!loop) {
                    let maxSlides = Math.min(4, Math.floor(hotelCount / 2)); // Adjusted max slides logic slightly
                    if (slidesPerView > maxSlides) {
                        nextTick(() => {
                            banner.hotel_carousel.settings.slidesPerView = maxSlides;
                            showModal(
                                'Adjustment',
                                `With ${hotelCount} hotels in a Double Carousel, max 'Slides Per View' is ${maxSlides}. Adjusted automatically.`
                            );
                        });
                        return;
                    }
                }
             }

             if (!isDoubled && !loop) {
                if (hotelCount < slidesPerView) {
                    if (newVal.slidesPerView !== oldVal?.slidesPerView) { // Optional chaining for oldVal
                        nextTick(() => {
                            banner.hotel_carousel.settings.slidesPerView = hotelCount;
                            showModal(
                                'Adjustment',
                                `Slides Per View adjusted to ${hotelCount} (you only have ${hotelCount} hotel(s) selected).`
                            );
                        });
                    }
                }
            }
        },
        { deep: true, immediate: true }
    );
}