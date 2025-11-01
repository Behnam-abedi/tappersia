// tappersia/assets/js/admin/app-logic/composables/useTourCarousel.js
const { ref, reactive, onMounted, onUnmounted, nextTick, computed, watch } = Vue;

// به جای export function، یک آبجکت تعریف کامپوننت export کنید
export const TourCarouselLogic = {
    props: {
        tourIds: { type: Array, required: true },
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
         const uniqueId = ref(`yab-tour-carousel-vue-${Date.now()}-${Math.random().toString(36).substring(7)}`); // Added unique ID

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
        // ... بقیه منطق setup ...

         const containerWidth = computed(() => {
            const cardWidth = 295;
            const spaceBetween = props.settings.spaceBetween || 18;
            const slidesPerView = props.settings.slidesPerView || 3;
             if (slidesPerView === 1) return cardWidth; // Handle mobile view width
            return (cardWidth * slidesPerView) + (spaceBetween * (slidesPerView - 1));
        });

        const gridHeight = computed(() => {
            const cardHeight = cardSettings.value.height || 375;
            const verticalSpace = 20; // Default grid gap
            return (cardHeight * 2) + verticalSpace;
        });

         const slidesToRender = computed(() => {
            // ... (keep existing logic) ...
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
                     const minLoopCount = slidesPerView * 2;
                     if (tourCount > 0 && tourCount < minLoopCount) {
                       while (finalTours.length < minLoopCount) {
                           finalTours.push(...originalTours);
                       }
                       // Trim excess if needed
                       finalTours = finalTours.slice(0, Math.max(minLoopCount, tourCount * 2));
                     } else if (tourCount === 0) {
                        finalTours = []; // Handle edge case of 0 items
                     }
                }
                return finalTours;
            }
            return originalTours;
        });

        const getCardBackground = (settings) => {
             // ... (keep existing logic) ...
            if (!settings) return '#FFFFFF';
            if (settings.backgroundType === 'gradient') {
                if (!settings.gradientStops || settings.gradientStops.length === 0) return 'transparent'; // Handle empty stops
                const stops = settings.gradientStops.map(s => `${s.color} ${s.stop}%`).join(', ');
                return `linear-gradient(${settings.gradientAngle || 90}deg, ${stops})`;
            }
            return settings.bgColor || '#FFFFFF';
        };

         const escapeHTML = (str) => {
             if (!str) return '';
             return String(str).replace(/[&<>"']/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[m]));
         };

         const generateTourCardHTML = (tour) => {
            const card = cardSettings.value;
            // --- SKELETON ---
            if (!tour) {
                return `
                <div class="yab-tour-card-skeleton yab-skeleton-loader" style="width: 295px; height: ${card.height || 375}px; background-color: #fff; border-radius: 14px; padding: 9px; display: flex; flex-direction: column; gap: 9px; overflow: hidden; border: 1px solid #f0f0f0;">
                    <div class="yab-skeleton-image" style="width: 100%; height: ${card.imageHeight || 204}px; background-color: #f0f0f0; border-radius: 14px;"></div>
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
            // --- ACTUAL CARD ---
            const salePrice = tour.salePrice != null ? tour.salePrice.toFixed(2) : (tour.price != null ? tour.price.toFixed(2) : '0.00');
            const rate = tour.rate != null ? tour.rate.toFixed(1) : '0.0';
            const rateCount = tour.rateCount != null ? tour.rateCount : 0;
            const durationDays = tour.durationDays != null ? tour.durationDays : '?';
            const startProvinceName = tour.startProvince?.name || 'N/A';
            const detailUrl = tour.detailUrl || '#';
            const bannerImageUrl = tour.bannerImage?.url || 'https://placehold.co/276x204/e0e0e0/cccccc?text=No+Image';

            const rtlFlex = isRTL.value ? 'row-reverse' : 'row';
            const rtlTextAlign = isRTL.value ? 'right' : 'left';
            const rtlArrow = isRTL.value ? '-135deg' : '45deg';
            const provincePos = isRTL.value ? `left: ${card.province?.side || 11}px;` : `right: ${card.province?.side || 11}px;`;

            return `
                <div class="yab-tour-card" style="
                    position: relative; text-decoration: none; color: inherit; display: block;
                    width: 295px; height: ${card.height || 375}px;
                    background: ${getCardBackground(card)};
                    border: ${card.borderWidth || 1}px solid ${card.borderColor || '#E0E0E0'};
                    border-radius: ${card.borderRadius || 14}px;
                    overflow: hidden; display: flex; flex-direction: column;
                    padding: ${card.padding || 9}px;
                    direction: ${isRTL.value ? 'rtl' : 'ltr'};
                    box-sizing: border-box; /* Added */
                    ">
                    <a href="${escapeHTML(detailUrl)}" target="_blank" style="text-decoration: none; color: inherit; display: flex; flex-direction: column; height: 100%; outline: none; -webkit-tap-highlight-color: transparent;">
                        <div style="position: relative; width: 100%; height: ${card.imageHeight || 204}px; flex-shrink: 0;"> 
                            <img src="${escapeHTML(bannerImageUrl)}" alt="${escapeHTML(tour.title)}" style="width: 100%; height: 100%; object-fit: cover; border-radius: ${card.borderRadius > 2 ? card.borderRadius - 2 : card.borderRadius}px;" />
                            <div style="position: absolute; bottom: ${card.province?.bottom || 9}px; ${provincePos} min-height: 23px; display: flex; align-items: center; justify-content: center; border-radius: 29px; background: ${card.province?.bgColor || 'rgba(14,14,14,0.2)'}; padding: 0 11px; backdrop-filter: blur(${card.province?.blur || 3}px);">
                                <span style="color: ${card.province?.color || '#FFFFFF'}; font-size: ${card.province?.fontSize || 14}px; font-weight: ${card.province?.fontWeight || 500}; line-height: 24px;">${escapeHTML(startProvinceName)}</span>
                            </div>
                        </div>
                        <div style="display: flex; flex-direction: column; justify-content: space-between; flex-grow: 1; padding: 14px 5px 5px 5px; text-align: ${rtlTextAlign}; min-height: 0;">
                            <div><h4 style="font-weight: ${card.title?.fontWeight || 600}; font-size: ${card.title?.fontSize || 14}px; line-height: ${card.title?.lineHeight || 1.5}; color: ${card.title?.color || '#000000ff'}; text-overflow: ellipsis; overflow: hidden; display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 2; margin: 0;">${escapeHTML(tour.title)}</h4></div>
                            <div style="margin-top: auto; margin-bottom: 10px; display: flex; align-items: center; justify-content: space-between; padding: 0 4px; direction:ltr; flex-direction: ${rtlFlex};">
                                <div style="display: flex; flex-direction: row; gap: 4px; align-items: baseline;">
                                    <span style="font-size: ${card.price?.fontSize || 14}px; font-weight: ${card.price?.fontWeight || 500}; color: ${card.price?.color || '#00BAA4'};">€${salePrice}</span>
                                    <span style="font-size: ${card.duration?.fontSize || 12}px; font-weight: ${card.duration?.fontWeight || 400}; color: ${card.duration?.color || '#757575'};">/${durationDays} Days</span>
                                </div>
                                <div style="display: flex; flex-direction: row; gap: 5px; align-items: baseline;">
                                    <span style="font-size: ${card.rating?.fontSize || 13}px; font-weight: ${card.rating?.fontWeight || 700}; color: ${card.rating?.color || '#333333'};">${rate}</span>
                                    <span style="font-size: ${card.reviews?.fontSize || 12}px; font-weight: ${card.reviews?.fontWeight || 400}; color: ${card.reviews?.color || '#757575'};">(${rateCount} Reviews)</span>
                                </div>
                            </div>
                            <div style="padding: 0 4px; flex-shrink: 0;"> 
                                <div style="direction:ltr; display:flex; height: 33px; width: 100%; align-items: center; justify-content: space-between; border-radius: 5px; background-color: ${card.button?.bgColor || '#00BAA4'}; padding: 0 20px; text-decoration: none; flex-direction: ${rtlFlex}; box-sizing: border-box;"> 
                                    <span style="font-size: ${card.button?.fontSize || 13}px; font-weight: ${card.button?.fontWeight || 600}; color: ${card.button?.color || '#FFFFFF'};">View More</span>
                                    <div style="width: ${card.button?.arrowSize || 10}px; height: ${card.button?.arrowSize || 10}px; border-top: 2px solid ${card.button?.color || '#FFFFFF'}; border-right: 2px solid ${card.button?.color || '#FFFFFF'}; transform: rotate(${rtlArrow}); border-radius: 2px;"></div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>`;
        };

        const fetchAndRenderTours = async (idsToFetch, retries = 2) => {
             // ... (keep existing logic) ...
             const uniqueIds = [...new Set(idsToFetch)].filter(id => !fetchedIds.has(id));
            if (uniqueIds.length === 0) return;
            uniqueIds.forEach(id => fetchedIds.add(id)); // Mark as attempting to fetch

            try {
                const data = await props.ajax.post('yab_fetch_tour_details_by_ids', { tour_ids: uniqueIds });
                if (data && Array.isArray(data)) {
                    data.forEach(tourData => {
                        if (!swiperRef.value) return;
                        const slidesToUpdate = swiperRef.value.querySelectorAll(`.swiper-slide[data-tour-id="${tourData.id}"]`);
                        if (slidesToUpdate.length > 0) {
                            slidesToUpdate.forEach(slide => {
                                 if (slide.querySelector('.yab-tour-card-skeleton')) { // Check if skeleton exists
                                     // Image preloading
                                     const img = new Image();
                                     const imageUrl = tourData.bannerImage?.url || 'https://placehold.co/276x204/e0e0e0/cccccc?text=No+Image';
                                     img.onload = () => {
                                         slide.innerHTML = generateTourCardHTML(tourData);
                                         slide.classList.add('is-loaded'); // Add class for potential animation
                                     };
                                     img.onerror = () => { // Still render card if image fails
                                        console.error(`Failed to load image for tour ID ${tourData.id}: ${imageUrl}`);
                                        slide.innerHTML = generateTourCardHTML(tourData);
                                        slide.classList.add('is-loaded');
                                     };
                                     img.src = imageUrl;
                                 }
                            });
                        }
                    });
                } else {
                    console.error('Tour AJAX Error: Invalid data received.', data);
                    // Optionally mark these IDs as failed if needed
                }
            } catch (error) {
                console.error('Tour AJAX call failed!', error);
                // Remove failed IDs from fetchedIds so retry can happen
                uniqueIds.forEach(id => fetchedIds.delete(id));
                if (retries > 0) {
                     console.warn(`Retrying fetch for tour IDs: ${uniqueIds.join(', ')}. Retries left: ${retries - 1}`);
                     setTimeout(() => {
                        fetchAndRenderTours(uniqueIds, retries - 1);
                     }, 2000); // Wait 2 seconds before retrying
                 } else {
                     console.error(`Failed to fetch tour IDs after multiple retries: ${uniqueIds.join(', ')}`);
                      // Optionally update UI for failed cards
                     uniqueIds.forEach(id => {
                        if (!swiperRef.value) return;
                        const slidesToUpdate = swiperRef.value.querySelectorAll(`.swiper-slide[data-tour-id="${id}"]`);
                        slidesToUpdate.forEach(slide => {
                            if (slide.querySelector('.yab-tour-card-skeleton')) {
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
                    const tourId = parseInt(slide.dataset.tourId, 10);
                    if (!isNaN(tourId)) idsToFetch.add(tourId);
                });
            }
            if (idsToFetch.size > 0) {
                fetchAndRenderTours(Array.from(idsToFetch));
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
                    slideEl.setAttribute('data-tour-id', id);
                    // slideEl.style.width = '295px'; // Let Swiper handle width based on slidesPerView
                    slideEl.innerHTML = generateTourCardHTML(null); // Render skeleton
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

         watch(() => [props.tourIds, props.settings], () => {
             // Reset fetched IDs when tour list changes significantly OR settings change
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
    <div :id="uniqueId" :style="{ maxWidth: containerWidth + 'px', width: '100%', margin: '0 auto' }" :dir="settings.direction" class="yab-tour-carousel-preview"> <div :style="{ marginBottom: (headerSettings.marginTop || 28) + 'px' }" class="flex flex-col">
            <div class="mb-[13px] flex w-full flex-row justify-between items-center" >
                <div>
                    <span :style="{
                        fontSize: (headerSettings.fontSize || 24) + 'px',
                        fontWeight: headerSettings.fontWeight || '700',
                        color: headerSettings.color || '#FFFFFF' /* Default to white for admin */
                    }">{{ headerSettings.text || 'Top Iran Tours' }}</span>
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