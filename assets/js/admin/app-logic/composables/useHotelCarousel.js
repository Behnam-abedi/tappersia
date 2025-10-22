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
                    // Append to the swiperRef element itself or document head
                    if(swiperRef.value) {
                       swiperRef.value.appendChild(styleTag.value);
                    } else {
                       document.head.appendChild(styleTag.value);
                    }
                }
                 // Ensure styleTag exists before setting innerHTML
                if (styleTag.value) {
                   styleTag.value.innerHTML = css;
                }
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
                           // Ensure enough items for loop duplication logic
                           while (finalItems.length < minLoopCount) {
                               finalItems.push(...originalHotels);
                           }
                           // Trim excess if needed, though Swiper usually handles this
                           finalItems = finalItems.slice(0, Math.max(minLoopCount, hotelCount * 2)); // Ensure enough, but not excessively many
                         } else if (hotelCount === 0) {
                            finalItems = []; // Handle edge case of 0 items
                         }
                    }
                     return finalItems;
                }
                return originalHotels;
            });

            // --- START: NEW CARD LOGIC ---

            // Helper enum for Rating Labels
            const RatingLabel = {
              Excellent: 'Excellent',
              VeryGood: 'Very Good',
              Good: 'Good',
              Average: 'Average',
              Poor: 'Poor',
              New: 'New'
            };

            // Helper functions
            const getRatingLabel = (score) => {
              if (!score || score == 0) {
                return RatingLabel.New;
              }
              if (score >= 4.6) {
                return RatingLabel.Excellent;
              } else if (score >= 4.1) {
                return RatingLabel.VeryGood;
              } else if (score >= 3.6) {
                return RatingLabel.Good;
              } else if (score >= 3.0) {
                return RatingLabel.Average;
              } else {
                return RatingLabel.Poor;
              }
            };

            const getTagClass = (tag) => {
                if (!tag) return 'hotel-label-default';
                const tagName = tag.toLowerCase();
                const tagMap = {
                    luxury: 'hotel-label-luxury',
                    business: 'hotel-label-business',
                    boutique: 'hotel-label-boutique',
                    traditional: 'hotel-label-traditional',
                    economy: 'hotel-label-economy',
                    hostel: 'hotel-label-hostel'
                };
                return tagMap[tagName] || 'hotel-label-default';
            };

            const escapeHTML = (str) => {
                if (!str) return '';
                // Basic escaping for preventing XSS in simple text insertion
                return String(str).replace(/[&<>"']/g, function(m) {
                    return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[m];
                });
            };

            const generateHotelCardHTML = (hotel) => {
                 // --- SKELETON LOADER ---
                if (!hotel) {
                    const cardHeight = 357;
                    const imageHeight = 176;
                    // Using Tailwind classes for skeleton
                    return `
                    <div name="card-skeleton" class="m-3 min-h-[${cardHeight}px] w-[295px] rounded-[16px] border border-[#E5E5E5] p-[9px] bg-white animate-pulse overflow-hidden">
                      <div class="h-[${imageHeight}px] w-[276px] rounded-[14px] bg-gray-300"></div>
                      <div class="mx-[19px] mt-[14px]">
                        <div class="min-h-[31px] w-full mb-[7px]">
                          <div class="h-4 bg-gray-300 rounded w-3/4 mb-2"></div>
                          <div class="h-4 bg-gray-300 rounded w-1/2"></div>
                        </div>
                        <div name="description-skeleton">
                          <div name="rating-skeleton" class="flex flex-row items-center gap-[6px]">
                            <div class="h-5 w-8 rounded-[3px] bg-gray-300"></div>
                            <div class="h-4 bg-gray-300 rounded w-16"></div>
                            <div class="h-3 bg-gray-300 rounded w-8"></div>
                          </div>
                          <div name="tags-skeleton" class="mt-[7px] flex flex-row gap-[5px]">
                            <div class="h-5 w-12 rounded-[3px] bg-gray-300"></div>
                            <div class="h-5 w-12 rounded-[3px] bg-gray-300"></div>
                          </div>
                        </div>
                        <hr class="mt-[9.5px] mb-[7.5px] border-[#EEEEEE]" />
                        <div name="price-skeleton" class="flex flex-col">
                          <div class="h-3 bg-gray-300 rounded w-8 mb-1"></div>
                          <div class="flex flex-row items-center justify-between">
                            <div class="flex items-center gap-[5px]">
                              <div class="h-5 bg-gray-300 rounded w-16"></div>
                              <div class="h-4 bg-gray-300 rounded w-10"></div>
                            </div>
                            <div class="h-3 bg-gray-300 rounded w-12"></div>
                          </div>
                        </div>
                      </div>
                    </div>`;
                }
                // --- END SKELETON LOADER ---


                // --- CARD LOGIC ---
                const {
                    coverImage, isFeatured = false, discount = 0, minPrice = 0,
                    star = 0, title = 'N/A', avgRating, reviewCount = 0,
                    customTags = [], detailUrl = '#'
                } = hotel;

                const imageUrl = coverImage?.url || 'https://placehold.co/276x176/e0e0e0/cccccc?text=No+Image';

                // Discount Logic
                const hasDiscount = discount > 0 && minPrice > 0; // Ensure minPrice > 0 to avoid division by zero
                 // Correct calculation and rounding: calculate percentage first, then round.
                const discountPercentage = hasDiscount ? Math.round((discount / (minPrice + discount)) * 100) : 0;
                const originalPrice = hasDiscount ? (minPrice + discount).toFixed(2) : '0.00';


                // Star Logic
                const stars = '★'.repeat(star) + '☆'.repeat(5 - star);
                const starText = `${star} Star`;

                // Rating Logic - Use Math.floor(avgRating * 10) / 10 for one decimal place or handle null/undefined
                 const ratingScore = avgRating != null ? (Math.round(avgRating * 10) / 10).toFixed(1) : null;
                const ratingLabel = getRatingLabel(avgRating); // Pass the original avgRating

                // Tags Logic
                const tagsHtml = (customTags || []).map(tag =>
                    `<span class="${getTagClass(tag)} hotel-label-base">${escapeHTML(tag)}</span>`
                ).join('');

                // --- TEMPLATE ---
                 // Note: Using inline styles derived from Tailwind for preview accuracy as direct Tailwind might not apply in JS string
                 return `
                <div name="card" class="yab-hotel-card m-0 min-h-[357px] w-[295px] rounded-[16px] border border-[#E5E5E5] p-[9px] bg-white box-border font-['Roboto',_sans-serif]">
                  <a href="${escapeHTML(detailUrl)}" target="_blank" style="text-decoration: none; color: inherit; display: block;">
                    <div class="relative h-[176px] w-[276px] rounded-[14px]" name="header-content-image">
                      <div class="absolute z-10 flex h-full w-full flex-col justify-between px-[13px] py-[13px] box-border">
                        <div class="flex w-full items-start justify-between">
                          ${isFeatured ? `<div class="flex w-fit items-center justify-center rounded-[20px] bg-[#F66A05] px-[7px] py-[5px] text-[12px] leading-[1] font-medium text-[#ffffff]">Best Seller</div>` : '<div></div>' /* Placeholder */}
                          ${hasDiscount && discountPercentage > 0 ? `<div class="flex w-fit items-center justify-center rounded-[20px] bg-[#FB2D51] px-[10px] py-[5px] text-[12px] leading-[1] font-medium text-[#ffffff]">${discountPercentage}%</div>` : ''}
                        </div>
                        <div class="flex flex-row items-center gap-[5px] self-start">
                          <div class="text-[17px] text-[#FCC13B]" style="line-height:0.7;">${stars}</div>
                          <div class="text-[12px] leading-[13px] text-white">${escapeHTML(starText)}</div>
                        </div>
                      </div>
                      <div name="black-highlight" class="absolute flex h-full w-full items-end rounded-b-[14px]" style="background-image: linear-gradient(to top, rgba(0,0,0,0.83) 0%, rgba(0,0,0,0) 38%, rgba(0,0,0,0) 100%);"></div>
                      <img src="${escapeHTML(imageUrl)}" alt="${escapeHTML(title)}" class="h-full w-full rounded-[14px] object-cover" />
                    </div>
                    <div name="body-content" class="mx-[19px] mt-[14px] text-[#333]">
                      <div name="title" class="min-h-[34px] w-full"> 
                        <h4 class="line-clamp-2 text-[14px] leading-[17px] font-semibold text-[#333333] m-0">${escapeHTML(title)}</h4>
                      </div>
                      <div name="description">
                        <div name="rating" class="mt-[7px] flex flex-row items-center gap-[6px]">
                          ${ratingScore !== null ? `<div name="rate"><span class="w-fit rounded-[3px] bg-[#5191FA] px-[6px] py-[2px] text-[11px] leading-[1] text-[#ffffff]">${ratingScore}</span></div>` : ''}
                          <div name="text-rate" class="text-[12px] leading-[15px] text-[#333333] ">
                            <span>${escapeHTML(ratingLabel)}</span>
                          </div>
                          <div name="rate-count" class="text-[10px] leading-[12px] text-[#999999]">
                            <span>(${reviewCount})</span>
                          </div>
                        </div>
                        <div name="tags">
                          <div class="flex flex-row flex-wrap gap-[5px]">
                            ${tagsHtml}
                          </div>
                        </div>
                      </div>
                      <hr class="mt-[9.5px] mb-[7.5px] border-0 border-t border-[#EEEEEE]" />
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
                // --- END TEMPLATE ---
            };
            // --- END NEW CARD LOGIC ---

            const fetchAndRenderHotels = async (idsToFetch) => {
                const uniqueIds = [...new Set(idsToFetch)].filter(id => !fetchedIds.has(id));
                if (uniqueIds.length === 0) return;
                uniqueIds.forEach(id => fetchedIds.add(id));
                try {
                     // Fetch ALL necessary fields for the new card
                     const data = await props.ajax.post('yab_fetch_hotel_details_by_ids', { hotel_ids: uniqueIds });
                     if (data && Array.isArray(data)) {
                        data.forEach(hotelData => {
                            if (!swiperRef.value) return;
                            const slidesToUpdate = swiperRef.value.querySelectorAll(`.swiper-slide[data-hotel-id="${hotelData.id}"]`);
                            if (slidesToUpdate.length > 0) {
                                slidesToUpdate.forEach(slide => {
                                    // Ensure all fields are passed correctly
                                    const fullHotelData = {
                                        id: hotelData.id,
                                        coverImage: hotelData.coverImage,
                                        isFeatured: hotelData.isFeatured, // Make sure this is returned
                                        discount: hotelData.discount,     // Make sure this is returned
                                        minPrice: hotelData.minPrice,
                                        star: hotelData.star,
                                        title: hotelData.title,
                                        avgRating: hotelData.avgRating,
                                        reviewCount: hotelData.reviewCount,
                                        customTags: hotelData.customTags, // Make sure this is returned
                                        detailUrl: hotelData.detailUrl
                                    };
                                    slide.innerHTML = generateHotelCardHTML(fullHotelData);
                                    slide.classList.add('is-loaded'); // Add class to trigger potential CSS animation
                                });
                            }
                        });
                     }
                } catch (error) { console.error('Hotel AJAX call failed!', error); }
            };

            const checkAndLoadVisibleSlides = (swiper) => {
                 if (!swiper || !swiper.slides || swiper.slides.length === 0) return;
                const idsToFetch = new Set();
                const slides = Array.from(swiper.slides);
                const isGrid = swiper.params.grid && swiper.params.grid.rows > 1;
                const rows = isGrid ? swiper.params.grid.rows : 1;
                // Ensure slidesPerView is treated as a number
                 let slidesPerView = 1;
                 if (swiper.params.slidesPerView && swiper.params.slidesPerView !== 'auto') {
                     slidesPerView = parseInt(swiper.params.slidesPerView, 10);
                     if (isNaN(slidesPerView)) slidesPerView = 1; // Fallback if parsing fails
                 } else if (swiper.params.slidesPerView === 'auto') {
                     // Estimate based on visible slides if 'auto'
                     slidesPerView = swiper.visibleSlides ? swiper.visibleSlides.length : 1;
                 }


                 // Use Math.max to prevent negative slice indexes
                 const startIndex = Math.max(0, swiper.activeIndex);
                 const slidesToLoadCount = Math.max(1, (slidesPerView * rows) * 2); // Ensure at least 1 slide is checked

                const slidesToCheck = slides.slice(startIndex, startIndex + slidesToLoadCount);


                if (slidesToCheck.length > 0) {
                    slidesToCheck.forEach(slide => {
                        const hotelId = parseInt(slide.dataset.hotelId, 10);
                        if (hotelId && !isNaN(hotelId)) idsToFetch.add(hotelId); // Check isNaN
                    });
                }
                 if (idsToFetch.size > 0) {
                     // Ensure idsToFetch contains only numbers before fetching
                     const validIds = Array.from(idsToFetch).filter(id => !isNaN(id));
                     if (validIds.length > 0) {
                         fetchAndRenderHotels(validIds);
                     }
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
                        slideEl.innerHTML = generateHotelCardHTML(null); // Render skeleton first
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
                        },
                         // Ensure Swiper calculates slides correctly, especially with loop/grid
                        observer: true,
                        observeParents: true,
                     };


                    if (props.settings.autoplay && props.settings.autoplay.enabled) {
                        swiperOptions.autoplay = {
                            delay: props.settings.autoplay.delay || 3000,
                            disableOnInteraction: false,
                        };
                    }

                    if (props.settings.navigation && props.settings.navigation.enabled) {
                        swiperOptions.navigation = {
                            nextEl: `#${uniqueId.value} .tappersia-carusel-next`, // Scope selectors
                            prevEl: `#${uniqueId.value} .tappersia-carusel-perv`,
                        };
                    }
                    if (props.settings.pagination && props.settings.pagination.enabled) {
                        swiperOptions.pagination = {
                            el: `#${uniqueId.value} .swiper-pagination`, // Scope selectors
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

                     try {
                       swiperInstance.value = new Swiper(swiperRef.value, swiperOptions);
                     } catch (error) {
                       console.error("Swiper initialization failed:", error);
                     }
                }
            };

            // Watch for changes in hotelIds or settings to re-init Swiper
             watch(() => [props.hotelIds, props.settings], () => {
                 // Reset fetched IDs when hotel list changes significantly
                 fetchedIds.clear();
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

            // Return values needed by the template
            return { swiperRef, containerWidth, gridHeight, isRTL, headerSettings, uniqueId };
        },
        // --- TEMPLATE (Using Tailwind classes directly) ---
        template: `
            <div :id="uniqueId" :style="{ width: settings.slidesPerView > 1 ? (containerWidth + 'px') : '295px', margin: '0 auto' }" :dir="settings.direction" class="yab-hotel-carousel-preview">
                <!-- Header -->
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
                                <div class="w-[10px] h-[10px] border-t-2 border-r-2 border-black rounded-[2px]" :style="{ transform: isRTL ? 'rotate(45deg)' : 'rotate(-135deg)' }"></div>
                            </div>
                            <div class="tappersia-carusel-next flex h-[36px] w-[36px] items-center justify-center rounded-[8px] bg-white shadow-[inset_0_0_0_2px_#E5E5E5] cursor-pointer" :class="isRTL ? 'pl-[3px]' : 'pr-[3px]'">
                                <div class="w-[10px] h-[10px] border-t-2 border-r-2 border-black rounded-[2px]" :style="{ transform: isRTL ? 'rotate(-135deg)' : 'rotate(45deg)' }"></div>
                            </div>
                        </div>
                    </div>
                    <div :style="{ textAlign: isRTL ? 'right' : 'left' }" class="relative">
                        <div class="w-full h-[1px] rounded-[2px] bg-[#E2E2E2]"></div>
                        <div class="absolute mt-[-2px] w-[15px] h-[2px] rounded-[2px]"
                             :style="{ backgroundColor: headerSettings.lineColor || '#00BAA4', ...(isRTL ? { right: 0 } : { left: 0 }) }"></div>
                    </div>
                </div>

                <!-- Swiper -->
                <div class="swiper"
                     :ref="el => swiperRef = el"
                     :style="settings.isDoubled ? { height: gridHeight + 'px', 'padding-bottom': '10px', overflow: 'hidden' } : { overflow: 'hidden', 'padding-bottom': '10px' }">
                    <div class="swiper-wrapper">
                        <!-- Slides are dynamically generated in initSwiper -->
                    </div>
                </div>
                <div v-if="settings.pagination && settings.pagination.enabled" class="swiper-pagination !relative mt-[10px]"></div>
            </div>
        `
    };
}
