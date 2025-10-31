<?php
// tappersia/admin/views/banner-types/promotion-banner/promotion-banner-preview.php
?>

<div class="bg-[#434343] p-4 rounded-lg">
    <h3 class="preview-title">Live Preview</h3>

    <transition name="fade" mode="out-in">
        <div v-if="currentView === 'desktop'" class="flex flex-col items-center">
            <span class="text-xs text-gray-400 mb-2">Desktop View</span>
            <div class="flex justify-center w-full bg-[#292929] rounded-lg p-8">
                <div class="yab-promo-banner-wrapper" 
                     :style="{ 
                        borderRadius: `${banner.promotion.borderRadius}px`,
                        overflow: 'hidden',
                        width: '100%',
                        position: 'relative',
                        direction: banner.promotion.direction
                     }">
                    
                    <div :style="{
                        position: 'absolute', top: 0, left: 0, right: 0, bottom: 0,
                        borderRadius: 'inherit',
                        boxShadow: `inset 0 0 0 ${banner.promotion.borderWidth}px ${banner.promotion.borderColor}`,
                        zIndex: 10,
                        pointerEvents: 'none'
                    }"></div>

                    <div class="yab-promo-header"
                         :style="{ 
                            background: getPromoBackgroundStyle(banner.promotion, 'header'),
                            padding: `${banner.promotion.headerPaddingY}px ${banner.promotion.headerPaddingX}px`,
                            display: 'flex', alignItems: 'center', gap: '10px',
                            flexDirection: banner.promotion.direction === 'rtl' ? 'row-reverse' : 'row'
                         }">
                        <img v-if="banner.promotion.iconUrl" :src="banner.promotion.iconUrl" :style="{width: `${banner.promotion.iconSize}px`, height: `${banner.promotion.iconSize}px`}" />
                        <span :style="{
                            color: banner.promotion.headerTextColor,
                            fontSize: `${banner.promotion.headerFontSize}px`,
                            fontWeight: banner.promotion.headerFontWeight,
                            textAlign: banner.promotion.direction === 'rtl' ? 'right' : 'left',
                            flexGrow: 1
                        }">{{ banner.promotion.headerText }}</span>
                    </div>
                    <div class="yab-promo-body"
                         :style="{
                            background: getPromoBackgroundStyle(banner.promotion, 'body'),
                            padding: `${banner.promotion.bodyPaddingY}px ${banner.promotion.bodyPaddingX}px`
                         }">
                         <p :style="{
                            color: banner.promotion.bodyTextColor,
                            fontSize: `${banner.promotion.bodyFontSize}px`,
                            fontWeight: banner.promotion.bodyFontWeight,
                            lineHeight: banner.promotion.bodyLineHeight,
                            textAlign: banner.promotion.direction === 'rtl' ? 'right' : 'left',
                            margin: 0
                         }" v-html="previewBodyText"></p>
                    </div>
                </div>
                </div>
        </div>

        <div v-else-if="currentView === 'mobile'" class="flex flex-col items-center">
             <span class="text-xs text-gray-400 mb-2">Mobile View</span>
            <div class="w-[375px] h-auto bg-[#292929] rounded-2xl p-4 flex justify-center items-center mx-auto">
                <div class="yab-promo-banner-wrapper" 
                     :style="{ 
                        borderRadius: `${banner.promotion_mobile.borderRadius}px`,
                        overflow: 'hidden',
                        width: '100%',
                        position: 'relative',
                        direction: banner.promotion.direction
                     }">
                    
                    <div :style="{
                        position: 'absolute', top: 0, left: 0, right: 0, bottom: 0,
                        borderRadius: 'inherit',
                        boxShadow: `inset 0 0 0 ${banner.promotion_mobile.borderWidth}px ${banner.promotion.borderColor}`,
                        zIndex: 10,
                        pointerEvents: 'none'
                    }"></div>

                    <div class="yab-promo-header"
                         :style="{ 
                            background: getPromoBackgroundStyle(banner.promotion_mobile, 'header'),
                            padding: `${banner.promotion_mobile.headerPaddingY}px ${banner.promotion_mobile.headerPaddingX}px`,
                            display: 'flex', alignItems: 'center', gap: '10px',
                            flexDirection: banner.promotion.direction === 'rtl' ? 'row-reverse' : 'row'
                         }">
                        <img v-if="banner.promotion.iconUrl" :src="banner.promotion.iconUrl" :style="{width: `${banner.promotion_mobile.iconSize}px`, height: `${banner.promotion_mobile.iconSize}px`}" />
                        <span :style="{
                            color: banner.promotion.headerTextColor,
                            fontSize: `${banner.promotion_mobile.headerFontSize}px`,
                            fontWeight: banner.promotion_mobile.headerFontWeight,
                            textAlign: banner.promotion.direction === 'rtl' ? 'right' : 'left',
                            flexGrow: 1
                        }">{{ banner.promotion.headerText }}</span>
                    </div>
                    <div class="yab-promo-body"
                         :style="{
                            background: getPromoBackgroundStyle(banner.promotion_mobile, 'body'),
                            padding: `${banner.promotion_mobile.bodyPaddingY}px ${banner.promotion_mobile.bodyPaddingX}px`
                         }">
                         <p :style="{
                            color: banner.promotion.bodyTextColor,
                            fontSize: `${banner.promotion_mobile.bodyFontSize}px`,
                            fontWeight: banner.promotion_mobile.bodyFontWeight,
                            lineHeight: banner.promotion_mobile.bodyLineHeight,
                            textAlign: banner.promotion.direction === 'rtl' ? 'right' : 'left',
                            margin: 0
                         }" v-html="previewBodyText"></p>
                    </div>
                </div>
                </div>
        </div>
    </transition>
</div>