<?php
// tappersia/public/Renderers/class-tour-carousel-renderer.php

if (!class_exists('Yab_Tour_Carousel_Renderer')) {
    require_once __DIR__ . '/class-abstract-banner-renderer.php';

    class Yab_Tour_Carousel_Renderer extends Yab_Abstract_Banner_Renderer {

        /**
         * Clones slides for loop mode based on the final specified logic.
         * Cloning happens only if loop is enabled and tour count is less than or equal to slidesPerView.
         * In that case, the slide array is duplicated.
         *
         * @param array $tours The array of selected tour IDs.
         * @param int $slides_per_view The number of slides visible at once.
         * @return array The correctly adjusted array of tour IDs for the carousel loop.
         */
        private function get_cloned_slides_for_loop(array $tours, int $slides_per_view): array {
            $tour_count = count($tours);
            
            // If there are tours and their count is less than or equal to slidesPerView, duplicate the array.
            if ($tour_count > 0 && $tour_count <= $slides_per_view) {
                return array_merge($tours, $tours);
            }

            // Otherwise, return the original array. No cloning needed.
            return $tours;
        }

        public function render(): string {
            if (empty($this->data['tour_carousel']) || empty($this->data['tour_carousel']['selectedTours'])) {
                return '';
            }

            $banner_id = $this->banner_id;
            $settings = $this->data['tour_carousel']['settings'] ?? [];
            $slides_per_view = $settings['slidesPerView'] ?? 3;
            $space_between = $settings['spaceBetween'] ?? 18;
            $loop = $settings['loop'] ?? false;
            $centered_slides = $loop; 

            // *** FIX: Apply the final, corrected cloning logic ***
            $selected_tours = $this->data['tour_carousel']['selectedTours'];
            if ($loop) {
                $selected_tours = $this->get_cloned_slides_for_loop($selected_tours, $slides_per_view);
            }

            $card_width = 295;
            $container_width = ($card_width * $slides_per_view) + ($space_between * ($slides_per_view - 1));

            ob_start();
            ?>
            <style>
                #yab-tour-carousel-<?php echo esc_attr($banner_id); ?> { padding: 10px; }
                #yab-tour-carousel-<?php echo esc_attr($banner_id); ?> .yab-tour-carousel-container { max-width: <?php echo esc_attr($container_width); ?>px; margin: 0 auto; position: relative; }
                #yab-tour-carousel-<?php echo esc_attr($banner_id); ?> .swiper-slide { width: 295px !important; }
                #yab-tour-carousel-<?php echo esc_attr($banner_id); ?> .swiper-pagination-bullet-active { background-color: #00BAA4 !important; }
                
                #yab-tour-carousel-<?php echo esc_attr($banner_id); ?> .swiper-button-next,
                #yab-tour-carousel-<?php echo esc_attr($banner_id); ?> .swiper-button-prev {
                    position: absolute; top: 25px; transform: translateY(-50%); width: 36px; height: 36px;
                    background: white; border-radius: 8px; box-shadow: inset 0 0 0 2px #E5E5E5;
                    display: flex; align-items: center; justify-content: center; z-index: 10; cursor: pointer;
                }
                #yab-tour-carousel-<?php echo esc_attr($banner_id); ?> .swiper-button-next::after,
                #yab-tour-carousel-<?php echo esc_attr($banner_id); ?> .swiper-button-prev::after {
                    content: ''; width: 10px; height: 10px; border-top: 2px solid black; border-right: 2px solid black; border-radius: 2px;
                }
                #yab-tour-carousel-<?php echo esc_attr($banner_id); ?> .swiper-button-prev { right: 50px; left: auto; padding-left: 3px; }
                #yab-tour-carousel-<?php echo esc_attr($banner_id); ?> .swiper-button-prev::after { transform: rotate(-135deg); }
                #yab-tour-carousel-<?php echo esc_attr($banner_id); ?> .swiper-button-next { right: 10px; padding-right: 3px; }
                #yab-tour-carousel-<?php echo esc_attr($banner_id); ?> .swiper-button-next::after { transform: rotate(45deg); }
                
                #yab-tour-carousel-<?php echo esc_attr($banner_id); ?> .swiper-button-disabled {
                    opacity: 0.35;
                    cursor: auto;
                    pointer-events: none;
                }
                .yab-tour-card-skeleton {
                    width: 295px; height: 375px; background-color: #f0f0f0; border-radius: 14px; padding: 9px;
                    display: flex; flex-direction: column; gap: 14px; overflow: hidden;
                }
                .yab-skeleton-image { width: 100%; height: 204px; background-color: #e0e0e0; border-radius: 14px; }
                .yab-skeleton-text { background-color: #e0e0e0; border-radius: 4px; }
                .yab-skeleton-title { width: 80%; height: 24px; }
                .yab-skeleton-line { width: 60%; height: 16px; }
                .yab-skeleton-footer { display: flex; justify-content: space-between; align-items: center; margin-top: auto; padding-bottom: 5px; }
                .yab-skeleton-price { width: 40%; height: 20px; }
                .yab-skeleton-rating { width: 30%; height: 16px; }
                .is-loaded .yab-tour-card { animation: yab-fade-in 0.5s ease-in-out; }

            </style>
            <div id="yab-tour-carousel-<?php echo esc_attr($banner_id); ?>" class="yab-tour-carousel-wrapper">
                <div class="yab-tour-carousel-container">
                     <div style="margin-bottom: 28px; border-bottom: 1px solid #E2E2E2; position: relative;">
                        <span style="font-size: 24px; font-weight: bold; display: inline-block; padding-bottom: 13px;">Top Iran Tours</span>
                        <div style="position: absolute; bottom: -1px; width: 15px; height: 2px; background-color: #00BAA4; border-radius: 2px;"></div>
                    </div>
                    <div class="swiper" style="overflow: hidden; padding-bottom: 10px;">
                        <div class="swiper-wrapper">
                            <?php foreach ($selected_tours as $tour_id) : ?>
                                <div class="swiper-slide" data-tour-id="<?php echo esc_attr($tour_id); ?>">
                                    <div class="yab-tour-card-skeleton yab-skeleton-loader">
                                        <div class="yab-skeleton-image"></div>
                                        <div style="padding: 14px 5px 5px 5px; display: flex; flex-direction: column; gap: 10px; flex-grow: 1;">
                                            <div class="yab-skeleton-text yab-skeleton-title"></div>
                                            <div class="yab-skeleton-text yab-skeleton-line" style="width: 40%;"></div>
                                            <div class="yab-skeleton-footer">
                                                <div class="yab-skeleton-text yab-skeleton-price"></div>
                                                <div class="yab-skeleton-text yab-skeleton-rating"></div>
                                            </div>
                                            <div class="yab-skeleton-text" style="height: 33px; width: 100%; margin-top: 10px;"></div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="swiper-pagination" style="position: static; margin-top: 20px;"></div>
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-button-next"></div>
                </div>
            </div>
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const container = document.getElementById('yab-tour-carousel-<?php echo esc_attr($banner_id); ?>');
                const swiperEl = container.querySelector('.swiper');
                const fetchedIds = new Set();
                const slidesPerView = <?php echo esc_js($slides_per_view); ?>;

                const fetchTourData = (idsToFetch) => {
                    const uniqueIds = idsToFetch.filter(id => !fetchedIds.has(id));
                    if (uniqueIds.length === 0) return;
                    
                    uniqueIds.forEach(id => fetchedIds.add(id));
                    
                    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: new URLSearchParams({ 'action': 'yab_fetch_tour_details_by_ids', 'tour_ids': uniqueIds })
                    })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            result.data.forEach(tour => {
                                const slides = container.querySelectorAll(`.swiper-slide[data-tour-id="${tour.id}"]`);
                                slides.forEach(slide => {
                                    if(slide.querySelector('.yab-tour-card-skeleton')) {
                                        const image = new Image();
                                        image.src = tour.bannerImage.url;
                                        image.onload = () => {
                                            slide.innerHTML = `
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
                                                                <span style="font-size: 14px; font-weight: 500; line-height: 24px; color: #00BAA4;">€${tour.salePrice.toFixed(2)}</span>
                                                                <span style="font-size: 12px; font-weight: 400; line-height: 24px; color: #757575;">/${tour.durationDays} Days</span>
                                                            </div>
                                                            <div style="display: flex; flex-direction: row; gap: 5px; align-items: baseline;">
                                                                <span style="font-size: 13px; font-weight: 700; line-height: 24px; color: #333333;">${tour.rate.toFixed(1)}</span>
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
                                            slide.classList.add('is-loaded');
                                        };
                                    }
                                });
                            });
                        }
                    })
                    .catch(error => console.error('Error fetching tour details:', error));
                };

                const checkAndLoadSlides = (swiper) => {
                    const idsToFetch = [];
                    swiper.slides.forEach(slide => {
                        if (slide.classList.contains('swiper-slide-visible')) {
                            const tourId = parseInt(slide.dataset.tourId, 10);
                            if (tourId && !fetchedIds.has(tourId)) {
                                idsToFetch.push(tourId);
                            }
                        }
                    });
                    if (idsToFetch.length > 0) {
                        fetchTourData([...new Set(idsToFetch)]);
                    }
                };

                new Swiper(swiperEl, {
                    slidesPerView: slidesPerView,
                    spaceBetween: <?php echo esc_js($space_between); ?>,
                    slidesPerGroup: 1,
                    centeredSlides: <?php echo esc_js($centered_slides) ? 'true' : 'false'; ?>,
                    loop: <?php echo esc_js($loop) ? 'true' : 'false'; ?>,
                    navigation: {
                        nextEl: container.querySelector('.swiper-button-next'),
                        prevEl: container.querySelector('.swiper-button-prev'),
                    },
                    pagination: {
                        el: container.querySelector('.swiper-pagination'),
                        clickable: true,
                    },
                    on: {
                        init: (swiper) => checkAndLoadSlides(swiper),
                        slideChange: (swiper) => checkAndLoadSlides(swiper),
                    }
                });
            });
            </script>
            <?php
            return ob_get_clean();
        }
    }
}