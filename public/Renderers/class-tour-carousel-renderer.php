<?php
// tappersia/public/Renderers/class-tour-carousel-renderer.php

if (!class_exists('Yab_Tour_Carousel_Renderer')) {
    require_once __DIR__ . '/class-abstract-banner-renderer.php';

    class Yab_Tour_Carousel_Renderer extends Yab_Abstract_Banner_Renderer {

        public function render(): string {
            if (empty($this->data['tour_carousel']) || empty($this->data['tour_carousel']['selectedTours'])) {
                return '';
            }

            $banner_id = $this->banner_id;
            $tour_ids_json = json_encode($this->data['tour_carousel']['selectedTours']);

            ob_start();
            ?>
            <style>
                #yab-tour-carousel-<?php echo esc_attr($banner_id); ?> {
                    padding: 10px;
                }
                #yab-tour-carousel-<?php echo esc_attr($banner_id); ?> .swiper-slide {
                    width: 295px !important; /* Ensure fixed width */
                }
                #yab-tour-carousel-<?php echo esc_attr($banner_id); ?> .yab-tour-card-skeleton {
                    animation: pulse 1.5s infinite ease-in-out;
                    background-color: #e0e0e0;
                    border-radius: 8px;
                }
                #yab-tour-carousel-<?php echo esc_attr($banner_id); ?> .swiper-pagination-bullet-active {
                    background-color: #00BAA4 !important;
                }
                #yab-tour-carousel-<?php echo esc_attr($banner_id); ?> .swiper-button-next,
                #yab-tour-carousel-<?php echo esc_attr($banner_id); ?> .swiper-button-prev {
                    color: #00BAA4 !important;
                }
                @keyframes pulse {
                    0%, 100% { opacity: 1; }
                    50% { opacity: 0.5; }
                }
            </style>
            <div id="yab-tour-carousel-<?php echo esc_attr($banner_id); ?>" class="yab-tour-carousel-wrapper">
                <div class="swiper" style="width: 100%; overflow: hidden;">
                    <div class="swiper-wrapper">
                        <?php foreach ($this->data['tour_carousel']['selectedTours'] as $tour_id) : ?>
                            <div class="swiper-slide" data-tour-id="<?php echo esc_attr($tour_id); ?>">
                                <div class="yab-tour-card-skeleton" style="width: 295px; height: 375px;"></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="swiper-pagination" style="bottom: -5px !important;"></div>
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-button-next"></div>
                </div>
            </div>
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const container = document.getElementById('yab-tour-carousel-<?php echo esc_attr($banner_id); ?>');
                const swiperEl = container.querySelector('.swiper');
                const fetchedIds = new Set();

                const fetchTourData = (idsToFetch) => {
                    if (idsToFetch.length === 0) return;

                    idsToFetch.forEach(id => fetchedIds.add(id));

                    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: new URLSearchParams({
                            'action': 'yab_fetch_tour_details_by_ids',
                            'tour_ids': idsToFetch
                        })
                    })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            result.data.forEach(tour => {
                                const slide = container.querySelector(`.swiper-slide[data-tour-id="${tour.id}"]`);
                                if (slide) {
                                    slide.innerHTML = `
                                        <a href="${tour.detailUrl}" target="_blank" class="yab-tour-card" style="position: relative; text-decoration: none; color: inherit; display: block; width: 295px; height: 375px; background-color: #FFF; border-radius: 8px; overflow: hidden; display: flex; flex-direction: column; box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
                                            <div style="position: relative; width: 100%; height: 180px;">
                                                <img src="${tour.bannerImage.url}" style="width: 100%; height: 100%; object-fit: cover;" />
                                                <div style="position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(to top, rgba(0,0,0,0.7), transparent); padding: 20px 12px 8px;">
                                                    <span style="color: white; font-size: 14px; font-weight: 500;">${tour.startProvince.name}</span>
                                                </div>
                                            </div>
                                            <div style="padding: 12px; display: flex; flex-direction: column; justify-content: space-between; flex-grow: 1;">
                                                <h4 style="font-weight: bold; font-size: 16px; margin: 0 0 8px 0; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis; min-height: 45px;">${tour.title}</h4>
                                                <div style="font-size: 12px; color: #666; margin-bottom: 8px;">${tour.durationDays} Days</div>
                                                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: auto;">
                                                    <div>
                                                        <span style="font-weight: bold; font-size: 18px; color: #00BAA4;">€${tour.salePrice.toFixed(2)}</span>
                                                    </div>
                                                    <div style="font-size: 12px; color: #333;">
                                                        <span>${tour.rate.toFixed(1)} ★</span>
                                                        <span style="color: #999;">(${tour.rateCount})</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    `;
                                }
                            });
                        }
                    })
                    .catch(error => console.error('Error fetching tour details:', error));
                };

                const checkAndLoadSlides = (swiper) => {
                    const idsToFetch = [];
                    const { activeIndex } = swiper;
                    const slidesPerView = 3; 

                    for (let i = activeIndex; i < activeIndex + slidesPerView + 1; i++) {
                        if (swiper.slides[i]) {
                            const tourId = parseInt(swiper.slides[i].getAttribute('data-tour-id'), 10);
                            if (tourId && !fetchedIds.has(tourId)) {
                                idsToFetch.push(tourId);
                            }
                        }
                    }
                    if (idsToFetch.length > 0) {
                        fetchTourData(idsToFetch);
                    }
                };

                new Swiper(swiperEl, {
                    slidesPerView: 3,
                    spaceBetween: 18,
                    slidesPerGroup: 1,
                    loop: false,
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