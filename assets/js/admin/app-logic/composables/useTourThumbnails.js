const { ref, watch, onMounted } = Vue;

export function useTourThumbnails(banner, ajax, thumbnailContainerRef) {
    const thumbnailTours = ref([]);
    const isLoadingThumbnails = ref(false);
    let sortableInstance = null;

    const fetchThumbnailTours = async () => {
        const ids = banner.tour_carousel.selectedTours;
        if (!ids || ids.length === 0) {
            thumbnailTours.value = [];
            return;
        }

        isLoadingThumbnails.value = true;
        try {
            const toursData = await ajax.post('yab_fetch_tour_details_by_ids', { tour_ids: ids });
            thumbnailTours.value = ids.map(id => toursData.find(t => t.id === id)).filter(Boolean);
        } catch (error) {
            console.error('Could not fetch tour thumbnails:', error);
            thumbnailTours.value = [];
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
                    
                    if (JSON.stringify(banner.tour_carousel.selectedTours) !== JSON.stringify(newOrderIds)) {
                         banner.tour_carousel.selectedTours = newOrderIds;
                    }
                }
            });
        }
    };

    // FIX: Simplified watcher to ensure thumbnails are always fetched on change.
    watch(() => banner.tour_carousel.selectedTours, () => {
        fetchThumbnailTours();
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
        thumbnailTours,
        isLoadingThumbnails
    };
}