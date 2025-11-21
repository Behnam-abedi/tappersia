<?php
// tappersia/admin/views/banner-types/promotion-banner/promotion-banner-settings.php
?>


<div class="bg-[#434343] p-5 rounded-lg shadow-xl mr-2">
    <h3 class="font-bold text-xl text-white tracking-wide mb-5">Device</h3>
     <div class="flex  bg-[#292929] rounded-lg p-1">
        <button @click="currentView = 'desktop'" :class="{'active-tab': currentView === 'desktop'}" class="flex-1 tab-button rounded-md">Desktop</button>
        <button @click="currentView = 'mobile'" :class="{'active-tab': currentView === 'mobile'}" class="flex-1 tab-button rounded-md">Mobile</button>
    </div>
</div>
<div class="bg-[#434343] p-5 rounded-lg shadow-xl mr-2" v-if="currentView === 'desktop'">
    <h3 class="font-bold text-xl text-white tracking-wide mb-5">Alignment</h3>
        <div >
            <div class="flex rounded-lg bg-[#292929] overflow-hidden p-1">
                <button @click="banner.promotion.direction = 'ltr'" :class="banner.promotion.direction === 'ltr' ? 'active-tab' : ''" class="flex-1 tab-button rounded-md">Left</button> <?php // FIX: Use banner.promotion.direction ?>
                <button @click="banner.promotion.direction = 'rtl'" :class="banner.promotion.direction === 'rtl' ? 'active-tab' : ''" class="flex-1 tab-button rounded-md">Right</button> <?php // FIX: Use banner.promotion.direction ?>
            </div>
        </div>
</div>

<?php // *** START: Added v-set wrapper *** ?>
<div  class="flex flex-col gap-6">

    <div class="bg-[#434343] p-5 rounded-lg shadow-xl mr-2">
        <h3 class="font-bold text-xl text-white tracking-wide mb-5">General Settings</h3>
        <div class="space-y-4">
            <div>
                <h4 class="section-title">Border</h4>
                
                <?php // --- START: Added Enable/Disable Toggle --- ?>
                <div class="flex items-center justify-between bg-[#292929] p-2 rounded-md mb-2">
                    <label class="setting-label-sm !mb-0">Enable Border</label>
                    <label class="relative inline-flex items-center cursor-pointer" title="Toggle banner border">
                        <input type="checkbox" v-model="settings.enableBorder" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-600 rounded-full peer peer-focus:ring-2 peer-focus:ring-blue-500 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div>
                    </label>
                </div>
                <?php // --- END: Added Enable/Disable Toggle --- ?>

                <?php // --- START: Added v-if --- ?>
                <div v-if="settings.enableBorder" class="grid grid-cols-3 gap-2">
                <?php // --- END: Added v-if --- ?>
                    <div v-if="currentView === 'desktop'">
                        <label class="setting-label-sm">Color</label>
                        <div class="flex items-center gap-1">
                            <div
                                :style="{ backgroundColor: settings.borderColor }"
                                class="w-8 h-[40px] rounded border border-gray-500 flex-shrink-0"
                                title="Selected color preview">
                            </div>
                            <input
                                aria-label="Border color input"
                                type="text"
                                :value="settings.borderColor"
                                @input="event => settings.borderColor = event.target.value"
                                data-coloris
                                class="yab-form-input clr-field flex-grow"
                                placeholder="Select color...">
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

        </div>
    </div>


    <div class="bg-[#434343] p-5 rounded-lg shadow-xl mr-2">
        <h3 class="font-bold text-xl text-white tracking-wide mb-5">Header Settings</h3>
        <div class="flex flex-col gap-3">
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
            
            <?php // *** START: GRADIENT UI FOR HEADER *** ?>
            <div>
                <h4 class="section-title">Background</h4>
                <div class="flex gap-2 mb-2 bg-[#292929] rounded-lg p-1">
                    <button @click="settings.headerBackgroundType = 'solid'" :class="{'active-tab': settings.headerBackgroundType === 'solid'}" class="flex-1 tab-button rounded-md">Solid</button>
                    <button @click="settings.headerBackgroundType = 'gradient'" :class="{'active-tab': settings.headerBackgroundType === 'gradient'}" class="flex-1 tab-button rounded-md">Gradient</button>
                </div>
                <div v-if="settings.headerBackgroundType === 'solid'" class="space-y-2">
                    <label class="setting-label-sm">Background Color</label>
                    <div class="flex items-center gap-1">
                        <div
                            :style="{ backgroundColor: settings.headerBgColor }"
                            class="w-8 h-[40px] rounded border border-gray-500 flex-shrink-0"
                            title="Selected color preview">
                        </div>
                        <input
                            aria-label="Header background color input"
                            type="text"
                            :value="settings.headerBgColor"
                            @input="event => settings.headerBgColor = event.target.value"
                            data-coloris
                            class="yab-form-input clr-field flex-grow"
                            placeholder="#FF731B">
                    </div>
                </div>
                <div v-else class="space-y-4">
                    <div>
                        <label class="setting-label-sm">Gradient Angle: {{ settings.headerGradientAngle }}deg</label>
                        <div class="flex items-center gap-2">
                            <input type="range" v-model.number="settings.headerGradientAngle" min="0" max="360" class="w-full">
                            <input type="number" v-model.number="settings.headerGradientAngle" class="yab-form-input w-20 text-center">
                        </div>
                    </div>
                    <div>
                        <label class="setting-label-sm">Gradient Colors</label>
                        <div v-for="(stop, index) in settings.headerGradientStops" :key="index" class="bg-[#292929] p-3 rounded-lg mb-2 space-y-2">
                            <div class="flex items-center justify-between">
                                 <span class="text-xs font-bold text-gray-300">Color Stop #{{ index + 1 }}</span>
                                 <button v-if="settings.headerGradientStops.length > 1" @click="removeGradientStop(settings.headerGradientStops, index)" class="text-red-500 hover:text-red-400 text-xs">Remove</button>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div class="flex items-center gap-1">
                                    <div
                                        :style="{ backgroundColor: stop.color }"
                                        class="w-8 h-[40px] rounded border border-gray-500 flex-shrink-0"
                                        title="Selected color preview">
                                    </div>
                                    <input
                                        aria-label="Gradient stop color input"
                                        type="text"
                                        :value="stop.color"
                                        @input="event => stop.color = event.target.value"
                                        data-coloris
                                        class="yab-form-input clr-field flex-grow"
                                        placeholder="e.g., transparent">
                                </div>
                                <button @click="stop.color = 'transparent'" class="bg-gray-600 text-white text-xs rounded-md hover:bg-gray-500">Set Transparent</button>
                            </div>
                            <div>
                                <label class="setting-label-sm">Position: {{ stop.stop }}%</label>
                                <input type="range" v-model.number="stop.stop" min="0" max="100" class="w-full">
                            </div>
                        </div>
                         <button @click="addGradientStop(settings.headerGradientStops)" class="w-full bg-blue-600 text-white text-sm py-2 rounded-md hover:bg-blue-700 mt-2">Add Color Stop</button>
                    </div>
                </div>
            </div>
            <?php // *** END: GRADIENT UI FOR HEADER *** ?>

            
            <div v-if="currentView === 'desktop'">
                <hr class="section-divider">
                <h4 class="section-title">Icon</h4>
                 <div  class="flex gap-2 items-center mb-2">
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
                <h4 class="section-title">Text</h4>
                <div v-if="currentView === 'desktop'">
                    <label class="setting-label-sm">Header Text</label>
                    <input type="text" v-model="banner.promotion.headerText" class="yab-form-input mb-2" placeholder="Header Text"> <?php // FIX: Use banner.promotion.headerText ?>
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
                        <div class="flex items-center gap-1">
                            <div
                                :style="{ backgroundColor: banner.promotion.headerTextColor }" <?php // FIX: Use banner.promotion.headerTextColor ?>
                                class="w-8 h-[40px] rounded border border-gray-500 flex-shrink-0"
                                title="Selected color preview">
                            </div>
                            <input
                                aria-label="Header text color input"
                                type="text"
                                :value="banner.promotion.headerTextColor" <?php // FIX: Use banner.promotion.headerTextColor ?>
                                @input="event => banner.promotion.headerTextColor = event.target.value" <?php // FIX: Use banner.promotion.headerTextColor ?>
                                data-coloris
                                class="yab-form-input clr-field flex-grow"
                                placeholder="#FFFFFF">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="bg-[#434343] p-5 rounded-lg shadow-xl mr-2">
        <h3 class="font-bold text-xl text-white tracking-wide mb-5">Body Settings</h3>
        <div class="flex flex-col gap-3">
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
            
            <?php // *** START: GRADIENT UI FOR BODY *** ?>
            <div>
                <h4 class="section-title">Background</h4>
                 <div class="flex gap-2 mb-2 bg-[#292929] rounded-lg p-1">
                    <button @click="settings.bodyBackgroundType = 'solid'" :class="{'active-tab': settings.bodyBackgroundType === 'solid'}" class="flex-1 tab-button rounded-md">Solid</button>
                    <button @click="settings.bodyBackgroundType = 'gradient'" :class="{'active-tab': settings.bodyBackgroundType === 'gradient'}" class="flex-1 tab-button rounded-md">Gradient</button>
                </div>
                <div v-if="settings.bodyBackgroundType === 'solid'">
                     <label class="setting-label-sm">Background Color</label>
                    <div class="flex items-center gap-1">
                        <div
                            :style="{ backgroundColor: settings.bodyBgColor }"
                            class="w-8 h-[40px] rounded border border-gray-500 flex-shrink-0"
                            title="Selected color preview">
                        </div>
                        <input
                            aria-label="Body background color input"
                            type="text"
                            :value="settings.bodyBgColor"
                            @input="event => settings.bodyBgColor = event.target.value"
                            data-coloris
                            class="yab-form-input clr-field flex-grow"
                            placeholder="#f071001f">
                    </div>
                </div>
                 <div v-else class="space-y-4">
                    <div>
                        <label class="setting-label-sm">Gradient Angle: {{ settings.bodyGradientAngle }}deg</label>
                        <div class="flex items-center gap-2">
                            <input type="range" v-model.number="settings.bodyGradientAngle" min="0" max="360" class="w-full">
                            <input type="number" v-model.number="settings.bodyGradientAngle" class="yab-form-input w-20 text-center">
                        </div>
                    </div>
                    <div>
                        <label class="setting-label-sm">Gradient Colors</label>
                        <div v-for="(stop, index) in settings.bodyGradientStops" :key="index" class="bg-[#292929] p-3 rounded-lg mb-2 space-y-2">
                            <div class="flex items-center justify-between">
                                 <span class="text-xs font-bold text-gray-300">Color Stop #{{ index + 1 }}</span>
                                 <button v-if="settings.bodyGradientStops.length > 1" @click="removeGradientStop(settings.bodyGradientStops, index)" class="text-red-500 hover:text-red-400 text-xs">Remove</button>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div class="flex items-center gap-1">
                                    <div
                                        :style="{ backgroundColor: stop.color }"
                                        class="w-8 h-[40px] rounded border border-gray-500 flex-shrink-0"
                                        title="Selected color preview">
                                    </div>
                                    <input
                                        aria-label="Gradient stop color input"
                                        type="text"
                                        :value="stop.color"
                                        @input="event => stop.color = event.target.value"
                                        data-coloris
                                        class="yab-form-input clr-field flex-grow"
                                        placeholder="e.g., transparent">
                                </div>
                                <button @click="stop.color = 'transparent'" class="bg-gray-600 text-white text-xs rounded-md hover:bg-gray-500">Set Transparent</button>
                            </div>
                            <div>
                                <label class="setting-label-sm">Position: {{ stop.stop }}%</label>
                                <input type="range" v-model.number="stop.stop" min="0" max="100" class="w-full">
                            </div>
                        </div>
                         <button @click="addGradientStop(settings.bodyGradientStops)" class="w-full bg-blue-600 text-white text-sm py-2 rounded-md hover:bg-blue-700 mt-2">Add Color Stop</button>
                    </div>
                </div>
            </div>
            <?php // *** END: GRADIENT UI FOR BODY *** ?>

             <hr class="section-divider">

            <div>
                <h4 class="section-title">Content</h4>
                <div v-if="currentView === 'desktop'">
                    <div class="flex justify-between items-center mb-1">
                        <label class="setting-label-sm">Body Text</label>
                        <button @click="makeSelectedTextPlaceholder" class="text-xs bg-[#00baa4] text-white px-2 py-1 rounded-md hover:bg-opacity-80 transition-all">Create Link</button>
                    </div>
                    <textarea ref="bodyTextarea" v-model="banner.promotion.bodyText" rows="4" class="yab-form-input mb-2 text-white" placeholder="e.g., For more info, see [[our guide]]."></textarea> <?php // FIX: Use banner.promotion.bodyText ?>
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
                        <div class="flex items-center gap-1">
                            <div
                                :style="{ backgroundColor: banner.promotion.bodyTextColor }" <?php // FIX: Use banner.promotion.bodyTextColor ?>
                                class="w-8 h-[40px] rounded border border-gray-500 flex-shrink-0"
                                title="Selected color preview">
                            </div>
                            <input
                                aria-label="Body text color input"
                                type="text"
                                :value="banner.promotion.bodyTextColor" <?php // FIX: Use banner.promotion.bodyTextColor ?>
                                @input="event => banner.promotion.bodyTextColor = event.target.value" <?php // FIX: Use banner.promotion.bodyTextColor ?>
                                data-coloris
                                class="yab-form-input clr-field flex-grow"
                                placeholder="#212121">
                        </div>
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
                    <div class="flex items-center gap-1">
                        <div
                            :style="{ backgroundColor: link.color }"
                            class="w-8 h-[40px] rounded border border-gray-500 flex-shrink-0"
                            title="Selected color preview">
                        </div>
                        <input
                            aria-label="Link color input"
                            type="text"
                            :value="link.color"
                            @input="event => link.color = event.target.value"
                            data-coloris
                            class="yab-form-input clr-field flex-grow"
                            placeholder="#FF731B">
                    </div>
                 </div>
            </div>
        </div>
    </div>
</div> <?php // *** END: Added v-set wrapper *** ?>