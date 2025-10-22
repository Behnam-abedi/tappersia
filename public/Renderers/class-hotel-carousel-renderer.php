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
                /* General Wrapper Styles */
                .yab-hotel-carousel-wrapper-<?php echo $banner_id; ?> {
                    font-family: 'Roboto', sans-serif !important; /* Ensure font */
                }
                 .yab-hotel-carousel-wrapper-<?php echo $banner_id; ?> * {
                    font-family: inherit !important;
                 }

                /* Responsive Display */
                .yab-hotel-carousel-wrapper-<?php echo $banner_id; ?> .yab-hotel-carousel-mobile { display: none; }
                .yab-hotel-carousel-wrapper-<?php echo $banner_id; ?> .yab-hotel-carousel-desktop { display: block; }

                @media (max-width: 768px) {
                    .yab-hotel-carousel-wrapper-<?php echo $banner_id; ?> .yab-hotel-carousel-desktop { display: none; }
                    .yab-hotel-carousel-wrapper-<?php echo $banner_id; ?> .yab-hotel-carousel-mobile { display: block; }
                }

                /* Tag Styles (Scoped) */
                #yab-hotel-carousel-<?php echo esc_attr($banner_id . '_desktop'); ?> .hotel-label-base,
                #yab-hotel-carousel-<?php echo esc_attr($banner_id . '_mobile'); ?> .hotel-label-base {
                     margin-top: 7px; width: fit-content; border-radius: 3px; padding: 2px 6px; font-size: 11px; line-height: 1; display: inline-block; box-sizing: border-box;
                }
                #yab-hotel-carousel-<?php echo esc_attr($banner_id . '_desktop'); ?> .hotel-label-luxury,
                #yab-hotel-carousel-<?php echo esc_attr($banner_id . '_mobile'); ?> .hotel-label-luxury { background: #333333; color: #fff; }
                #yab-hotel-carousel-<?php echo esc_attr($banner_id . '_desktop'); ?> .hotel-label-business,
                #yab-hotel-carousel-<?php echo esc_attr($banner_id . '_mobile'); ?> .hotel-label-business { background: #DAF6FF; color: #04A5D8; }
                #yab-hotel-carousel-<?php echo esc_attr($banner_id . '_desktop'); ?> .hotel-label-boutique,
                #yab-hotel-carousel-<?php echo esc_attr($banner_id . '_mobile'); ?> .hotel-label-boutique { background: #f8f3b0; color: #a8a350; }
                #yab-hotel-carousel-<?php echo esc_attr($banner_id . '_desktop'); ?> .hotel-label-traditional,
                #yab-hotel-carousel-<?php echo esc_attr($banner_id . '_mobile'); ?> .hotel-label-traditional { background: #FAECE0; color: #B68960; }
                #yab-hotel-carousel-<?php echo esc_attr($banner_id . '_desktop'); ?> .hotel-label-economy,
                #yab-hotel-carousel-<?php echo esc_attr($banner_id . '_mobile'); ?> .hotel-label-economy { background: #FFE9F7; color: #FF48C3; }
                #yab-hotel-carousel-<?php echo esc_attr($banner_id . '_desktop'); ?> .hotel-label-hostel,
                #yab-hotel-carousel-<?php echo esc_attr($banner_id . '_mobile'); ?> .hotel-label-hostel { background: #B0B0B0; color: #FFF; }
                #yab-hotel-carousel-<?php echo esc_attr($banner_id . '_desktop'); ?> .hotel-label-default,
                #yab-hotel-carousel-<?php echo esc_attr($banner_id . '_mobile'); ?> .hotel-label-default { background: #e0e0e0; color: #555; }

                /* Skeleton Animation (FIX: Lighter shimmer) */
                 .yab-hotel-carousel-wrapper-<?php echo $banner_id; ?> .yab-skeleton-loader {
                    position: relative;
                    overflow: hidden;
                    background-color: #ffffff; /* Lighter base color */
                 }
                .yab-hotel-carousel-wrapper-<?php echo $banner_id; ?> .yab-skeleton-loader > div {
                    background-color: #f0f0f0 !important; /* Lighter elements */
                }
                 .yab-hotel-carousel-wrapper-<?php echo $banner_id; ?> .yab-skeleton-loader [name="description-skeleton"] div {
                    background-color: #f0f0f0 !important;
                 }
                .yab-hotel-carousel-wrapper-<?php echo $banner_id; ?> .yab-skeleton-loader::before {
                    content: '';
                    position: absolute;
                    inset: 0;
                    transform: translateX(-100%);
                    background: linear-gradient(90deg, transparent, rgba(0, 0, 0, 0.05), transparent); /* Softer shimmer */
                    animation: yab-shimmer-<?php echo $banner_id; ?> 1.5s infinite;
                }
                 @keyframes yab-shimmer-<?php echo $banner_id; ?> { 100% { transform: translateX(100%); } }

                /* Fade-in Animation */
                .yab-hotel-carousel-wrapper-<?php echo $banner_id; ?> .is-loaded .yab-hotel-card {
                     animation: yab-fade-in-<?php echo $banner_id; ?> 0.5s ease-in-out;
                 }
                 @keyframes yab-fade-in-<?php echo $banner_id; ?> { from { opacity: 0; } to { opacity: 1; } }
                 
                 /* FIX: Remove link focus outline */
                 .yab-hotel-carousel-wrapper-<?php echo $banner_id; ?> .yab-hotel-card a:focus,
                 .yab-hotel-carousel-wrapper-<?php echo $banner_id; ?> .yab-hotel-card a:active {
                    outline: none !important;
                    box-shadow: none !important;
                    -webkit-tap-highlight-color: transparent; /* For mobile */
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
            $hotel_count = count($original_hotels_ids);

            $slides_per_view = $settings['slidesPerView'] ?? ($view === 'desktop' ? 3 : 1);
            $space_between = $settings['spaceBetween'] ?? ($view === 'desktop' ? 22 : 15); // Adjust mobile spacing
            $loop = $settings['loop'] ?? false;
            $is_doubled = ($view === 'desktop') && ($settings['isDoubled'] ?? false); // Doubled only for desktop
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

            // Loop logic (same as before)
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
                     // Ensure enough items, but not excessively many
                    $final_items = array_slice($final_items, 0, max($min_loop_count, $hotel_count * 2));
                }
                $slides_to_render = $final_items;
            }

            $card_width = 295;
             // Container width logic correction for single slide view
            $container_width = ($view === 'desktop' || $slides_per_view > 1)
                ? (($card_width * $slides_per_view) + ($space_between * ($slides_per_view - 1)))
                : $card_width;
            $grid_height = (357 * 2) + 20; // New card height

            $unique_id = $banner_id . '_' . $view;

            ob_start();
            ?>
             <style>
                /* Scoped Swiper Styles */
                #yab-hotel-carousel-<?php echo esc_attr($unique_id); ?> .swiper-slide {
                    width: <?php echo $card_width; ?>px !important;
                    box-sizing: border-box; /* Ensure padding/border are included */
                    <?php if ($is_doubled): ?>
                        height: calc((100% - <?php echo esc_js($space_between); ?>px) / 2) !important;
                        margin-bottom: <?php echo esc_js($space_between); ?>px !important;
                    <?php else: ?>
                        height: auto !important; /* Ensure single row slides have auto height */
                    <?php endif; ?>
                }
                /* Navigation Buttons */
                #yab-hotel-carousel-<?php echo esc_attr($unique_id); ?> .tappersia-carusel-next,
                #yab-hotel-carousel-<?php echo esc_attr($unique_id); ?> .tappersia-carusel-perv {
                    background: white; border-radius: 8px; box-shadow: inset 0 0 0 2px #E5E5E5;
                    display: flex; align-items: center; justify-content: center;
                    z-index: 10; cursor: pointer; width: 36px; height: 36px; box-sizing: border-box;
                }
                #yab-hotel-carousel-<?php echo esc_attr($unique_id); ?> .tappersia-carusel-next { <?php echo $is_rtl ? 'padding-left: 2px' : 'padding-right: 4px'; ?> }
                #yab-hotel-carousel-<?php echo esc_attr($unique_id); ?> .tappersia-carusel-perv { <?php echo $is_rtl ? 'padding-right: 4px' : 'padding-left: 4px'; ?> }

                 #yab-hotel-carousel-<?php echo esc_attr($unique_id); ?> .tappersia-carusel-next > div,
                 #yab-hotel-carousel-<?php echo esc_attr($unique_id); ?> .tappersia-carusel-perv > div {
                    width: 10px; height: 10px;
                    border-top: 2px solid black; border-right: 2px solid black; border-radius: 2px;
                }
                 #yab-hotel-carousel-<?php echo esc_attr($unique_id); ?> .tappersia-carusel-perv > div { transform: rotate(<?php echo $is_rtl ? '45deg' : '-135deg'; ?>); }
                 #yab-hotel-carousel-<?php echo esc_attr($unique_id); ?> .tappersia-carusel-next > div { transform: rotate(<?php echo $is_rtl ? '-135deg' : '45deg'; ?>); }

                /* Pagination */
                 #yab-hotel-carousel-<?php echo esc_attr($unique_id); ?> .swiper-pagination-bullet {
                    background-color: <?php echo esc_attr($pagination_color); ?> !important;
                    width: 20px !important; height: 6px !important; border-radius: 4px !important; opacity: 1 !important;
                 }
                 #yab-hotel-carousel-<?php echo esc_attr($unique_id); ?> .swiper-pagination-bullet-active {
                    background-color: <?php echo esc_attr($pagination_active_color); ?> !important;
                 }
                /* Disabled state */
                 #yab-hotel-carousel-<?php echo esc_attr($unique_id); ?> .swiper-button-disabled {
                     opacity: 0.5 !important; cursor: not-allowed !important; pointer-events: none !important;
                 }
            </style>
            <div id="yab-hotel-carousel-<?php echo esc_attr($unique_id); ?>" dir="<?php echo esc_attr($direction); ?>">
                 <div style="max-width: <?php echo esc_attr($container_width); ?>px; margin: 0 auto; position: relative;">
                    <?php if(!empty($header_settings['text'])): ?>
                     <div style="margin-bottom: <?php echo esc_attr($header_settings['marginTop'] ?? 28); ?>px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 13px;">
                            <span style="font-size: <?php echo esc_attr($header_settings['fontSize'] ?? 24); ?>px; font-weight: <?php echo esc_attr($header_settings['fontWeight'] ?? '700'); ?>; color: <?php echo esc_attr($header_settings['color'] ?? '#000000'); ?>; line-height: 1.2;"><?php echo esc_html($header_settings['text']); ?></span>
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
                            // --- SKELETON LOADER HTML (FIXED) ---
                            $card_height_esc = '357';
                            $image_height_esc = '176';
                            // Skeleton now uses m-0 to match card, and lighter bg-colors
                            $skeleton_html = <<<HTML
                                <div name="card-skeleton" class="yab-skeleton-loader" style="margin: 0; min-height: {$card_height_esc}px; width: {$card_width}px; border-radius: 16px; border: 1px solid #E5E5E5; padding: 9px; background-color: #ffffff; box-sizing: border-box; overflow: hidden;">
                                  <div style="height: {$image_height_esc}px; width: 276px; border-radius: 14px; background-color: #f0f0f0;"></div>
                                  <div style="margin: 14px 19px 0 19px;">
                                    <div style="min-height: 34px; width: 100%; margin-bottom: 7px;">
                                      <div style="height: 16px; background-color: #f0f0f0; border-radius: 4px; width: 75%; margin-bottom: 8px;"></div>
                                      <div style="height: 16px; background-color: #f0f0f0; border-radius: 4px; width: 50%;"></div>
                                    </div>
                                    <div name="description-skeleton">
                                      <div name="rating-skeleton" style="display: flex; flex-direction: row; align-items: center; gap: 6px;">
                                        <div style="height: 20px; width: 32px; border-radius: 3px; background-color: #f0f0f0;"></div>
                                        <div style="height: 16px; background-color: #f0f0f0; border-radius: 4px; width: 64px;"></div>
                                        <div style="height: 12px; background-color: #f0f0f0; border-radius: 4px; width: 32px;"></div>
                                      </div>
                                      <div name="tags-skeleton" style="margin-top: 7px; display: flex; flex-direction: row; gap: 5px;">
                                        <div style="height: 20px; width: 48px; border-radius: 3px; background-color: #f0f0f0;"></div>
                                        <div style="height: 20px; width: 48px; border-radius: 3px; background-color: #f0f0f0;"></div>
                                      </div>
                                    </div>
                                    <hr style="margin: 9.5px 0 7.5px 0; border: 0; border-top: 1px solid #EEEEEE;" />
                                    <div name="price-skeleton" style="display: flex; flex-direction: column;">
                                      <div style="height: 12px; background-color: #f0f0f0; border-radius: 4px; width: 32px; margin-bottom: 4px;"></div>
                                      <div style="display: flex; flex-direction: row; align-items: center; justify-content: space-between;">
                                        <div style="display: flex; align-items: center; gap: 5px;">
                                          <div style="height: 20px; background-color: #f0f0f0; border-radius: 4px; width: 64px;"></div>
                                          <div style="height: 16px; background-color: #f0f0f0; border-radius: 4px; width: 40px;"></div>
                                        </div>
                                        <div style="height: 12px; background-color: #f0f0f0; border-radius: 4px; width: 48px;"></div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
HTML;
                            // --- END SKELETON LOADER ---

                            foreach ($slides_to_render as $hotel_id) : ?>
                                <div class="swiper-slide" data-hotel-id="<?php echo esc_attr($hotel_id); ?>">
                                    <?php echo $skeleton_html; // Output skeleton ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                     <?php if ($pagination_enabled): ?>
                    <div class="swiper-pagination" style="position: static; margin-top: 10px; line-height: 1;"></div>
                    <?php endif; ?>
                </div>
            </div>
             <script>
            // IIFE to avoid global scope pollution
            (function() {
                // Use a function to ensure DOMContentLoaded has fired
                var initSwiper = function() {
                    const container = document.getElementById('yab-hotel-carousel-<?php echo esc_js($unique_id); ?>');
                    if (!container || container.classList.contains('yab-swiper-initialized')) {
                        // Prevent re-initialization if already done or container not found
                        return;
                    }
                    container.classList.add('yab-swiper-initialized'); // Mark as initialized

                    const swiperEl = container.querySelector('.swiper');
                    if (!swiperEl) {
                         console.error("Swiper element not found for <?php echo esc_js($unique_id); ?>.");
                         return;
                    }

                    const fetchedIds = new Set();
                    const isRTL = <?php echo json_encode($is_rtl); ?>;
                    const ajaxUrl = '<?php echo admin_url('admin-ajax.php'); ?>';

                    // --- Helper Functions ---
                     const RatingLabel = { Excellent: 'Excellent', VeryGood: 'Very Good', Good: 'Good', Average: 'Average', Poor: 'Poor', New: 'New' };
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
                        if (typeof str !== 'string') str = String(str || '');
                        const p = document.createElement('p');
                        p.textContent = str;
                        return p.innerHTML;
                     };
                    // --- End Helper Functions ---

                    // --- Card Generation Function ---
                    const generateHotelCardHTML = (hotel) => {
                        if (!hotel) return ''; // Should not happen

                        const { coverImage, isFeatured = false, discount = 0, minPrice = 0, star = 0, title = 'N/A', avgRating, reviewCount = 0, customTags = [], detailUrl = '#' } = hotel;
                        const imageUrl = coverImage?.url || 'https://placehold.co/276x176/e0e0e0/cccccc?text=No+Image';
                        const hasDiscount = discount > 0 && minPrice > 0;
                        const discountPercentage = hasDiscount ? Math.round((discount / (minPrice + discount)) * 100) : 0;
                        const originalPrice = hasDiscount ? (minPrice + discount).toFixed(2) : '0.00';
                        const stars = '★'.repeat(star) + '☆'.repeat(5 - star);
                        const starText = `${star} Star`;
                        const ratingScore = avgRating != null ? (Math.round(avgRating * 10) / 10).toFixed(1) : null;
                        const ratingLabel = getRatingLabel(avgRating);
                        const tagsHtml = (customTags || []).map(tag => `<span class="${getTagClass(tag)} hotel-label-base">${escapeHTML(tag)}</span>`).join('');

                        // Using template literals and styles inspired by Tailwind classes
                        return `
                        <div name="card" class="yab-hotel-card" style="margin: 0; min-height: 357px; width: 295px; border-radius: 16px; border: 1px solid #E5E5E5; padding: 9px; background: #fff; box-sizing: border-box; font-family: 'Roboto', sans-serif;">
                          <a href="${escapeHTML(detailUrl)}" target="_blank" style="text-decoration: none; color: inherit; display: block; outline: none; -webkit-tap-highlight-color: transparent;"> {/* FIX: Added outline: none & tap highlight */}
                            <div style="position: relative; height: 176px; width: 276px; border-radius: 14px;" name="header-content-image">
                              <div style="position: absolute; z-index: 10; display: flex; height: 100%; width: 100%; flex-direction: column; justify-content: space-between; padding: 13px; box-sizing: border-box;">
                                <div style="display: flex; width: 100%; align-items: flex-start; justify-content: space-between;">
                                  ${isFeatured ? `<div style="display: flex; width: fit-content; align-items: center; justify-content: center; border-radius: 20px; background: #F66A05; padding: 5px 7px; font-size: 12px; line-height: 1; font-weight: 500; color: #ffffff;">Best Seller</div>` : '<div></div>'}
                                  ${hasDiscount && discountPercentage > 0 ? `<div style="display: flex; width: fit-content; align-items: center; justify-content: center; border-radius: 20px; background: #FB2D51; padding: 5px 10px; font-size: 12px; line-height: 1; font-weight: 500; color: #ffffff;">${discountPercentage}%</div>` : ''}
                                </div>
                                <div style="display: flex; flex-direction: row; align-items: center; gap: 5px; align-self: flex-start;">
                                  <div style="font-size: 17px; color: #FCC13B; line-height: 0.7;">${stars}</div>
                                  <div style="font-size: 12px; line-height: 13px; color: white;">${escapeHTML(starText)}</div>
                                </div>
                              </div>
                              <div name="black-highlight" style="position: absolute; display: flex; height: 100%; width: 100%; align-items: flex-end; border-radius: 0 0 14px 14px; background-image: linear-gradient(to top, rgba(0,0,0,0.83) 0%, rgba(0,0,0,0) 38%, rgba(0,0,0,0) 100%);"></div>
                              <img src="${escapeHTML(imageUrl)}" alt="${escapeHTML(title)}" style="height: 100%; width: 100%; border-radius: 14px; object-fit: cover;" />
                            </div>
                            <div name="body-content" style="margin: 14px 19px 0 19px; color: #333;">
                              <div name="title" style="min-height: 34px; width: 100%;">
                                <h4 style="font-size: 14px; line-height: 17px; font-weight: 600; color: #333333; margin: 0; overflow: hidden; display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 2;">${escapeHTML(title)}</h4>
                              </div>
                              <div name="description">
                                <div name="rating" style="margin-top: 7px; display: flex; flex-direction: row; align-items: center; gap: 6px;">
                                  ${ratingScore !== null ? `<div name="rate"><span style="width: fit-content; border-radius: 3px; background: #5191FA; padding: 2px 6px; font-size: 11px; line-height: 1; color: #ffffff;">${ratingScore}</span></div>` : ''}
                                  <div name="text-rate" style="font-size: 12px; line-height: 15px; color: #333333;padding-top:1px"> {/* FIX: Added padding-top for alignment */}
                                    <span>${escapeHTML(ratingLabel)}</span>
                                  </div>
                                  <div name="rate-count" style="font-size: 10px; line-height: 12px; color: #999999;">
                                    <span>(${reviewCount})</span>
                                  </div>
                                </div>
                                <div name="tags">
                                  <div style="display: flex; flex-direction: row; flex-wrap: wrap; gap: 5px;">
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
                    // --- End Card Generation ---

                    // --- Data Fetching ---
                    const fetchHotelData = (idsToFetch) => {
                        const uniqueIds = Array.from(new Set(idsToFetch)).filter(id => !fetchedIds.has(id));
                        if (uniqueIds.length === 0) return;
                        uniqueIds.forEach(id => fetchedIds.add(id));

                        const body = new URLSearchParams();
                        body.append('action', 'yab_fetch_hotel_details_by_ids');
                        uniqueIds.forEach(id => body.append('hotel_ids[]', id));

                        fetch(ajaxUrl, { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: body })
                            .then(r => r.ok ? r.json() : Promise.reject(`HTTP error! status: ${r.status}`))
                            .then(res => {
                                if (res.success && Array.isArray(res.data)) {
                                    res.data.forEach(hotel => {
                                        if (!hotel || !hotel.id) return; // Skip if invalid data
                                        container.querySelectorAll(`.swiper-slide[data-hotel-id="${hotel.id}"]`).forEach(slide => {
                                            if (slide.querySelector('[name="card-skeleton"]')) { // Check if it's still a skeleton
                                                 // Preload image before replacing skeleton
                                                 const img = new Image();
                                                 const imageUrl = hotel.coverImage?.url || 'https://placehold.co/276x176/e0e0e0/cccccc?text=No+Image';
                                                 img.onload = () => {
                                                     slide.innerHTML = generateHotelCardHTML(hotel);
                                                     slide.classList.add('is-loaded');
                                                 };
                                                 img.onerror = () => { // Fallback if image fails
                                                     slide.innerHTML = generateHotelCardHTML(hotel);
                                                     slide.classList.add('is-loaded');
                                                 };
                                                 img.src = imageUrl;
                                            }
                                        });
                                    });
                                } else {
                                    console.error('AJAX error or invalid data:', res.data?.message || 'Unknown error');
                                }
                            })
                            .catch(error => console.error('AJAX call failed!', error));
                    };
                    // --- End Data Fetching ---

                    // --- Swiper Logic ---
                    const checkAndLoadSlides = (swiper) => {
                        if (!swiper || !swiper.slides || swiper.slides.length === 0 || !swiper.params) return;
                        const idsToFetch = new Set();
                        const slides = Array.from(swiper.slides);
                        const isGrid = swiper.params.grid && swiper.params.grid.rows > 1;
                        const rows = isGrid ? swiper.params.grid.rows : 1;

                        let slidesPerView = 1;
                        if(swiper.params.slidesPerView && swiper.params.slidesPerView !== 'auto'){
                            slidesPerView = parseInt(swiper.params.slidesPerView, 10);
                            if (isNaN(slidesPerView)) slidesPerView = 1;
                        } else if (swiper.params.slidesPerView === 'auto') {
                             slidesPerView = swiper.visibleSlides ? swiper.visibleSlides.length : 1;
                        }

                        const startIndex = Math.max(0, swiper.activeIndex || 0); // Default activeIndex to 0 if undefined
                        const slidesToLoadCount = Math.max(1, (slidesPerView * rows) * 2);
                        const slidesToCheck = slides.slice(startIndex, startIndex + slidesToLoadCount);

                        slidesToCheck.forEach(slide => {
                            const hotelId = parseInt(slide.dataset.hotelId, 10);
                            if (!isNaN(hotelId)) idsToFetch.add(hotelId);
                        });

                        if (idsToFetch.size > 0) fetchHotelData(Array.from(idsToFetch));
                    };

                     const swiperOptions = {
                        slidesPerView: <?php echo esc_js($slides_per_view); ?>,
                        spaceBetween: <?php echo esc_js($space_between); ?>,
                        loop: <?php echo json_encode($loop); ?>,
                        dir: '<?php echo esc_js($direction); ?>',
                        on: {
                            init: (s) => setTimeout(() => checkAndLoadSlides(s), 150), // Delay initial load slightly
                            slideChange: checkAndLoadSlides,
                            resize: checkAndLoadSlides
                        },
                        observer: true, // For dynamic content changes
                        observeParents: true, // If container resizes
                    };

                     <?php if ($autoplay_enabled): ?> swiperOptions.autoplay = { delay: <?php echo esc_js($autoplay_delay); ?>, disableOnInteraction: false }; <?php endif; ?>
                     <?php if ($navigation_enabled): ?> swiperOptions.navigation = { nextEl: container.querySelector('.tappersia-carusel-next'), prevEl: container.querySelector('.tappersia-carusel-perv') }; <?php endif; ?>
                     <?php if ($pagination_enabled): ?> swiperOptions.pagination = { el: container.querySelector('.swiper-pagination'), clickable: true }; <?php endif; ?>
                     <?php if ($is_doubled): ?>
                        swiperOptions.grid = { rows: 2, fill: '<?php echo esc_js($grid_fill); ?>' };
                        swiperOptions.slidesPerGroup = 1;
                        swiperOptions.spaceBetween = 20; // Override spaceBetween for grid
                     <?php endif; ?>

                     // Initialize Swiper only if the element exists
                     if (typeof Swiper !== 'undefined') {
                         try {
                             new Swiper(swiperEl, swiperOptions);
                         } catch (e) {
                             console.error("Swiper initialization failed for <?php echo esc_js($unique_id); ?>:", e);
                         }
                     } else {
                         console.error("Swiper library not loaded for <?php echo esc_js($unique_id); ?>.");
                     }
                    // --- End Swiper Logic ---
                };
                
                // Wait for DOMContentLoaded to ensure Swiper is loaded and elements exist
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', initSwiper);
                } else {
                    // DOM already loaded
                    initSwiper();
                }
            })(); // End IIFE
            </script>
            <?php
            return ob_get_clean();
        }
    }
}
?>

