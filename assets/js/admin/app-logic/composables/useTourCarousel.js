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

            const isRTL = computed(() => props.settings.direction === 'rtl');
            const headerSettings = computed(() => props.settings.header || {});

            const containerWidth = computed(() => {
                const cardWidth = 295;
                const spaceBetween = props.settings.spaceBetween || 18;
                const slidesPerView = props.settings.slidesPerView || 3;
                return (cardWidth * slidesPerView) + (spaceBetween * (slidesPerView - 1));
            });
            
            const gridHeight = computed(() => {
                const cardHeight = 375;
                const verticalSpace = 20;
                return (cardHeight * 2) + verticalSpace;
            });

            const slidesToRender = computed(() => {
                const originalTours = [...props.tourIds];
                const settings = props.settings;
                const tourCount = originalTours.length;
            
                if (tourCount === 0) return [];
            
                if (settings.loop) {
                    const slidesPerView = settings.slidesPerView;
                    let finalTours = [...originalTours];
            
                    if (settings.isDoubled) {
                        const minLoopCount = (2 * slidesPerView) + 2;
                        if (tourCount > 0 && tourCount < minLoopCount) {
                            const repeatCount = Math.ceil(minLoopCount / tourCount);
                            finalTours = [];
                            for (let i = 0; i < repeatCount; i++) {
                                finalTours.push(...originalTours);
                            }
                        }
                    } else {
                        if (tourCount > 0 && tourCount < slidesPerView * 2) {
                            while (finalTours.length < slidesPerView * 2) {
                                finalTours.push(...originalTours);
                            }
                        }
                    }
                    return finalTours;
                }
                return originalTours;
            });

            const generateTourCardHTML = (tour) => {
                if (!tour) {
                    return `
                    <div class="yab-tour-card-skeleton yab-skeleton-loader" style="width: 295px; height: 375px; background-color: #fff; border-radius: 14px; padding: 9px; display: flex; flex-direction: column; gap: 9px; overflow: hidden; border: 1px solid #f0f0f0;">
                        <div class="yab-skeleton-image" style="width: 100%; height: 204px; background-color: #f0f0f0; border-radius: 14px;"></div>
                        <div style="padding: 14px 5px 5px 5px; display: flex; flex-direction: column; gap: 10px; flex-grow: 1;">
                            <div class="yab-skeleton-text" style="width: 80%; height: 20px; background-color: #f0f0f0; border-radius: 4px;"></div>
                            <div class="yab-skeleton-text" style="width: 40%; height: 16px; background-color: transparent; border-radius: 4px;"></div>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: auto; padding-bottom: 5px;">
                                <div class="yab-skeleton-text" style="width: 40%; height: 20px; background-color: #f0f0f0; border-radius: 4px;"></div>
                                <div class="yab-skeleton-text" style="width: 30%; height: 16px; background-color: #f0f0f0; border-radius: 4px;"></div>
                            </div>
                            <div class="yab-skeleton-text" style="height: 33px; width: 100%; margin-top: 10px; background-color: #f0f0f0; border-radius: 4px;"></div>
                        </div>
                    </div>`;
                }
                const salePrice = tour.salePrice ? tour.salePrice.toFixed(2) : '0.00';
                const rate = tour.rate ? tour.rate.toFixed(1) : '0.0';

                return `
                    <div class="yab-tour-card" style="position: relative; text-decoration: none; color: inherit; display: block; width: 295px; height: 375px; background-color: #FFF; border-radius: 14px; overflow: hidden; display: flex; flex-direction: column; box-shadow: 0 4px 12px rgba(0,0,0,0.08); padding: 9px;">
                        <div style="position: relative; width: 100%; height: 204px;">
                            <img src="${tour.bannerImage.url}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 14px;" />
                            <div style="position: absolute; bottom: 0; ${isRTL.value ? 'left: 0; margin: 0 0 9px 11px;' : 'right: 0; margin: 0 11px 9px 0;'} min-height: 23px; min-width: 65px; display: flex; align-items: center; justify-content: center; border-radius: 29px; background: rgba(14,14,14,0.2); padding: 0 11px; backdrop-filter: blur(3px);">
                                <span style="color: white; font-size: 14px; font-weight: 500; line-height: 24px;">${tour.startProvince.name}</span>
                            </div>
                        </div>
                        <div style="display: flex; flex-direction: column; justify-content: space-between; flex-grow: 1; padding: 14px 5px 5px 5px; text-align: ${isRTL.value ? 'right' : 'left'};">
                            <div>
                                <h4 style="font-weight: 600; font-size: 14px; line-height: 24px; color: black; text-overflow: ellipsis; overflow: hidden; white-space: wrap; margin: 0;">${tour.title}</h4>
                            </div>
                            <div  style="margin-top: auto; margin-bottom: 10px; display: flex; align-items: center; justify-content: space-between; padding: 0 4px;direction:ltr ${isRTL.value ? 'row-reverse' : 'row'}">
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
                                <a href="${tour.detailUrl}" target="_blank" class="flex h-[33px] w-full items-center justify-between rounded-[5px] bg-[#00BAA4] px-[20px]" style="direction:ltr;display:flex; height: 33px; width: 100%; align-items: center; justify-content: space-between; border-radius: 5px; background-color: #00BAA4; padding: 0 20px; text-decoration: none; flex-direction: ${isRTL.value ? 'row-reverse' : 'row'};">
                                    <span style="font-size: 13px; font-weight: 600; line-height: 16px; color: white;">View More</span>
                                    <div style="width: 10px; height: 10px; border-top: 2px solid white; border-right: 2px solid white; transform: rotate(${isRTL.value ? '-135deg' : '45deg'}); border-radius: 2px;"></div>
                                </a>
                            </div>
                        </div>
                    </div>`;
            };
            
            const fetchAndRenderTours = async (idsToFetch) => {
                const uniqueIds = [...new Set(idsToFetch)].filter(id => !fetchedIds.has(id));
                if (uniqueIds.length === 0) return;
                uniqueIds.forEach(id => fetchedIds.add(id));
                try {
                    const data = await props.ajax.post('yab_fetch_tour_details_by_ids', { tour_ids: uniqueIds });
                    if (data && Array.isArray(data)) {
                        data.forEach(tourData => {
                            if (!swiperRef.value) return;
                            const slidesToUpdate = swiperRef.value.querySelectorAll(`.swiper-slide[data-tour-id="${tourData.id}"]`);
                            if (slidesToUpdate.length > 0) {
                                slidesToUpdate.forEach(slide => {
                                    slide.innerHTML = generateTourCardHTML(tourData);
                                });
                            }
                        });
                    }
                } catch (error) { console.error('AJAX call failed!', error); }
            };
            
            const checkAndLoadVisibleSlides = (swiper) => {
                if (!swiper || !swiper.slides || swiper.slides.length === 0) return;
                const idsToFetch = new Set();
                const slides = Array.from(swiper.slides);
                const isGrid = swiper.params.grid && swiper.params.grid.rows > 1;
                const rows = isGrid ? swiper.params.grid.rows : 1;
                const slidesPerView = swiper.params.slidesPerView;
                const slidesToLoadCount = (slidesPerView * rows) * 2;
                const slidesToCheck = slides.slice(swiper.activeIndex, swiper.activeIndex + slidesToLoadCount);
                if (slidesToCheck.length > 0) {
                    slidesToCheck.forEach(slide => {
                        const tourId = parseInt(slide.dataset.tourId, 10);
                        if (tourId) idsToFetch.add(tourId);
                    });
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
                    
                    slidesToRender.value.forEach(id => {
                        const slideEl = document.createElement('div');
                        slideEl.className = 'swiper-slide';
                        slideEl.setAttribute('data-tour-id', id);
                        slideEl.style.width = '295px';
                        slideEl.innerHTML = generateTourCardHTML(null);
                        wrapper.appendChild(slideEl);
                    });

                    const swiperOptions = {
                        slidesPerView: props.settings.slidesPerView,
                        spaceBetween: props.settings.spaceBetween,
                        loop: props.settings.loop,
                        dir: props.settings.direction,
                        on: {
                            init: (swiper) => { setTimeout(() => checkAndLoadVisibleSlides(swiper), 150); },
                            slideChange: (swiper) => { checkAndLoadVisibleSlides(swiper); },
                            resize: (swiper) => { checkAndLoadVisibleSlides(swiper); }
                        }
                    };
                    
                    if (props.settings.autoplay && props.settings.autoplay.enabled) {
                        swiperOptions.autoplay = {
                            delay: props.settings.autoplay.delay || 3000,
                            disableOnInteraction: false,
                        };
                    }

                    if (props.settings.navigation && props.settings.navigation.enabled) {
                        swiperOptions.navigation = {
                            nextEl: '.tappersia-carusel-next',
                            prevEl: '.tappersia-carusel-perv',
                        };
                    }

                    if (props.settings.pagination && props.settings.pagination.enabled) {
                        swiperOptions.pagination = {
                            el: '.swiper-pagination',
                            clickable: true,
                        };
                    }

                    if (props.settings.isDoubled) {
                        swiperOptions.grid = {
                            rows: 2,
                            fill: props.settings.loop ? 'column' : props.settings.gridFill,
                        };
                        swiperOptions.slidesPerGroup = 1; 
                        swiperOptions.spaceBetween = 20;
                    }
                    
                    swiperInstance.value = new Swiper(swiperRef.value, swiperOptions);
                }
            };
            
            watch(() => [props.tourIds, props.settings], () => {
                nextTick(() => {
                    initSwiper();
                });
            }, { deep: true, immediate: true });

            onUnmounted(() => {
                if (swiperInstance.value) {
                    swiperInstance.value.destroy(true, true);
                }
            });

            return { swiperRef, containerWidth, gridHeight, isRTL, headerSettings }; 
        },
        template: `
            <div :style="{ width: containerWidth + 'px', margin: '0 auto' }" :dir="settings.direction">
                <div :style="{ marginBottom: (headerSettings.marginTop || 28) + 'px' }" class="flex flex-col">
                    <div class="mb-[13px] flex w-full flex-row justify-between items-center" >
                        <div>
                            <span :style="{ 
                                fontSize: (headerSettings.fontSize || 24) + 'px', 
                                fontWeight: headerSettings.fontWeight || '700',
                                color: headerSettings.color || '#000000'
                            }">{{ headerSettings.text || 'Top Iran Tours' }}</span>
                        </div>
                        <div v-if="settings.navigation && settings.navigation.enabled"  class="flex gap-[7px] items-center">
                            <div class="tappersia-carusel-perv flex h-[36px] w-[36px] items-center justify-center rounded-[8px] bg-white shadow-[inset_0_0_0_2px_#E5E5E5] cursor-pointer" :class="isRTL ? 'pr-[3px]' : 'pl-[3px]'">
                                <div style="width: 10px; height: 10px; border-top: 2px solid black; border-right: 2px solid black; border-radius: 2px;" :style="isRTL ? { transform: 'rotate(45deg)' } :{transform: 'rotate(-135deg)'}"></div>
                            </div>
                            <div class="tappersia-carusel-next flex h-[36px] w-[36px] items-center justify-center rounded-[8px] bg-white shadow-[inset_0_0_0_2px_#E5E5E5] cursor-pointer" :class="isRTL ? 'pl-[3px]' : 'pr-[3px]'">
                                <div style="width: 10px; height: 10px; border-top: 2px solid black; border-right: 2px solid black; transform: rotate(45deg); border-radius: 2px;" :style="isRTL ? { transform: 'rotate(-135deg)' } :{transform: 'rotate(45deg)'}"></div>
                            </div>
                        </div>
                    </div>
                    <div :style="{ textAlign: isRTL ? 'right' : 'left' }" class="relative">
                        <div class="w-full h-[1px] rounded-[2px] bg-[#E2E2E2]"></div>
                        <div style="position: absolute; margin-top: -2px; width: 15px; height: 2px; border-radius: 2px;" 
                             :style="{ backgroundColor: headerSettings.lineColor || '#00BAA4', ...(isRTL ? { right: 0 } : { left: 0 }) }"></div>
                    </div>
                </div>

                <div class="swiper"
                     :ref="el => swiperRef = el"
                     :style="settings.isDoubled ? { height: gridHeight + 'px', 'padding-bottom': '10px', overflow: 'hidden' } : { overflow: 'hidden', 'padding-bottom': '10px' }">
                    <div class="swiper-wrapper">
                        </div>
                </div>
                <div v-if="settings.pagination && settings.pagination.enabled" class="swiper-pagination" style="position: static; margin-top: 30px;"></div>
            </div>
        `
    };
}