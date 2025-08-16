<header class="sticky top-7 bg-[#434343]/60 backdrop-blur-md p-7 z-20  flex items-center justify-between shadow-lg">
    <div class="flex items-center gap-4">
            <a :href="allBannersUrl" class="text-gray-400 hover:text-white">&larr; All Banners</a>
            <span class="text-gray-600">|</span>
        <input type="text" v-model="banner.name" placeholder="Enter Banner Name..." class="!bg-[#656565] !text-white border border-gray-600 rounded px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-[#00baa4] w-64 ltr">
        <span class="text-sm">: Title</span>
    </div>
    
    <div class="flex items-center gap-5">
        <div class="flex items-center gap-2">
            <div class="flex rounded-lg bg-[#656565] overflow-hidden">
                <button @click="banner.displayMethod = 'Fixed'" :class="banner.displayMethod === 'Fixed' ? 'bg-[#00baa4] text-white' : 'text-gray-300'" class="px-3 py-1 text-sm transition-colors duration-300 flex-1">Fixed</button>
                <button @click="banner.displayMethod = 'Embeddable'" :class="banner.displayMethod === 'Embeddable' ? 'bg-[#00baa4] text-white' : 'text-gray-300'" class="px-3 py-1 text-sm transition-colors duration-300 flex-1">Embeddable</button>
            </div>
            <span class="text-sm">: Display Method</span>
        </div>

        <div v-if="banner.displayMethod === 'Embeddable'" class="flex items-center gap-2">
            <input type="text" :value="shortcode" readonly @click="copyShortcode" class="w-52 !bg-[#656565] !text-white text-left rounded px-2 py-1 text-sm cursor-pointer" title="Click to copy">
            <span class="text-sm ">: Shortcode</span>
        </div>

        <div class="flex items-center">
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" v-model="banner.isActive" class="sr-only peer">
                <div class="w-11 h-6 bg-gray-600 rounded-full peer peer-focus:ring-2 peer-focus:ring-red-500 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div>
            </label>
            <span class="text-sm mr-3">: Status</span>
        </div>

        <button @click="saveBanner" :disabled="isSaving" class="bg-[#00baa4] text-white font-bold px-8 py-1.5 rounded hover:bg-opacity-80 transition-all flex items-center gap-2 disabled:bg-gray-500 disabled:cursor-not-allowed">
            <span v-if="isSaving" class="dashicons dashicons-update animate-spin"></span>
            {{ isSaving ? 'Saving...' : 'Save' }}
        </button>
    </div>
</header>

<main class="grid grid-cols-12 gap-3 p-6">
    <div class="col-span-4 overflow-y-auto ltr flex gap-3 flex-col" style="max-height: calc(100vh - 120px);">
        <div v-for="(b, key) in { left: banner.left, right: banner.right }" :key="key" class="bg-[#434343] p-5 rounded-lg shadow-xl">
            
            <h3 class="font-bold text-xl text-white capitalize tracking-wide mb-5">{{ key }} Banner Settings</h3>
            
            <div class="flex flex-col gap-5">
                <div>
                    <h4 class="section-title">Background</h4>
                    <div class="flex gap-2 mb-2">
                        <button @click="b.backgroundType = 'solid'" :class="{'active-tab': b.backgroundType === 'solid'}" class="flex-1 tab-button">Solid Color</button>
                        <button @click="b.backgroundType = 'gradient'" :class="{'active-tab': b.backgroundType === 'gradient'}" class="flex-1 tab-button">Gradient</button>
                    </div>
                    <div v-if="b.backgroundType === 'solid'" class="flex items-center gap-2">
                        <input type="color" v-model="b.bgColor" class="yab-color-picker">
                        <input type="text" v-model="b.bgColor" class="flex-1 text-input" placeholder="#hexcode">
                    </div>
                    <div v-else>
                        <div class="flex items-center gap-2 mb-2 ">
                            <input type="color" v-model="b.gradientColor1" class="yab-color-picker">
                            <input type="text" v-model="b.gradientColor1" class="flex-1 text-input" placeholder="#hexcode">
                            <input type="color" v-model="b.gradientColor2" class="yab-color-picker">
                            <input type="text" v-model="b.gradientColor2" class="flex-1 text-input" placeholder="#hexcode">
                        </div>
                        <div class="flex items-center gap-2">
                            <label class="text-sm text-gray-300">Angle:</label>
                            <input type="number" v-model.number="b.gradientAngle" class="w-20 text-input" placeholder="e.g., 90">
                            <span class="text-sm text-gray-400">deg</span>
                        </div>
                    </div>
                </div>
                <hr class="section-divider">
                <div>
                    <h4 class="section-title">Image</h4>
                    <div class="flex gap-2 items-center">
                        <button @click="openMediaUploader(key)" class="flex-1 bg-blue-600 text-white px-3 py-1.5 rounded-md hover:bg-blue-700 text-sm">
                            {{ b.imageUrl ? 'Change Image' : 'Select Image' }}
                        </button>
                        <button v-if="b.imageUrl" @click="removeImage(key)" class="bg-red-600 text-white px-3 py-1.5 rounded-md hover:bg-red-700 text-sm">
                            Remove
                        </button>
                    </div>
                    <div v-if="b.imageUrl" class="mt-3 space-y-3">
                        <div v-if="!b.enableCustomImageSize" class="flex items-center gap-2">
                            <label class="text-sm text-gray-300 w-20">Image Fit:</label>
                            <select v-model="b.imageFit" class="flex-1 select-input">
                                <option value="cover">Cover</option>
                                <option value="contain">Contain</option>
                                <option value="fill">Fill</option>
                                <option value="none">None (Natural Size)</option>
                            </select>
                        </div>
                        <div class="flex items-center justify-between bg-[#292929] p-2 rounded-md">
                            <label class="text-sm text-gray-300">Enable Custom Image Size</label>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" v-model="b.enableCustomImageSize" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-600 rounded-full peer peer-focus:ring-2 peer-focus:ring-blue-500 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div>
                            </label>
                        </div>
                         <div v-if="b.enableCustomImageSize" class="grid grid-cols-2 gap-2">
                            <input type="number" v-model.number="b.imageWidth" class="text-input" placeholder="Width (px)">
                            <input type="number" v-model.number="b.imageHeight" class="text-input" placeholder="Height (px)">
                        </div>
                         <div class="grid grid-cols-2 gap-2 mt-2">
                             <input type="number" v-model.number="b.imagePosRight" class="text-input" placeholder="Right (px)">
                             <input type="number" v-model.number="b.imagePosBottom" class="text-input" placeholder="Bottom (px)">
                        </div>
                    </div>
                </div>
                <hr class="section-divider">
                <div>
                        <h4 class="section-title">Content Alignment</h4>
                        <div class="flex rounded-lg bg-[#292929] overflow-hidden ">
                        <button @click="b.alignment = 'right'" :class="b.alignment === 'right' ? 'bg-[#00baa4] text-white' : 'text-gray-300'" class="px-3 py-1 text-sm transition-colors duration-300 flex-1">Left</button>
                        <button @click="b.alignment = 'center'" :class="b.alignment === 'center' ? 'bg-[#00baa4] text-white' : 'text-gray-300'" class="px-3 py-1 text-sm transition-colors duration-300 flex-1">Center</button>
                        <button @click="b.alignment = 'left'" :class="b.alignment === 'left' ? 'bg-[#00baa4] text-white' : 'text-gray-300'" class="px-3 py-1 text-sm transition-colors duration-300 flex-1">Right</button>
                    </div>
                </div>
                <hr class="section-divider">
                <div>
                    <h4 class="section-title">Title</h4>
                    <input type="text" v-model="b.titleText" class="w-full text-input mb-2" placeholder="Title Text">
                    <div class="grid grid-cols-[auto,1fr,1fr,1fr] gap-2">
                        <input type="color" v-model="b.titleColor" class="yab-color-picker">
                        <input type="text" v-model="b.titleColor" class="text-input" placeholder="#hexcode">
                        <input type="number" v-model.number="b.titleSize" class="text-input" placeholder="Size (px)">
                        <select v-model="b.titleWeight" class="select-input">
                            <option value="400">Normal</option><option value="500">Medium</option><option value="600">Semi-Bold</option><option value="700">Bold</option><option value="800">Extra Bold</option>
                        </select>
                    </div>
                </div>
                <hr class="section-divider">
                <div>
                    <h4 class="section-title">Description</h4>
                    <textarea v-model="b.descText" rows="3" class="w-full text-input mb-2" placeholder="Description Text"></textarea>
                    <div class="grid grid-cols-[auto,1fr,1fr,1fr] gap-2">
                        <input type="color" v-model="b.descColor" class="yab-color-picker">
                        <input type="text" v-model="b.descColor" class="text-input" placeholder="#hexcode">
                        <input type="number" v-model.number="b.descSize" class="text-input" placeholder="Size (px)">
                        <select v-model="b.descWeight" class="select-input">
                            <option value="400">Normal</option><option value="500">Medium</option><option value="600">Semi-Bold</option>
                        </select>
                    </div>
                </div>
                <hr class="section-divider">
                <div>
                    <h4 class="section-title">Button</h4>
                    <input type="text" v-model="b.buttonText" class="w-full text-input mb-2" placeholder="Button Text">
                    <input type="text" v-model="b.buttonLink" class="w-full text-input mb-2" placeholder="Button Link (URL)">
                    <div class="grid grid-cols-[auto,1fr,auto,1fr] gap-2 items-center mb-2">
                        <input type="color" v-model="b.buttonBgColor" class="yab-color-picker" title="Button BG">
                        <input type="text" v-model="b.buttonBgColor" class="text-input" placeholder="BG #hex">
                        <input type="color" v-model="b.buttonTextColor" class="yab-color-picker" title="Button Text">
                        <input type="text" v-model="b.buttonTextColor" class="text-input" placeholder="Text #hex">
                    </div>
                    <div class="grid grid-cols-[auto,1fr,auto,1fr] gap-2 items-center">
                        <input type="color" v-model="b.buttonBgHoverColor" class="yab-color-picker" title="Button Hover BG">
                        <input type="text" v-model="b.buttonBgHoverColor" class="text-input" placeholder="Hover #hex">
                         <input type="number" v-model.number="b.buttonFontSize" class="text-input col-span-2" placeholder="Font Size (px)">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-span-8 sticky top-[120px] space-y-4">
        <div class="bg-[#434343] p-4 rounded-lg">
            <h3 class="preview-title">Live Preview</h3>
            <div class="flex flex-row-reverse gap-2 justify-center">
                <div v-for="(b, key) in { left: banner.left, right: banner.right }" :key="`preview-${key}`" 
                    class="rounded-[11.35px] relative overflow-hidden flex flex-shrink-0" 
                    :style="{ background: bannerStyles(b), width: '432px', height: '177px' }">
                    
                    <img v-if="b.imageUrl" :src="b.imageUrl" :style="imageStyleObject(b)" />

                    <div class="w-full h-full p-4 md:p-6 flex flex-col z-10 relative" :style="{ alignItems: contentAlignment(b.alignment), textAlign: b.alignment }">
                        <h4 class="font-bold" :style="{ color: b.titleColor, fontSize: b.titleSize + 'px', fontWeight: b.titleWeight }">{{ b.titleText }}</h4>
                        <p class="mt-2 leading-tight" :class="b.alignment==='right'?'ltr text-left':'rtl text-right'" :style="{ color: b.descColor, fontSize: b.descSize + 'px', fontWeight: b.descWeight, whiteSpace: 'pre-wrap' ,}">{{ b.descText }}</p>
                        <a v-if="b.buttonText" :href="b.buttonLink" target="_blank" 
                            class="py-2 px-4 rounded mt-auto" 
                            :style="{ backgroundColor: b.buttonBgColor, color: b.buttonTextColor, fontSize: b.buttonFontSize + 'px', alignSelf: buttonAlignment(b.alignment) }">
                            {{ b.buttonText }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="banner.displayMethod === 'Fixed'" class="bg-[#434343] p-4 rounded-lg">
            <h3 class="preview-title">Display Conditions</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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