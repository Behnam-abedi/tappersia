<?php require YAB_PLUGIN_DIR . 'admin/views/components/banner-editor-header.php'; ?>

<main class="grid grid-cols-12 gap-6 p-6 ltr">
    <div class="col-span-4 overflow-y-auto flex flex-col gap-6 [&>*:last-child]:mb-[40px] mr-2" style="max-height: calc(100vh - 120px);">
        <div class="bg-[#434343] p-5 rounded-lg shadow-xl mr-2">
            <div class="flex mb-4 bg-[#292929] rounded-lg p-1">
                <button @click="currentView = 'desktop'" :class="{'active-tab': currentView === 'desktop'}" class="flex-1 tab-button rounded-md">Desktop</button>
                <button @click="currentView = 'mobile'" :class="{'active-tab': currentView === 'mobile'}" class="flex-1 tab-button rounded-md">Mobile</button>
            </div>

            <div v-if="banner.simple && banner.simple_mobile" :key="currentView">
                <h3 class="font-bold text-xl text-white tracking-wide mb-4 capitalize">{{ currentView }} Settings</h3>
            
                <div class="flex flex-col gap-5">
                    <?php require 'simple-banner-settings.php'; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-span-8 sticky top-[120px] space-y-4">
        <div class="bg-[#434343] p-4 rounded-lg">
            <h3 class="preview-title">Live Preview</h3>
            
            <transition name="fade" mode="out-in">
                <div v-if="currentView === 'desktop'" class="flex flex-col items-center">
                    <span class="text-xs text-gray-400 mb-2">Desktop View</span>
                    <div class="flex justify-center w-full bg-[#292929] rounded-lg p-4">
                        <div class="yab-simple-banner-wrapper" 
                            :style="{ 
                                width: '100%', 
                                height: banner.simple.height + 'px', 
                                minHeight: banner.simple.height + 'px',
                                borderRadius: banner.simple.borderRadius + 'px', 
                                background: bannerStyles(banner.simple),
                                padding: banner.simple.paddingY + 'px ' + banner.simple.paddingX + 'px',
                                display: 'flex',
                                alignItems: 'center',
                                justifyContent: 'space-between',
                                boxSizing: 'border-box',
                                direction: banner.simple.direction,
                                flexDirection: banner.simple.direction === 'rtl' ? 'row-reverse' : 'row'
                            }">
                            <span :style="{ 
                                fontSize: banner.simple.textSize + 'px',
                                fontWeight: banner.simple.textWeight,
                                color: banner.simple.textColor
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
                                  boxSizing: 'border-box'
                               }">
                                {{ banner.simple.buttonText }}
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
                                minHeight: 'fit-content',
                                borderRadius: banner.simple_mobile.borderRadius + 'px', 
                                background: bannerStyles(banner.simple_mobile),
                                padding: banner.simple_mobile.paddingY + 'px ' + banner.simple_mobile.paddingX + 'px',
                                display: 'flex',
                                alignItems: 'center',
                                justifyContent: 'space-between',
                                boxSizing: 'border-box',
                                flexDirection: 'column',
                                gap: '15px'
                            }">
                            <span :style="{ 
                                fontSize: banner.simple_mobile.textSize + 'px',
                                fontWeight: banner.simple_mobile.textWeight,
                                color: banner.simple_mobile.textColor,
                                textAlign: 'center'
                            }">
                                {{ banner.simple_mobile.text }}
                            </span>
                            <a :href="banner.simple_mobile.buttonLink" 
                               target="_blank" 
                               :style="{ 
                                  backgroundColor: banner.simple_mobile.buttonBgColor,
                                  borderRadius: banner.simple_mobile.buttonBorderRadius + 'px',
                                  color: banner.simple_mobile.buttonTextColor,
                                  fontSize: banner.simple_mobile.buttonFontSize + 'px',
                                  fontWeight: banner.simple_mobile.buttonFontWeight,
                                  padding: banner.simple_mobile.buttonPaddingY + 'px ' + banner.simple_mobile.buttonPaddingX + 'px',
                                  minWidth: banner.simple_mobile.buttonMinWidth + 'px',
                                  textDecoration: 'none',
                                  textAlign: 'center',
                                  boxSizing: 'border-box'
                               }">
                                {{ banner.simple_mobile.buttonText }}
                            </a>
                        </div>
                    </div>
                </div>
            </transition>
        </div>
        
        <transition name="fade">
            <div v-if="banner.displayMethod === 'Fixed'">
                <?php require YAB_PLUGIN_DIR . 'admin/views/components/display-conditions.php'; ?>
            </div>
        </transition>
    </div>
</main>