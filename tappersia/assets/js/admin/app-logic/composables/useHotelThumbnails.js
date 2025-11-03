// assets/js/admin/app-logic/composables/useHotelThumbnails.js
const { ref, watch, onMounted } = Vue;

export function useHotelThumbnails(banner, ajax, thumbnailContainerRef) {
    const thumbnailHotels = ref([]); // Renamed
    const isLoadingThumbnails = ref(false);
    let sortableInstance = null;

    const fetchThumbnailHotels = async () => { // Renamed
        const ids = banner.hotel_carousel.selectedHotels; // Use selectedHotels
        if (!ids || ids.length === 0) {
            thumbnailHotels.value = [];
            return;
        }

        isLoadingThumbnails.value = true;
        try {
            // Use the correct AJAX action for hotels
            const hotelsData = await ajax.post('yab_fetch_hotel_details_by_ids', { hotel_ids: ids });
            // Ensure hotelsData has necessary thumbnail info (e.g., coverImage.url)
            thumbnailHotels.value = ids.map(id => hotelsData.find(h => h.id === id)).filter(Boolean);
        } catch (error) {
            console.error('Could not fetch hotel thumbnails:', error);
            thumbnailHotels.value = [];
        } finally {
            isLoadingThumbnails.value = false;
        }
    };

    const initSortable = () => {
        if (thumbnailContainerRef.value) {
            if (sortableInstance) {
                sortableInstance.destroy();
            }
            sortableInstance = new Sortable(thumbnailContainerRef.value, {
                animation: 150,
                ghostClass: 'yab-sortable-ghost',
                onEnd: (evt) => {
                    const newOrderIds = Array.from(evt.target.children).map(child => parseInt(child.dataset.id, 10));

                    // Use selectedHotels
                    if (JSON.stringify(banner.hotel_carousel.selectedHotels) !== JSON.stringify(newOrderIds)) {
                         banner.hotel_carousel.selectedHotels = newOrderIds;
                    }
                }
            });
        }
    };

    // Watch selectedHotels
    watch(() => banner.hotel_carousel.selectedHotels, () => {
        fetchThumbnailHotels(); // Call renamed function
    }, {
        immediate: true,
        deep: true
    });

    onMounted(() => {
        watch(thumbnailContainerRef, (newEl) => {
            if (newEl) {
                initSortable();
            }
        }, { immediate: true });
    });

    return {
        thumbnailHotels, // Renamed export
        isLoadingThumbnails
    };
}