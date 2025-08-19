// import { reactive, computed } from 'vue'; // <--- این خط حذف شد

const { reactive, computed } = Vue; // <--- این خط جایگزین شد

/**
 * Manages the logic for Display Conditions (searching and sorting posts, pages, categories).
 */
export function useDisplayConditions(banner, ajax) {
    const siteData = reactive({ posts: [], pages: [], categories: [] });
    const searchTerms = reactive({ posts: '', pages: '', categories: '' });
    const searchLoading = reactive({ posts: false, pages: false, categories: false });
    let searchTimeout = null;

    const searchContent = (type) => {
        clearTimeout(searchTimeout);
        if (!searchTerms[type]) return;

        searchTimeout = setTimeout(async () => {
            searchLoading[type] = true;
            try {
                const data = await ajax.post('yab_search_content', {
                    search_term: searchTerms[type],
                    content_type: type
                });
                const idField = type === 'categories' ? 'term_id' : 'ID';
                const existingIds = new Set(siteData[type].map(item => item[idField]));
                const newItems = data.filter(item => !existingIds.has(item[idField]));
                siteData[type].push(...newItems);
            } catch (error) {
                console.error(`Error searching ${type}:`, error);
            } finally {
                searchLoading[type] = false;
            }
        }, 300);
    };

    const createSortedList = (type, idField) => computed(() => {
        const allItems = siteData[type] || [];
        const selectedIds = new Set(banner.displayOn[type] || []);
        const term = searchTerms[type].toLowerCase();
        
        const filteredItems = term ? allItems.filter(item => (item.post_title || item.name).toLowerCase().includes(term)) : allItems;
        
        const selected = filteredItems.filter(item => selectedIds.has(item[idField]));
        const unselected = filteredItems.filter(item => !selectedIds.has(item[idField]));
        
        return [...selected, ...unselected];
    });

    const sortedPosts = createSortedList('posts', 'ID');
    const sortedPages = createSortedList('pages', 'ID');
    const sortedCategories = createSortedList('categories', 'term_id');
    
    return { siteData, searchTerms, searchLoading, searchContent, sortedPosts, sortedPages, sortedCategories };
}