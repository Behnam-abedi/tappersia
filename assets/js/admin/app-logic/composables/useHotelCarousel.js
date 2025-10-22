// assets/js/admin/app-logic/composables/useHotelCarousel.js
const { ref, reactive, onMounted, onUnmounted, nextTick, computed, watch } = Vue;

export function useHotelCarousel() {
    return {
        props: {
            hotelIds: { type: Array, required: true },
            ajax: { type: Object, required: true },
            settings: { type: Object, required: true }
        },
        setup(props) {
            const swiperRef = ref(null);
            const swiperInstance = ref(null);
            const fetchedIds = reactive(new Set());
            const styleTag = ref(null);

            const isRTL = computed(() => props.settings.direction === 'rtl');
            const headerSettings = computed(() => props.settings.header || {});
            const cardSettings = computed(() => props.settings.card || {});

            const updatePaginationStyles = () => {
                if (!swiperRef.value) return;
                // Add unique ID to swiperRef for style scoping
                swiperRef.value.id = `yab-hotel-carousel-vue-${Date.now()}`; 
                
                const paginationSettings = props.settings.pagination || {};
                const color = paginationSettings.paginationColor || 'rgba(0, 186, 164, 0.31)';
                const activeColor = paginationSettings.paginationActiveColor || '#00BAA4';

                const css = `
                    #${swiperRef.value.id} .swiper-pagination-bullet { background-color: ${color} !important; }
                    #${swiperRef.value.id} .swiper-pagination-bullet-active { background-color: ${activeColor} !important; }
                `;

                if (!styleTag.value) {
                    styleTag.value = document.createElement('style');
                    styleTag.value.id = `style-${swiperRef.value.id}`;
                    document.head.appendChild(styleTag.value);
                }
                styleTag.value.innerHTML = css;
            };

            const containerWidth = computed(() => {
                const cardWidth = 295;
                const spaceBetween = props.settings.spaceBetween || 18;
                const slidesPerView = props.settings.slidesPerView || 3;
                return (cardWidth * slidesPerView) + (spaceBetween * (slidesPerView - 1));
            });
            
            const gridHeight = computed(() => {
                const cardHeight = cardSettings.value.height || 375;
                const verticalSpace = 20;
                return (cardHeight * 2) + verticalSpace;
            });

            const slidesToRender = computed(() => {
                const originalHotels = [...props.hotelIds];
                const settings = props.settings;
                const hotelCount = originalHotels.length;
            
                if (hotelCount === 0) return [];
            
                if (settings.loop) {
                    const slidesPerView = settings.slidesPerView;
                    let finalItems = [...originalHotels];
            
                    if (settings.isDoubled) {
                        const minLoopCount = (2 * slidesPerView) + 2;
                        if (hotelCount > 0 && hotelCount < minLoopCount) {
                            const repeatCount = Math.ceil(minLoopCount / hotelCount);
                            finalItems = [];
                            for (let i = 0; i < repeatCount; i++) {
                                finalItems.push(...originalHotels);
                            }
                        }
                    } else {
                        const minLoopCount = slidesPerView * 2;
                        if (hotelCount > 0 && hotelCount < minLoopCount) {
                            while (finalItems.length < minLoopCount) {
                                finalItems.push(...originalHotels);
                            }
                        }
                    }
                    return finalItems;
                }
                return originalHotels;
            });

            const getCardBackground = (settings) => {
                if (!settings) return '#FFFFFF';
                if (settings.backgroundType === 'gradient') {
                    const stops = (settings.gradientStops || []).map(s => `${s.color} ${s.stop}%`).join(', ');
                    return `linear-gradient(${settings.gradientAngle || 90}deg, ${stops})`;
                }
                return settings.bgColor || '#FFFFFF';
            };

            // --- START: SKELETON FIX ---
            // This is now an exact copy of the tour carousel skeleton
            const generateHotelCardHTML = (hotel) => {
                const card = cardSettings.value;
                if (!hotel) {
                    const cardHeight = card.height || 375;
                    const imageHeight = card.imageHeight || 204;
                    return `
                    <div class="yab-hotel-card-skeleton yab-skeleton-loader" style="width: 295px; height: ${cardHeight}px; background-color: #fff; border-radius: 14px; padding: 9px; display: flex; flex-direction: column; gap: 9px; overflow: hidden; border: 1px solid #f0f0f0;">
                        <div class="yab-skeleton-image" style="width: 100%; height: ${imageHeight}px; background-color: #f0f0f0; border-radius: 14px;"></div>
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
                // --- END: SKELETON FIX ---

                 const minPrice = hotel.minPrice ? hotel.minPrice.toFixed(2) : '0.00';
                 const avgRating = hotel.avgRating ? (Math.floor(hotel.avgRating * 10) / 10) : null;
                 const starRating = hotel.star || 0;
                 const reviewCount = hotel.reviewCount || 0;
                 const coverImage = hotel.coverImage ? hotel.coverImage.url : 'https://placehold.co/295x204/292929/434343?text=No+Image';

                 const rtlFlex = isRTL.value ? 'row-reverse' : 'row';
                 const rtlTextAlign = isRTL.value ? 'right' : 'left';
                 const provincePos = isRTL.value ? `left: ${card.province.side}px;` : `right: ${card.province.side}px;`;
                 const arrowRotate = isRTL.value ? '-135deg' : '45deg';

                return `
                <div class="yab-hotel-card" style="position: relative; text-decoration: none; color: inherit; display: block; width: 295px; height: ${card.height}px; background: ${getCardBackground(card)}; border: ${card.borderWidth}px solid ${card.borderColor}; border-radius: ${card.borderRadius}px; overflow: hidden; display: flex; flex-direction: column; padding: ${card.padding}px; direction: ${isRTL.value ? 'rtl' : 'ltr'};">
                    <a href="${hotel.detailUrl || '#'}" target="_blank" style="text-decoration: none; color: inherit; display: flex; flex-direction: column; height: 100%;">
                        <div style="position: relative; width: 100%; height: ${card.imageHeight}px;">
                            <img src="${coverImage}" style="width: 100%; height: 100%; object-fit: cover; border-radius: ${card.borderRadius > 2 ? card.borderRadius - 2 : card.borderRadius}px;" />
                             <div style="position: absolute; bottom: ${card.province.bottom}px; ${provincePos} min-height: 23px; display: flex; align-items: center; justify-content: center; border-radius: 29px; background: ${card.province.bgColor}; padding: 0 11px; backdrop-filter: blur(${card.province.blur}px);">
                                <span style="color: ${card.province.color}; font-size: ${card.province.fontSize}px; font-weight: ${card.province.fontWeight}; line-height: 24px;">${hotel.province.name}</span>
                            </div>
                        </div>
                        <div style="display: flex; flex-direction: column; justify-content: space-between; flex-grow: 1; padding: 14px 5px 5px 5px; text-align: ${rtlTextAlign};">
                            <div><h4 style="font-weight: ${card.title.fontWeight}; font-size: ${card.title.fontSize}px; line-height: ${card.title.lineHeight}; color: ${card.title.color}; text-overflow: ellipsis; overflow: hidden; white-space: wrap; margin: 0;">${hotel.title}</h4></div>
                            <div style="margin-top: 5px; display: flex; align-items: center; justify-content: ${rtlTextAlign === 'right' ? 'flex-end' : 'flex-start'};">
                                <div style="color: #ffc107; display: flex;">
                                     ${[...Array(5)].map((_, i) => `<span style="font-size: 14px; width: 14px; height: 14px; line-height: 1;">${i < starRating ? '★' : '☆'}</span>`).join('')}
                                </div>
                            </div>
                            <div style="margin-top: auto; margin-bottom: 10px; display: flex; align-items: center; justify-content: space-between; padding: 0 4px; direction: ltr; flex-direction: ${rtlFlex};">
                                <div style="display: flex; flex-direction: row; gap: 5px; align-items: baseline;">
                                     ${avgRating !== null ? `<span style="font-size: ${card.rating.fontSize}px; font-weight: ${card.rating.fontWeight}; color: ${card.rating.color};">${avgRating}</span>` : ''}
                                    <span style="font-size: ${card.reviews.fontSize}px; font-weight: ${card.reviews.fontWeight}; color: ${card.reviews.color};">(${reviewCount} Reviews)</span>
                                </div>
                                <div style="display: flex; flex-direction: row; gap: 4px; align-items: baseline;">
                                    <span style="font-size: ${card.price.fontSize}px; font-weight: ${card.price.fontWeight}; color: ${card.price.color};">${'€' + minPrice}</span>
                                    <span style="font-size: ${card.duration.fontSize}px; font-weight: ${card.duration.fontWeight}; color: ${card.duration.color};">/ night</span>
                                </div>
                            </div>
                            <div style="padding: 0 4px;">
                                <div style="direction: ltr; display: flex; height: 33px; width: 100%; align-items: center; justify-content: space-between; border-radius: 5px; background-color: ${card.button.bgColor}; padding: 0 20px; text-decoration: none; flex-direction: ${rtlFlex};">
                                    <span style="font-size: ${card.button.fontSize}px; font-weight: ${card.button.fontWeight}; color: ${card.button.color};">View Details</span>
                                    <div style="width: ${card.button.arrowSize}px; height: ${card.button.arrowSize}px; border-top: 2px solid ${card.button.color}; border-right: 2px solid ${card.button.color}; transform: rotate(${arrowRotate}); border-radius: 2px;"></div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>`;
            };

            const fetchAndRenderHotels = async (idsToFetch) => {
                const uniqueIds = [...new Set(idsToFetch)].filter(id => !fetchedIds.has(id));
                if (uniqueIds.length === 0) return;
                uniqueIds.forEach(id => fetchedIds.add(id));
                try {
                    const data = await props.ajax.post('yab_fetch_hotel_details_by_ids', { hotel_ids: uniqueIds });
                    if (data && Array.isArray(data)) {
                        data.forEach(hotelData => {
                            if (!swiperRef.value) return;
                            const slidesToUpdate = swiperRef.value.querySelectorAll(`.swiper-slide[data-hotel-id="${hotelData.id}"]`);
                            if (slidesToUpdate.length > 0) {
                                slidesToUpdate.forEach(slide => {
                                    slide.innerHTML = generateHotelCardHTML(hotelData);
                                    slide.classList.add('is-loaded'); 
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
                 const startIndex = Math.max(0, swiper.activeIndex);
                 const slidesToCheck = slides.slice(startIndex, startIndex + slidesToLoadCount);

                if (slidesToCheck.length > 0) {
                    slidesToCheck.forEach(slide => {
                        const hotelId = parseInt(slide.dataset.hotelId, 10);
                        if (hotelId) idsToFetch.add(hotelId);
                    });
                }
                if (idsToFetch.size > 0) {
                    fetchAndRenderHotels(Array.from(idsToFetch));
                }
            };

            const initSwiper = () => {
                 if (swiperInstance.value) {
                    swiperInstance.value.destroy(true, true);
                    swiperInstance.value = null; 
                 }
                if (swiperRef.value) {
                    swiperRef.value.id = `yab-hotel-carousel-vue-${Date.now()}`; 

                    const wrapper = swiperRef.value.querySelector('.swiper-wrapper');
                    if (!wrapper) return; 
                    wrapper.innerHTML = ''; 

                    slidesToRender.value.forEach(id => {
                        const slideEl = document.createElement('div');
                        slideEl.className = 'swiper-slide';
                        slideEl.setAttribute('data-hotel-id', id);
                        slideEl.style.width = '295px'; 
                        slideEl.innerHTML = generateHotelCardHTML(null);
                        wrapper.appendChild(slideEl);
                    });

                    updatePaginationStyles(); 

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

                    // --- START: CONTROLS FIX ---
                    // Reverted to simple class selectors, exactly like useTourCarousel.js
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
                    // --- END: CONTROLS FIX ---

                     if (props.settings.isDoubled) {
                        swiperOptions.grid = { rows: 2, fill: props.settings.loop ? 'column' : props.settings.gridFill };
                        swiperOptions.slidesPerGroup = 1; 
                        swiperOptions.spaceBetween = 20; 
                    }

                    swiperInstance.value = new Swiper(swiperRef.value, swiperOptions);
                }
            };

            watch(() => [props.hotelIds, props.settings], () => {
                nextTick(() => {
                    initSwiper();
                });
            }, { deep: true, immediate: true });

            onUnmounted(() => {
                if (swiperInstance.value) {
                    swiperInstance.value.destroy(true, true);
                    swiperInstance.value = null;
                }
                 if (styleTag.value && styleTag.value.parentNode) {
                     styleTag.value.parentNode.removeChild(styleTag.value);
                     styleTag.value = null;
                 }
            });

            return { swiperRef, containerWidth, gridHeight, isRTL, headerSettings };
        },
        template: `
            <div :style="{ width: settings.slidesPerView > 1 ? (containerWidth + 'px') : '295px', margin: '0 auto' }" :dir="settings.direction">
                <div :style="{ marginBottom: (headerSettings.marginTop || 28) + 'px' }" class="flex flex-col">
                    <div class="mb-[13px] flex w-full flex-row justify-between items-center" >
                        <div>
                            <span :style="{ 
                                fontSize: (headerSettings.fontSize || 24) + 'px', 
                                fontWeight: headerSettings.fontWeight || '700',
                                color: headerSettings.color || '#FFFFFF' 
                            }">
                                {{ headerSettings.text || 'Top Iran Hotels' }}
                            </span>
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
                <div v-if="settings.pagination && settings.pagination.enabled" class="swiper-pagination" style="position: static; margin-top: 10px;"></div>
            </div>
        `
    };
}