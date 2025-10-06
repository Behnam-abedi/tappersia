// tappersia/assets/js/admin/app-logic/composables/useTourCarousel.js
const { ref, reactive, onMounted, onUnmounted, nextTick } = Vue;

export function useTourCarousel() {
    return {
        props: {
            tourIds: { type: Array, required: true },
            ajax: { type: Object, required: true },
        },
        setup(props) {
            const swiperRef = ref(null);
            const swiperInstance = ref(null);
            const tours = ref([]);
            const fetchedIds = reactive(new Set());

            const createInitialSkeletons = () => {
                tours.value = props.tourIds.map(id => ({ id, isLoading: true }));
            };

            const fetchTourData = async (idsToFetch) => {
                if (idsToFetch.length === 0) return;
                idsToFetch.forEach(id => fetchedIds.add(id));

                try {
                    const data = await props.ajax.post('yab_fetch_tour_details_by_ids', {
                        tour_ids: idsToFetch
                    });
                    data.forEach(tourData => {
                        const index = tours.value.findIndex(t => t.id === tourData.id);
                        if (index !== -1) {
                            tours.value[index] = { ...tourData, isLoading: false };
                        }
                    });
                } catch (error) {
                    console.error('Failed to fetch tour details:', error);
                }
            };
            
            const initSwiper = () => {
                if (swiperInstance.value) swiperInstance.value.destroy(true, true);
                if (swiperRef.value) {
                    swiperInstance.value = new Swiper(swiperRef.value, {
                        slidesPerView: 3,
                        spaceBetween: 18,
                        slidesPerGroup: 1,
                        navigation: {
                            nextEl: swiperRef.value.querySelector('.swiper-button-next'),
                            prevEl: swiperRef.value.querySelector('.swiper-button-prev'),
                        },
                        pagination: {
                            el: swiperRef.value.querySelector('.swiper-pagination'),
                            clickable: true,
                        },
                        on: {
                            slideChange: (swiper) => checkAndLoadSlides(swiper),
                            init: (swiper) => checkAndLoadSlides(swiper),
                        }
                    });
                }
            };
            
            const checkAndLoadSlides = (swiper) => {
                const idsToFetch = [];
                const { activeIndex } = swiper;
                const slidesPerView = 3; 

                for (let i = activeIndex; i < activeIndex + slidesPerView + 1; i++) {
                    if (i < tours.value.length) {
                        const tour = tours.value[i];
                        if (tour && tour.isLoading && !fetchedIds.has(tour.id)) {
                            idsToFetch.push(tour.id);
                        }
                    }
                }
                if (idsToFetch.length > 0) {
                    fetchTourData(idsToFetch);
                }
            };

            onMounted(async () => {
                createInitialSkeletons();
                await nextTick();
                initSwiper();
            });
            
            onUnmounted(() => {
                if (swiperInstance.value) swiperInstance.value.destroy(true, true);
            });

            return { tours, swiperRef }; // Return state for the template
        },
        // Define the template directly inside the component
        template: `
                <div
        class="swiper w-[100%] overflow-hidden p-[10px]"
        :ref="el => swiperRef = el"
        >
        <div class="mb-5 flex flex-col">
            <div class="mb-[13px] flex w-full flex-row justify-between">
            <div>
                <span class="text-[24px] font-bold">Top Iran Tours</span>
            </div>

            <div class="flex flex-row gap-[7px] leading-[29px]">
                <!-- Prev Button -->
                <div
                class="tappersia-carusel-perv flex h-[36px] w-[36px] items-center justify-center rounded-[8px] bg-white pl-[3px] shadow-[inset_0_0_0_2px_#E5E5E5]"
                >
                <div class="w-[10px] h-[10px] border-t-2 border-r-2 border-black rotate-[-135deg] rounded-[2px]"></div>
                </div>

                <!-- Next Button -->
                <div
                class="tappersia-carusel-next flex h-[36px] w-[36px] items-center justify-center rounded-[8px] bg-white pr-[3px] shadow-[inset_0_0_0_2px_#E5E5E5]"
                >
                <div class="w-[10px] h-[10px] border-t-2 border-r-2 border-black rotate-[45deg] rounded-[2px]"></div>
                </div>
            </div>
            </div>

            <div>
            <div class="w-full h-[1px] rounded-[2px] bg-[#E2E2E2]"></div>
            <div class="absolute mt-[-2px] w-[15px] h-[2px] rounded-[2px] bg-[#00BAA4]"></div>
            </div>
        </div>

        <div class="swiper-wrapper">
            <div
            v-for="(tour, index) in tours"
            :key="tour.id || 'skeleton-' + index"
            class="swiper-slide max-w-[295px]"
            >
            <!-- Skeleton -->
            <div
                v-if="tour.isLoading"
                class="yab-tour-card-skeleton w-[295px] h-[375px] bg-[#3a3a3a] rounded-[14px] animate-pulse"
            ></div>

            <!-- Tour Card -->
            <div
                v-else
                class="yab-tour-card relative block w-[295px] h-[375px] flex flex-col rounded-[14px] bg-white overflow-hidden p-[9px] text-inherit no-underline"
            >
                <!-- Image Section -->
                <div class="relative w-full h-[204px]">
                <img
                    :src="tour.bannerImage.url"
                    class="w-full h-full object-cover rounded-[14px]"
                />

                <div
                    class="absolute bottom-0 right-0 mx-[11px] mb-[9px] flex min-h-[23px] min-w-[65px] items-center justify-center rounded-[29px] bg-[rgba(14,14,14,0.2)] px-[11px] backdrop-blur-[3px]"
                >
                    <span class="text-white text-[14px] font-medium leading-[24px]">
                    {{ tour.startProvince.name }}
                    </span>
                </div>
                </div>

                <!-- Content Section -->
                <div class="flex flex-col justify-between flex-grow px-[5px] pt-[14px] pb-[5px]">
                <div>
                    <h4 class="font-semibold text-[14px] leading-[24px] text-black truncate">
                    {{ tour.title }}
                    </h4>
                </div>

                <!-- Price & Rating -->
                <div class="mt-auto mb-[10px] flex items-center justify-between px-[4px]">
                    <div class="flex flex-row gap-[4px]">
                    <span class="text-[14px] font-medium leading-[24px] text-[#00BAA4]">
                        €{{ tour.salePrice.toFixed(2) }}
                    </span>
                    <span class="text-[12px] font-normal leading-[24px] text-[#757575]">
                        /{{ tour.durationDays }} Days
                    </span>
                    </div>

                    <div class="flex flex-row gap-[5px] text-[#333333]">
                    <span class="text-[13px] font-bold leading-[24px] text-[#333333]">
                        {{ tour.rate.toFixed(1) }}
                    </span>
                    <span class="text-[12px] font-normal leading-[24px] text-[#757575]">
                        ({{ tour.rateCount }} Reviews)
                    </span>
                    </div>
                </div>

                <!-- View More Button -->
                <div class="px-[4px]">
                    <a
                    :href="tour.detailUrl"
                    target="_blank"
                    class="flex h-[33px] w-full items-center justify-between rounded-[5px] bg-[#00BAA4] px-[20px]"
                    >
                    <span class="text-[13px] font-semibold leading-[16px] text-white">
                        View More
                    </span>
                    <div
                        class="w-[10px] h-[10px] border-t-2 border-r-2 border-white rotate-[45deg] rounded-[2px]"
                    ></div>
                    </a>
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