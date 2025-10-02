<?php
// tappersia/admin/views/banner-types/promotion-banner/promotion-banner-settings.php
?>

<div class="bg-[#434343] p-5 rounded-lg shadow-xl mr-2">
     <div class="flex mb-4 bg-[#292929] rounded-lg p-1">
        <button @click="currentView = 'desktop'" :class="{'active-tab': currentView === 'desktop'}" class="flex-1 tab-button rounded-md">Desktop</button>
        <button @click="currentView = 'mobile'" :class="{'active-tab': currentView === 'mobile'}" class="flex-1 tab-button rounded-md">Mobile</button>
    </div>
</div>
<div class="bg-[#434343] p-5 rounded-lg shadow-xl mr-2">
    <h3 class="font-bold text-xl text-white tracking-wide mb-5">General Settings</h3>
    <div class="space-y-4">
        <div>
            <h4 class="section-title">Border</h4>
            <div class="grid grid-cols-3 gap-2">
                <div v-if="currentView === 'desktop'">
                    <label class="setting-label-sm">Color</label>
                    <div class="yab-color-input-wrapper">
                        <input type="color" v-model="settings.borderColor" class="yab-color-picker">
                        <input type="text" v-model="settings.borderColor" class="yab-hex-input" placeholder="Color">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2" :class="currentView === 'desktop' ? 'col-span-2' : 'col-span-3'">
                    <div>
                        <label class="setting-label-sm">Width (px)</label>
                        <input type="number" v-model.number="settings.borderWidth" class="yab-form-input" placeholder="e.g., 1">
                    </div>
                    <div>
                        <label class="setting-label-sm">Radius (px)</label>
                        <input type="number" v-model.number="settings.borderRadius" class="yab-form-input" placeholder="e.g., 12">
                    </div>
                </div>
            </div>
        </div>
        <hr class="section-divider">
        <div v-if="currentView === 'desktop'">
            <label class="setting-label-sm">Content Direction</label>
            <div class="flex rounded-lg bg-[#292929] overflow-hidden">
                <button @click="settings.direction = 'ltr'" :class="settings.direction === 'ltr' ? 'active-tab' : ''" class="flex-1 tab-button rounded-l-lg">Left to Right</button>
                <button @click="settings.direction = 'rtl'" :class="settings.direction === 'rtl' ? 'active-tab' : ''" class="flex-1 tab-button rounded-r-lg">Right to Left</button>
            </div>
        </div>
    </div>
</div>

<div class="bg-[#434343] p-5 rounded-lg shadow-xl mr-2">
    <h3 class="font-bold text-xl text-white tracking-wide mb-5">Header Settings</h3>
    <div class="flex flex-col gap-5">
        <div>
            <h4 class="section-title">Background</h4>
            <div class="flex gap-2 mb-2 bg-[#292929] rounded-lg">
                <button @click="settings.headerBackgroundType = 'solid'" :class="{'active-tab': settings.headerBackgroundType === 'solid'}" class="flex-1 tab-button rounded-l-lg">Solid</button>
                <button @click="settings.headerBackgroundType = 'gradient'" :class="{'active-tab': settings.headerBackgroundType === 'gradient'}" class="flex-1 tab-button rounded-r-lg">Gradient</button>
            </div>
            <div v-if="settings.headerBackgroundType === 'solid'" class="space-y-2">
                <label class="setting-label-sm">Background Color</label>
                <div class="yab-color-input-wrapper"><input type="color" v-model="settings.headerBgColor" class="yab-color-picker"><input type="text" v-model="settings.headerBgColor" class="yab-hex-input" placeholder="#FF731B"></div>
            </div>
            <div v-else class="space-y-2">
                <div>
                    <label class="setting-label-sm">Gradient Colors</label>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="yab-color-input-wrapper"><input type="color" v-model="settings.headerGradientColor1" class="yab-color-picker"><input type="text" v-model="settings.headerGradientColor1" class="yab-hex-input"></div>
                        <div class="yab-color-input-wrapper"><input type="color" v-model="settings.headerGradientColor2" class="yab-color-picker"><input type="text" v-model="settings.headerGradientColor2" class="yab-hex-input"></div>
                    </div>
                </div>
                <div>
                    <label class="setting-label-sm">Gradient Angle</label>
                    <input type="number" v-model.number="settings.headerGradientAngle" class="yab-form-input" placeholder="e.g., 90">
                </div>
            </div>
        </div>
        <hr class="section-divider">
        <div>
            <h4 class="section-title">Icon</h4>
             <div v-if="currentView === 'desktop'" class="flex gap-2 items-center mb-2">
                <button @click="openMediaUploader('promotion')" class="flex-1 bg-blue-600 text-white px-3 py-1.5 rounded-md hover:bg-blue-700 text-sm">
                    {{ banner.promotion.iconUrl ? 'Change Icon' : 'Select Icon' }}
                </button>
                <button v-if="banner.promotion.iconUrl" @click="removeImage('promotion')" class="bg-red-600 text-white px-3 py-1.5 rounded-md hover:bg-red-700 text-sm">Remove</button>
            </div>
            <div v-if="banner.promotion.iconUrl">
                <label class="setting-label-sm">Icon Size (px)</label>
                <input type="number" v-model.number="settings.iconSize" class="yab-form-input" placeholder="e.g., 24">
            </div>
        </div>
        <hr class="section-divider">
         <div>
            <h4 class="section-title">Layout</h4>
             <div class="grid grid-cols-2 gap-2">
                 <div>
                    <label class="setting-label-sm">Padding Y (px)</label>
                    <input type="number" v-model.number="settings.headerPaddingY" class="yab-form-input" placeholder="e.g., 12">
                 </div>
                 <div>
                    <label class="setting-label-sm">Padding X (px)</label>
                    <input type="number" v-model.number="settings.headerPaddingX" class="yab-form-input" placeholder="e.g., 20">
                </div>
            </div>
        </div>
        <hr class="section-divider">
        <div>
            <h4 class="section-title">Text</h4>
            <div v-if="currentView === 'desktop'">
                <label class="setting-label-sm">Header Text</label>
                <input type="text" v-model="settings.headerText" class="yab-form-input mb-2" placeholder="Header Text">
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div class="grid grid-cols-2 gap-2" :class="currentView === 'desktop' ? 'col-span-1' : 'col-span-2'">
                   <div>
                       <label class="setting-label-sm">Font Size (px)</label>
                       <input type="number" v-model.number="settings.headerFontSize" class="yab-form-input" placeholder="e.g., 18">
                   </div>
                     <div>
                        <label class="setting-label-sm">Font Weight</label>
                        <select v-model="settings.headerFontWeight" class="yab-form-input"><option value="400">Normal</option><option value="700">Bold</option></select>
                    </div>
                </div>
                 <div v-if="currentView === 'desktop'">
                    <label class="setting-label-sm">Color</label>
                    <div class="yab-color-input-wrapper"><input type="color" v-model="settings.headerTextColor" class="yab-color-picker"><input type="text" v-model="settings.headerTextColor" class="yab-hex-input" placeholder="#FFFFFF"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="bg-[#434343] p-5 rounded-lg shadow-xl mr-2">
    <h3 class="font-bold text-xl text-white tracking-wide mb-5">Body Settings</h3>
    <div class="flex flex-col gap-5">
        <div>
            <h4 class="section-title">Background</h4>
             <div class="flex gap-2 mb-2 bg-[#292929] rounded-lg">
                <button @click="settings.bodyBackgroundType = 'solid'" :class="{'active-tab': settings.bodyBackgroundType === 'solid'}" class="flex-1 tab-button rounded-l-lg">Solid</button>
                <button @click="settings.bodyBackgroundType = 'gradient'" :class="{'active-tab': settings.bodyBackgroundType === 'gradient'}" class="flex-1 tab-button rounded-r-lg">Gradient</button>
            </div>
            <div v-if="settings.bodyBackgroundType === 'solid'">
                 <label class="setting-label-sm">Background Color</label>
                <div class="yab-color-input-wrapper"><input type="color" v-model="settings.bodyBgColor" class="yab-color-picker"><input type="text" v-model="settings.bodyBgColor" class="yab-hex-input" placeholder="#f071001f"></div>
            </div>
             <div v-else class="space-y-2">
                <div>
                    <label class="setting-label-sm">Gradient Colors</label>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="yab-color-input-wrapper"><input type="color" v-model="settings.bodyGradientColor1" class="yab-color-picker"><input type="text" v-model="settings.bodyGradientColor1" class="yab-hex-input"></div>
                        <div class="yab-color-input-wrapper"><input type="color" v-model="settings.bodyGradientColor2" class="yab-color-picker"><input type="text" v-model="settings.bodyGradientColor2" class="yab-hex-input"></div>
                    </div>
                </div>
                <div>
                    <label class="setting-label-sm">Gradient Angle</label>
                    <input type="number" v-model.number="settings.bodyGradientAngle" class="yab-form-input" placeholder="e.g., 90">
                </div>
            </div>
        </div>
         <hr class="section-divider">
         <div>
            <h4 class="section-title">Layout</h4>
             <div class="grid grid-cols-2 gap-2">
                 <div>
                    <label class="setting-label-sm">Padding Y (px)</label>
                    <input type="number" v-model.number="settings.bodyPaddingY" class="yab-form-input" placeholder="e.g., 5">
                 </div>
                 <div>
                    <label class="setting-label-sm">Padding X (px)</label>
                    <input type="number" v-model.number="settings.bodyPaddingX" class="yab-form-input" placeholder="e.g., 20">
                </div>
            </div>
        </div>
        <hr class="section-divider">
        <div>
            <h4 class="section-title">Content</h4>
            <div v-if="currentView === 'desktop'">
                <div class="flex justify-between items-center mb-1">
                    <label class="setting-label-sm">Body Text (Use [[placeholder]] for links)</label>
                    <button @click="makeSelectedTextPlaceholder" class="text-xs bg-[#00baa4] text-white px-2 py-1 rounded-md hover:bg-opacity-80 transition-all">Create Link from Selected Text</button>
                </div>
                <textarea ref="bodyTextarea" v-model="settings.bodyText" rows="4" class="yab-form-input mb-2 text-white" placeholder="e.g., For more info, see [[our guide]]."></textarea>
            </div>
             <div class="grid grid-cols-2 gap-2">
                 <div class="grid grid-cols-2 gap-2" :class="currentView === 'desktop' ? 'col-span-1' : 'col-span-2'">
                   <div>
                       <label class="setting-label-sm">Font Size (px)</label>
                       <input type="number" v-model.number="settings.bodyFontSize" class="yab-form-input" placeholder="e.g., 15">
                   </div>
                     <div>
                        <label class="setting-label-sm">Font Weight</label>
                        <select v-model="settings.bodyFontWeight" class="yab-form-input"><option value="400">Normal</option><option value="700">Bold</option></select>
                    </div>
                </div>
                <div v-if="currentView === 'desktop'">
                    <label class="setting-label-sm">Text Color</label>
                    <div class="yab-color-input-wrapper"><input type="color" v-model="settings.bodyTextColor" class="yab-color-picker"><input type="text" v-model="settings.bodyTextColor" class="yab-hex-input" placeholder="#212121"></div>
                </div>
            </div>
        </div>
         <hr class="section-divider">
        <div v-if="currentView === 'desktop'">
             <h4 class="section-title">Links</h4>
             <div v-if="banner.promotion.links.length === 0" class="text-center text-gray-400 text-sm py-2">
                No links defined. Select text in the body and use the button above to create one.
             </div>
             <div v-for="(link, index) in banner.promotion.links" :key="index" class="space-y-2 bg-[#292929] p-3 rounded-md mb-3">
                <div class="flex justify-between items-center mb-2">
                    <label class="setting-label-sm font-bold text-gray-300">Link for [[{{ link.placeholder }}]]</label>
                    <button @click="removePromoLink(index)" class="text-red-500 text-xs hover:text-red-400">Remove</button>
                </div>
                <label class="setting-label-sm">URL</label>
                <input type="text" v-model="link.url" class="yab-form-input" placeholder="https://example.com">
                <label class="setting-label-sm">Link Color</label>
                <div class="yab-color-input-wrapper"><input type="color" v-model="link.color" class="yab-color-picker"><input type="text" v-model="link.color" class="yab-hex-input" placeholder="#FF731B"></div>
             </div>
        </div>
    </div>
</div>