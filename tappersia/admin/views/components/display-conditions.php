<?php
// tappersia/admin/views/components/display-conditions.php
?>
<?php // *** FIX: The redundant v-if="banner.displayMethod === 'Fixed'" has been removed from the root div *** ?>
<div class="bg-[#434343] p-4 rounded-lg">
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