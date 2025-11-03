<?php
// tappersia/admin/views/banner-types/sticky-simple-banner/sticky-simple-banner-preview.php
?>
<div class="bg-[#434343] p-4 rounded-lg">
    <h3 class="preview-title">Live Preview</h3>
    
    <transition name="fade" mode="out-in">
        <div v-if="currentView === 'desktop'" class="flex flex-col items-center">
            <span class="text-xs text-gray-400 mb-2">Desktop View</span>
            <div class="flex justify-center w-full bg-[#292929] rounded-lg p-4">
                <div class="yab-simple-banner-wrapper" 
                    :style="{ 
                        width: '100%', 
                        height: 'auto', 
                        minHeight: banner.sticky_simple.minHeight + 'px',
                        borderRadius: banner.sticky_simple.borderRadius + 'px', 
                        background: bannerStyles(banner.sticky_simple),
                        padding: banner.sticky_simple.paddingY + 'px ' + banner.sticky_simple.paddingX + banner.sticky_simple.paddingXUnit,
                        display: 'flex',
                        alignItems: 'center',
                        justifyContent: 'space-between',
                        boxSizing: 'border-box',
                        flexDirection: banner.sticky_simple.direction === 'rtl' ? 'row-reverse' : 'row',
                        gap: '15px'
                    }">
                    <span :style="{ 
                        fontSize: banner.sticky_simple.textSize + 'px',
                        fontWeight: banner.sticky_simple.textWeight,
                        color: banner.sticky_simple.textColor,
                        flexGrow: 1,
                        textAlign: banner.sticky_simple.direction === 'rtl' ? 'right' : 'left'
                    }">
                        {{ banner.sticky_simple.text }}
                    </span>
                    <a :href="banner.sticky_simple.buttonLink" 
                       target="_blank" 
                       :style="{ 
                          backgroundColor: banner.sticky_simple.buttonBgColor,
                          borderRadius: banner.sticky_simple.buttonBorderRadius + 'px',
                          color: banner.sticky_simple.buttonTextColor,
                          fontSize: banner.sticky_simple.buttonFontSize + 'px',
                          fontWeight: banner.sticky_simple.buttonFontWeight,
                          padding: banner.sticky_simple.buttonPaddingY + 'px ' + banner.sticky_simple.buttonPaddingX + 'px',
                          minWidth: banner.sticky_simple.buttonMinWidth + 'px',
                          textDecoration: 'none',
                          textAlign: 'center',
                          boxSizing: 'border-box',
                          flexShrink: 0,
                          lineHeight: 1,
                          transition: 'background-color 0.3s'
                       }"
                       @mouseover="event.currentTarget.style.backgroundColor = banner.sticky_simple.buttonBgHoverColor || banner.sticky_simple.buttonBgColor"
                       @mouseout="event.currentTarget.style.backgroundColor = banner.sticky_simple.buttonBgColor"
                       >
                        {{ banner.sticky_simple.buttonText }}
                    </a>
                </div>
            </div>
        </div>
        
        <div v-else-if="currentView === 'mobile'" class="flex flex-col items-center">
            <span class="text-xs text-gray-400 mb-2">Mobile View</span>
            <div class="w-[375px] h-auto bg-[#292929] rounded-2xl p-4 flex justify-center items-center mx-auto">
                <div class="yab-simple-banner-wrapper w-full" 
                    :style="{ 
                        height: 'auto',
                        minHeight: banner.sticky_simple_mobile.minHeight + 'px',
                        borderRadius: banner.sticky_simple_mobile.borderRadius + 'px', 
                        background: bannerStyles(banner.sticky_simple_mobile),
                        padding: banner.sticky_simple_mobile.paddingY + 'px ' + banner.sticky_simple_mobile.paddingX + banner.sticky_simple_mobile.paddingXUnit,
                        display: 'flex',
                        alignItems: 'center',
                        justifyContent: 'space-between',
                        boxSizing: 'border-box',
                        flexDirection: banner.sticky_simple.direction === 'rtl' ? 'row-reverse' : 'row',
                        gap: '15px'
                    }">
                    <span :style="{ 
                        fontSize: banner.sticky_simple_mobile.textSize + 'px',
                        fontWeight: banner.sticky_simple_mobile.textWeight,
                        color: banner.sticky_simple.textColor,
                        flexGrow: 1,
                        textAlign: banner.sticky_simple.direction === 'rtl' ? 'right' : 'left'
                    }">
                        {{ banner.sticky_simple.text }}
                    </span>
                    <a :href="banner.sticky_simple.buttonLink" 
                       target="_blank" 
                       :style="{ 
                          backgroundColor: banner.sticky_simple.buttonBgColor,
                          borderRadius: banner.sticky_simple_mobile.buttonBorderRadius + 'px',
                          color: banner.sticky_simple.buttonTextColor,
                          fontSize: banner.sticky_simple_mobile.buttonFontSize + 'px',
                          fontWeight: banner.sticky_simple_mobile.buttonFontWeight,
                          padding: banner.sticky_simple_mobile.buttonPaddingY + 'px ' + banner.sticky_simple_mobile.buttonPaddingX + 'px',
                          minWidth: banner.sticky_simple_mobile.buttonMinWidth + 'px',
                          textDecoration: 'none',
                          textAlign: 'center',
                          boxSizing: 'border-box',
                          flexShrink: 0,
                          lineHeight: 1,
                          transition: 'background-color 0.3s'
                       }"
                       @mouseover="event.currentTarget.style.backgroundColor = banner.sticky_simple.buttonBgHoverColor || banner.sticky_simple.buttonBgColor"
                       @mouseout="event.currentTarget.style.backgroundColor = banner.sticky_simple.buttonBgColor"
                       >
                        {{ banner.sticky_simple.buttonText }}
                    </a>
                </div>
            </div>
        </div>
    </transition>
</div>