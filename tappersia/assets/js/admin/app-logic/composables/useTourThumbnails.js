const { ref, watch, onMounted, nextTick } = Vue; // nextTick دیگر نیاز نیست، اما بودنش مشکلی ندارد

export function useTourThumbnails(banner, ajax, thumbnailContainerRef) {
    // *** شروع تغییر ۱: تغییر نام متغیر ***
    const isLoadingTourThumbnails = ref(false);
    // *** پایان تغییر ۱ ***
    const thumbnailTours = ref([]);
    let sortableInstance = null;

    const fetchThumbnailTours = async () => {
        const ids = banner.tour_carousel.selectedTours;
        if (!ids || ids.length === 0) {
            thumbnailTours.value = [];
            if (sortableInstance) {
                sortableInstance.destroy();
                sortableInstance = null;
            }
            return;
        }

        // *** شروع تغییر ۲: استفاده از نام جدید ***
        isLoadingTourThumbnails.value = true;
        // *** پایان تغییر ۲ ***
        try {
            const toursData = await ajax.post('yab_fetch_tour_details_by_ids', { tour_ids: ids });
            thumbnailTours.value = ids.map(id => toursData.find(t => t.id === id)).filter(Boolean);
        } catch (error) {
            console.error('Could not fetch tour thumbnails:', error);
            thumbnailTours.value = [];
        } finally {
            // *** شروع تغییر ۳: استفاده از نام جدید ***
            isLoadingTourThumbnails.value = false;
            // *** پایان تغییر ۳ ***
        }
    };

    const initSortable = () => {
        if (thumbnailContainerRef.value) { 
            if (sortableInstance) {
                sortableInstance.destroy(); 
                sortableInstance = null;
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
        }, {
            immediate: true 
        });
    });

    return {
        thumbnailTours,
        // *** شروع تغییر ۴: بازگرداندن متغیر با نام جدید ***
        isLoadingTourThumbnails 
        // *** پایان تغییر ۴ ***
    };
}