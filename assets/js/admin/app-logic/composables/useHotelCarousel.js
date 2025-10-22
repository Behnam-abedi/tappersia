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
            const cardSettings = computed(() => props.settings.card || {}); // We might not use this if card is hardcoded

            const updatePaginationStyles = () => {
                if (!swiperRef.value) return;
                swiperRef.value.id = `yab-hotel-carousel-vue-${Date.now()}`;
                
                const paginationSettings = props.settings.pagination || {};
                const color = paginationSettings.paginationColor || 'rgba(0, 186, 164, 0.31)';
                const activeColor = paginationSettings.paginationActiveColor || '#00BAA4';

                // --- START: CSS FIX ---
                // Add tag styles to head
                const css = `
                    #${swiperRef.value.id} .swiper-pagination-bullet { background-color: ${color} !important; }
                    #${swiperRef.value.id} .swiper-pagination-bullet-active { background-color: ${activeColor} !important; }
                    
                    /* Tag Styles */
                    #${swiperRef.value.id} .hotel-label-luxury { background: #333333; color: #fff; }
                    #${swiperRef.value.id} .hotel-label-business { background: #DAF6FF; color: #04A5D8; }
                    #${swiperRef.value.id} .hotel-label-boutique { background: #f8f3b0; color: #a8a350; }
                    #${swiperRef.value.id} .hotel-label-traditional { background: #FAECE0; color: #B68960; }
                    #${swiperRef.value.id} .hotel-label-economy { background: #FFE9F7; color: #FF48C3; }
                    #${swiperRef.value.id} .hotel-label-hostel { background: #B0B0B0; color: #FFF; }
                    #${swiperRef.value.id} .hotel-label-default { background: #e0e0e0; color: #555; }
                `;
                // --- END: CSS FIX ---

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
                const cardHeight = 357; // Hardcoded height from your HTML example
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

            // --- START: CARD GENERATION FIX ---

            // Rating Label Helper
            const getRatingLabel = (score) => {
                if (!score || score == 0) return 'New';
                if (score >= 4.6) return 'Excellent';
                if (score >= 4.1) return 'Very Good';
                if (score >= 3.6) return 'Good';
                if (score >= 3.0) return 'Average';
                return 'Poor';
            };
            
            // Tag Class Helper
            const getTagClass = (tag) => {
                const tagName = tag.toLowerCase();
                const validTags = ['luxury', 'business', 'boutique', 'traditional', 'economy', 'hostel'];
                if (validTags.includes(tagName)) {
                    return `hotel-label-${tagName}`;
                }
                return 'hotel-label-default'; // Fallback class
            };

            const generateHotelCardHTML = (hotel) => {
                // --- SKELETON FIX: Exact copy from Tour ---
                if (!hotel) {
                    const cardHeight = 357; // Use new card height
                    const imageHeight = 176; // Use new card image height
                    return `
                    <div class="yab-hotel-card-skeleton yab-skeleton-loader" style="width: 295px; height: ${cardHeight}px; background-color: #fff; border-radius: 14px; padding: 9px; display: flex; flex-direction: column; gap: 9px; overflow: hidden; border: 1px solid #f0f0f0;">
                        <div class="yab-skeleton-image" style="width: 100%; height: ${imageHeight}px; background-color: #f0f0f0; border-radius: 14px;"></div>
                        <div style="padding: 14px 19px 5px 19px; display: flex; flex-direction: column; gap: 10px; flex-grow: 1;">
                            <div class="yab-skeleton-text" style="width: 80%; height: 20px; background-color: #f0f0f0; border-radius: 4px;"></div>
                            <div class="yab-skeleton-text" style="width: 40%; height: 16px; background-color: transparent; border-radius: 4px;"></div>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: auto; padding-bottom: 5px;">
                                <div class="yab-skeleton-text" style="width: 40%; height: 20px; background-color: #f0f0f0; border-radius: 4px;"></div>
                                <div class="yab-skeleton-text" style="width: 30%; height: 16px; background-color: #f0f0f0; border-radius: 4px;"></div>
                            </div>
                        </div>
                    </div>`;
                }
                // --- END SKELETON FIX ---

                // --- NEW CARD LOGIC ---
                const {
                    coverImage, isFeatured, discount = 0, minPrice = 0,
                    star = 0, title = 'N/A', avgRating, reviewCount = 0,
                    customTags = [], detailUrl = '#'
                } = hotel;

                const imageUrl = coverImage ? coverImage.url : 'https://placehold.co/276x176/292929/434343?text=No+Image';

                // Discount Logic
                const hasDiscount = discount > 0;
                const discountPercentage = hasDiscount ? Math.round(discount / (minPrice + discount) * 100) : 0;
                const originalPrice = hasDiscount ? (minPrice + discount).toFixed(2) : 0;

                // Star Logic
                const stars = '★'.repeat(star) + '☆'.repeat(5 - star);
                const starText = `${star} Star`;

                // Rating Logic
                const ratingScore = avgRating ? (Math.floor(avgRating * 10) / 10) : null;
                const ratingLabel = getRatingLabel(ratingScore);
                
                // Tags Logic
                const tagsHtml = customTags.map(tag => 
                    `<span class="${getTagClass(tag)} hotel-label-base" style="margin-top: 7px; width: fit-content; border-radius: 3px; padding: 2px 6px; font-size: 11px; line-height: 1;">${tag}</span>`
                ).join('');
                
                return `
                <div name="card" class="m-0 min-h-[357px] w-[295px] rounded-[16px] border border-[#E5E5E5] p-[9px] bg-white" style="font-family: 'Roboto', sans-serif;">
                  <a href="${detailUrl}" target="_blank" style="text-decoration: none;">
                    <div class="relative h-[176px] w-[276px] rounded-[14px]" name="header-content-image">
                      <div class="absolute z-10 flex h-full w-full flex-col justify-between px-[13px] py-[13px]">
                        <div class="flex w-full items-start justify-between">
                          ${isFeatured ? `<div class="flex w-fit items-center justify-center rounded-[20px] bg-[#F66A05] px-[7px] py-[5px] text-[12px] leading-[1] font-medium text-[#ffffff]">Best Seller</div>` : '<div></div>'}
                          ${hasDiscount ? `<div class="flex w-fit items-center justify-center rounded-[20px] bg-[#FB2D51] px-[10px] py-[5px] text-[12px] leading-[1] font-medium text-[#ffffff]">${discountPercentage}%</div>` : ''}
                        </div>
                        <div class="flex flex-row items-center gap-[5px] self-start">
                          <div class="text-[17px] text-[#FCC13B]" style="line-height:0.7;">${stars}</div>
                          <div class="text-[12px] leading-[13px] text-white">${starText}</div>
                        </div>
                      </div>
                      <div name="black-highlight" class="absolute flex h-full w-full items-end rounded-b-[14px] bg-gradient-to-t from-[rgba(0,0,0,0.83)_0%] via-[rgba(0,0,0,0)_38%] to-[rgba(0,0,0,0)_100%]"></div>
                      <img src="${imageUrl}" alt="${title}" class="h-full w-full rounded-[14px] object-cover" />
                    </div>
                    <div name="body-content" class="mx-[19px] mt-[14px]">
                      <div name="title" class="min-h-[31px] w-full">
                        <h4 class="line-clamp-2 text-[14px] leading-[17px] font-semibold text-[#333333]" style="overflow: hidden; display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 2;">${title}</h4>
                      </div>
                      <div name="description">
                        <div name="rating" class="mt-[7px] flex flex-row items-center gap-[6px]">
                          ${ratingScore !== null ? `<div name="rate"><span class="w-fit rounded-[3px] bg-[#5191FA] px-[6px] py-[2px] text-[11px] leading-[1] text-[#ffffff]">${ratingScore}</span></div>` : ''}
                          <div name="text-rate" class="text-[12px] leading-[15px] text-[#333333]">
                            <span>${ratingLabel}</span>
                          </div>
                          <div name="rate-count" class="text-[10px] leading-[12px] text-[#999999]">
                            <span>(${reviewCount})</span>
                          </div>
                        </div>
                        <div name="tags">
                          <div class="flex flex-row gap-[5px] flex-wrap">
                            ${tagsHtml}
                          </div>
                        </div>
                      </div>
                      <hr class="mt-[9.5px] mb-[7.5px] border-[#EEEEEE]" />
                      <div name="price" class="flex flex-col">
                        <div class="text-[12px] leading-[14px] text-[#999999]">
                          <span>From</span>
                        </div>
                        <div class="flex flex-row items-center justify-between">
                          <div class="flex items-center gap-[5px]">
                            <span class="text-[16px] leading-[19px] font-bold text-[#00BAA4]">€${minPrice.toFixed(2)}</span>
                            <span class="text-[13px] leading-[16px] text-[#555555]"> / night</span>
                          </div>
                          ${hasDiscount ? `<div><span name="orginal-price" class="text-[12px] leading-[14px] text-[#999999] line-through">€${originalPrice}</span></div>` : ''}
                        </div>
                      </div>
                    </div>
                  </a>
                </div>
                `;
                // --- END NEW CARD LOGIC ---
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

                    // --- START: CONTROLS FIX (Exact copy from tour) ---
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
        // --- START: TEMPLATE FIX (Exact copy from tour) ---
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
        // --- END: TEMPLATE FIX ---
    };
}