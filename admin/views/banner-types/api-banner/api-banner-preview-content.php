<?php
// tappersia/admin/views/banner-types/api-banner/api-banner-preview-content.php
?>
<div v-if="apiItem" style="display: flex;flex-direction: column;justify-content: space-between;height: 100%;">
    <div>
        <h3 class="font-bold" :style="{ color: settings.titleColor, fontSize: settings.titleSize + 'px', fontWeight: settings.titleWeight, margin: 0, lineHeight: 1.4 }">{{ apiItem.title }}</h3>
        <div class="flex items-center mt-[9px]" :style="{ justifyContent: settings.layout === 'right' ? 'flex-end' : 'flex-start' }">
            <div class="text-yellow-400 flex items-center">
                <span v-for="n in 5" :key="n" :style="{ fontSize: settings.starSize + 'px', width: settings.starSize + 'px', height: settings.starSize + 'px', lineHeight: 1 }">{{ n <= (isApiHotel ? apiItem.star : ceil(apiItem.rate)) ? '★' : '☆' }}</span>
            </div>
            <div class="border-l border-gray-600 h-4 mx-[13px]"></div>
            <span :style="{ color: settings.cityColor, fontSize: settings.citySize + 'px' }">{{ isApiHotel ? apiItem.province.name : apiItem.startProvince.name }}</span>
        </div>
    </div>
    <div class="flex items-center justify-between" style="margin-top: 11px;">
        <div class="flex items-center" :style="{flexDirection: settings.layout === 'right' ? 'row-reverse' : 'row'}">
            <div v-if="(isApiHotel ? apiItem.avgRating : apiItem.rate) != null" class="flex items-center justify-center rounded" :style="{ padding: '0px 10px', backgroundColor: settings.ratingBoxBgColor ,lineHeight: 1.6}">
                <span :style="{ color: settings.ratingBoxColor, fontSize: settings.ratingBoxSize + 'px', fontWeight: settings.ratingBoxWeight }">{{ formatRating(isApiHotel ? apiItem.avgRating : apiItem.rate) }}</span>
            </div>
            <span class="mx-[8px]" :style="{ color: settings.ratingTextColor, fontSize: settings.ratingTextSize + 'px', fontWeight: settings.ratingTextWeight }">{{ getRatingLabel(isApiHotel ? apiItem.avgRating : apiItem.rate) }}</span>
            <span v-if="(isApiHotel ? apiItem.reviewCount : apiItem.rateCount) != null" :style="{ color: settings.reviewColor, fontSize: settings.reviewSize + 'px' }">({{ isApiHotel ? apiItem.reviewCount : apiItem.rateCount }} reviews)</span>
        </div>
        <div>
            <div class="flex items-baseline gap-1.5">
                <span :style="{ color: settings.priceFromColor, fontSize: settings.priceFromSize + 'px' }">from</span>
                <span :style="{ color: settings.priceAmountColor, fontSize: settings.priceAmountSize + 'px', fontWeight: settings.priceAmountWeight }">€{{ (isApiHotel ? apiItem.minPrice : apiItem.price).toFixed(2) }}</span>
                <span v-if="isApiHotel" :style="{ color: settings.priceNightColor, fontSize: settings.priceFromSize + 'px' }">/ night</span>
            </div>
        </div>
    </div>
</div>