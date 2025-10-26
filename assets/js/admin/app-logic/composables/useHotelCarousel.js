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
            const cardSettings = computed(() => props.settings.card || {}); // Get card settings

            const updatePaginationStyles = () => {
                // ... (no changes needed here) ...
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
                // If slidesPerView is 1 (e.g., mobile), width should just be card width
                 if (slidesPerView === 1) {
                     return cardWidth;
                 }
                return (cardWidth * slidesPerView) + (spaceBetween * (slidesPerView - 1));
            });

            const gridHeight = computed(() => {
                const cardHeight = cardSettings.value.height || 357; // Use height from settings
                const verticalSpace = 20; // Default grid gap
                return (cardHeight * 2) + verticalSpace;
            });

            const slidesToRender = computed(() => {
                // ... (no changes needed here) ...
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
            const RatingLabel = { Excellent: 'Excellent', VeryGood: 'Very Good', Good: 'Good', Average: 'Average', Poor: 'Poor', New: 'New' };

            // Helper functions
            const getRatingLabel = (score) => { /* ... (no changes) ... */
              if (!score || score == 0) return RatingLabel.New;
              if (score >= 4.6) return RatingLabel.Excellent;
              if (score >= 4.1) return RatingLabel.VeryGood;
              if (score >= 3.6) return RatingLabel.Good;
              if (score >= 3.0) return RatingLabel.Average;
              return RatingLabel.Poor;
            };

            const getTagClass = (tag) => { /* ... (no changes) ... */
                if (!tag) return 'hotel-label-default';
                const tagName = tag.toLowerCase();
                const tagMap = { luxury: 'hotel-label-luxury', business: 'hotel-label-business', boutique: 'hotel-label-boutique', traditional: 'hotel-label-traditional', economy: 'hotel-label-economy', hostel: 'hotel-label-hostel' };
                return tagMap[tagName] || 'hotel-label-default';
            };

            const escapeHTML = (str) => { /* ... (no changes) ... */
                if (!str) return '';
                // Basic escaping for preventing XSS in simple text insertion
                return String(str).replace(/[&<>"']/g, function(m) {
                    return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[m];
                });
            };

            const generateHotelCardHTML = (hotel) => {
                 const card = cardSettings.value; // Access card settings

                 // --- SKELETON LOADER ---
                if (!hotel) {
                    // Use card height from settings
                    const cardHeight = card.height || 357;
                    const imageHeight = card.image?.height || 176;
                    const imageRadius = card.image?.radius || 14;
                    const cardRadius = card.borderRadius || 16;
                    const cardPadding = card.padding || 9;
                    const contentMarginX = card.bodyContent?.marginX || 19;
                    const contentMarginTop = card.bodyContent?.marginTop || 14;

                    // Calculate image width based on card padding
                    const imageWidth = 295 - (cardPadding * 2);

                    return `
                    <div name="card-skeleton" class="yab-hotel-card-skeleton yab-skeleton-loader" style="margin: 0; height:357px; width: 295px; border-radius: ${cardRadius}px; border: ${card.borderWidth}px solid ${card.borderColor}; padding: ${cardPadding}px; background-color: ${card.bgColor}; box-sizing: border-box; overflow: hidden;">
                      <div style="height: ${imageHeight}px; width: ${imageWidth}px; border-radius: ${imageRadius}px; background-color: #f0f0f0;"></div>
                      <div style="margin: ${contentMarginTop}px ${contentMarginX}px 0 ${contentMarginX}px;">
                        <div style="min-height: ${card.title?.minHeight || 34}px; width: 100%; margin-bottom: 7px;">
                          <div style="height: 16px; background-color: #f0f0f0; border-radius: 4px; width: 75%; margin-bottom: 8px;"></div>
                          <div style="height: 16px; background-color: #f0f0f0; border-radius: 4px; width: 50%;"></div>
                        </div>
                        <div name="description-skeleton">
                          <div name="rating-skeleton" style="display: flex; flex-direction: row; align-items: center; gap: ${card.rating?.gap || 6}px; margin-top: ${card.rating?.marginTop || 7}px;">
                            <div style="height: 30px; width: 60%; border-radius: ${card.rating?.boxRadius || 3}px; background-color: #f0f0f0;"></div>
                          </div>
                        </div>
                        <hr style="margin: ${card.divider?.marginTop || 9.5}px 0 ${card.divider?.marginBottom || 7.5}px 0; border: 0; border-top: 1px solid ${card.divider?.color || '#EEEEEE'};" />
                        <div name="price-skeleton" style="display: flex; flex-direction: row;justify-content:space-between">
                           <div style="height: 30px; background-color: #f0f0f0; border-radius: 4px; width: 40%; margin-bottom: 4px;"></div>
                           <div style="height: 30px; background-color: #f0f0f0; border-radius: 4px; width: 40%; margin-bottom: 4px;"></div>

                        </div>
                      </div>
                    </div>`;
                }
                // --- END SKELETON LOADER ---


                // --- CARD LOGIC ---
                 // Destructure data as before
                const { coverImage, isFeatured = false, discount = 0, minPrice = 0, star = 0, title = 'N/A', avgRating, reviewCount = 0, customTags = [], detailUrl = '#' } = hotel;

                const imageUrl = coverImage?.url || 'https://placehold.co/276x176/e0e0e0/cccccc?text=No+Image';
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

                 // Calculate image overlay gradient
                 const overlayGradient = `linear-gradient(to top, ${card.imageOverlay?.gradientEndColor || 'rgba(0,0,0,0.83)'} ${card.imageOverlay?.gradientEndPercent || 0}%, ${card.imageOverlay?.gradientStartColor || 'rgba(0,0,0,0)'} ${card.imageOverlay?.gradientStartPercent || 38}%, ${card.imageOverlay?.gradientStartColor || 'rgba(0,0,0,0)'} 100%)`;

                // --- TEMPLATE ---
                 // Apply styles from cardSettings (card variable)
                 return `
                <div name="card" class="yab-hotel-card" style="margin: 0; min-height: ${card.height}px; width: 295px; border-radius: ${card.borderRadius}px; border: ${card.borderWidth}px solid ${card.borderColor}; padding: ${card.padding}px; background-color: ${card.bgColor}; box-sizing: border-box; font-family: 'Roboto', sans-serif;">
                  <a href="${escapeHTML(detailUrl)}" target="_blank" style="text-decoration: none; color: inherit; display: block; outline: none;">
                    <div style="position: relative; height: ${card.image?.height || 176}px; width: ${295 - (card.padding * 2)}px; border-radius: ${card.image?.radius || 14}px;" name="header-content-image">
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
                    <div name="body-content" style="margin: ${card.bodyContent?.marginTop || 14}px ${card.bodyContent?.marginX || 19}px 0 ${card.bodyContent?.marginX || 19}px; color: ${card.bodyContent?.textColor || '#333'};">
                      <div name="title" style="min-height: ${card.title?.minHeight || 34}px; width: 100%;">
                        <h4 style="font-size: ${card.title?.fontSize || 14}px; line-height: ${card.title?.lineHeight || 1.2}; font-weight: ${card.title?.fontWeight || 600}; color: ${card.title?.color || '#333333'}; margin: 0; overflow: hidden; display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 2;">${escapeHTML(title)}</h4>
                      </div>
                      <div name="description">
                        <div name="rating" style="margin-top: ${card.rating?.marginTop || 7}px; display: flex; flex-direction: row; align-items: center; gap: ${card.rating?.gap || 6}px;">
                          ${ratingScore !== null ? `<div name="rate"><span style="width: fit-content; border-radius: ${card.rating?.boxRadius || 3}px; background: ${card.rating?.boxBgColor || '#5191FA'}; padding: ${card.rating?.boxPaddingY || 2}px ${card.rating?.boxPaddingX || 6}px; font-size: ${card.rating?.boxFontSize || 11}px; line-height: 1; color: ${card.rating?.boxColor || '#ffffff'};">${ratingScore}</span></div>` : ''}
                          <div name="text-rate" style="font-size: ${card.rating?.labelFontSize || 12}px; line-height: 15px; color: ${card.rating?.labelColor || '#333333'}; padding-top: 1px;">
                            <span>${escapeHTML(ratingLabel)}</span>
                          </div>
                          <div name="rate-count" style="font-size: ${card.rating?.countFontSize || 10}px; line-height: 12px; color: ${card.rating?.countColor || '#999999'};">
                            <span>(${reviewCount})</span>
                          </div>
                        </div>
                        <div name="tags">
                          <div style="display: flex; flex-direction: row; flex-wrap: wrap; gap: ${card.tags?.gap || 5}px;">
                            ${tagsHtml}
                          </div>
                        </div>
                      </div>
                      <hr style="margin: ${card.divider?.marginTop || 9.5}px 0 ${card.divider?.marginBottom || 7.5}px 0; border: 0; border-top: 1px solid ${card.divider?.color || '#EEEEEE'};" />
                      <div name="price" style="display: flex; flex-direction: column;">
                        <div style="font-size: ${card.price?.fromSize || 12}px; line-height: 14px; color: ${card.price?.fromColor || '#999999'};">
                          <span>From</span>
                        </div>
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
                // --- END TEMPLATE ---
            };
            // --- END NEW CARD LOGIC ---

const fetchAndRenderHotels = async (idsToFetch, retries = 2) => { // Added retries parameter
                const uniqueIds = [...new Set(idsToFetch)].filter(id => !fetchedIds.has(id));
                if (uniqueIds.length === 0) return;
                // Add IDs to fetchedIds *before* fetching to prevent immediate retries on fast failures
                uniqueIds.forEach(id => fetchedIds.add(id));
                try {
                     const data = await props.ajax.post('yab_fetch_hotel_details_by_ids', { hotel_ids: uniqueIds });
                     if (data && Array.isArray(data)) {
                        data.forEach(hotelData => {
                            if (!swiperRef.value) return;
                            const slidesToUpdate = swiperRef.value.querySelectorAll(`.swiper-slide[data-hotel-id="${hotelData.id}"]`);
                            if (slidesToUpdate.length > 0) {
                                slidesToUpdate.forEach(slide => {
                                    // Make sure skeleton exists before replacing
                                    if (slide.querySelector('[name="card-skeleton"]')) {
                                        const fullHotelData = { /* ... (keep existing object structure) ... */
                                            id: hotelData.id,
                                            coverImage: hotelData.coverImage,
                                            isFeatured: hotelData.isFeatured,
                                            discount: hotelData.discount,
                                            minPrice: hotelData.minPrice,
                                            star: hotelData.star,
                                            title: hotelData.title,
                                            avgRating: hotelData.avgRating,
                                            reviewCount: hotelData.reviewCount,
                                            customTags: hotelData.customTags,
                                            detailUrl: hotelData.detailUrl
                                        };
                                        // Use image preloading for smoother transition
                                        const img = new Image();
                                        const imageUrl = fullHotelData.coverImage?.url || 'https://placehold.co/276x176/e0e0e0/cccccc?text=No+Image';
                                        img.onload = () => {
                                            slide.innerHTML = generateHotelCardHTML(fullHotelData);
                                            slide.classList.add('is-loaded');
                                        };
                                        img.onerror = () => { // Still render card if image fails
                                            console.error(`Failed to load image for hotel ID ${fullHotelData.id}: ${imageUrl}`);
                                            slide.innerHTML = generateHotelCardHTML(fullHotelData);
                                            slide.classList.add('is-loaded');
                                        };
                                        img.src = imageUrl;
                                    }
                                });
                            }
                        });
                     } else {
                        // Handle case where API succeeds but returns empty/invalid data
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
                        // Optionally display an error message on the specific cards
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
                 // ... (no changes needed here) ...
                 if (!swiper || !swiper.slides || swiper.slides.length === 0 || !swiper.params) return;
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
                 const startIndex = Math.max(0, swiper.activeIndex || 0); // Default activeIndex to 0
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
                // ... (Swiper initialization logic, no changes needed for card styling itself) ...
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
                        // slideEl.style.width = '295px'; // Let Swiper handle width
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
                 // Reset fetched IDs when hotel list changes significantly OR settings change
                 fetchedIds.clear();
                 nextTick(() => {
                     initSwiper();
                 });
             }, { deep: true, immediate: true });


            onUnmounted(() => {
                // ... (no changes needed here) ...
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
        // --- TEMPLATE (No changes needed here) ---
        template: `
            <div :id="uniqueId" :style="{ maxWidth: containerWidth + 'px', width: '100%', margin: '0 auto' }" :dir="settings.direction" class="yab-hotel-carousel-preview"> 
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
                     :style="settings.isDoubled ? { height: gridHeight + 'px', paddingBottom: '10px', overflow: 'hidden' } : { overflow: 'hidden', paddingBottom: '10px' }"> 
                    <div class="swiper-wrapper">
                        <!-- Slides are dynamically generated in initSwiper -->
                    </div>
                </div>
                <div v-if="settings.pagination && settings.pagination.enabled" class="swiper-pagination !relative mt-[10px]"></div>
            </div>
        `
    };
}
