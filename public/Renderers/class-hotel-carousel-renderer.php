<?php
// tappersia/public/Renderers/class-hotel-carousel-renderer.php

if (!class_exists('Yab_Hotel_Carousel_Renderer')) {
    require_once __DIR__ . '/class-abstract-banner-renderer.php';

    class Yab_Hotel_Carousel_Renderer extends Yab_Abstract_Banner_Renderer {

        public function render(): string {
            if (empty($this->data['hotel_carousel']) || empty($this->data['hotel_carousel']['selectedHotels'])) {
                return '';
            }

            $banner_id = $this->banner_id;
            $desktop_settings = $this->data['hotel_carousel']['settings'] ?? [];
            $mobile_settings = $this->data['hotel_carousel']['settings_mobile'] ?? $desktop_settings;
            $original_hotels_ids = $this->data['hotel_carousel']['selectedHotels'];

            ob_start();
            ?>
            <style>
                .yab-hotel-carousel-wrapper-<?php echo $banner_id; ?> .yab-hotel-carousel-mobile { display: none; }
                .yab-hotel-carousel-wrapper-<?php echo $banner_id; ?> .yab-hotel-carousel-desktop { display: block; }

                @media (max-width: 768px) {
                    .yab-hotel-carousel-wrapper-<?php echo $banner_id; ?> .yab-hotel-carousel-desktop { display: none; }
                    .yab-hotel-carousel-wrapper-<?php echo $banner_id; ?> .yab-hotel-carousel-mobile { display: block; }
                }
                
                /* START: Tag Styles */
                .hotel-label-base { margin-top: 7px; width: fit-content; border-radius: 3px; padding: 2px 6px; font-size: 11px; line-height: 1; }
                .hotel-label-luxury { background: #333333; color: #fff; }
                .hotel-label-business { background: #DAF6FF; color: #04A5D8; }
                .hotel-label-boutique { background: #f8f3b0; color: #a8a350; }
                .hotel-label-traditional { background: #FAECE0; color: #B68960; }
                .hotel-label-economy { background: #FFE9F7; color: #FF48C3; }
                .hotel-label-hostel { background: #B0B0B0; color: #FFF; }
                .hotel-label-default { background: #e0e0e0; color: #555; }
                /* END: Tag Styles */

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
            $card_settings = $settings['card'] ?? []; // We might not use card_settings if hardcoding HTML
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

            $slides_to_render = $original_hotels_ids;

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

            $card_width = 295; // New card width
            $container_width = ($card_width * $slides_per_view) + ($space_between * ($slides_per_view - 1));
            $grid_height = (357 + 20) * 2; // New card height (357) + spacing
            
            $unique_id = $banner_id . '_' . $view;

            ob_start();
            ?>
             <style>
                /* --- START: CONTROLS/PAGINATION FIX (Exact copy from tour) --- */
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
                #yab-hotel-carousel-<?php echo esc_attr($unique_id); ?> .tappersia-carusel-next {
                    <?php echo $is_rtl ? 'padding-left: 2px' : 'padding-right: 4px'; ?>
                }
                #yab-hotel-carousel-<?php echo esc_attr($unique_id); ?> .tappersia-carusel-perv {
                    <?php echo $is_rtl ? 'padding-right: 4px' : 'padding-left: 4px'; ?>
                }
                #yab-hotel-carousel-<?php echo esc_attr($unique_id); ?> .tappersia-carusel-next > div, 
                #yab-hotel-carousel-<?php echo esc_attr($unique_id); ?> .tappersia-carusel-perv > div { 
                    width: 10px; height: 10px; 
                    border-top: 2px solid black; border-right: 2px solid black; border-radius: 2px; 
                }
                #yab-hotel-carousel-<?php echo esc_attr($unique_id); ?> .tappersia-carusel-perv > div { 
                    transform: rotate(<?php echo $is_rtl ? '45deg' : '-135deg'; ?>); 
                }
                #yab-hotel-carousel-<?php echo esc_attr($unique_id); ?> .tappersia-carusel-next > div { 
                    transform: rotate(<?php echo $is_rtl ? '-135deg' : '45deg'; ?>); 
                }
                .is-loaded .yab-hotel-card { animation: yab-fade-in 0.5s ease-in-out; }

                #yab-hotel-carousel-<?php echo esc_attr($unique_id); ?> .swiper-pagination-bullet {
                    background-color: <?php echo esc_attr($pagination_color); ?> !important;
                }
                #yab-hotel-carousel-<?php echo esc_attr($unique_id); ?> .swiper-pagination-bullet-active {
                    background-color: <?php echo esc_attr($pagination_active_color); ?> !important;
                }
                /* --- END: CONTROLS/PAGINATION FIX --- */
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
                            // --- START: SKELETON FIX (Exact copy from tour) ---
                            $card_height_esc = '357'; // New height
                            $image_height_esc = '176'; // New image height
                            $skeleton_html = <<<HTML
                                <div class="yab-hotel-card-skeleton yab-skeleton-loader" style="width: 295px; height: {$card_height_esc}px; background-color: #fff; border-radius: 14px; padding: 9px; display: flex; flex-direction: column; gap: 9px; overflow: hidden; border: 1px solid #e7e7e7;">
                                    <div class="yab-skeleton-image" style="width: 100%; height: {$image_height_esc}px; background-color: #e7e7e7; border-radius: 14px;"></div>
                                    <div style="padding: 14px 19px 5px 19px; display: flex; flex-direction: column; gap: 10px; flex-grow: 1;">
                                        <div class="yab-skeleton-text" style="width: 80%; height: 20px; background-color: #e7e7e7; border-radius: 4px;"></div>
                                        <div class="yab-skeleton-text" style="width: 40%; height: 16px; background-color: transparent; border-radius: 4px;"></div>
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: auto; padding-bottom: 5px;">
                                            <div class="yab-skeleton-text" style="width: 40%; height: 20px; background-color: #e7e7e7; border-radius: 4px;"></div>
                                            <div class="yab-skeleton-text" style="width: 30%; height: 16px; background-color: #e7e7e7; border-radius: 4px;"></div>
                                        </div>
                                    </div>
                                </div>
HTML;
                            // --- END: SKELETON FIX ---
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
                
                // Helper functions for card generation
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

                // --- START: NEW CARD HTML FUNCTION ---
                const generateHotelCardHTML = (hotel) => {
                    if (!hotel) return '';
                    
                    const {
                        coverImage, isFeatured, discount = 0, minPrice = 0,
                        star = 0, title = 'N/A', avgRating, reviewCount = 0,
                        customTags = [], detailUrl = '#'
                    } = hotel;

                    const imageUrl = coverImage ? coverImage.url : 'https://placehold.co/276x176/e0e0e0/999999?text=No+Image';

                    // Logic
                    const hasDiscount = discount > 0;
                    const discountPercentage = hasDiscount ? Math.round(discount / (minPrice + discount) * 100) : 0;
                    const originalPrice = hasDiscount ? (minPrice + discount).toFixed(2) : 0;
                    
                    let stars = '';
                    for (let i = 0; i < 5; i++) {
                        stars += (i < star) ? '★' : '☆';
                    }
                    const starText = `${star} Star`;
                    
                    const ratingScore = avgRating ? (Math.floor(avgRating * 10) / 10) : null;
                    const ratingLabel = getRatingLabel(ratingScore);

                    const tagsHtml = customTags.map(tag => 
                        `<span class="${getTagClass(tag)} hotel-label-base" style="margin-top: 7px; width: fit-content; border-radius: 3px; padding: 2px 6px; font-size: 11px; line-height: 1; display: inline-block;">${escapeHTML(tag)}</span>`
                    ).join('');
                    
                    const escapeHTML = (str) => str.replace(/[&<>"']/g, function(m) {
                        return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[m];
                    });

                    return `
                    <div name="card" class="yab-hotel-card" style="margin: 3px; min-height: 357px; width: 295px; border-radius: 16px; border: 1px solid #E5E5E5; padding: 9px; background: #fff; box-sizing: border-box; font-family: 'Roboto', sans-serif;">
                      <a href="${escapeHTML(detailUrl)}" target="_blank" style="text-decoration: none; color: inherit;">
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
                        <div name="body-content" style="margin: 14px 19px 0 19px;">
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
                };
                // --- END: NEW CARD HTML FUNCTION ---
                
                const fetchHotelData = (idsToFetch) => {
                    const uniqueIds = Array.from(new Set(idsToFetch)).filter(id => !fetchedIds.has(id));
                    if (uniqueIds.length === 0) return;
                    uniqueIds.forEach(id => fetchedIds.add(id));
                    
                    const body = new URLSearchParams();
                    body.append('action', 'yab_fetch_hotel_details_by_ids');
                    uniqueIds.forEach(id => {
                        body.append('hotel_ids[]', id);
                    });

                    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: body
                    }).then(r => r.json()).then(res => {
                        if(res.success && Array.isArray(res.data)) res.data.forEach(hotel => {
                            container.querySelectorAll(`.swiper-slide[data-hotel-id="${hotel.id}"]`).forEach(slide => {
                                if(slide.querySelector('.yab-hotel-card-skeleton')) {
                                    const image = new Image();
                                    const imageUrl = hotel.coverImage ? hotel.coverImage.url : 'https://placehold.co/276x176/e0e0e0/999999?text=No+Image';
                                    image.src = imageUrl;
                                    image.onload = () => { 
                                        slide.innerHTML = generateHotelCardHTML(hotel); 
                                        slide.classList.add('is-loaded'); 
                                    };
                                    image.onerror = () => {
                                        slide.innerHTML = generateHotelCardHTML(hotel); 
                                        slide.classList.add('is-loaded'); 
                                    };
                                }
                            });
                        });
                    }).catch(error => console.error('AJAX call failed!', error));
                };
                
                const checkAndLoadSlides = (swiper) => {
                    if (!swiper || !swiper.slides || swiper.slides.length === 0) return;
                    const idsToFetch = new Set();
                    const slides = Array.from(swiper.slides);
                    const isGrid = swiper.params.grid && swiper.params.grid.rows > 1;
                    const rows = isGrid ? swiper.params.grid.rows : 1;
                    const slidesPerView = swiper.params.slidesPerView;
                    const slidesToLoadCount = (slidesPerView * rows) * 2;
                    const slidesToCheck = slides.slice(swiper.activeIndex, swiper.activeIndex + slidesToLoadCount);
                    
                    if (slidesToCheck.length > 0) {
                        slidesToCheck.forEach(slide => { if(slide.dataset.hotelId) idsToFetch.add(parseInt(slide.dataset.hotelId, 10)); });
                    }
                    if (idsToFetch.size > 0) { fetchHotelData(Array.from(idsToFetch)); }
                };
                
                 const swiperOptions = {
                    slidesPerView: <?php echo esc_js($slides_per_view); ?>,
                    spaceBetween: <?php echo esc_js($space_between); ?>,
                    loop: <?php echo json_encode($loop); ?>,
                    dir: '<?php echo esc_js($direction); ?>',
                    on: { init: (s) => setTimeout(() => checkAndLoadSlides(s), 150), slideChange: checkAndLoadSlides, resize: checkAndLoadSlides }
                };
                
                // --- START: CONTROLS FIX (Exact copy from tour) ---
                <?php if ($autoplay_enabled): ?> swiperOptions.autoplay = { delay: <?php echo esc_js($autoplay_delay); ?>, disableOnInteraction: false }; <?php endif; ?>
                <?php if ($navigation_enabled): ?> swiperOptions.navigation = { nextEl: container.querySelector('.tappersia-carusel-next'), prevEl: container.querySelector('.tappersia-carusel-perv') }; <?php endif; ?>
                <?php if ($pagination_enabled): ?> swiperOptions.pagination = { el: container.querySelector('.swiper-pagination'), clickable: true }; <?php endif; ?>
                <?php if ($is_doubled): ?> 
                    swiperOptions.grid = { rows: 2, fill: '<?php echo esc_js($grid_fill); ?>' }; 
                    swiperOptions.slidesPerGroup = 1; 
                    swiperOptions.spaceBetween = 20;
                <?php endif; ?>
                // --- END: CONTROLS FIX ---

                new Swiper(swiperEl, swiperOptions);
            });
            </script>
            <?php
            return ob_get_clean();
        }
    }
}
?>