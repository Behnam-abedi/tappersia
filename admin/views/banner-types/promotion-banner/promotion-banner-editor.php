<?php
// tappersia/admin/views/banner-types/promotion-banner-editor.php
require YAB_PLUGIN_DIR . 'admin/views/components/banner-editor-header.php'; 
?>

<main class="grid grid-cols-12 gap-6 p-6 ltr">
    <div class="col-span-4 overflow-y-auto flex flex-col gap-6 [&>*:last-child]:mb-[40px]" style="max-height: calc(100vh - 120px);">
        
        <div class="bg-[#434343] p-5 rounded-lg shadow-xl">
            <h3 class="font-bold text-xl text-white tracking-wide mb-5">General Settings</h3>
            <div class="space-y-4">
                <div>
                    <h4 class="section-title">Border</h4>
                    <div class="grid grid-cols-3 gap-2">
                        <div>
                            <label class="setting-label-sm">Color</label>
                            <div class="yab-color-input-wrapper">
                                <input type="color" v-model="banner.promotion.borderColor" class="yab-color-picker">
                                <input type="text" v-model="banner.promotion.borderColor" class="yab-hex-input" placeholder="Color">
                            </div>
                        </div>
                        <div>
                            <label class="setting-label-sm">Width (px)</label>
                            <input type="number" v-model.number="banner.promotion.borderWidth" class="yab-form-input" placeholder="e.g., 1">
                        </div>
                        <div>
                            <label class="setting-label-sm">Radius (px)</label>
                            <input type="number" v-model.number="banner.promotion.borderRadius" class="yab-form-input" placeholder="e.g., 12">
                        </div>
                    </div>
                </div>
                <hr class="section-divider">
                <div>
                    <label class="setting-label-sm">Content Direction</label>
                    <div class="flex rounded-lg bg-[#292929] overflow-hidden">
                        <button @click="banner.promotion.direction = 'ltr'" :class="banner.promotion.direction === 'ltr' ? 'active-tab' : ''" class="flex-1 tab-button rounded-l-lg">Left to Right</button>
                        <button @click="banner.promotion.direction = 'rtl'" :class="banner.promotion.direction === 'rtl' ? 'active-tab' : ''" class="flex-1 tab-button rounded-r-lg">Right to Left</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-[#434343] p-5 rounded-lg shadow-xl">
            <h3 class="font-bold text-xl text-white tracking-wide mb-5">Header Settings</h3>
            <div class="flex flex-col gap-5">
                <div>
                    <h4 class="section-title">Background</h4>
                    <div class="flex gap-2 mb-2 bg-[#292929] rounded-lg">
                        <button @click="banner.promotion.headerBackgroundType = 'solid'" :class="{'active-tab': banner.promotion.headerBackgroundType === 'solid'}" class="flex-1 tab-button rounded-l-lg">Solid</button>
                        <button @click="banner.promotion.headerBackgroundType = 'gradient'" :class="{'active-tab': banner.promotion.headerBackgroundType === 'gradient'}" class="flex-1 tab-button rounded-r-lg">Gradient</button>
                    </div>
                    <div v-if="banner.promotion.headerBackgroundType === 'solid'" class="space-y-2">
                        <label class="setting-label-sm">Background Color</label>
                        <div class="yab-color-input-wrapper"><input type="color" v-model="banner.promotion.headerBgColor" class="yab-color-picker"><input type="text" v-model="banner.promotion.headerBgColor" class="yab-hex-input" placeholder="#FF731B"></div>
                    </div>
                    <div v-else class="space-y-2">
                        <div>
                            <label class="setting-label-sm">Gradient Colors</label>
                            <div class="grid grid-cols-2 gap-2">
                                <div class="yab-color-input-wrapper"><input type="color" v-model="banner.promotion.headerGradientColor1" class="yab-color-picker"><input type="text" v-model="banner.promotion.headerGradientColor1" class="yab-hex-input"></div>
                                <div class="yab-color-input-wrapper"><input type="color" v-model="banner.promotion.headerGradientColor2" class="yab-color-picker"><input type="text" v-model="banner.promotion.headerGradientColor2" class="yab-hex-input"></div>
                            </div>
                        </div>
                        <div>
                            <label class="setting-label-sm">Gradient Angle</label>
                            <input type="number" v-model.number="banner.promotion.headerGradientAngle" class="yab-form-input" placeholder="e.g., 90">
                        </div>
                    </div>
                </div>
                <hr class="section-divider">
                <div>
                    <h4 class="section-title">Icon</h4>
                    <div class="flex gap-2 items-center mb-2">
                        <button @click="openMediaUploader('promotion')" class="flex-1 bg-blue-600 text-white px-3 py-1.5 rounded-md hover:bg-blue-700 text-sm">
                            {{ banner.promotion.iconUrl ? 'Change Icon' : 'Select Icon' }}
                        </button>
                        <button v-if="banner.promotion.iconUrl" @click="removeImage('promotion')" class="bg-red-600 text-white px-3 py-1.5 rounded-md hover:bg-red-700 text-sm">Remove</button>
                    </div>
                    <div v-if="banner.promotion.iconUrl">
                        <label class="setting-label-sm">Icon Size (px)</label>
                        <input type="number" v-model.number="banner.promotion.iconSize" class="yab-form-input" placeholder="e.g., 24">
                    </div>
                </div>
                <hr class="section-divider">
                 <div>
                    <h4 class="section-title">Layout</h4>
                     <div class="grid grid-cols-2 gap-2">
                         <div>
                            <label class="setting-label-sm">Padding Y (px)</label>
                            <input type="number" v-model.number="banner.promotion.headerPaddingY" class="yab-form-input" placeholder="e.g., 12">
                         </div>
                         <div>
                            <label class="setting-label-sm">Padding X (px)</label>
                            <input type="number" v-model.number="banner.promotion.headerPaddingX" class="yab-form-input" placeholder="e.g., 20">
                        </div>
                    </div>
                </div>
                <hr class="section-divider">
                <div>
                    <h4 class="section-title">Text</h4>
                    <label class="setting-label-sm">Header Text</label>
                    <input type="text" v-model="banner.promotion.headerText" class="yab-form-input mb-2" placeholder="Header Text">
                    <div class="grid grid-cols-3 gap-2">
                        <div>
                            <label class="setting-label-sm">Color</label>
                            <div class="yab-color-input-wrapper"><input type="color" v-model="banner.promotion.headerTextColor" class="yab-color-picker"><input type="text" v-model="banner.promotion.headerTextColor" class="yab-hex-input" placeholder="#FFFFFF"></div>
                        </div>
                        <div>
                           <label class="setting-label-sm">Font Size (px)</label>
                           <input type="number" v-model.number="banner.promotion.headerFontSize" class="yab-form-input" placeholder="e.g., 18">
                        </div>
                         <div>
                            <label class="setting-label-sm">Font Weight</label>
                            <select v-model="banner.promotion.headerFontWeight" class="yab-form-input"><option value="400">Normal</option><option value="700">Bold</option></select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-[#434343] p-5 rounded-lg shadow-xl">
            <h3 class="font-bold text-xl text-white tracking-wide mb-5">Body Settings</h3>
            <div class="flex flex-col gap-5">
                <div>
                    <h4 class="section-title">Background</h4>
                     <div class="flex gap-2 mb-2 bg-[#292929] rounded-lg">
                        <button @click="banner.promotion.bodyBackgroundType = 'solid'" :class="{'active-tab': banner.promotion.bodyBackgroundType === 'solid'}" class="flex-1 tab-button rounded-l-lg">Solid</button>
                        <button @click="banner.promotion.bodyBackgroundType = 'gradient'" :class="{'active-tab': banner.promotion.bodyBackgroundType === 'gradient'}" class="flex-1 tab-button rounded-r-lg">Gradient</button>
                    </div>
                    <div v-if="banner.promotion.bodyBackgroundType === 'solid'">
                         <label class="setting-label-sm">Background Color</label>
                        <div class="yab-color-input-wrapper"><input type="color" v-model="banner.promotion.bodyBgColor" class="yab-color-picker"><input type="text" v-model="banner.promotion.bodyBgColor" class="yab-hex-input" placeholder="#f071001f"></div>
                    </div>
                     <div v-else class="space-y-2">
                        <div>
                            <label class="setting-label-sm">Gradient Colors</label>
                            <div class="grid grid-cols-2 gap-2">
                                <div class="yab-color-input-wrapper"><input type="color" v-model="banner.promotion.bodyGradientColor1" class="yab-color-picker"><input type="text" v-model="banner.promotion.bodyGradientColor1" class="yab-hex-input"></div>
                                <div class="yab-color-input-wrapper"><input type="color" v-model="banner.promotion.bodyGradientColor2" class="yab-color-picker"><input type="text" v-model="banner.promotion.bodyGradientColor2" class="yab-hex-input"></div>
                            </div>
                        </div>
                        <div>
                            <label class="setting-label-sm">Gradient Angle</label>
                            <input type="number" v-model.number="banner.promotion.bodyGradientAngle" class="yab-form-input" placeholder="e.g., 90">
                        </div>
                    </div>
                </div>
                 <hr class="section-divider">
                 <div>
                    <h4 class="section-title">Layout</h4>
                     <div class="grid grid-cols-2 gap-2">
                         <div>
                            <label class="setting-label-sm">Padding Y (px)</label>
                            <input type="number" v-model.number="banner.promotion.bodyPaddingY" class="yab-form-input" placeholder="e.g., 5">
                         </div>
                         <div>
                            <label class="setting-label-sm">Padding X (px)</label>
                            <input type="number" v-model.number="banner.promotion.bodyPaddingX" class="yab-form-input" placeholder="e.g., 20">
                        </div>
                    </div>
                </div>
                <hr class="section-divider">
                <div>
                    <h4 class="section-title">Content</h4>
                    <div class="flex justify-between items-center mb-1">
                        <label class="setting-label-sm">Body Text (Use [[placeholder]] for links)</label>
                        <button @click="makeSelectedTextPlaceholder" class="text-xs bg-[#00baa4] text-white px-2 py-1 rounded-md hover:bg-opacity-80 transition-all">Create Link from Selected Text</button>
                    </div>
                    <textarea ref="bodyTextarea" v-model="banner.promotion.bodyText" rows="4" class="yab-form-input mb-2 text-white" placeholder="e.g., For more info, see [[our guide]]."></textarea>
                     <div class="grid grid-cols-3 gap-2">
                        <div>
                            <label class="setting-label-sm">Text Color</label>
                            <div class="yab-color-input-wrapper"><input type="color" v-model="banner.promotion.bodyTextColor" class="yab-color-picker"><input type="text" v-model="banner.promotion.bodyTextColor" class="yab-hex-input" placeholder="#212121"></div>
                        </div>
                        <div>
                           <label class="setting-label-sm">Font Size (px)</label>
                           <input type="number" v-model.number="banner.promotion.bodyFontSize" class="yab-form-input" placeholder="e.g., 15">
                        </div>
                         <div>
                            <label class="setting-label-sm">Font Weight</label>
                            <select v-model="banner.promotion.bodyFontWeight" class="yab-form-input"><option value="400">Normal</option><option value="700">Bold</option></select>
                        </div>
                    </div>
                </div>
                 <hr class="section-divider">
                <div>
                     <h4 class="section-title">Links</h4>
                     <div v-if="banner.promotion.links.length === 0" class="text-center text-gray-400 text-sm py-2">
                        No links defined. Select text in the body and use the button above to create one.
                     </div>
                     <div v-for="(link, index) in banner.promotion.links" :key="index" class="space-y-2 bg-[#292929] p-3 rounded-md mb-3 relative">
                        <div class="flex justify-between items-center">
                            <label class="setting-label-sm font-bold text-gray-300">Link for [[{{ link.placeholder }}]]</label>
                            <button @click="removePromoLink(index)" class="text-red-500 text-xs absolute top-2 right-2">Remove</button>
                        </div>
                        <label class="setting-label-sm">URL</label>
                        <input type="text" v-model="link.url" class="yab-form-input" placeholder="https://example.com">
                        <label class="setting-label-sm">Link Color</label>
                        <div class="yab-color-input-wrapper"><input type="color" v-model="link.color" class="yab-color-picker"><input type="text" v-model="link.color" class="yab-hex-input" placeholder="#f07100"></div>
                     </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-span-8 sticky top-[120px] space-y-4">
        <div class="bg-[#434343] p-4 rounded-lg">
            <h3 class="preview-title">Live Preview</h3>
            <div class="flex justify-center items-center bg-[#292929] rounded-lg p-8">
                 <div class="yab-promo-banner-wrapper" 
                     :style="{ 
                        border: `${banner.promotion.borderWidth}px solid ${banner.promotion.borderColor}`,
                        borderRadius: `${banner.promotion.borderRadius}px`,
                        overflow: 'hidden',
                        width: '100%',
                        direction: banner.promotion.direction
                     }">
                    <div class="yab-promo-header"
                         :style="{ 
                            background: getPromoBackgroundStyle(banner.promotion, 'header'),
                            padding: `${banner.promotion.headerPaddingY}px ${banner.promotion.headerPaddingX}px`,
                            display: 'flex',
                            alignItems: 'center',
                            gap: '10px'
                         }">
                        <img v-if="banner.promotion.iconUrl" :src="banner.promotion.iconUrl" :style="{width: `${banner.promotion.iconSize}px`, height: `${banner.promotion.iconSize}px`}" />
                        <span :style="{
                            color: banner.promotion.headerTextColor,
                            fontSize: `${banner.promotion.headerFontSize}px`,
                            fontWeight: banner.promotion.headerFontWeight,
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
                            textAlign: 'justify',
                            margin: 0
                         }" v-html="previewBodyText"></p>
                    </div>
                </div>
            </div>
        </div>
        <transition name="yab-modal-fade">
            <div v-if="banner.displayMethod === 'Fixed'">
                <?php require YAB_PLUGIN_DIR . 'admin/views/components/display-conditions.php'; ?>
            </div>
        </transition>
    </div>
</main>