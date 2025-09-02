<div id="yab-list-app" class="bg-[#232323] text-white min-h-screen font-sans" dir="ltr" v-cloak>

    <transition name="fade" mode="out-in">
        <div v-if="appState === 'selection'" key="selection" class="flex items-center justify-center h-screen">
            <div class="p-8 text-center">
                <h1 class="text-3xl font-bold mb-8 text-gray-200">All Elements</h1>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div @click="selectType('single-banner')" class="cursor-pointer bg-[#656565] p-8 rounded-lg transform hover:-translate-y-1 transition-all duration-300 group flex justify-center items-center flex-col gap-2">
                        <span class="dashicons dashicons-format-image text-5xl mb-4 text-[#00baa4] group-hover:text-white transition-colors flex justify-center"></span>
                        <h3 class="font-semibold text-lg text-gray-200 group-hover:text-white">Single Banners</h3>
                    </div>
                    <div @click="selectType('double-banner')" class="cursor-pointer bg-[#656565] p-8 rounded-lg transform hover:-translate-y-1 transition-all duration-300 group flex justify-center items-center flex-col gap-2">
                        <span class="dashicons dashicons-columns text-5xl mb-4 text-[#00baa4] group-hover:text-white transition-colors flex justify-center"></span>
                        <h3 class="font-semibold text-lg text-gray-200 group-hover:text-white">Double Banners</h3>
                    </div>
                     <div @click="selectType('api-banner')" class="cursor-pointer bg-[#656565] p-8 rounded-lg transform hover:-translate-y-1 transition-all duration-300 group flex justify-center items-center flex-col gap-2">
                        <span class="dashicons dashicons-rest-api text-5xl mb-4 text-[#00baa4] group-hover:text-white transition-colors flex justify-center"></span>
                        <h3 class="font-semibold text-lg text-gray-200 group-hover:text-white">API Banners</h3>
                    </div>
                    <div @click="selectType('simple-banner')" class="cursor-pointer bg-[#656565] p-8 rounded-lg transform hover:-translate-y-1 transition-all duration-300 group flex justify-center items-center flex-col gap-2">
                        <span class="dashicons dashicons-text-page text-5xl mb-4 text-[#00baa4] group-hover:text-white transition-colors flex justify-center"></span>
                        <h3 class="font-semibold text-lg text-gray-200 group-hover:text-white">Simple Banners</h3>
                    </div>
                    <div @click="selectType('sticky-simple-banner')" class="cursor-pointer bg-[#656565] p-8 rounded-lg transform hover:-translate-y-1 transition-all duration-300 group flex justify-center items-center flex-col gap-2">
                        <span class="dashicons dashicons-sticky text-5xl mb-4 text-[#00baa4] group-hover:text-white transition-colors flex justify-center"></span>
                        <h3 class="font-semibold text-lg text-gray-200 group-hover:text-white">Sticky Simple Banners</h3>
                    </div>
                    <div @click="selectType('promotion-banner')" class="cursor-pointer bg-[#656565] p-8 rounded-lg transform hover:-translate-y-1 transition-all duration-300 group flex justify-center items-center flex-col gap-2">
                        <span class="dashicons dashicons-megaphone text-5xl mb-4 text-[#00baa4] group-hover:text-white transition-colors flex justify-center"></span>
                        <h3 class="font-semibold text-lg text-gray-200 group-hover:text-white">Promotion Banners</h3>
                    </div>
                    <div @click="selectType('content-html-banner')" class="cursor-pointer bg-[#656565] p-8 rounded-lg transform hover:-translate-y-1 transition-all duration-300 group flex justify-center items-center flex-col gap-2">
                        <span class="dashicons dashicons-editor-code text-5xl mb-4 text-[#00baa4] group-hover:text-white transition-colors flex justify-center"></span>
                        <h3 class="font-semibold text-lg text-gray-200 group-hover:text-white">Content HTML</h3>
                    </div>
                    <div @click="selectType('content-html-sidebar-banner')" class="cursor-pointer bg-[#656565] p-8 rounded-lg transform hover:-translate-y-1 transition-all duration-300 group flex justify-center items-center flex-col gap-2">
                        <span class="dashicons dashicons-align-pull-right text-5xl mb-4 text-[#00baa4] group-hover:text-white transition-colors flex justify-center"></span>
                        <h3 class="font-semibold text-lg text-gray-200 group-hover:text-white">Content HTML Sidebar</h3>
                    </div>
                    </div>
            </div>
        </div>

        <div v-else-if="appState === 'list'" key="list" class="">
            <?php require YAB_PLUGIN_DIR . 'admin/views/components/banner-list-header.php'; ?>

            <div class="bg-[#434343] rounded-lg shadow-lg overflow-hidden m-6">
                <table class="w-full text-left">
                    <thead class="bg-[#292929]">
                        <tr>
                            <th class="p-4 font-semibold">Name</th>
                            <th class="p-4 font-semibold">Shortcode</th>
                            <th class="p-4 font-semibold">Display</th>
                            <th class="p-4 font-semibold">Status</th>
                            <th class="p-4 font-semibold">Date</th>
                            <th class="p-4 font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="paginatedBanners.length === 0">
                            <td colspan="6" class="text-center p-8 text-gray-400">No banners found.</td>
                        </tr>
                        <tr v-for="banner in paginatedBanners" :key="banner.id" class="border-b border-gray-700 hover:bg-[#4f4f4f] transition-colors">
                            <td class="p-4">
                                <a :href="banner.edit_url" class="font-bold hover:text-[#00baa4]">{{ banner.title }}</a>
                            </td>
                            <td class="p-4">
                                <input type="text" readonly :value="banner.shortcode" class="shortcode-input" @click="copyShortcode">
                            </td>
                            <td class="p-4 text-gray-300">{{ banner.display_method }}</td>
                            <td class="p-4">
                                <span class="inline-flex items-center gap-2 px-2 py-1 rounded-full text-xs font-medium" :class="banner.is_active ? 'bg-green-900 text-green-300' : 'bg-red-900 text-red-300'">
                                    <span class="w-2 h-2 rounded-full" :class="banner.is_active ? 'bg-green-500' : 'bg-red-500'"></span>
                                    {{ banner.is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="p-4 text-gray-400">{{ banner.date }}</td>
                            <td class="p-4">
                                <div class="flex gap-4">
                                    <a :href="banner.edit_url" class="text-blue-400 hover:text-blue-300">Edit</a>
                                    <button @click="confirmDelete(banner.id)" class="text-red-400 hover:text-red-300 bg-transparent border-none cursor-pointer p-0">Trash</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="totalPages > 1" class="flex items-center justify-center mt-6">
                <nav class="flex items-center gap-2">
                    <button @click="prevPage" :disabled="currentPage === 1" class="pagination-arrow">&laquo;</button>
                    <span class="text-gray-400">Page {{ currentPage }} of {{ totalPages }}</span>
                    <button @click="nextPage" :disabled="currentPage === totalPages" class="pagination-arrow">&raquo;</button>
                </nav>
            </div>
        </div>
    </transition>

    <yab-modal ref="modalComponent"></yab-modal>

</div>

<style>
    [v-cloak] { display: none; }
    #wpcontent { padding-left: 0; }
    .search-input { background: #292929; border: 1px solid #656565; color: white; padding: 8px 12px; border-radius: 5px; }
    .shortcode-input { background: #292929; border: 1px solid #656565; color: #a0aec0; padding: 4px 8px; border-radius: 5px; cursor: pointer; width: 100%; }
    .pagination-arrow {
        background: #434343;
        color: white;
        border: 1px solid #656565;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        transition: background-color 0.2s;
    }
    .pagination-arrow:hover:not(:disabled) { background: #656565; }
    .pagination-arrow:disabled { opacity: 0.5; cursor: not-allowed; }
</style>