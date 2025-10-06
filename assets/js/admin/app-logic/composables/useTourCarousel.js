// tappersia/assets/js/admin/app-logic/composables/useTourCarousel.js
const { ref, reactive, onMounted, onUnmounted, nextTick, computed, watch } = Vue;

export function useTourCarousel() {
    return {
        props: {
            tourIds: { type: Array, required: true },
            ajax: { type: Object, required: true },
            settings: { type: Object, required: true }
        },
        setup(props) {
            const swiperRef = ref(null);
            const swiperInstance = ref(null);
            const tours = ref([]);
            const fetchedIds = reactive(new Set());

            const containerWidth = computed(() => {
                const cardWidth = 295;
                const spaceBetween = props.settings.spaceBetween || 18;
                const slidesPerView = props.settings.slidesPerView || 3;
                return (cardWidth * slidesPerView) + (spaceBetween * (slidesPerView - 1));
            });

            // Each tour now tracks its own data and image loading state
            const createInitialSkeletons = () => {
                tours.value = props.tourIds.map(id => reactive({
                    id,
                    data: null,
                    isDataLoading: true,
                    isImageLoaded: false
                }));
            };

            const fetchTourData = async (idsToFetch) => {
                if (idsToFetch.length === 0) return;
                idsToFetch.forEach(id => fetchedIds.add(id));

                try {
                    const data = await props.ajax.post('yab_fetch_tour_details_by_ids', {
                        tour_ids: idsToFetch
                    });
                    data.forEach(tourData => {
                        // In loop mode, multiple slides can have the same data-tour-id
                        tours.value.filter(t => t.id === tourData.id).forEach(tour => {
                             if (tour) {
                                tour.data = tourData;
                                tour.isDataLoading = false;
                            }
                        });
                    });
                } catch (error) {
                    console.error('Failed to fetch tour details:', error);
                }
            };

            const onImageLoad = (tourId) => {
                 tours.value.filter(t => t.id === tourId).forEach(tour => {
                     if (tour) {
                        tour.isImageLoaded = true;
                    }
                 });
            };

            const initSwiper = () => {
                if (swiperInstance.value) {
                    swiperInstance.value.destroy(true, true);
                }
                if (swiperRef.value) {
                    swiperInstance.value = new Swiper(swiperRef.value, {
                        slidesPerView: props.settings.slidesPerView,
                        spaceBetween: props.settings.spaceBetween,
                        slidesPerGroup: 1,
                        centeredSlides: props.settings.loop,
                        loop: props.settings.loop,
                        navigation: {
                            nextEl: '.tappersia-carusel-next',
                            prevEl: '.tappersia-carusel-perv',
                        },
                        pagination: {
                            el: '.swiper-pagination',
                            clickable: true,
                        },
                        on: {
                            init: (swiper) => checkAndLoadSlides(swiper),
                            slideChange: (swiper) => checkAndLoadSlides(swiper),
                        }
                    });
                }
            };

            const checkAndLoadSlides = (swiper) => {
                const idsToFetch = [];
                swiper.slides.forEach(slide => {
                    if (slide.classList.contains('swiper-slide-visible')) {
                        const tourId = parseInt(slide.dataset.tourId, 10);
                        if (tourId && !fetchedIds.has(tourId)) {
                            idsToFetch.push(tourId);
                        }
                    }
                });
                if (idsToFetch.length > 0) {
                    fetchTourData([...new Set(idsToFetch)]);
                }
            };

            watch(() => [props.settings.slidesPerView, props.settings.loop], () => {
                createInitialSkeletons(); // Re-create slides when settings change to reset loading state
                nextTick(() => {
                    initSwiper();
                });
            }, { deep: true });

            onMounted(async () => {
                createInitialSkeletons();
                await nextTick();
                initSwiper();
            });

            onUnmounted(() => {
                if (swiperInstance.value) {
                    swiperInstance.value.destroy(true, true);
                }
            });

            return { tours, swiperRef, containerWidth, onImageLoad };
        },
        template: `
            <div :style="{ width: containerWidth + 'px', margin: '0 auto' }">
                <div class="mb-5 flex flex-col">
                    <div class="mb-[13px] flex w-full flex-row justify-between items-center">
                        <div><span class="text-[24px] font-bold">Top Iran Tours</span></div>
                        <div class="flex flex-row gap-[7px] items-center">
                            <div class="tappersia-carusel-perv flex h-[36px] w-[36px] items-center justify-center rounded-[8px] bg-white pl-[3px] shadow-[inset_0_0_0_2px_#E5E5E5] cursor-pointer">
                                <div class="w-[10px] h-[10px] border-t-2 border-r-2 border-black rotate-[-135deg] rounded-[2px]"></div>
                            </div>
                            <div class="tappersia-carusel-next flex h-[36px] w-[36px] items-center justify-center rounded-[8px] bg-white pr-[3px] shadow-[inset_0_0_0_2px_#E5E5E5] cursor-pointer">
                                <div class="w-[10px] h-[10px] border-t-2 border-r-2 border-black rotate-[45deg] rounded-[2px]"></div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="w-full h-[1px] rounded-[2px] bg-[#E2E2E2]"></div>
                        <div class="absolute mt-[-2px] w-[15px] h-[2px] rounded-[2px] bg-[#00BAA4]"></div>
                    </div>
                </div>

                <div class="swiper" :ref="el => swiperRef = el">
                    <div class="swiper-wrapper">
                        <div v-for="tour in tours" :key="tour.id" class="swiper-slide" :data-tour-id="tour.id" style="width: 295px !important;">
                           <div v-if="tour.isDataLoading || !tour.isImageLoaded" class="yab-tour-card-skeleton w-[295px] h-[375px] bg-[#3a3a3a] rounded-[14px] animate-pulse"></div>
                            <div v-show="!tour.isDataLoading && tour.isImageLoaded" class="yab-tour-card relative block w-[295px] h-[375px] flex flex-col rounded-[14px] bg-white overflow-hidden p-[9px] text-inherit no-underline">
                                <div class="relative w-full h-[204px]">
                                    <img v-if="tour.data" :src="tour.data.bannerImage.url" class="w-full h-full object-cover rounded-[14px]" @load="onImageLoad(tour.id)" />
                                    <div class="absolute bottom-0 right-0 mx-[11px] mb-[9px] flex min-h-[23px] min-w-[65px] items-center justify-center rounded-[29px] bg-[rgba(14,14,14,0.2)] px-[11px] backdrop-blur-[3px]">
                                        <span class="text-white text-[14px] font-medium leading-[24px]">{{ tour.data?.startProvince.name }}</span>
                                    </div>
                                </div>
                                <div class="flex flex-col justify-between flex-grow px-[5px] pt-[14px] pb-[5px]">
                                    <div><h4 class="font-semibold text-[14px] leading-[24px] text-black truncate">{{ tour.data?.title }}</h4></div>
                                    <div class="mt-auto mb-[10px] flex items-center justify-between px-[4px]">
                                        <div class="flex flex-row gap-[4px]">
                                            <span class="text-[14px] font-medium leading-[24px] text-[#00BAA4]">€{{ tour.data?.salePrice.toFixed(2) }}</span>
                                            <span class="text-[12px] font-normal leading-[24px] text-[#757575]">/{{ tour.data?.durationDays }} Days</span>
                                        </div>
                                        <div class="flex flex-row gap-[5px] text-[#333333]">
                                            <span class="text-[13px] font-bold leading-[24px] text-[#333333]">{{ tour.data?.rate.toFixed(1) }}</span>
                                            <span class="text-[12px] font-normal leading-[24px] text-[#757575]">({{ tour.data?.rateCount }} Reviews)</span>
                                        </div>
                                    </div>
                                    <div class="px-[4px]">
                                        <a :href="tour.data?.detailUrl" target="_blank" class="flex h-[33px] w-full items-center justify-between rounded-[5px] bg-[#00BAA4] px-[20px]">
                                            <span class="text-[13px] font-semibold leading-[16px] text-white">View More</span>
                                            <div class="w-[10px] h-[10px] border-t-2 border-r-2 border-white rotate-[45deg] rounded-[2px]"></div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="swiper-pagination static mt-[30px]"></div>
            </div>
        `
    };
}