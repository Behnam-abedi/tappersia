<?php
// tappersia/admin/views/banner-types/simple-banner/simple-banner-preview.php
?>
<div class="bg-[#434343] p-4 rounded-lg">
    <h3 class="preview-title">Live Preview</h3>
    
    <transition name="fade" mode="out-in">
        <div v-if="currentView === 'desktop'" class="flex flex-col items-center">
            <div class="flex justify-center w-full bg-[#292929] rounded-lg p-4">
                <div class="yab-simple-banner-wrapper" 
                    :style="{ 
                        width: '100%', 
                        height: 'auto', 
                        minHeight: banner.simple.minHeight + 'px',
                        borderRadius: banner.simple.borderRadius + 'px', 
                        background: bannerStyles(banner.simple),
                        padding: banner.simple.paddingY + 'px ' + banner.simple.paddingX + banner.simple.paddingXUnit,
                        display: 'flex',
                        alignItems: 'center',
                        justifyContent: 'space-between',
                        boxSizing: 'border-box',
                        flexDirection: banner.simple.direction === 'rtl' ? 'row-reverse' : 'row',
                        gap: '15px'
                    }">
                    <span :style="{ 
                        fontSize: banner.simple.textSize + 'px',
                        fontWeight: banner.simple.textWeight,
                        color: banner.simple.textColor,
                        flexGrow: 1,
                        textAlign: banner.simple.direction === 'rtl' ? 'right' : 'left'
                    }">
                        {{ banner.simple.text }}
                    </span>
                    <a :href="banner.simple.buttonLink" 
                       target="_blank" 
                       :style="{ 
                          backgroundColor: banner.simple.buttonBgColor,
                          borderRadius: banner.simple.buttonBorderRadius + 'px',
                          color: banner.simple.buttonTextColor,
                          fontSize: banner.simple.buttonFontSize + 'px',
                          fontWeight: banner.simple.buttonFontWeight,
                          padding: banner.simple.buttonPaddingY + 'px ' + banner.simple.buttonPaddingX + 'px',
                          minWidth: banner.simple.buttonMinWidth + 'px',
                          textDecoration: 'none',
                          textAlign: 'center',
                          boxSizing: 'border-box',
                          flexShrink: 0,
                          lineHeight: 1,
                          transition: 'background-color 0.3s'
                       }"
                       @mouseover="event.currentTarget.style.backgroundColor = banner.simple.buttonBgHoverColor || banner.simple.buttonBgColor"
                       @mouseout="event.currentTarget.style.backgroundColor = banner.simple.buttonBgColor"
                       >
                        {{ banner.simple.buttonText }}
                    </a>
                </div>
            </div>
        </div>
        
        <div v-else-if="currentView === 'mobile'" class="flex flex-col items-center">
            <div class="w-[375px] h-auto bg-[#292929] rounded-2xl p-4 flex justify-center items-center mx-auto">
                <div class="yab-simple-banner-wrapper w-full" 
                    :style="{ 
                        height: 'auto',
                        minHeight: banner.simple_mobile.minHeight + 'px',
                        borderRadius: banner.simple_mobile.borderRadius + 'px', 
                        background: bannerStyles(banner.simple_mobile),
                        padding: banner.simple_mobile.paddingY + 'px ' + banner.simple_mobile.paddingX + banner.simple_mobile.paddingXUnit,
                        display: 'flex',
                        alignItems: 'center',
                        justifyContent: 'space-between',
                        boxSizing: 'border-box',
                        flexDirection: banner.simple.direction === 'rtl' ? 'row-reverse' : 'row',
                        gap: '15px'
                    }">
                    <span :style="{ 
                        fontSize: banner.simple_mobile.textSize + 'px',
                        fontWeight: banner.simple_mobile.textWeight,
                        color: banner.simple.textColor,
                        flexGrow: 1,
                        textAlign: banner.simple.direction === 'rtl' ? 'right' : 'left'
                    }">
                        {{ banner.simple.text }}
                    </span>
                    <a :href="banner.simple.buttonLink" 
                       target="_blank" 
                       :style="{ 
                          backgroundColor: banner.simple.buttonBgColor,
                          borderRadius: banner.simple_mobile.buttonBorderRadius + 'px',
                          color: banner.simple.buttonTextColor,
                          fontSize: banner.simple_mobile.buttonFontSize + 'px',
                          fontWeight: banner.simple_mobile.buttonFontWeight,
                          padding: banner.simple_mobile.buttonPaddingY + 'px ' + banner.simple_mobile.buttonPaddingX + 'px',
                          minWidth: banner.simple_mobile.buttonMinWidth + 'px',
                          textDecoration: 'none',
                          textAlign: 'center',
                          boxSizing: 'border-box',
                          flexShrink: 0,
                          lineHeight: 1,
                          transition: 'background-color 0.3s'
                       }"
                       @mouseover="event.currentTarget.style.backgroundColor = banner.simple.buttonBgHoverColor || banner.simple.buttonBgColor"
                       @mouseout="event.currentTarget.style.backgroundColor = banner.simple.buttonBgColor"
                       >
                        {{ banner.simple.buttonText }}
                    </a>
                </div>
            </div>
        </div>
    </transition>
</div>