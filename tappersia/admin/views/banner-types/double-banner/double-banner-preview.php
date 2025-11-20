<?php
// tappersia/admin/views/banner-types/double-banner/double-banner-preview.php
?>
<div class="bg-[#434343] p-4 rounded-lg">
    <h3 class="preview-title">Live Preview</h3>
    
    <transition name="fade" mode="out-in">
        <div v-if="currentView === 'desktop'" class="flex flex-col items-center">
            <div class="flex flex-row justify-center w-full" style="gap: 20px;">
                <div v-for="(b, key) in banner.double.desktop" :key="`preview-desktop-${key}`" 
                    class="relative overflow-hidden flex-shrink-0" 
                    :style="{ 
                        width: '50%',
                        minHeight: `${b.minHeight}px`,
                        height: 'auto',
                        border: b.enableBorder ? `${b.borderWidth}px solid ${b.borderColor}` : 'none',
                        borderRadius: `${b.borderRadius}px`
                    }">
                    <img v-if="b.imageUrl" :src="b.imageUrl" :style="{...imageStyleObject(b), zIndex: b.layerOrder === 'image-below-overlay' ? 1 : 2}" />
                    <div class="absolute inset-0" :style="{background: bannerStyles(b), zIndex: b.layerOrder === 'image-below-overlay' ? 2 : 1}"></div>
                    <div class="absolute inset-0 flex flex-col z-10"  :style="{ padding: `${b.paddingY}px ${b.paddingX}px`, width: `${b.contentWidth}${b.contentWidthUnit}`, alignItems: contentAlignment(b.alignment), textAlign: b.alignment, zIndex: 3 }">
                        <div style="flex-grow: 1;">
                            <h2 :style="{ color: b.titleColor, fontSize: b.titleSize + 'px', fontWeight: b.titleWeight, margin: 0 }">{{ b.titleText }}</h2>
                            <p :style="{ color: b.descColor, fontSize: b.descSize + 'px', fontWeight: b.descWeight, whiteSpace: 'pre-wrap', marginTop: b.marginTopDescription + 'px', marginBottom: '0' }">{{ b.descText }}</p>
                        </div>
                        <a v-if="b.buttonText" :href="b.buttonLink" target="_blank" :style="{ backgroundColor: b.buttonBgColor, color: b.buttonTextColor, fontSize: b.buttonFontSize + 'px', fontWeight: b.buttonFontWeight, borderRadius: `${b.buttonBorderRadius}px`, padding: `${b.buttonPaddingY}px ${b.buttonPaddingX}px`, textDecoration: 'none', display: 'inline-flex', alignItems: 'center', justifyContent: 'center', marginTop: b.buttonMarginTop + 'px', marginBottom: b.buttonMarginBottom + 'px' }">{{ b.buttonText }}</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div v-else-if="currentView === 'mobile'" class="flex flex-col items-center">
             <div class="w-[375px] h-auto bg-[#292929] rounded-2xl p-4 flex flex-col items-center mx-auto" style="gap: 20px;">
                <div v-for="(b, key) in banner.double.mobile" :key="`preview-mobile-${key}`"
                    class="relative overflow-hidden flex-shrink-0 w-full"
                    :style="{
                        width: '100%',
                        minHeight: `${b.minHeight}px`,
                        height: 'auto',
                        border: b.enableBorder ? `${b.borderWidth}px solid ${b.borderColor}` : 'none',
                        borderRadius: `${b.borderRadius}px`
                    }">
                    <img v-if="b.imageUrl" :src="b.imageUrl" :style="{...imageStyleObject(b), zIndex: banner.double.desktop[key].layerOrder === 'image-below-overlay' ? 1 : 2}" />
                    <div class="absolute inset-0" :style="{background: bannerStyles(b), zIndex: banner.double.desktop[key].layerOrder === 'image-below-overlay' ? 2 : 1}"></div>
                    <div class="absolute inset-0 flex flex-col z-10" :style="{ padding: `${b.paddingY}px ${b.paddingX}px`, width: `${b.contentWidth}${b.contentWidthUnit}`, alignItems: contentAlignment(banner.double.desktop[key].alignment), textAlign: banner.double.desktop[key].alignment, zIndex: 3 }">
                        <div style="flex-grow: 1;">
                            <h2 :style="{ color: b.titleColor, fontSize: b.titleSize + 'px', fontWeight: b.titleWeight, margin: 0 }">{{ b.titleText }}</h2>
                            <p :style="{ color: b.descColor, fontSize: b.descSize + 'px', fontWeight: b.descWeight, whiteSpace: 'pre-wrap', marginTop: b.marginTopDescription + 'px', marginBottom: '0' }">{{ b.descText }}</p>
                        </div>
                        <a v-if="b.buttonText" :href="b.buttonLink" target="_blank" :style="{ backgroundColor: b.buttonBgColor, color: b.buttonTextColor, fontSize: b.buttonFontSize + 'px', fontWeight: b.buttonFontWeight, borderRadius: `${b.buttonBorderRadius}px`, padding: `${b.buttonPaddingY}px ${b.buttonPaddingX}px`, textDecoration: 'none', display: 'inline-flex', alignItems: 'center', justifyContent: 'center', marginTop: b.buttonMarginTop + 'px', marginBottom: b.buttonMarginBottom + 'px' }">{{ b.buttonText }}</a>
                    </div>
                </div>
             </div>
        </div>
    </transition>
</div>