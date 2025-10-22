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
            const uniqueId = ref(`yab-hotel-carousel-vue-${Date.now()}`); // Unique ID for scoping

            const isRTL = computed(() => props.settings.direction === 'rtl');
            const headerSettings = computed(() => props.settings.header || {});
            // Card settings are now mostly derived from the new template logic
            // const cardSettings = computed(() => props.settings.card || {}); 

            const updatePaginationStyles = () => {
                if (!uniqueId.value) return;
                const paginationSettings = props.settings.pagination || {};
                const color = paginationSettings.paginationColor || 'rgba(0, 186, 164, 0.31)';
                const activeColor = paginationSettings.paginationActiveColor || '#00BAA4';

                // Scoped CSS using the unique ID
                const css = `
                    #${uniqueId.value} .swiper-pagination-bullet { background-color: ${color} !important; }
                    #${uniqueId.value} .swiper-pagination-bullet-active { background-color: ${activeColor} !important; }
                `;

                if (!styleTag.value) {
                    styleTag.value = document.createElement('style');
                    styleTag.value.id = `style-${uniqueId.value}`;
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
                const cardHeight = 357; // Hardcoded height from new design
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
            
            // --- START: NEW CARD LOGIC ---
            
            // Helper functions
            const getRatingLabel = (score) => {
                if (!score || score == 0) return 'New';
                if (score >= 4.6) return 'Excellent';
                if (score >= 4.1) return 'Very Good';
                if (score >= 3.6) return 'Good';
                if (score >= 3.0) return 'Average';
                return 'Poor';
            };
            
            const getTagClass = (tag) => {
                const tagName = tag.toLowerCase();
                const validTags = ['luxury', 'business', 'boutique', 'traditional', 'economy', 'hostel'];
                if (validTags.includes(tagName)) {
                    return `hotel-label-${tagName}`;
                }
                return 'hotel-label-default';
            };

            const escapeHTML = (str) => {
                if (!str) return '';
                return str.replace(/[&<>"']/g, function(m) {
                    return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[m];
                });
            };

            const generateHotelCardHTML = (hotel) => {
                // --- SKELETON FIX: Exact copy from Tour ---
                if (!hotel) {
                    const cardHeight = 357;
                    const imageHeight = 176;
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
                const tagsHtml = (customTags || []).map(tag => 
                    `<span class="${getTagClass(tag)} hotel-label-base" style="margin-top: 7px; width: fit-content; border-radius: 3px; padding: 2px 6px; font-size: 11px; line-height: 1; display: inline-block;">${escapeHTML(tag)}</span>`
                ).join('');
                
                return `
                <div name="card" class="yab-hotel-card" style="margin: 0; min-height: 357px; width: 295px; border-radius: 16px; border: 1px solid #E5E5E5; padding: 9px; background: #fff; box-sizing: border-box; font-family: 'Roboto', sans-serif;">
                  <a href="${escapeHTML(detailUrl)}" target="_blank" style="text-decoration: none;">
                    <div style="position: relative; height: 176px; width: 276px; border-radius: 14px;" name="header-content-image">
                      <div style="position: absolute; z-index: 10; display: flex; height: 100%; width: 100%; flex-direction: column; justify-content: space-between; padding: 13px; box-sizing: border-box;">
                        <div style="display: flex; width: 100%; align-items: flex-start; justify-content: space-between;">
                          ${isFeatured ? `<div style="display: flex; width: fit-content; align-items: center; justify-content: center; border-radius: 20px; background: #F66A05; padding: 5px 7px; font-size: 12px; line-height: 1; font-weight: 500; color: #ffffff;">Best Seller</div>` : '<div></div>'}
                          ${hasDiscount ? `<div style="display: flex; width: fit-content; align-items: center; justify-content: center; border-radius: 20px; background: #FB2D51; padding: 5px 10px; font-size: 12px; line-height: 1; font-weight: 500; color: #ffffff;">${discountPercentage}%</div>` : ''}
                        </div>
                        <div style="display: flex; flex-direction: row; align-items: center; gap: 5px; align-self: flex-start;">
                          <div style="font-size: 17px; color: #FCC13B; line-height: 0.7;">${stars}</div>
                          <div style="font-size: 12px; line-height: 13px; color: white;">${escapeHTML(starText)}</div>
                        </div>
                      </div>
                      <div name="black-highlight" style="position: absolute; display: flex; height: 100%; width: 100%; align-items: flex-end; border-radius: 0 0 14px 14px; background-image: linear-gradient(to top, rgba(0,0,0,0.83) 0%, rgba(0,0,0,0) 38%, rgba(0,0,0,0) 100%);"></div>
                      <img src="${escapeHTML(imageUrl)}" alt="${escapeHTML(title)}" style="height: 100%; width: 100%; border-radius: 14px; object-fit: cover;" />
                    </div>
                    <div name="body-content" style="margin: 14px 19px 0 19px; color: #333;">
                      <div name="title" style="min-height: 31px; width: 100%;">
                        <h4 style="font-size: 14px; line-height: 17px; font-weight: 600; color: #333333; margin: 0; overflow: hidden; display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 2;">${escapeHTML(title)}</h4>
                      </div>
                      <div name="description">
                        <div name="rating" style="margin-top: 7px; display: flex; flex-direction: row; align-items: center; gap: 6px;">
                          ${ratingScore !== null ? `<div name="rate"><span style="width: fit-content; border-radius: 3px; background: #5191FA; padding: 2px 6px; font-size: 11px; line-height: 1; color: #ffffff;">${ratingScore}</span></div>` : ''}
                          <div name="text-rate" style="font-size: 12px; line-height: 15px; color: #333333;">
                            <span>${escapeHTML(ratingLabel)}</span>
                          </div>
                          <div name="rate-count" style="font-size: 10px; line-height: 12px; color: #999999;">
                            <span>(${reviewCount})</span>
                          </div>
                        </div>
                        <div name="tags">
                          <div style="display: flex; flex-direction: row; gap: 5px; flex-wrap: wrap;">
                            ${tagsHtml}
                          </div>
                        </div>
                      </div>
                      <hr style="margin: 9.5px 0 7.5px 0; border: 0; border-top: 1px solid #EEEEEE;" />
                      <div name="price" style="display: flex; flex-direction: column;">
                        <div style="font-size: 12px; line-height: 14px; color: #999999;">
                          <span>From</span>
                        </div>
                        <div style="display: flex; flex-direction: row; align-items: center; justify-content: space-between;">
                          <div style="display: flex; align-items: center; gap: 5px;">
                            <span style="font-size: 16px; line-height: 19px; font-weight: 700; color: #00BAA4;">€${minPrice.toFixed(2)}</span>
                            <span style="font-size: 13px; line-height: 16px; color: #555555;"> / night</span>
                          </div>
                          ${hasDiscount ? `<div><span name="orginal-price" style="font-size: 12px; line-height: 14px; color: #999999; text-decoration: line-through;">€${originalPrice}</span></div>` : ''}
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
                    swiperRef.value.id = uniqueId.value; // Set the unique ID

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
                    // These selectors are relative to the component's root div
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

            return { swiperRef, containerWidth, gridHeight, isRTL, headerSettings, uniqueId }; 
        },
        // --- START: TEMPLATE FIX (Exact copy from tour, added uniqueId) ---
        template: `
            <div :id="uniqueId" :style="{ width: settings.slidesPerView > 1 ? (containerWidth + 'px') : '295px', margin: '0 auto' }" :dir="settings.direction">
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