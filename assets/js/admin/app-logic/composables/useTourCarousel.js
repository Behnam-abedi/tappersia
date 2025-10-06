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
            const fetchedIds = reactive(new Set());

            const containerWidth = computed(() => {
                const cardWidth = 295;
                const spaceBetween = props.settings.spaceBetween || 18;
                const slidesPerView = props.settings.slidesPerView || 3;
                return (cardWidth * slidesPerView) + (spaceBetween * (slidesPerView - 1));
            });

            const generateTourCardHTML = (tour) => {
                if (!tour) return '<div class="yab-tour-card-skeleton" style="width: 295px; height: 375px; background-color: #e0e0e0; border-radius: 14px; animation: pulse 1.5s infinite ease-in-out;"></div>';
                const salePrice = tour.salePrice ? tour.salePrice.toFixed(2) : '0.00';
                const rate = tour.rate ? tour.rate.toFixed(1) : '0.0';

                return `
                    <a href="${tour.detailUrl}" target="_blank" class="yab-tour-card" style="position: relative; text-decoration: none; color: inherit; display: block; width: 295px; height: 375px; background-color: #FFF; border-radius: 14px; overflow: hidden; display: flex; flex-direction: column; box-shadow: 0 4px 12px rgba(0,0,0,0.08); padding: 9px;">
                        <div style="position: relative; width: 100%; height: 204px;">
                            <img src="${tour.bannerImage.url}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 14px;" />
                            <div style="position: absolute; bottom: 0; right: 0; margin: 0 11px 9px 0; min-height: 23px; min-width: 65px; display: flex; align-items: center; justify-content: center; border-radius: 29px; background: rgba(14,14,14,0.2); padding: 0 11px; backdrop-filter: blur(3px);">
                                <span style="color: white; font-size: 14px; font-weight: 500; line-height: 24px;">${tour.startProvince.name}</span>
                            </div>
                        </div>
                        <div style="display: flex; flex-direction: column; justify-content: space-between; flex-grow: 1; padding: 14px 5px 5px 5px;">
                            <div>
                                <h4 style="font-weight: 600; font-size: 14px; line-height: 24px; color: black; text-overflow: ellipsis; overflow: hidden; white-space: nowrap; margin: 0;">${tour.title}</h4>
                            </div>
                            <div style="margin-top: auto; margin-bottom: 10px; display: flex; align-items: center; justify-content: space-between; padding: 0 4px;">
                                <div style="display: flex; flex-direction: row; gap: 4px; align-items: baseline;">
                                    <span style="font-size: 14px; font-weight: 500; line-height: 24px; color: #00BAA4;">€${salePrice}</span>
                                    <span style="font-size: 12px; font-weight: 400; line-height: 24px; color: #757575;">/${tour.durationDays} Days</span>
                                </div>
                                <div style="display: flex; flex-direction: row; gap: 5px; align-items: baseline;">
                                    <span style="font-size: 13px; font-weight: 700; line-height: 24px; color: #333333;">${rate}</span>
                                    <span style="font-size: 12px; font-weight: 400; line-height: 24px; color: #757575;">(${tour.rateCount} Reviews)</span>
                                </div>
                            </div>
                            <div style="padding: 0 4px;">
                                <div class="flex h-[33px] w-full items-center justify-between rounded-[5px] bg-[#00BAA4] px-[20px]" style="display:flex; height: 33px; width: 100%; align-items: center; justify-content: space-between; border-radius: 5px; background-color: #00BAA4; padding: 0 20px;">
                                    <span style="font-size: 13px; font-weight: 600; line-height: 16px; color: white;">View More</span>
                                    <div style="width: 10px; height: 10px; border-top: 2px solid white; border-right: 2px solid white; transform: rotate(45deg); border-radius: 2px;"></div>
                                </div>
                            </div>
                        </div>
                    </a>`;
            };

            const fetchAndRenderTours = async (idsToFetch) => {
                const uniqueIds = [...new Set(idsToFetch)].filter(id => !fetchedIds.has(id));
                if (uniqueIds.length === 0) return;

                console.log(`[Request] Fetching data for Tour IDs: ${uniqueIds.join(', ')}`);
                uniqueIds.forEach(id => fetchedIds.add(id));

                try {
                    const data = await props.ajax.post('yab_fetch_tour_details_by_ids', { tour_ids: uniqueIds });
                    if (data && Array.isArray(data)) {
                        data.forEach(tourData => {
                            const slidesToUpdate = swiperRef.value.querySelectorAll(`.swiper-slide[data-tour-id="${tourData.id}"]`);
                            if (slidesToUpdate.length > 0) {
                                slidesToUpdate.forEach(slide => {
                                    slide.innerHTML = generateTourCardHTML(tourData);
                                });
                            }
                        });
                    }
                } catch (error) {
                    console.error('AJAX call failed!', error);
                }
            };
            
            const checkAndLoadVisibleSlides = (swiper) => {
                if (!swiper || !swiper.slides || props.tourIds.length === 0) return;

                const idsToFetch = new Set();
                const slidesPerView = swiper.params.slidesPerView;
                const totalOriginalSlides = props.tourIds.length;
                
                const startIndex = swiper.params.loop ? swiper.realIndex : swiper.activeIndex;

                for (let i = 0; i < slidesPerView; i++) {
                    const currentOriginalIndex = (startIndex + i) % totalOriginalSlides;
                     if (!isNaN(currentOriginalIndex)) {
                        const tourId = props.tourIds[currentOriginalIndex];
                        if (tourId) idsToFetch.add(tourId);
                    }
                }
                
                const nextSlideIndex = (startIndex + slidesPerView) % totalOriginalSlides;
                const nextTourId = props.tourIds[nextSlideIndex];
                if (nextTourId) {
                    idsToFetch.add(nextTourId);
                }

                if (idsToFetch.size > 0) {
                    fetchAndRenderTours(Array.from(idsToFetch));
                }
            };

            const initSwiper = () => {
                if (swiperInstance.value) {
                    swiperInstance.value.destroy(true, true);
                }
                if (swiperRef.value) {
                    const wrapper = swiperRef.value.querySelector('.swiper-wrapper');
                    wrapper.innerHTML = '';
                    props.tourIds.forEach(id => {
                        const slideEl = document.createElement('div');
                        slideEl.className = 'swiper-slide';
                        slideEl.setAttribute('data-tour-id', id);
                        slideEl.style.width = '295px';
                        slideEl.innerHTML = generateTourCardHTML(null);
                        wrapper.appendChild(slideEl);
                    });

                    swiperInstance.value = new Swiper(swiperRef.value, {
                        slidesPerView: props.settings.slidesPerView,
                        spaceBetween: props.settings.spaceBetween,
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
                            init: (swiper) => {
                                setTimeout(() => {
                                    const initialIds = props.tourIds.slice(0, swiper.params.slidesPerView);
                                    fetchAndRenderTours(initialIds);
                                }, 100); 
                            },
                            slideChange: (swiper) => {
                                checkAndLoadVisibleSlides(swiper);
                            }
                        }
                    });
                }
            };

            watch(() => [props.tourIds, props.settings.slidesPerView, props.settings.loop], () => {
                fetchedIds.clear();
                nextTick(() => {
                    initSwiper();
                });
            }, { deep: true, immediate: true });

            onUnmounted(() => {
                if (swiperInstance.value) {
                    swiperInstance.value.destroy(true, true);
                }
            });

            return { swiperRef, containerWidth }; 
        },
        template: `
            <div :style="{ width: containerWidth + 'px', margin: '0 auto' }">
                <div class="mb-5 flex flex-col">
                    <div class="mb-[13px] flex w-full flex-row justify-between items-center">
                        <div><span class="text-[24px] font-bold">Top Iran Tours</span></div>
                        <div class="flex flex-row gap-[7px] items-center">
                            <div class="tappersia-carusel-perv flex h-[36px] w-[36px] items-center justify-center rounded-[8px] bg-white pl-[3px] shadow-[inset_0_0_0_2px_#E5E5E5] cursor-pointer">
                                <div style="width: 10px; height: 10px; border-top: 2px solid black; border-right: 2px solid black; transform: rotate(-135deg); border-radius: 2px;"></div>
                            </div>
                            <div class="tappersia-carusel-next flex h-[36px] w-[36px] items-center justify-center rounded-[8px] bg-white pr-[3px] shadow-[inset_0_0_0_2px_#E5E5E5] cursor-pointer">
                                <div style="width: 10px; height: 10px; border-top: 2px solid black; border-right: 2px solid black; transform: rotate(45deg); border-radius: 2px;"></div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="w-full h-[1px] rounded-[2px] bg-[#E2E2E2]"></div>
                        <div style="position: absolute; margin-top: -2px; width: 15px; height: 2px; background-color: #00BAA4; border-radius: 2px;"></div>
                    </div>
                </div>

                <div class="swiper" :ref="el => swiperRef = el">
                    <div class="swiper-wrapper">
                        </div>
                </div>
                <div class="swiper-pagination" style="position: static; margin-top: 30px;"></div>
            </div>
        `
    };
}