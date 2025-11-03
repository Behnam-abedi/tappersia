// tappersia/assets/js/admin/app-logic/composables/useHotelCarousel.js
const { ref, reactive, onMounted, onUnmounted, nextTick, computed, watch } = Vue;

// به جای export function، یک آبجکت تعریف کامپوننت export کنید
export const HotelCarouselLogic = {
    props: {
        hotelIds: { type: Array, required: true },
        ajax: { type: Object, required: true },
        settings: { type: Object, required: true }
    },
    setup(props) {
         // ... تمام منطق setup فعلی شما اینجا قرار می‌گیرد ...
         // فقط مطمئن شوید که مقادیر مورد نیاز template را return می‌کنید
         const swiperRef = ref(null);
         const swiperInstance = ref(null);
         const fetchedIds = reactive(new Set());
         const styleTag = ref(null);
         const uniqueId = ref(`yab-hotel-carousel-vue-${Date.now()}-${Math.random().toString(36).substring(7)}`); // Added unique ID

        const isRTL = computed(() => props.settings.direction === 'rtl');
        const headerSettings = computed(() => props.settings.header || {});
        const cardSettings = computed(() => props.settings.card || {});

        const updatePaginationStyles = () => {
             // ... (keep existing logic but use uniqueId.value for scoping) ...
            if (!uniqueId.value) return; // Use uniqueId.value
            const paginationSettings = props.settings.pagination || {};
            const color = paginationSettings.paginationColor || '#00BAA44F';
            const activeColor = paginationSettings.paginationActiveColor || '#00BAA4';

            // Scoped CSS
            const css = `
                #${uniqueId.value} .swiper-pagination-bullet { background-color: ${color} !important; }
                #${uniqueId.value} .swiper-pagination-bullet-active { background-color: ${activeColor} !important; }
                #${uniqueId.value} .swiper-button-disabled { opacity: 0.5 !important; cursor: not-allowed !important; pointer-events: none !important; }
            `;

            if (!styleTag.value) {
                styleTag.value = document.createElement('style');
                styleTag.value.id = `style-${uniqueId.value}`; // Use uniqueId
                 if (swiperRef.value) {
                     swiperRef.value.appendChild(styleTag.value);
                 } else {
                    // Fallback: Append to head if swiperRef not ready
                    document.head.appendChild(styleTag.value);
                 }
            }
             if (styleTag.value) { // Ensure exists before setting
                 styleTag.value.innerHTML = css;
             }
         };

         const containerWidth = computed(() => {
            const cardWidth = 295;
            const spaceBetween = props.settings.spaceBetween || 18;
            const slidesPerView = props.settings.slidesPerView || 3;
             if (slidesPerView === 1) return cardWidth; // Handle mobile view width
            return (cardWidth * slidesPerView) + (spaceBetween * (slidesPerView - 1));
        });

        const gridHeight = computed(() => {
            const cardHeight = cardSettings.value.height || 357; // Use height from settings
            const verticalSpace = 20; // Default grid gap
            return (cardHeight * 2) + verticalSpace;
        });

        const slidesToRender = computed(() => {
             // ... (keep existing logic) ...
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
                       // Trim excess if needed
                       finalItems = finalItems.slice(0, Math.max(minLoopCount, hotelCount * 2));
                     } else if (hotelCount === 0) {
                        finalItems = []; // Handle edge case of 0 items
                     }
                }
                 return finalItems;
            }
            return originalHotels;
        });

         // Helper enum for Rating Labels
         const RatingLabel = { Excellent: 'Excellent', VeryGood: 'Very Good', Good: 'Good', Average: 'Average', Poor: 'Poor', New: 'New' };

         // Helper functions
         const getRatingLabel = (score) => {
             if (!score || score == 0) return RatingLabel.New;
             if (score >= 4.6) return RatingLabel.Excellent;
             if (score >= 4.1) return RatingLabel.VeryGood;
             if (score >= 3.6) return RatingLabel.Good;
             if (score >= 3.0) return RatingLabel.Average;
             return RatingLabel.Poor;
         };

         const getTagClass = (tag) => {
             if (!tag) return 'hotel-label-default';
             const tagName = tag.toLowerCase();
             const tagMap = { luxury: 'hotel-label-luxury', business: 'hotel-label-business', boutique: 'hotel-label-boutique', traditional: 'hotel-label-traditional', economy: 'hotel-label-economy', hostel: 'hotel-label-hostel' };
             return tagMap[tagName] || 'hotel-label-default';
         };

         const escapeHTML = (str) => {
             if (!str) return '';
             return String(str).replace(/[&<>"']/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[m]));
         };

        const generateHotelCardHTML = (hotel) => {
             const card = cardSettings.value;
            // --- SKELETON ---
            if (!hotel) {
                return `
                <div name="card-skeleton" class="yab-hotel-card-skeleton yab-skeleton-loader" style="margin: 0; height:auto;min-height:${card.height || 357}px; width: 295px; border-radius: 16px; border: 1px solid #f5f5f5ff; padding: 9px; background-color: #ffffff; box-sizing: border-box; overflow: hidden;">
                    <div style="height: ${card.image?.height || 176}px; width: 100%; border-radius: 14px; background-color: #f0f0f0;"></div>
                    <div style="margin: 14px 19px 0 19px;">
                        <div style="min-height: ${card.title?.minHeight || 34}px; width: 100%; margin-bottom: 7px;">
                            <div style="height: 16px; background-color: #f0f0f0; border-radius: 4px; width: 75%; margin-bottom: 8px;"></div>
                            <div style="height: 16px; background-color: #f0f0f0; border-radius: 4px; width: 50%;"></div>
                        </div>
                        <div name="description-skeleton">
                            <div name="rating-skeleton" style="display: flex; flex-direction: row; align-items: center; gap: 6px; margin-top: 7px;">
                                <div style="height: 20px; width: 60%; border-radius: 3px; background-color: #f0f0f0;"></div> </div>
                            <div name="tags-skeleton" style="margin-top: 7px; height: 15px; width: 40%; background-color: #f0f0f0; border-radius: 3px;"></div>
                        </div>
                        <hr style="margin: 9.5px 0 7.5px 0; border: 0; border-top: 1px solid #EEEEEE;" />
                        <div name="price-skeleton" style="display: flex; flex-direction: column; gap: 5px;">
                           <div style="height: 14px; background-color: #f0f0f0; border-radius: 4px; width: 30%;"></div>
                           <div style="display: flex; justify-content: space-between;">
                               <div style="height: 19px; background-color: #f0f0f0; border-radius: 4px; width: 45%;"></div>
                               <div style="height: 14px; background-color: #f0f0f0; border-radius: 4px; width: 35%;"></div>
                           </div>
                        </div>
                    </div>
                </div>`;
            }
            // --- ACTUAL CARD ---
            const { coverImage, isFeatured = false, discount = 0, minPrice = 0, star = 0, title = 'N/A', avgRating, reviewCount = 0, customTags = [], detailUrl = '#' } = hotel;
            const imageUrl = coverImage?.url || '[https://placehold.co/276x176/e0e0e0/cccccc?text=No+Image](https://placehold.co/276x176/e0e0e0/cccccc?text=No+Image)';
            const hasDiscount = discount > 0 && minPrice > 0;
            const discountPercentage = hasDiscount ? Math.round((discount / (minPrice + discount)) * 100) : 0;
            const originalPrice = hasDiscount ? (minPrice + discount).toFixed(2) : '0.00';
            const stars = '★'.repeat(star) + '☆'.repeat(5 - star);
            const starText = `${star} Star`;
            const ratingScore = avgRating != null ? (Math.round(avgRating * 10) / 10).toFixed(1) : null;
            const ratingLabel = getRatingLabel(avgRating);
            const tagsHtml = (customTags || []).map(tag =>
                `<span class="${getTagClass(tag)} hotel-label-base" style="font-size: ${card.tags?.fontSize || 11}px; padding: ${card.tags?.paddingY || 2}px ${card.tags?.paddingX || 6}px; border-radius: ${card.tags?.radius || 3}px; margin-top: ${card.tags?.marginTop || 7}px;">${escapeHTML(tag)}</span>`
            ).join('');
            const overlayGradient = `linear-gradient(to top, ${card.imageOverlay?.gradientEndColor || 'rgba(0,0,0,0.83)'} ${card.imageOverlay?.gradientEndPercent || 0}%, ${card.imageOverlay?.gradientStartColor || 'rgba(0,0,0,0)'} ${card.imageOverlay?.gradientStartPercent || 38}%, ${card.imageOverlay?.gradientStartColor || 'rgba(0,0,0,0)'} 100%)`;
            const imageWidth = 295 - (card.padding * 2);

            return `
            <div name="card" class="yab-hotel-card" style="margin: 0; height:auto;min-height: ${card.height}px; width: 295px; border-radius: ${card.borderRadius}px; border: ${card.borderWidth}px solid ${card.borderColor}; padding: ${card.padding}px; background-color: ${card.bgColor}; box-sizing: border-box; font-family: 'Roboto', sans-serif; display: flex; flex-direction: column;">
              <a href="${escapeHTML(detailUrl)}" target="_blank" style="text-decoration: none; color: inherit; display: flex; flex-direction: column; height: 100%; outline: none; -webkit-tap-highlight-color: transparent;flex:1">
                <div style="position: relative; height: ${card.image?.height || 176}px; width:100%; border-radius: ${card.image?.radius || 14}px; flex-shrink: 0;" name="header-content-image">
                  <div style="position: absolute; z-index: 10; display: flex; height: 100%; width: 100%; flex-direction: column; justify-content: space-between; padding: ${card.imageContainer?.paddingY || 13}px ${card.imageContainer?.paddingX || 13}px; box-sizing: border-box;">
                    <div style="display: flex; width: 100%; align-items: flex-start; justify-content: space-between;">
                       ${isFeatured ? `<div style="display: flex; width: fit-content; align-items: center; justify-content: center; border-radius: ${card.badges?.bestSeller?.radius || 20}px; background: ${card.badges?.bestSeller?.bgColor || '#F66A05'}; padding: ${card.badges?.bestSeller?.paddingY || 5}px ${card.badges?.bestSeller?.paddingX || 7}px; font-size: ${card.badges?.bestSeller?.fontSize || 12}px; line-height: 1; font-weight: 500; color: ${card.badges?.bestSeller?.textColor || '#ffffff'};">Best Seller</div>` : '<div></div>'}
                       ${hasDiscount && discountPercentage > 0 ? `<div style="display: flex; width: fit-content; align-items: center; justify-content: center; border-radius: ${card.badges?.discount?.radius || 20}px; background: ${card.badges?.discount?.bgColor || '#FB2D51'}; padding: ${card.badges?.discount?.paddingY || 5}px ${card.badges?.discount?.paddingX || 10}px; font-size: ${card.badges?.discount?.fontSize || 12}px; line-height: 1; font-weight: 500; color: ${card.badges?.discount?.textColor || '#ffffff'};">${discountPercentage}%</div>` : ''}
                    </div>
                    <div style="display: flex; flex-direction: row; align-items: center; gap: 5px; align-self: flex-start;">
                      <div style="font-size: ${card.stars?.shapeSize || 17}px; color: ${card.stars?.shapeColor || '#FCC13B'}; line-height: 0.7;">${stars}</div>
                      <div style="font-size: ${card.stars?.textSize || 12}px; line-height: 13px; color: ${card.stars?.textColor || '#ffffff'};">${escapeHTML(starText)}</div>
                    </div>
                  </div>
                  <div name="black-highlight" style="position: absolute; display: flex; height: 100%; width: 100%; align-items: flex-end; border-bottom-left-radius: ${card.image?.radius || 14}px; border-bottom-right-radius: ${card.image?.radius || 14}px; background-image: ${overlayGradient};"></div>
                  <img src="${escapeHTML(imageUrl)}" alt="${escapeHTML(title)}" style="height: 100%; width: 100%; border-radius: ${card.image?.radius || 14}px; object-fit: cover;" />
                </div>
                <div name="body-content" style="margin: ${card.bodyContent?.marginTop || 14}px ${card.bodyContent?.marginX || 19}px 0 ${card.bodyContent?.marginX || 19}px; color: ${card.bodyContent?.textColor || '#333'}; flex-grow: 1; display: flex; flex-direction: column; min-height: 0;">
                  <div name="title" style="min-height: ${card.title?.minHeight || 34}px; width: 100%;"> <h4 style="font-size: ${card.title?.fontSize || 14}px; line-height: ${card.title?.lineHeight || 1.2}; font-weight: ${card.title?.fontWeight || 600}; color: ${card.title?.color || '#333333'}; margin: 0; overflow: hidden; display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 2;">${escapeHTML(title)}</h4> </div>
                  <div name="description" style="margin-top:auto">
                    <div name="rating" style="margin-top: ${card.rating?.marginTop || 7}px; display: flex; flex-direction: row; align-items: center; gap: ${card.rating?.gap || 6}px;">
                      ${ratingScore !== null ? `<div name="rate"><span style="width: fit-content; border-radius: ${card.rating?.boxRadius || 3}px; background: ${card.rating?.boxBgColor || '#5191FA'}; padding: ${card.rating?.boxPaddingY || 2}px ${card.rating?.boxPaddingX || 6}px; font-size: ${card.rating?.boxFontSize || 11}px; line-height: 1; color: ${card.rating?.boxColor || '#ffffff'};">${ratingScore}</span></div>` : ''}
                      <div name="text-rate" style="font-size: ${card.rating?.labelFontSize || 12}px; line-height: 15px; color: ${card.rating?.labelColor || '#333333'}; padding-top: 1px;"> <span>${escapeHTML(ratingLabel)}</span> </div>
                      <div name="rate-count" style="font-size: ${card.rating?.countFontSize || 10}px; line-height: 12px; color: ${card.rating?.countColor || '#999999'};"> <span>(${reviewCount})</span> </div>
                    </div>
                    <div name="tags"> <div style="display: flex; flex-direction: row; flex-wrap: wrap; gap: ${card.tags?.gap || 5}px;"> ${tagsHtml} </div> </div>
                  </div>
                  <hr style="margin: ${card.divider?.marginTop || 9.5}px 0 ${card.divider?.marginBottom || 7.5}px 0; border: 0; border-top: 1px solid ${card.divider?.color || '#EEEEEE'};" />
                  <div name="price" style="display: flex; flex-direction: column; margin-top: 10px;">
                    <div style="font-size: ${card.price?.fromSize || 12}px; line-height: 14px; color: ${card.price?.fromColor || '#999999'};"> <span>From</span> </div>
                    <div style="display: flex; flex-direction: row; align-items: center; justify-content: space-between;">
                      <div style="display: flex; align-items: center; gap: 5px;">
                        <span style="font-size: ${card.price?.amountSize || 16}px; line-height: 19px; font-weight: ${card.price?.amountWeight || 700}; color: ${card.price?.amountColor || '#00BAA4'};">€${minPrice.toFixed(2)}</span>
                        <span style="font-size: ${card.price?.nightSize || 13}px; line-height: 16px; color: ${card.price?.nightColor || '#555555'};"> / night</span>
                      </div>
                      ${hasDiscount ? `<div><span name="orginal-price" style="font-size: ${card.price?.originalSize || 12}px; line-height: 14px; color: ${card.price?.originalColor || '#999999'}; text-decoration: line-through;">€${originalPrice}</span></div>` : ''}
                    </div>
                  </div>
                </div>
              </a>
            </div>
            `;
         };

         const fetchAndRenderHotels = async (idsToFetch, retries = 2) => {
             // ... (keep existing logic) ...
            const uniqueIds = [...new Set(idsToFetch)].filter(id => !fetchedIds.has(id));
            if (uniqueIds.length === 0) return;
            uniqueIds.forEach(id => fetchedIds.add(id)); // Mark as attempting to fetch

            try {
                const data = await props.ajax.post('yab_fetch_hotel_details_by_ids', { hotel_ids: uniqueIds });
                 if (data && Array.isArray(data)) {
                    data.forEach(hotelData => {
                        if (!swiperRef.value) return;
                        const slidesToUpdate = swiperRef.value.querySelectorAll(`.swiper-slide[data-hotel-id="${hotelData.id}"]`);
                        if (slidesToUpdate.length > 0) {
                            slidesToUpdate.forEach(slide => {
                                if (slide.querySelector('[name="card-skeleton"]')) { // Check if skeleton exists
                                    // Image preloading
                                    const img = new Image();
                                    const imageUrl = hotelData.coverImage?.url || '[https://placehold.co/276x176/e0e0e0/cccccc?text=No+Image](https://placehold.co/276x176/e0e0e0/cccccc?text=No+Image)';
                                    img.onload = () => {
                                        slide.innerHTML = generateHotelCardHTML(hotelData);
                                        slide.classList.add('is-loaded'); // Add class for potential animation
                                    };
                                    img.onerror = () => { // Still render card if image fails
                                        console.error(`Failed to load image for hotel ID ${hotelData.id}: ${imageUrl}`);
                                        slide.innerHTML = generateHotelCardHTML(hotelData);
                                        slide.classList.add('is-loaded');
                                    };
                                    img.src = imageUrl;
                                }
                            });
                        }
                    });
                 } else {
                    console.error('Hotel AJAX Error: Invalid data received.', data);
                    // Optionally mark these IDs as failed if needed
                 }
            } catch (error) {
                 console.error('Hotel AJAX call failed!', error);
                // Remove failed IDs from fetchedIds so retry can happen
                uniqueIds.forEach(id => fetchedIds.delete(id));
                 if (retries > 0) {
                     console.warn(`Retrying fetch for hotel IDs: ${uniqueIds.join(', ')}. Retries left: ${retries - 1}`);
                     setTimeout(() => {
                        fetchAndRenderHotels(uniqueIds, retries - 1);
                     }, 2000); // Wait 2 seconds before retrying
                 } else {
                     console.error(`Failed to fetch hotel IDs after multiple retries: ${uniqueIds.join(', ')}`);
                     // Optionally update UI for failed cards
                     uniqueIds.forEach(id => {
                        if (!swiperRef.value) return;
                        const slidesToUpdate = swiperRef.value.querySelectorAll(`.swiper-slide[data-hotel-id="${id}"]`);
                        slidesToUpdate.forEach(slide => {
                            if (slide.querySelector('[name="card-skeleton"]')) {
                                slide.innerHTML = '<div style="color: red; text-align: center; padding: 20px;">Load failed</div>';
                            }
                        });
                     });
                 }
            }
        };

         const checkAndLoadVisibleSlides = (swiper) => {
             // ... (keep existing logic) ...
            if (!swiper || !swiper.slides || swiper.slides.length === 0 || !swiper.params) return;
            const idsToFetch = new Set();
            const slides = Array.from(swiper.slides);
            const isGrid = swiper.params.grid && swiper.params.grid.rows > 1;
            const rows = isGrid ? swiper.params.grid.rows : 1;
             let slidesPerView = 1;
             if (swiper.params.slidesPerView && swiper.params.slidesPerView !== 'auto') {
                 slidesPerView = parseInt(swiper.params.slidesPerView, 10);
                 if (isNaN(slidesPerView)) slidesPerView = 1;
             } else if (swiper.params.slidesPerView === 'auto') {
                 slidesPerView = swiper.visibleSlides ? swiper.visibleSlides.length : 1;
             }
            const startIndex = Math.max(0, swiper.activeIndex || 0);
            const slidesToLoadCount = Math.max(1, (slidesPerView * rows) * 2);
            const slidesToCheck = slides.slice(startIndex, startIndex + slidesToLoadCount);

            if (slidesToCheck.length > 0) {
                slidesToCheck.forEach(slide => {
                    const hotelId = parseInt(slide.dataset.hotelId, 10);
                    if (!isNaN(hotelId)) idsToFetch.add(hotelId);
                });
            }
            if (idsToFetch.size > 0) {
                fetchAndRenderHotels(Array.from(idsToFetch));
            }
         };

         const initSwiper = () => {
             // ... (keep existing logic) ...
            if (swiperInstance.value) {
                swiperInstance.value.destroy(true, true);
                swiperInstance.value = null; // Ensure old instance is nullified
            }
            if (swiperRef.value) {
                 swiperRef.value.id = uniqueId.value; // Assign unique ID

                const wrapper = swiperRef.value.querySelector('.swiper-wrapper');
                 if (!wrapper) return; // Exit if wrapper not found
                wrapper.innerHTML = ''; // Clear existing slides

                slidesToRender.value.forEach(id => {
                    const slideEl = document.createElement('div');
                    slideEl.className = 'swiper-slide';
                    slideEl.setAttribute('data-hotel-id', id);
                    // slideEl.style.width = '295px'; // Let Swiper handle width
                    slideEl.innerHTML = generateHotelCardHTML(null); // Render skeleton
                    wrapper.appendChild(slideEl);
                });

                updatePaginationStyles(); // Apply dynamic styles

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
                     observer: true, // Re-init on DOM changes
                     observeParents: true, // Re-init if parent DOM changes
                };

                if (props.settings.autoplay && props.settings.autoplay.enabled) {
                    swiperOptions.autoplay = {
                        delay: props.settings.autoplay.delay || 3000,
                        disableOnInteraction: false,
                    };
                }

                if (props.settings.navigation && props.settings.navigation.enabled) {
                    swiperOptions.navigation = {
                        nextEl: `#${uniqueId.value} .tappersia-carusel-next`, // Use scoped selector
                        prevEl: `#${uniqueId.value} .tappersia-carusel-perv`,
                    };
                }

                if (props.settings.pagination && props.settings.pagination.enabled) {
                    swiperOptions.pagination = {
                        el: `#${uniqueId.value} .swiper-pagination`, // Use scoped selector
                        clickable: true,
                    };
                }

                if (props.settings.isDoubled) {
                    swiperOptions.grid = {
                        rows: 2,
                        fill: props.settings.loop ? 'column' : props.settings.gridFill,
                    };
                    swiperOptions.slidesPerGroup = 1;
                    swiperOptions.spaceBetween = 20; // Ensure grid gap is set
                }

                // Use try-catch for robustness
                 try {
                   swiperInstance.value = new Swiper(swiperRef.value, swiperOptions);
                 } catch (error) {
                   console.error("Swiper initialization failed:", error);
                 }
            }
        };

         watch(() => [props.hotelIds, props.settings], () => {
             // Reset fetched IDs when hotel list changes significantly OR settings change
             fetchedIds.clear();
             nextTick(() => {
                 initSwiper();
             });
         }, { deep: true, immediate: true });

         onUnmounted(() => {
             // ... (keep existing logic) ...
            if (swiperInstance.value) {
                swiperInstance.value.destroy(true, true);
                swiperInstance.value = null;
            }
             if (styleTag.value && styleTag.value.parentNode) {
                 styleTag.value.parentNode.removeChild(styleTag.value);
                 styleTag.value = null;
             }
         });


        return { swiperRef, containerWidth, gridHeight, isRTL, headerSettings, uniqueId }; // Return uniqueId
    },
    // template: `...` // Template شما بدون تغییر باقی می‌ماند
    template: `
    <div :id="uniqueId" :style="{ maxWidth: containerWidth + 'px', width: '100%', margin: '0 auto' }" :dir="settings.direction" class="yab-hotel-carousel-preview"> <div :style="{ marginBottom: (headerSettings.marginTop || 28) + 'px' }" class="flex flex-col">
            <div class="mb-[13px] flex w-full flex-row justify-between items-center" >
                <div>
                    <span :style="{
                        fontSize: (headerSettings.fontSize || 24) + 'px',
                        fontWeight: headerSettings.fontWeight || '700',
                        color: headerSettings.color || '#FFFFFF' 
                    }">
                        {{ headerSettings.text || 'Top Rated Hotels' }}
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

        <div class="swiper"
             :ref="el => swiperRef = el"
             :style="settings.isDoubled ? { height: gridHeight + 'px', paddingBottom: '10px', overflow: 'hidden' } : { overflow: 'hidden', paddingBottom: '10px' }">
            <div class="swiper-wrapper">
                </div>
        </div>
        <div v-if="settings.pagination && settings.pagination.enabled" class="swiper-pagination !relative mt-[10px]"></div> </div>
`
};