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
            $header_settings = $settings['header'] ?? [];
            $original_tours_ids = $this->data['tour_carousel']['selectedTours'];
            $tour_count = count($original_tours_ids);
            
            $slides_per_view = $settings['slidesPerView'] ?? 3;
            $space_between = $settings['spaceBetween'] ?? 18;
            $loop = $settings['loop'] ?? false;
            $is_doubled = $settings['isDoubled'] ?? false;
            $direction = $settings['direction'] ?? 'ltr';
            $is_rtl = $direction === 'rtl';
            $grid_fill = $settings['loop'] ? 'column' : ($settings['gridFill'] ?? 'column');
            
            $autoplay_enabled = $settings['autoplay']['enabled'] ?? false;
            $autoplay_delay = $settings['autoplay']['delay'] ?? 3000;
            $navigation_enabled = $settings['navigation']['enabled'] ?? true;
            $pagination_enabled = $settings['pagination']['enabled'] ?? true;

            $slides_to_render = $original_tours_ids;

            if ($loop && $tour_count > 0) {
                if ($is_doubled) {
                    $min_loop_count = (2 * $slides_per_view) + 2;
                    if ($tour_count < $min_loop_count) {
                        $repeat_count = ceil($min_loop_count / $tour_count);
                        $final_tours = [];
                        for ($i = 0; $i < $repeat_count; $i++) { $final_tours = array_merge($final_tours, $original_tours_ids); }
                        $slides_to_render = $final_tours;
                    }
                } else {
                    if ($tour_count < $slides_per_view * 2) {
                        $final_tours = $original_tours_ids;
                        while (count($final_tours) < $slides_per_view * 2) { $final_tours = array_merge($final_tours, $original_tours_ids); }
                        $slides_to_render = $final_tours;
                    }
                }
            }
            
            $card_width = 295;
            $container_width = ($card_width * $slides_per_view) + ($space_between * ($slides_per_view - 1));
            $grid_height = (375 * 2) + 20;

            ob_start();
            ?>
            <style>
                #yab-tour-carousel-<?php echo esc_attr($banner_id); ?> .swiper-slide { width: 295px !important; <?php if ($is_doubled): ?> height: calc((100% - 20px) / 2) !important; margin: 0 !important; <?php endif; ?> }
                #yab-tour-carousel-<?php echo esc_attr($banner_id); ?> .swiper-button-next, #yab-tour-carousel-<?php echo esc_attr($banner_id); ?> .swiper-button-prev { background: white; border-radius: 8px; box-shadow: inset 0 0 0 2px #E5E5E5; display: flex; align-items: center; justify-content: center; z-index: 10; cursor: pointer; width: 36px; height: 36px; }
                #yab-tour-carousel-<?php echo esc_attr($banner_id); ?> .swiper-button-next::after, #yab-tour-carousel-<?php echo esc_attr($banner_id); ?> .swiper-button-prev::after { content: ''; width: 10px; height: 10px; border-top: 2px solid black; border-right: 2px solid black; border-radius: 2px; }
                #yab-tour-carousel-<?php echo esc_attr($banner_id); ?> .swiper-button-prev::after { transform: rotate(-135deg); }
                #yab-tour-carousel-<?php echo esc_attr($banner_id); ?> .swiper-button-next::after { transform: rotate(45deg); }
                .is-loaded .yab-tour-card { animation: yab-fade-in 0.5s ease-in-out; }
            </style>
            <div id="yab-tour-carousel-<?php echo esc_attr($banner_id); ?>" class="yab-tour-carousel-wrapper" dir="<?php echo esc_attr($direction); ?>">
                <div class="yab-tour-carousel-container" style="max-width: <?php echo esc_attr($container_width); ?>px; margin: 0 auto; position: relative;">
                     <div style="margin-bottom: <?php echo esc_attr($header_settings['marginTop'] ?? 28); ?>px; position: relative;">
                        <div style="display: flex; justify-content: space-between; align-items: center; flex-direction: <?php echo $is_rtl ? 'row-reverse' : 'row'; ?>; padding-bottom: 13px;">
                            <span style="font-size: <?php echo esc_attr($header_settings['fontSize'] ?? 24); ?>px; font-weight: <?php echo esc_attr($header_settings['fontWeight'] ?? '700'); ?>; color: <?php echo esc_attr($header_settings['color'] ?? '#000000'); ?>;"><?php echo esc_html($header_settings['text'] ?? 'Top Iran Tours'); ?></span>
                            <?php if ($navigation_enabled): ?>
                            <div style="display: flex; flex-direction: <?php echo $is_rtl ? 'row-reverse' : 'row' ?>; gap: 7px; align-items: center;">
                               <div class="swiper-button-prev" style="position: static; transform: none;"></div>
                               <div class="swiper-button-next" style="position: static; transform: none;"></div>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div style="width: 100%; height: 1px; background-color: #E2E2E2; border-radius: 2px;"></div>
                        <div style="position: absolute; bottom: 0; <?php echo $is_rtl ? 'right: 0;' : 'left: 0;'; ?> margin-top: -2px; width: 15px; height: 2px; background-color: <?php echo esc_attr($header_settings['lineColor'] ?? '#00BAA4'); ?>; border-radius: 2px;"></div>
                    </div>
                    <div class="swiper" style="overflow: hidden; padding-bottom: 10px; <?php echo $is_doubled ? 'height: ' . $grid_height . 'px;' : ''; ?>">
                        <div class="swiper-wrapper">
                            <?php foreach ($slides_to_render as $tour_id) : ?>
                                <div class="swiper-slide" data-tour-id="<?php echo esc_attr($tour_id); ?>">
                                    <div class="yab-tour-card-skeleton yab-skeleton-loader" style="width: 295px; height: 375px;"></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php if ($pagination_enabled): ?>
                    <div class="swiper-pagination" style="position: static; margin-top: 20px;"></div>
                    <?php endif; ?>
                </div>
            </div>
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const container = document.getElementById('yab-tour-carousel-<?php echo esc_attr($banner_id); ?>');
                const swiperEl = container.querySelector('.swiper');
                const fetchedIds = new Set();
                const isRTL = <?php echo json_encode($is_rtl); ?>;

                const generateTourCardHTML = (tour) => {
                    if (!tour) return '';
                    const salePrice = tour.salePrice ? tour.salePrice.toFixed(2) : '0.00';
                    const rate = tour.rate ? tour.rate.toFixed(1) : '0.0';
                    const rtlFlex = isRTL ? 'row-reverse' : 'row';
                    const rtlTextAlign = isRTL ? 'right' : 'left';
                    const rtlArrow = isRTL ? '-135deg' : '45deg';
                    const provincePos = isRTL ? 'left: 11px;' : 'right: 11px;';
                    
                    return `
                    <div class="yab-tour-card" style="position: relative; text-decoration: none; color: inherit; display: block; width: 295px; height: 375px; background-color: #FFF; border-radius: 14px; overflow: hidden; display: flex; flex-direction: column; box-shadow: 0 4px 12px rgba(0,0,0,0.08); padding: 9px; direction: ${isRTL ? 'rtl' : 'ltr'};">
                        <div style="position: relative; width: 100%; height: 204px;">
                            <img src="${tour.bannerImage.url}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 14px;" />
                            <div style="position: absolute; bottom: 0; ${provincePos} margin-bottom: 9px; min-height: 23px; min-width: 65px; display: flex; align-items: center; justify-content: center; border-radius: 29px; background: rgba(14,14,14,0.2); padding: 0 11px; backdrop-filter: blur(3px);">
                                <span style="color: white; font-size: 14px; font-weight: 500; line-height: 24px;">${tour.startProvince.name}</span>
                            </div>
                        </div>
                        <div style="display: flex; flex-direction: column; justify-content: space-between; flex-grow: 1; padding: 14px 5px 5px 5px; text-align: ${rtlTextAlign};">
                            <div><h4 style="font-weight: 600; font-size: 14px; line-height: 24px; color: black; margin: 0;">${tour.title}</h4></div>
                            <div style="margin-top: auto; margin-bottom: 10px; display: flex; align-items: center; justify-content: space-between; padding: 0 4px; flex-direction: ${rtlFlex};">
                                <div style="display: flex; flex-direction: row; gap: 4px; align-items: baseline;">
                                    <span style="font-size: 14px; font-weight: 500; line-height: 24px; color: #00BAA4;">€${salePrice}</span>
                                    <span style="font-size: 12px; font-weight: 400; line-height: 24px; color: #757575;">/${tour.durationDays} Days</span>
                                </div>
                                <div style="display: flex; flex-direction: row; gap: 5px; align-items: baseline;">
                                    <span style="font-size: 13px; font-weight: 700; line-height: 24px; color: #333333;">${rate}</span>
                                    <span style="font-size: 12px; font-weight: 400; line-height: 24px; color: #757575;">(${tour.rateCount} Reviews)</span>
                                </div>
                            </div>
                            <a href="${tour.detailUrl}" target="_blank" style="display:flex; height: 33px; width: 100%; align-items: center; justify-content: space-between; border-radius: 5px; background-color: #00BAA4; padding: 0 20px; text-decoration: none; flex-direction: ${rtlFlex};">
                                <span style="font-size: 13px; font-weight: 600; line-height: 16px; color: white;">View More</span>
                                <div style="width: 10px; height: 10px; border-top: 2px solid white; border-right: 2px solid white; transform: rotate(${rtlArrow}); border-radius: 2px;"></div>
                            </a>
                        </div>
                    </div>`;
                };
                
                const fetchTourData = (idsToFetch) => {
                    const uniqueIds = Array.from(new Set(idsToFetch)).filter(id => !fetchedIds.has(id));
                    if (uniqueIds.length === 0) return;
                    uniqueIds.forEach(id => fetchedIds.add(id));
                    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: new URLSearchParams({ 'action': 'yab_fetch_tour_details_by_ids', 'tour_ids': uniqueIds })
                    }).then(r => r.json()).then(res => {
                        if(res.success) res.data.forEach(tour => {
                            container.querySelectorAll(`.swiper-slide[data-tour-id="${tour.id}"]`).forEach(slide => {
                                if(slide.querySelector('.yab-tour-card-skeleton')) {
                                    const image = new Image();
                                    image.src = tour.bannerImage.url;
                                    image.onload = () => { slide.innerHTML = generateTourCardHTML(tour); slide.classList.add('is-loaded'); };
                                }
                            });
                        });
                    });
                };
                
                const checkAndLoadSlides = (swiper) => {
                    if (!swiper || !swiper.slides || swiper.slides.length === 0) return;
                    const idsToFetch = new Set();
                    const slides = Array.from(swiper.slides);
                    const rows = (swiper.params.grid && swiper.params.grid.rows > 1) ? swiper.params.grid.rows : 1;
                    const slidesToLoadCount = (swiper.params.slidesPerView * rows) * 2;
                    const slidesToCheck = slides.slice(swiper.activeIndex, swiper.activeIndex + slidesToLoadCount);
                    if (slidesToCheck.length > 0) {
                        slidesToCheck.forEach(slide => { if(slide.dataset.tourId) idsToFetch.add(parseInt(slide.dataset.tourId, 10)); });
                    }
                    if (idsToFetch.size > 0) { fetchTourData(Array.from(idsToFetch)); }
                };
                
                const swiperOptions = {
                    slidesPerView: <?php echo esc_js($slides_per_view); ?>,
                    spaceBetween: <?php echo esc_js($space_between); ?>,
                    loop: <?php echo esc_js($loop) ? 'true' : 'false'; ?>,
                    dir: '<?php echo esc_js($direction); ?>',
                    on: { init: (s) => setTimeout(() => checkAndLoadSlides(s), 150), slideChange: checkAndLoadSlides, resize: checkAndLoadSlides }
                };
                
                <?php if ($autoplay_enabled): ?> swiperOptions.autoplay = { delay: <?php echo esc_js($autoplay_delay); ?>, disableOnInteraction: false }; <?php endif; ?>
                <?php if ($navigation_enabled): ?> swiperOptions.navigation = { nextEl: container.querySelector('.swiper-button-next'), prevEl: container.querySelector('.swiper-button-prev') }; <?php endif; ?>
                <?php if ($pagination_enabled): ?> swiperOptions.pagination = { el: container.querySelector('.swiper-pagination'), clickable: true }; <?php endif; ?>
                <?php if ($is_doubled): ?> swiperOptions.grid = { rows: 2, fill: '<?php echo esc_js($grid_fill); ?>' }; swiperOptions.spaceBetween = 20; <?php endif; ?>

                new Swiper(swiperEl, swiperOptions);
            });
            </script>
            <?php
            return ob_get_clean();
        }
    }
}