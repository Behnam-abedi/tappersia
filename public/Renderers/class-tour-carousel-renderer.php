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
            $settings = $this->data['tour_carousel']['settings'] ?? [];
            $slides_per_view = $settings['slidesPerView'] ?? 3;
            $space_between = $settings['spaceBetween'] ?? 18;
            $loop = $settings['loop'] ?? false;
            $centered_slides = $loop; // Center only when loop is true

            $card_width = 295;
            $container_width = ($card_width * $slides_per_view) + ($space_between * ($slides_per_view - 1));

            ob_start();
            ?>
            <style>
                #yab-tour-carousel-<?php echo esc_attr($banner_id); ?> { padding: 10px; }
                #yab-tour-carousel-<?php echo esc_attr($banner_id); ?> .yab-tour-carousel-container { max-width: <?php echo esc_attr($container_width); ?>px; margin: 0 auto; position: relative; }
                #yab-tour-carousel-<?php echo esc_attr($banner_id); ?> .swiper-slide { width: 295px !important; }
                #yab-tour-carousel-<?php echo esc_attr($banner_id); ?> .yab-tour-card-skeleton { animation: pulse 1.5s infinite ease-in-out; background-color: #e0e0e0; border-radius: 8px; }
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

                @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }
            </style>
            <div id="yab-tour-carousel-<?php echo esc_attr($banner_id); ?>" class="yab-tour-carousel-wrapper">
                <div class="yab-tour-carousel-container">
                     <div style="margin-bottom: 28px; border-bottom: 1px solid #E2E2E2; position: relative;">
                        <span style="font-size: 24px; font-weight: bold; display: inline-block; padding-bottom: 13px;">Top Iran Tours</span>
                        <div style="position: absolute; bottom: -1px; width: 15px; height: 2px; background-color: #00BAA4; border-radius: 2px;"></div>
                    </div>
                    <div class="swiper" style="overflow: hidden; padding-bottom: 10px;">
                        <div class="swiper-wrapper">
                            <?php foreach ($this->data['tour_carousel']['selectedTours'] as $tour_id) : ?>
                                <div class="swiper-slide" data-tour-id="<?php echo esc_attr($tour_id); ?>">
                                    <div class="yab-tour-card-skeleton" style="width: 295px; height: 375px;"></div>
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
                    if (idsToFetch.length === 0) return;
                    idsToFetch.forEach(id => fetchedIds.add(id));
                    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: new URLSearchParams({ 'action': 'yab_fetch_tour_details_by_ids', 'tour_ids': idsToFetch })
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
                                                <a href="${tour.detailUrl}" target="_blank" class="yab-tour-card" style="position: relative; text-decoration: none; color: inherit; display: block; width: 295px; height: 375px; background-color: #FFF; border-radius: 8px; overflow: hidden; display: flex; flex-direction: column; box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
                                                    <div style="position: relative; width: 100%; height: 180px;"><img src="${tour.bannerImage.url}" style="width: 100%; height: 100%; object-fit: cover;" /><div style="position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(to top, rgba(0,0,0,0.7), transparent); padding: 20px 12px 8px;"><span style="color: white; font-size: 14px; font-weight: 500;">${tour.startProvince.name}</span></div></div>
                                                    <div style="padding: 12px; display: flex; flex-direction: column; justify-content: space-between; flex-grow: 1;">
                                                        <h4 style="font-weight: bold; font-size: 16px; margin: 0 0 8px 0; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis; min-height: 45px;">${tour.title}</h4>
                                                        <div style="font-size: 12px; color: #666; margin-bottom: 8px;">${tour.durationDays} Days</div>
                                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: auto;">
                                                            <div><span style="font-weight: bold; font-size: 18px; color: #00BAA4;">€${tour.salePrice.toFixed(2)}</span></div>
                                                            <div style="font-size: 12px; color: #333;"><span>${tour.rate.toFixed(1)} ★</span> <span style="color: #999;">(${tour.rateCount})</span></div>
                                                        </div>
                                                    </div>
                                                </a>`;
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