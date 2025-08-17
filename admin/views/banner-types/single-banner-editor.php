<header class="sticky top-7 bg-[#434343]/60 backdrop-blur-md p-4 z-20 flex items-center justify-between shadow-lg ltr">
    <div class="flex items-center gap-4">
        <a :href="allBannersUrl" class="text-gray-400 hover:text-white">&larr; All Banners</a>
        <span class="text-gray-600">|</span>
        <span class="text-sm">Title:</span>
        <input type="text" v-model="banner.name" placeholder="Enter Banner Name..." class="bg-[#656565] text-white border border-gray-600 rounded px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-[#00baa4] w-64">
    </div>

    <div class="flex items-center gap-5">
        <div class="flex items-center gap-2">
            <span class="text-sm">Display Method:</span>
            <div class="flex rounded-lg bg-[#656565] overflow-hidden">
                <button @click="banner.displayMethod = 'Fixed'" :class="banner.displayMethod === 'Fixed' ? 'bg-[#00baa4] text-white' : 'text-gray-300'" class="px-3 py-1 text-sm transition-colors duration-300 flex-1">Fixed</button>
                <button @click="banner.displayMethod = 'Embeddable'" :class="banner.displayMethod === 'Embeddable' ? 'bg-[#00baa4] text-white' : 'text-gray-300'" class="px-3 py-1 text-sm transition-colors duration-300 flex-1">Embeddable</button>
            </div>
        </div>

        <div v-if="banner.displayMethod === 'Embeddable'" class="flex items-center gap-2">
            <span class="text-sm">Shortcode:</span>
            <input type="text" :value="shortcode" readonly @click="copyShortcode" class="w-52 bg-[#656565] text-white text-left rounded px-2 py-1 text-sm cursor-pointer" title="Click to copy">
        </div>

        <div class="flex items-center gap-2">
            <span class="text-sm">Status:</span>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" v-model="banner.isActive" class="sr-only peer">
                <div class="w-11 h-6 bg-gray-600 rounded-full peer peer-focus:ring-2 peer-focus:ring-red-500 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div>
            </label>
        </div>

        <button @click="saveBanner" :disabled="isSaving" class="bg-[#00baa4] text-white font-bold px-8 py-1.5 rounded hover:bg-opacity-80 transition-all flex items-center gap-2 disabled:bg-gray-500 disabled:cursor-not-allowed">
            <span v-if="isSaving" class="dashicons dashicons-update animate-spin"></span>
            {{ isSaving ? 'Saving...' : 'Save' }}
        </button>
    </div>
</header>

<main class="grid grid-cols-12 gap-6 p-6 ltr">
    <div class="col-span-4 overflow-y-auto flex flex-col gap-6 [&>*:last-child]:mb-[40px]" style="max-height: calc(100vh - 120px);">
        <div class="bg-[#434343] p-5 rounded-lg shadow-xl">
            <h3 class="font-bold text-xl text-white tracking-wide mb-5">Banner Settings</h3>
            
            <div class="flex flex-col gap-5">
                <div>
                    <h4 class="section-title">Background</h4>
                    <div class="flex gap-2 mb-2">
                        <button @click="banner.single.backgroundType = 'solid'" :class="{'active-tab': banner.single.backgroundType === 'solid'}" class="flex-1 tab-button">Solid Color</button>
                        <button @click="banner.single.backgroundType = 'gradient'" :class="{'active-tab': banner.single.backgroundType === 'gradient'}" class="flex-1 tab-button">Gradient</button>
                    </div>
                    <div v-if="banner.single.backgroundType === 'solid'" class="flex items-center gap-2">
                        <input type="color" v-model="banner.single.bgColor" class="yab-color-picker">
                        <input type="text" v-model="banner.single.bgColor" class="flex-1 text-input" placeholder="#hexcode">
                    </div>
                    <div v-else>
                        <div class="flex items-center gap-2 mb-2">
                            <input type="color" v-model="banner.single.gradientColor1" class="yab-color-picker">
                            <input type="text" v-model="banner.single.gradientColor1" class="flex-1 text-input" placeholder="#hexcode">
                            <input type="color" v-model="banner.single.gradientColor2" class="yab-color-picker">
                            <input type="text" v-model="banner.single.gradientColor2" class="flex-1 text-input" placeholder="#hexcode">
                        </div>
                        <div class="flex items-center gap-2">
                            <label class="text-sm text-gray-300">Angle:</label>
                            <input type="number" v-model.number="banner.single.gradientAngle" class="w-20 text-input" placeholder="e.g., 90">
                            <span class="text-sm text-gray-400">deg</span>
                        </div>
                    </div>
                </div>
                <hr class="section-divider">
                <div>
                    <h4 class="section-title">Image</h4>
                    <div class="flex gap-2 items-center">
                        <button @click="openMediaUploader('single')" class="flex-1 bg-blue-600 text-white px-3 py-1.5 rounded-md hover:bg-blue-700 text-sm">
                            {{ banner.single.imageUrl ? 'Change Image' : 'Select Image' }}
                        </button>
                        <button v-if="banner.single.imageUrl" @click="removeImage('single')" class="bg-red-600 text-white px-3 py-1.5 rounded-md hover:bg-red-700 text-sm">
                            Remove
                        </button>
                    </div>
                    <div v-if="banner.single.imageUrl" class="mt-3 space-y-3">
                        <div v-if="!banner.single.enableCustomImageSize" class="flex items-center gap-2">
                            <label class="text-sm text-gray-300 w-20">Image Fit:</label>
                            <select v-model="banner.single.imageFit" class="flex-1 select-input">
                                <option value="cover">Cover</option>
                                <option value="contain">Contain</option>
                                <option value="fill">Fill</option>
                                <option value="none">None (Natural Size)</option>
                            </select>
                        </div>
                        <div class="flex items-center justify-between bg-[#292929] p-2 rounded-md">
                            <label class="text-sm text-gray-300">Enable Custom Image Size</label>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" v-model="banner.single.enableCustomImageSize" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-600 rounded-full peer peer-focus:ring-2 peer-focus:ring-blue-500 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div>
                            </label>
                        </div>
                         <div v-if="banner.single.enableCustomImageSize" class="grid grid-cols-2 gap-2">
                            <input type="number" v-model.number="banner.single.imageWidth" class="text-input" placeholder="Width (px)">
                            <input type="number" v-model.number="banner.single.imageHeight" class="text-input" placeholder="Height (px)">
                        </div>
                         <div class="grid grid-cols-2 gap-2 mt-2">
                             <input type="number" v-model.number="banner.single.imagePosRight" class="text-input" placeholder="Right (px)">
                             <input type="number" v-model.number="banner.single.imagePosBottom" class="text-input" placeholder="Bottom (px)">
                        </div>
                    </div>
                </div>
                <hr class="section-divider">
                <div>
                    <h4 class="section-title">Content Alignment</h4>
                    <div class="flex rounded-lg bg-[#292929] overflow-hidden">
                        <button @click="banner.single.alignment = 'right'" :class="banner.single.alignment === 'right' ? 'bg-[#00baa4] text-white' : 'text-gray-300'" class="px-3 py-1 text-sm transition-colors duration-300 flex-1">Left</button>
                        <button @click="banner.single.alignment = 'center'" :class="banner.single.alignment === 'center' ? 'bg-[#00baa4] text-white' : 'text-gray-300'" class="px-3 py-1 text-sm transition-colors duration-300 flex-1">Center</button>
                        <button @click="banner.single.alignment = 'left'" :class="banner.single.alignment === 'left' ? 'bg-[#00baa4] text-white' : 'text-gray-300'" class="px-3 py-1 text-sm transition-colors duration-300 flex-1">Right</button>
                    </div>
                </div>
                <hr class="section-divider">
                <div>
                    <h4 class="section-title">Title</h4>
                    <input type="text" v-model="banner.single.titleText" class="w-full text-input mb-2" placeholder="Title Text">
                    <div class="grid grid-cols-4 gap-2">
                        <input type="color" v-model="banner.single.titleColor" class="yab-color-picker !w-full">
                        <input type="text" v-model="banner.single.titleColor" class="text-input" placeholder="#hexcode">
                        <input type="number" v-model.number="banner.single.titleSize" class="text-input" placeholder="Size (px)">
                        <select v-model="banner.single.titleWeight" class="select-input">
                            <option value="400">Normal</option><option value="500">Medium</option><option value="600">Semi-Bold</option><option value="700">Bold</option><option value="800">Extra Bold</option>
                        </select>
                    </div>
                </div>
                <hr class="section-divider">
                <div>
                    <h4 class="section-title">Description</h4>
                    <textarea v-model="banner.single.descText" rows="3" class="w-full text-input mb-2" placeholder="Description Text"></textarea>
                    <div class="grid grid-cols-4 gap-2">
                        <input type="color" v-model="banner.single.descColor" class="yab-color-picker !w-full">
                        <input type="text" v-model="banner.single.descColor" class="text-input" placeholder="#hexcode">
                        <input type="number" v-model.number="banner.single.descSize" class="text-input" placeholder="Size (px)">
                        <select v-model="banner.single.descWeight" class="select-input">
                            <option value="400">Normal</option><option value="500">Medium</option><option value="600">Semi-Bold</option>
                        </select>
                    </div>
                </div>
                <hr class="section-divider">
                <div>
                    <h4 class="section-title">Button</h4>
                    <input type="text" v-model="banner.single.buttonText" class="w-full text-input mb-2" placeholder="Button Text">
                    <input type="text" v-model="banner.single.buttonLink" class="w-full text-input mb-2" placeholder="Button Link (URL)">
                    <div class="grid grid-cols-4 gap-2 items-center mb-2">
                        <input type="color" v-model="banner.single.buttonBgColor" class="yab-color-picker !w-full" title="Button BG">
                        <input type="text" v-model="banner.single.buttonBgColor" class="text-input !w-full" placeholder="BG #hex">
                        <input type="color" v-model="banner.single.buttonTextColor" class="yab-color-picker !w-full" title="Button Text">
                        <input type="text" v-model="banner.single.buttonTextColor" class="text-input !w-full" placeholder="Text #hex">
                    </div>
                    <div class="grid grid-cols-3 gap-2 items-center">
                        <input type="color" v-model="banner.single.buttonBgHoverColor" class="yab-color-picker !w-full" title="Button Hover BG">
                        <input type="text" v-model="banner.single.buttonBgHoverColor" class="text-input !w-full" placeholder="Hover #hex">
                         <input type="number" v-model.number="banner.single.buttonFontSize" class="text-input !w-full" placeholder="Font Size (px)">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-span-8 sticky top-[120px] space-y-4">
        <div class="bg-[#434343] p-4 rounded-lg">
            <h3 class="preview-title">Live Preview</h3>
            <div class="flex justify-center">
                <div class="rounded-lg relative overflow-hidden flex flex-shrink-0" 
                    :style="{ background: bannerStyles(banner.single), width: '886px', height: '178px' }">
                    
                    <img v-if="banner.single.imageUrl" :src="banner.single.imageUrl" :style="imageStyleObject(banner.single)" />

                    <div class="w-full h-full p-8 flex flex-col z-10 relative" :style="{ alignItems: contentAlignment(banner.single.alignment), textAlign: banner.single.alignment === 'left' ? 'right' : (banner.single.alignment === 'right' ? 'left' : 'center') }">
                        <h4 :style="{ color: banner.single.titleColor, fontSize: banner.single.titleSize + 'px', fontWeight: banner.single.titleWeight, margin: 0 }">{{ banner.single.titleText }}</h4>
                        <p class="mt-2 mb-6" :style="{ color: banner.single.descColor, fontSize: banner.single.descSize + 'px', fontWeight: banner.single.descWeight, whiteSpace: 'pre-wrap' }">{{ banner.single.descText }}</p>
                        <a v-if="banner.single.buttonText" :href="banner.single.buttonLink" target="_blank" 
                            class="py-2 px-4 rounded mt-auto" 
                            :style="{ backgroundColor: banner.single.buttonBgColor, color: banner.single.buttonTextColor, fontSize: banner.single.buttonFontSize + 'px', alignSelf: buttonAlignment(banner.single.alignment) }">
                            {{ banner.single.buttonText }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="banner.displayMethod === 'Fixed'" class="bg-[#434343] p-4 rounded-lg">
            <h3 class="preview-title">Display Conditions</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 ltr">
                <div class="condition-box">
                    <label class="condition-label">Show on Posts</label>
                    <input type="search" v-model="searchTerms.posts" @input="searchContent('posts')" placeholder="Search posts..." class="search-input">
                    <div class="h-48 overflow-y-auto p-2 space-y-1">
                         <div v-if="searchLoading.posts" class="text-center text-gray-400 py-2">Loading...</div>
                         <div v-else-if="sortedPosts.length === 0" class="text-center text-gray-400 py-2">No results found.</div>
                         <label v-else v-for="post in sortedPosts" :key="`post-${post.ID}`" class="checkbox-label">
                            <input type="checkbox" :value="post.ID" v-model="banner.displayOn.posts" class="!hidden peer" />
                            <span class="checkbox-custom">
                                <svg v-if="banner.displayOn.posts.includes(post.ID)" class="w-3 h-3 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                            </span>
                            <span class="checkbox-text">{{ post.post_title }}</span>
                        </label>
                    </div>
                </div>
                <div class="condition-box">
                    <label class="condition-label">Show on Categories</label>
                    <input type="search" v-model="searchTerms.categories" @input="searchContent('categories')" placeholder="Search categories..." class="search-input">
                    <div class="h-48 overflow-y-auto p-2 space-y-1">
                        <div v-if="searchLoading.categories" class="text-center text-gray-400 py-2">Loading...</div>
                        <div v-else-if="sortedCategories.length === 0" class="text-center text-gray-400 py-2">No results found.</div>
                        <label v-else v-for="cat in sortedCategories" :key="`cat-${cat.term_id}`" class="checkbox-label">
                            <input type="checkbox" :value="cat.term_id" v-model="banner.displayOn.categories" class="!hidden peer"/>
                            <span class="checkbox-custom">
                                <svg v-if="banner.displayOn.categories.includes(cat.term_id)" class="w-3 h-3 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                            </span>
                            <span class="checkbox-text">{{ cat.name }}</span>
                        </label>
                    </div>
                </div>
                <div class="condition-box">
                    <label class="condition-label">Show on Pages</label>
                    <input type="search" v-model="searchTerms.pages" @input="searchContent('pages')" placeholder="Search pages..." class="search-input">
                    <div class="h-48 overflow-y-auto p-2 space-y-1">
                        <div v-if="searchLoading.pages" class="text-center text-gray-400 py-2">Loading...</div>
                        <div v-else-if="sortedPages.length === 0" class="text-center text-gray-400 py-2">No results found.</div>
                        <label v-else v-for="page in sortedPages" :key="`page-${page.ID}`" class="checkbox-label">
                            <input type="checkbox" :value="page.ID" v-model="banner.displayOn.pages" class="!hidden peer" />
                            <span class="checkbox-custom">
                                <svg v-if="banner.displayOn.pages.includes(page.ID)" class="w-3 h-3 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                            </span>
                            <span class="checkbox-text">{{ page.post_title }}</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>