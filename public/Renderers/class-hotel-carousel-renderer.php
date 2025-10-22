<?php
// tappersia/public/Renderers/class-hotel-carousel-renderer.php

if (!class_exists('Yab_Hotel_Carousel_Renderer')) {
    require_once __DIR__ . '/class-abstract-banner-renderer.php';

    class Yab_Hotel_Carousel_Renderer extends Yab_Abstract_Banner_Renderer {

        public function render(): string {
            // Check for hotel_carousel and selectedHotels
            if (empty($this->data['hotel_carousel']) || empty($this->data['hotel_carousel']['selectedHotels'])) {
                return '';
            }

            $banner_id = $this->banner_id;
            $desktop_settings = $this->data['hotel_carousel']['settings'] ?? [];
            $mobile_settings = $this->data['hotel_carousel']['settings_mobile'] ?? $desktop_settings;
            // Use selectedHotels
            $original_hotels_ids = $this->data['hotel_carousel']['selectedHotels'];

            ob_start();
            ?>
            <style>
                /* Update CSS class prefix */
                .yab-hotel-carousel-wrapper-<?php echo $banner_id; ?> .yab-hotel-carousel-mobile { display: none; }
                .yab-hotel-carousel-wrapper-<?php echo $banner_id; ?> .yab-hotel-carousel-desktop { display: block; }

                @media (max-width: 768px) {
                    .yab-hotel-carousel-wrapper-<?php echo $banner_id; ?> .yab-hotel-carousel-desktop { display: none; }
                    .yab-hotel-carousel-wrapper-<?php echo $banner_id; ?> .yab-hotel-carousel-mobile { display: block; }
                }
                 /* Add scoped Swiper styles */
                #yab-hotel-carousel-<?php echo $banner_id; ?>_desktop .swiper-pagination-bullet,
                #yab-hotel-carousel-<?php echo $banner_id; ?>_mobile .swiper-pagination-bullet {
                    width: 20px !important; height: 6px !important; border-radius: 4px !important;
                }
                #yab-hotel-carousel-<?php echo $banner_id; ?>_desktop .swiper-button-disabled,
                #yab-hotel-carousel-<?php echo $banner_id; ?>_mobile .swiper-button-disabled {
                    opacity: 0.5 !important; cursor: auto !important; pointer-events: none !important;
                }

            </style>
            <div class="yab-hotel-carousel-wrapper-<?php echo $banner_id; ?>">
                <div class="yab-hotel-carousel-desktop">
                    <?php echo $this->render_view($banner_id, 'desktop', $desktop_settings, $original_hotels_ids); ?>
                </div>
                <div class="yab-hotel-carousel-mobile">
                    <?php echo $this->render_view($banner_id, 'mobile', $mobile_settings, $original_hotels_ids); ?>
                </div>
            </div>
            <?php
            return ob_get_clean();
        }

        private function render_view($banner_id, $view, $settings, $original_hotels_ids) {
            $header_settings = $settings['header'] ?? [];
            $card_settings = $settings['card'] ?? [];
            // Use hotel count
            $hotel_count = count($original_hotels_ids);

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
            $pagination_color = $settings['pagination']['paginationColor'] ?? 'rgba(0, 186, 164, 0.31)';
            $pagination_active_color = $settings['pagination']['paginationActiveColor'] ?? '#00BAA4';

            // Use hotel IDs
            $slides_to_render = $original_hotels_ids;

            // Loop logic remains similar, using hotel count
            if ($loop && $hotel_count > 0) {
                $final_items = $original_hotels_ids;
                if ($is_doubled) {
                    $min_loop_count = (2 * $slides_per_view) + 2;
                    while (count($final_items) < $min_loop_count) {
                        $final_items = array_merge($final_items, $original_hotels_ids);
                    }
                } else {
                     $min_loop_count = $slides_per_view * 2;
                     while (count($final_items) < $min_loop_count) {
                        $final_items = array_merge($final_items, $original_hotels_ids);
                    }
                }
                $slides_to_render = $final_items;
            }

            $card_width = 295; // Assuming same card width for now
            $container_width = ($card_width * $slides_per_view) + ($space_between * ($slides_per_view - 1));
            $grid_height = ($card_settings['height'] ?? 375) * 2 + 20; // Assuming same card height

            $unique_id = $banner_id . '_' . $view;

            ob_start();
            ?>
             <style>
                #yab-hotel-carousel-<?php echo esc_attr($unique_id); ?> .swiper-slide {
                    width: 295px !important;
                    <?php if ($is_doubled): ?>
                        height: calc((100% - 20px) / 2) !important;
                    <?php endif; ?>
                }
                 #yab-hotel-carousel-<?php echo esc_attr($unique_id); ?> .tappersia-carusel-next,
                #yab-hotel-carousel-<?php echo esc_attr($unique_id); ?> .tappersia-carusel-perv {
                    background: white; border-radius: 8px; box-shadow: inset 0 0 0 2px #E5E5E5;
                    display: flex; align-items: center; justify-content: center;
                    z-index: 10; cursor: pointer; width: 36px; height: 36px;
                }
                #yab-hotel-carousel-<?php echo esc_attr($unique_id); ?> .tappersia-carusel-next { <?php echo $is_rtl ? 'padding-left: 2px' : 'padding-right: 4px'; ?> }
                #yab-hotel-carousel-<?php echo esc_attr($unique_id); ?> .tappersia-carusel-perv { <?php echo $is_rtl ? 'padding-right: 4px' : 'padding-left: 4px'; ?> }
                #yab-hotel-carousel-<?php echo esc_attr($unique_id); ?> .tappersia-carusel-next > div,
                #yab-hotel-carousel-<?php echo esc_attr($unique_id); ?> .tappersia-carusel-perv > div { width: 10px; height: 10px; border-top: 2px solid black; border-right: 2px solid black; border-radius: 2px; }
                #yab-hotel-carousel-<?php echo esc_attr($unique_id); ?> .tappersia-carusel-perv > div { transform: rotate(<?php echo $is_rtl ? '45deg' : '-135deg'; ?>); }
                #yab-hotel-carousel-<?php echo esc_attr($unique_id); ?> .tappersia-carusel-next > div { transform: rotate(<?php echo $is_rtl ? '-135deg' : '45deg'; ?>); }
                .is-loaded .yab-hotel-card { animation: yab-fade-in 0.5s ease-in-out; }

                 #yab-hotel-carousel-<?php echo esc_attr($unique_id); ?> .swiper-pagination-bullet { background-color: <?php echo esc_attr($pagination_color); ?> !important; }
                #yab-hotel-carousel-<?php echo esc_attr($unique_id); ?> .swiper-pagination-bullet-active { background-color: <?php echo esc_attr($pagination_active_color); ?> !important; }
            </style>
            <div id="yab-hotel-carousel-<?php echo esc_attr($unique_id); ?>" dir="<?php echo esc_attr($direction); ?>">
                 <div style="max-width: <?php echo esc_attr($container_width); ?>px; margin: 0 auto; position: relative;">
                    <?php if(!empty($header_settings['text'])): ?>
                     <div style="margin-bottom: <?php echo esc_attr($header_settings['marginTop'] ?? 28); ?>px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 13px;">
                            <span style="font-size: <?php echo esc_attr($header_settings['fontSize'] ?? 24); ?>px; font-weight: <?php echo esc_attr($header_settings['fontWeight'] ?? '700'); ?>; color: <?php echo esc_attr($header_settings['color'] ?? '#000000'); ?>;"><?php echo esc_html($header_settings['text']); ?></span>
                            <?php if ($navigation_enabled): ?>
                            <div style="display: flex; gap: 7px; align-items: center;">
                               <div class="tappersia-carusel-perv"><div></div></div>
                               <div class="tappersia-carusel-next"><div></div></div>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div style="position: relative; text-align: <?php echo $is_rtl ? 'right' : 'left'; ?>;">
                            <div style="width: 100%; height: 1px; background-color: #E2E2E2; border-radius: 2px;"></div>
                            <div style="position: absolute; margin-top: -2px; width: 15px; height: 2px; background-color: <?php echo esc_attr($header_settings['lineColor'] ?? '#00BAA4'); ?>; border-radius: 2px; <?php echo $is_rtl ? 'right: 0;' : 'left: 0;'; ?>"></div>
                        </div>
                    </div>
                    <?php endif; ?>
                     <div class="swiper" style="overflow: hidden; padding-bottom: 10px; <?php echo $is_doubled ? 'height: ' . $grid_height . 'px;' : ''; ?>">
                        <div class="swiper-wrapper">
                            <?php
                            // Hotel Skeleton - Adapt if needed
                            $card_height_esc = esc_attr($card_settings['height'] ?? 375);
                            $image_height_esc = esc_attr($card_settings['imageHeight'] ?? 204);
                            $skeleton_html = <<<HTML
                                <div class="yab-hotel-card-skeleton yab-skeleton-loader" style="width: 295px; height: {$card_height_esc}px; background-color: #fff; border-radius: 14px; padding: 9px; display: flex; flex-direction: column; gap: 9px; overflow: hidden; border: 1px solid #e7e7e7;">
                                    <div class="yab-skeleton-image" style="width: 100%; height: {$image_height_esc}px; background-color: #e7e7e7; border-radius: 14px;"></div>
                                    <div style="padding: 14px 5px 5px 5px; display: flex; flex-direction: column; gap: 10px; flex-grow: 1;">
                                        <div class="yab-skeleton-text" style="width: 80%; height: 20px; background-color: #e7e7e7; border-radius: 4px;"></div>
                                        <div class="yab-skeleton-text" style="width: 50%; height: 16px; background-color: #e7e7e7; border-radius: 4px; margin-top: 5px;"></div>
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: auto; padding-bottom: 5px;">
                                            <div class="yab-skeleton-text" style="width: 40%; height: 20px; background-color: #e7e7e7; border-radius: 4px;"></div>
                                            <div class="yab-skeleton-text" style="width: 30%; height: 16px; background-color: #e7e7e7; border-radius: 4px;"></div>
                                        </div>
                                        <div class="yab-skeleton-text" style="height: 33px; width: 100%; margin-top: 10px; background-color: #e7e7e7; border-radius: 4px;"></div>
                                    </div>
                                </div>
HTML;
                            // Use hotel IDs
                            foreach ($slides_to_render as $hotel_id) : ?>
                                <div class="swiper-slide" data-hotel-id="<?php echo esc_attr($hotel_id); ?>">
                                    <?php echo $skeleton_html; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                     <?php if ($pagination_enabled): ?>
                    <div class="swiper-pagination" style="position: static; margin-top: 10px;"></div>
                    <?php endif; ?>
                </div>
            </div>
             <script>
            document.addEventListener('DOMContentLoaded', function() {
                const container = document.getElementById('yab-hotel-carousel-<?php echo esc_js($unique_id); ?>');
                if (!container) return;
                const swiperEl = container.querySelector('.swiper');
                const fetchedIds = new Set();
                const isRTL = <?php echo json_encode($is_rtl); ?>;
                const cardSettings = <?php echo json_encode($card_settings); ?>;

                const getCardBackground = (settings) => { /* ... (keep identical) ... */
                    if (!settings) return '#FFFFFF';
                    if (settings.backgroundType === 'gradient') {
                        const stops = (settings.gradientStops || []).map(s => `${s.color} ${s.stop}%`).join(', ');
                        return `linear-gradient(${settings.gradientAngle || 90}deg, ${stops})`;
                    }
                    return settings.bgColor || '#FFFFFF';
                 };

                 // --- generateHotelCardHTML ---
                const generateHotelCardHTML = (hotel) => {
                    if (!hotel) return '';
                    const minPrice = hotel.minPrice ? hotel.minPrice.toFixed(2) : '0.00';
                    const avgRating = hotel.avgRating ? (Math.floor(hotel.avgRating * 10) / 10) : null;
                    const ratingLabel = avgRating === null ? 'New' : (avgRating >= 4.6 ? 'Excellent' : (avgRating >= 4.1 ? 'Very Good' : (avgRating >= 3.6 ? 'Good' : (avgRating >= 3.0 ? 'Average' : 'Poor'))));
                    const starRating = hotel.star || 0;
                    const reviewCount = hotel.reviewCount || 0;
                    const coverImage = hotel.coverImage ? hotel.coverImage.url : 'https://placehold.co/295x204/e0e0e0/999999?text=No+Image';

                    const rtlFlex = isRTL ? 'row-reverse' : 'row';
                    const rtlTextAlign = isRTL ? 'right' : 'left';
                    const provincePos = isRTL ? `left: ${cardSettings.province.side}px;` : `right: ${cardSettings.province.side}px;`; // Reusing 'province' styles for city/location

                    return `
                    <div class="yab-hotel-card" style="
                        position: relative; text-decoration: none; color: inherit; display: block;
                        width: 295px; height: ${cardSettings.height}px;
                        background: ${getCardBackground(cardSettings)};
                        border: ${cardSettings.borderWidth}px solid ${cardSettings.borderColor};
                        border-radius: ${cardSettings.borderRadius}px;
                        overflow: hidden; display: flex; flex-direction: column;
                        padding: ${cardSettings.padding}px;
                        direction: ${isRTL ? 'rtl' : 'ltr'};">
                        <a href="${hotel.detailUrl || '#'}" target="_blank" style="text-decoration: none; color: inherit; display: flex; flex-direction: column; height: 100%;">
                            <div style="position: relative; width: 100%; height: ${cardSettings.imageHeight}px;">
                                <img src="${coverImage}" style="width: 100%; height: 100%; object-fit: cover; border-radius: ${cardSettings.borderRadius > 2 ? cardSettings.borderRadius - 2 : cardSettings.borderRadius}px;" />
                                <div style="position: absolute; bottom: ${cardSettings.province.bottom}px; ${provincePos} min-height: 23px; display: flex; align-items: center; justify-content: center; border-radius: 29px; background: ${cardSettings.province.bgColor}; padding: 0 11px; backdrop-filter: blur(${cardSettings.province.blur}px);">
                                    <span style="color: ${cardSettings.province.color}; font-size: ${cardSettings.province.fontSize}px; font-weight: ${cardSettings.province.fontWeight}; line-height: 24px;">${hotel.province.name}</span>
                                </div>
                            </div>
                            <div style="display: flex; flex-direction: column; justify-content: space-between; flex-grow: 1; padding: 14px 5px 5px 5px; text-align: ${rtlTextAlign};">
                                <div><h4 style="font-weight: ${cardSettings.title.fontWeight}; font-size: ${cardSettings.title.fontSize}px; line-height: ${cardSettings.title.lineHeight}; color: ${cardSettings.title.color}; text-overflow: ellipsis; overflow: hidden; white-space: wrap; margin: 0;">${hotel.title}</h4></div>
                                <div style="margin-top: 5px; display: flex; align-items: center; justify-content: ${rtlTextAlign === 'right' ? 'flex-end' : 'flex-start'};">
                                    <div style="color: #ffc107; display: flex;">
                                        ${[...Array(5)].map((_, i) => `<span style="font-size: 14px; width: 14px; height: 14px; line-height: 1;">${i < starRating ? '★' : '☆'}</span>`).join('')}
                                    </div>
                                </div>
                                <div style="margin-top: auto; margin-bottom: 10px; display: flex; align-items: center; justify-content: space-between; padding: 0 4px; direction: ltr; flex-direction: ${rtlFlex};">
                                    <div style="display: flex; flex-direction: row; gap: 5px; align-items: baseline;">
                                        ${avgRating !== null ? `<span style="font-size: ${cardSettings.rating.fontSize}px; font-weight: ${cardSettings.rating.fontWeight}; color: ${cardSettings.rating.color};">${avgRating}</span>` : ''}
                                        <span style="font-size: ${cardSettings.reviews.fontSize}px; font-weight: ${cardSettings.reviews.fontWeight}; color: ${cardSettings.reviews.color};">(${reviewCount} Reviews)</span>
                                    </div>
                                    <div style="display: flex; flex-direction: row; gap: 4px; align-items: baseline;">
                                        <span style="font-size: ${cardSettings.price.fontSize}px; font-weight: ${cardSettings.price.fontWeight}; color: ${cardSettings.price.color};">${'€' + minPrice}</span>
                                        <span style="font-size: ${cardSettings.duration.fontSize}px; font-weight: ${cardSettings.duration.fontWeight}; color: ${cardSettings.duration.color};">/ night</span>
                                    </div>
                                </div>
                                <div style="padding: 0 4px;">
                                    <div style="direction: ltr; display: flex; height: 33px; width: 100%; align-items: center; justify-content: space-between; border-radius: 5px; background-color: ${cardSettings.button.bgColor}; padding: 0 20px; text-decoration: none; flex-direction: ${rtlFlex};">
                                        <span style="font-size: ${cardSettings.button.fontSize}px; font-weight: ${cardSettings.button.fontWeight}; color: ${cardSettings.button.color};">View Details</span>
                                        <div style="width: ${cardSettings.button.arrowSize}px; height: ${cardSettings.button.arrowSize}px; border-top: 2px solid ${cardSettings.button.color}; border-right: 2px solid ${cardSettings.button.color}; transform: rotate(${isRTL ? '-135deg' : '45deg'}); border-radius: 2px;"></div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>`;
                };

                const fetchHotelData = (idsToFetch) => {
                    const uniqueIds = Array.from(new Set(idsToFetch)).filter(id => !fetchedIds.has(id));
                    if (uniqueIds.length === 0) return;
                    uniqueIds.forEach(id => fetchedIds.add(id));

                    const body = new URLSearchParams();
                    // *** ACTION NEEDED: Ensure 'yab_fetch_hotel_details_by_ids' exists ***
                    body.append('action', 'yab_fetch_hotel_details_by_ids');
                    uniqueIds.forEach(id => {
                        body.append('hotel_ids[]', id); // Changed parameter name
                    });

                    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: body
                    }).then(r => r.json()).then(res => {
                        if(res.success && Array.isArray(res.data)) res.data.forEach(hotel => {
                            container.querySelectorAll(`.swiper-slide[data-hotel-id="${hotel.id}"]`).forEach(slide => { // Changed data attribute
                                if(slide.querySelector('.yab-hotel-card-skeleton')) { // Check for hotel skeleton
                                    const image = new Image();
                                    const imageUrl = hotel.coverImage ? hotel.coverImage.url : 'https://placehold.co/295x204/e0e0e0/999999?text=No+Image';
                                    image.src = imageUrl;
                                    image.onload = () => {
                                        slide.innerHTML = generateHotelCardHTML(hotel);
                                        slide.classList.add('is-loaded');
                                    };
                                    image.onerror = () => { // Fallback
                                        slide.innerHTML = generateHotelCardHTML(hotel);
                                        slide.classList.add('is-loaded');
                                    };
                                }
                            });
                        });
                    }).catch(error => console.error('AJAX call failed!', error));
                };

                const checkAndLoadSlides = (swiper) => { /* ... (keep identical, just ensure data-hotel-id is used) ... */
                    if (!swiper || !swiper.slides || swiper.slides.length === 0) return;
                    const idsToFetch = new Set();
                    const slides = Array.from(swiper.slides);
                    const isGrid = swiper.params.grid && swiper.params.grid.rows > 1;
                    const rows = isGrid ? swiper.params.grid.rows : 1;
                    const slidesPerView = swiper.params.slidesPerView;
                    const slidesToLoadCount = (slidesPerView * rows) * 2;
                    const slidesToCheck = slides.slice(swiper.activeIndex, swiper.activeIndex + slidesToLoadCount);

                    if (slidesToCheck.length > 0) {
                        slidesToCheck.forEach(slide => { if(slide.dataset.hotelId) idsToFetch.add(parseInt(slide.dataset.hotelId, 10)); }); // Use hotelId
                    }
                    if (idsToFetch.size > 0) { fetchHotelData(Array.from(idsToFetch)); } // Call fetchHotelData
                 };

                 const swiperOptions = { /* ... (keep identical) ... */
                    slidesPerView: <?php echo esc_js($slides_per_view); ?>,
                    spaceBetween: <?php echo esc_js($space_between); ?>,
                    loop: <?php echo json_encode($loop); ?>,
                    dir: '<?php echo esc_js($direction); ?>',
                    on: { init: (s) => setTimeout(() => checkAndLoadSlides(s), 150), slideChange: checkAndLoadSlides, resize: checkAndLoadSlides }
                  };

                <?php if ($autoplay_enabled): ?> swiperOptions.autoplay = { delay: <?php echo esc_js($autoplay_delay); ?>, disableOnInteraction: false }; <?php endif; ?>
                <?php if ($navigation_enabled): ?> swiperOptions.navigation = { nextEl: container.querySelector('.tappersia-carusel-next'), prevEl: container.querySelector('.tappersia-carusel-perv') }; <?php endif; ?>
                <?php if ($pagination_enabled): ?> swiperOptions.pagination = { el: container.querySelector('.swiper-pagination'), clickable: true }; <?php endif; ?>
                <?php if ($is_doubled): ?>
                    swiperOptions.grid = { rows: 2, fill: '<?php echo esc_js($grid_fill); ?>' };
                    swiperOptions.slidesPerGroup = 1;
                    swiperOptions.spaceBetween = 20;
                <?php endif; ?>

                new Swiper(swiperEl, swiperOptions);
            });
            </script>
            <?php
            return ob_get_clean();
        }
    }
}
?>