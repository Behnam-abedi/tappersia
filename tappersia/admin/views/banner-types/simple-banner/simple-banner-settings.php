<div :set="settings = currentView === 'desktop' ? banner.simple : banner.simple_mobile">
    <div>
        <h4 class="section-title">Background</h4>
        <div class="flex gap-2 mb-2 bg-[#292929] rounded-lg border-none">
            <button @click="settings.backgroundType = 'solid'" :class="{'active-tab': settings.backgroundType === 'solid'}" class="flex-1 tab-button rounded-l-lg border-none">Solid Color</button>
            <button @click="settings.backgroundType = 'gradient'" :class="{'active-tab': settings.backgroundType === 'gradient'}" class="flex-1 tab-button rounded-r-lg border-none">Gradient</button>
        </div>
        <div v-if="settings.backgroundType === 'solid'" class="space-y-2">
            <label class="setting-label-sm">Background Color</label>
            <div class="flex items-center gap-1">
                <div
                    :style="{ backgroundColor: currentView === 'desktop' ? banner.simple.bgColor : banner.simple_mobile.bgColor }"
                    class="w-8 h-[40px] rounded border border-gray-500 flex-shrink-0"
                    title="Selected color preview">
                </div>
                <input
                    aria-label="Color input"
                    type="text"
                    :value="currentView === 'desktop' ? banner.simple.bgColor : banner.simple_mobile.bgColor"
                    @input="event => { if (currentView === 'desktop') banner.simple.bgColor = event.target.value; else banner.simple_mobile.bgColor = event.target.value; }"
                    data-coloris
                    class="yab-form-input clr-field flex-grow"
                    placeholder="Select color...">
            </div>
        </div>
        <div v-else class="space-y-4">
            <div>
                <label class="setting-label-sm">Gradient Angle: {{ settings.gradientAngle }}deg</label>
                <div class="flex items-center gap-2">
                    <input type="range" v-model.number="settings.gradientAngle" min="0" max="360" class="w-full">
                    <input type="number" v-model.number="settings.gradientAngle" class="yab-form-input w-20 text-center">
                </div>
            </div>
            <div>
                <label class="setting-label-sm">Gradient Colors</label>
                <div v-for="(stop, index) in settings.gradientStops" :key="index" class="bg-[#292929] p-3 rounded-lg mb-2 space-y-2">
                    <div class="flex items-center justify-between">
                         <span class="text-xs font-bold text-gray-300">Color Stop #{{ index + 1 }}</span>
                         <button v-if="settings.gradientStops.length > 1" @click="removeGradientStop(settings, index)" class="text-red-500 hover:text-red-400 text-xs">Remove</button>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="flex items-center gap-1">
                            <div
                                :style="{ backgroundColor: stop.color }"
                                class="w-8 h-[40px] rounded border border-gray-500 flex-shrink-0"
                                title="Selected color preview">
                            </div>
                            <input
                                aria-label="Gradient color input"
                                type="text"
                                :value="stop.color"
                                @input="event => stop.color = event.target.value"
                                data-coloris
                                class="yab-form-input clr-field flex-grow"
                                placeholder="Select color...">
                        </div>
                        <button @click="stop.color = 'transparent'" class="bg-gray-600 text-white text-xs rounded-md hover:bg-gray-500">Set Transparent</button>
                    </div>
                    <div>
                        <label class="setting-label-sm">Position: {{ stop.stop }}%</label>
                        <input type="range" v-model.number="stop.stop" min="0" max="100" class="w-full">
                    </div>
                </div>
                 <button @click="addGradientStop(settings)" class="w-full bg-blue-600 text-white text-sm py-2 rounded-md hover:bg-blue-700 mt-2">Add Color Stop</button>
            </div>
        </div>
    </div>
    <hr class="section-divider">
     <div>
        <h4 class="section-title">Layout</h4>
         <div class="grid grid-cols-2 gap-2">
            <div>
                <label class="setting-label-sm">Min Height (px)</label>
                <input type="number" v-model.number="settings.minHeight" class="yab-form-input" placeholder="e.g., 74">
             </div>
             <div>
                <label class="setting-label-sm">Border Radius (px)</label>
                <input type="number" v-model.number="settings.borderRadius" class="yab-form-input" placeholder="e.g., 10">
            </div>
        </div>
        <div v-if="currentView === 'desktop'" class="mt-4">
            <label class="setting-label-sm">Content Direction</label>
            <div class="flex rounded-lg bg-[#292929] overflow-hidden">
                <button @click="settings.direction = 'ltr'" :class="settings.direction === 'ltr' ? 'active-tab' : ''" class="flex-1 tab-button rounded-l-lg">Left to Right</button>
                <button @click="settings.direction = 'rtl'" :class="settings.direction === 'rtl' ? 'active-tab' : ''" class="flex-1 tab-button rounded-r-lg">Right to Left</button>
            </div>
        </div>
    </div>
    <hr class="section-divider">
    <div>
         <h4 class="section-title">Content Padding</h4>
         <div class="grid grid-cols-2 gap-2">
             <div>
                <label class="setting-label-sm">Top/Bottom (px)</label>
                <input type="number" v-model.number="settings.paddingY" class="yab-form-input" placeholder="e.g., 26">
             </div>
             <div>
                <label class="setting-label-sm">Left/Right</label>
                <div class="flex items-center gap-1">
                    <input type="number" v-model.number="settings.paddingX" class="yab-form-input" placeholder="e.g., 40">
                    <select v-model="settings.paddingXUnit" class="yab-form-input w-20"><option>px</option><option>%</option></select>
                </div>
            </div>
         </div>
    </div>
    <hr class="section-divider">
    <div class="space-y-2">
        <h4 class="section-title">Text</h4>
        <div v-if="currentView === 'desktop'">
            <label class="setting-label-sm">Text</label>
            <input type="text" v-model="settings.text" class="yab-form-input mb-2" placeholder="Banner Text">
        </div>
        <div class="grid grid-cols-2 gap-2">
            <div v-if="currentView === 'desktop'">
                <label class="setting-label-sm">Color</label>
                <div class="flex items-center gap-1">
                    <div
                        :style="{ backgroundColor: settings.textColor }"
                        class="w-8 h-[40px] rounded border border-gray-500 flex-shrink-0"
                        title="Selected color preview">
                    </div>
                    <input
                        aria-label="Text color input"
                        type="text"
                        :value="settings.textColor"
                        @input="event => settings.textColor = event.target.value"
                        data-coloris
                        class="yab-form-input clr-field flex-grow"
                        placeholder="Select color...">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-2" :class="{'col-span-2': currentView === 'mobile'}">
                <div>
                    <label class="setting-label-sm">Font Size (px)</label>
                    <input type="number" v-model.number="settings.textSize" class="yab-form-input" placeholder="e.g., 17">
                </div>
                <div>
                    <label class="setting-label-sm">Font Weight</label>
                    <select v-model="settings.textWeight" class="yab-form-input">
                        <option value="400">Normal</option><option value="500">Medium</option><option value="600">Semi-Bold</option><option value="700">Bold</option><option value="800">Extra Bold</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <hr class="section-divider">
    <div class="space-y-2">
        <h4 class="section-title">Button</h4>
        <div v-if="currentView === 'desktop'">
            <label class="setting-label-sm">Button Text</label>
            <input type="text" v-model="settings.buttonText" class="yab-form-input mb-2" placeholder="Button Text">
            <label class="setting-label-sm">Button Link (URL)</label>
            <input type="text" v-model="settings.buttonLink" class="yab-form-input mb-4" placeholder="https://example.com">
        </div>
        
        <div class="grid grid-cols-2 gap-2 mb-2" v-if="currentView === 'desktop'">
            <div>
                <label class="setting-label-sm">Background Color</label>
                <div class="flex items-center gap-1">
                    <div
                        :style="{ backgroundColor: settings.buttonBgColor }"
                        class="w-8 h-[40px] rounded border border-gray-500 flex-shrink-0"
                        title="Selected color preview">
                    </div>
                    <input
                        aria-label="Button background color input"
                        type="text"
                        :value="settings.buttonBgColor"
                        @input="event => settings.buttonBgColor = event.target.value"
                        data-coloris
                        class="yab-form-input clr-field flex-grow"
                        placeholder="Select color...">
                </div>
            </div>
            <div>
                <label class="setting-label-sm">Text Color</label>
                <div class="flex items-center gap-1">
                    <div
                        :style="{ backgroundColor: settings.buttonTextColor }"
                        class="w-8 h-[40px] rounded border border-gray-500 flex-shrink-0"
                        title="Selected color preview">
                    </div>
                    <input
                        aria-label="Button text color input"
                        type="text"
                        :value="settings.buttonTextColor"
                        @input="event => settings.buttonTextColor = event.target.value"
                        data-coloris
                        class="yab-form-input clr-field flex-grow"
                        placeholder="Select color...">
                </div>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-2">
            <div>
               <label class="setting-label-sm">Border Radius (px)</label>
               <input type="number" v-model.number="settings.buttonBorderRadius" class="yab-form-input" placeholder="e.g., 3">
            </div>
            <div>
                <label class="setting-label-sm">Font Size (px)</label>
                <input type="number" v-model.number="settings.buttonFontSize" class="yab-form-input" placeholder="e.g., 8">
            </div>
            <div>
                <label class="setting-label-sm">Font Weight</label>
                 <select v-model="settings.buttonFontWeight" class="yab-form-input">
                    <option value="400">Normal</option><option value="500">Medium</option><option value="600">Semi-Bold</option><option value="700">Bold</option>
                </select>
            </div>
             <div>
                <label class="setting-label-sm">Min Width (px)</label>
                <input type="number" v-model.number="settings.buttonMinWidth" class="yab-form-input" placeholder="e.g., 72">
            </div>
        </div>
        <div>
            <h4 class="section-title mt-4">Button Padding (px)</h4>
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="setting-label-sm">Top/Bottom</label>
                    <input type="number" v-model.number="settings.buttonPaddingY" class="yab-form-input" placeholder="e.g., 7">
                 </div>
                 <div>
                    <label class="setting-label-sm">Left/Right</label>
                    <input type="number" v-model.number="settings.buttonPaddingX" class="yab-form-input" placeholder="e.g., 15">
                </div>
            </div>
        </div>
    </div>
</div>
