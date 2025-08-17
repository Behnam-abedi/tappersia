const { createApp, ref, reactive, computed, onMounted, watch } = Vue;

function deepMerge(target, source) {
    for (const key in source) {
        if (source[key] && typeof source[key] === 'object' && !Array.isArray(source[key])) {
            if (!target[key]) Object.assign(target, { [key]: {} });
            deepMerge(target[key], source[key]);
        } else if (source[key] !== undefined && source[key] !== null) {
            target[key] = source[key];
        }
    }
}

createApp({
  setup() {
    const appState = ref('loading');
    const isSaving = ref(false);
    const modalComponent = ref(null);

    const createDefaultBannerPartState = () => ({
      alignment: 'left', backgroundType: 'solid', bgColor: '#232323',
      gradientColor1: '#232323', gradientColor2: '#1A2B48', gradientAngle: 90,
      titleText: 'Awesome Title', titleColor: '#ffffff', titleSize: 15, titleWeight: '700',
      descText: 'This is a short and engaging description for your new banner.',
      descColor: '#dddddd', descSize: 10, descWeight: '400',
      buttonText: 'Learn More', buttonLink: '#', buttonBgColor: '#00baa4',
      buttonTextColor: '#ffffff', buttonFontSize: 10, buttonBgHoverColor: '#008a7b',
      imageUrl: '', 
      imageFit: 'none',
      enableCustomImageSize: false,
      imageWidth: null, imageHeight: null,
      imagePosRight: 0, imagePosBottom: 0,
    });
    
    const banner = reactive({
      id: null, name: '', displayMethod: 'Fixed', isActive: true,
      type: null, // Set initial type to null
      displayOn: { posts: [], pages: [], categories: [] },
      left: createDefaultBannerPartState(),
      right: createDefaultBannerPartState(),
      single: createDefaultBannerPartState(),
    });

    const siteData = reactive({ posts: [], pages: [], categories: [] });
    const searchTerms = reactive({ posts: '', pages: '', categories: '' });
    const searchLoading = reactive({ posts: false, pages: false, categories: false });
    let searchTimeout = null;
    let mediaUploader;

    const allBannersUrl = computed(() => `admin.php?page=your-awesome-banner-list`);
    const CreateNewBanner = computed(() => `admin.php?page=your-awesome-banner`);

    const shortcode = computed(() => {
        if (!banner.type) return '';
        if (banner.displayMethod === 'Embeddable') {
            if (!banner.id) return `[${banner.type.replace('-','')} id="..."]`;
            return `[${banner.type.replace('-','')} id="${banner.id}"]`;
        }
        return `[${banner.type.replace('-','')}_fixed]`;
    });
    
    const bannerStyles = (b) => {
        if (!b) return '';
        if (b.backgroundType === 'gradient') {
            return `linear-gradient(${b.gradientAngle || 90}deg, ${b.gradientColor1}, ${b.gradientColor2})`;
        }
        return b.bgColor;
    };

    const imageStyleObject = (b) => {
        if (!b) return {};
        const style = {
            position: 'absolute',
            right: b.imagePosRight !== null ? `${b.imagePosRight}px` : '0px',
            bottom: b.imagePosBottom !== null ? `${b.imagePosBottom}px` : '0px',
        };

        if (b.enableCustomImageSize) {
            style.width = b.imageWidth !== null ? `${b.imageWidth}px` : 'auto';
            style.height = b.imageHeight !== null ? `${b.imageHeight}px` : 'auto';
        } else {
            style.objectFit = b.imageFit;
        }
        
        return style;
    };
    
    const contentAlignment = (align) => {
        if (align === 'right') return 'flex-end';
        if (align === 'center') return 'center';
        return 'flex-start';
    };

    const buttonAlignment = (align) => {
        if (align === 'right') return 'flex-end';
        if (align === 'center') return 'center';
        return 'flex-start';
    };
    
    const createSortedList = (type, idField) => {
        return computed(() => {
            const allItems = siteData[type];
            if (!allItems) return [];
            
            const selectedIds = new Set(banner.displayOn[type]);
            
            const term = searchTerms[type].toLowerCase();
            const filteredItems = term ? allItems.filter(item => (item.post_title || item.name).toLowerCase().includes(term)) : allItems;

            const selectedItems = filteredItems.filter(item => selectedIds.has(item[idField]));
            const unselectedItems = filteredItems.filter(item => !selectedIds.has(item[idField]));

            return [...selectedItems, ...unselectedItems];
        });
    };

    const sortedPosts = createSortedList('posts', 'ID');
    const sortedPages = createSortedList('pages', 'ID');
    const sortedCategories = createSortedList('categories', 'term_id');

    const selectElementType = (type) => {
      banner.type = type;
      appState.value = 'editor';
    };

    const showModal = (title, message) => {
        return modalComponent.value?.show({ title, message });
    };

    const saveBanner = () => {
        if (!banner.name) {
            showModal('Input Required', 'Please enter a name for the banner.');
            return;
        }
        isSaving.value = true;
        jQuery.ajax({
            url: window.yab_data.ajax_url, type: 'POST',
            data: { 
                action: 'yab_save_banner', 
                nonce: window.yab_data.nonce, 
                banner_data: JSON.stringify(banner),
                banner_type: banner.type 
            },
            success: (response) => {
                if(response.success) {
                    if (response.data.banner_id) banner.id = response.data.banner_id;
                    showModal('Success!', 'Banner saved successfully!');
                } else { 
                    showModal('Error', response.data.message || 'An unknown error occurred.'); 
                }
            },
            error: (jqXHR) => { 
                const message = jqXHR.responseJSON?.data?.message || 'An unknown AJAX error occurred.';
                showModal('Error', message);
            },
            complete: () => { isSaving.value = false; }
        });
    };
    
    const copyShortcode = (event) => {
        if (!banner.id && banner.displayMethod === 'Embeddable') {
            showModal('Info', 'Please save the banner first to get the shortcode.');
            return;
        }
        navigator.clipboard.writeText(event.target.value).then(() => {
            showModal('Success', 'Shortcode copied to clipboard!');
        });
    };

    const openMediaUploader = (targetBannerKey) => {
        mediaUploader = wp.media({ title: 'Select an Image', button: { text: 'Use this Image' }, multiple: false });
        mediaUploader.on('select', () => {
          const attachment = mediaUploader.state().get('selection').first().toJSON();
          banner[targetBannerKey].imageUrl = attachment.url;
        });
        mediaUploader.open();
    };

    const removeImage = (targetBannerKey) => {
        banner[targetBannerKey].imageUrl = '';
    };

    const searchContent = (type) => {
        clearTimeout(searchTimeout);
        if (!searchTerms[type]) return;

        searchTimeout = setTimeout(() => {
            searchLoading[type] = true;
            jQuery.ajax({
                url: window.yab_data.ajax_url,
                type: 'POST',
                data: {
                    action: 'yab_search_content',
                    nonce: window.yab_data.nonce,
                    search_term: searchTerms[type],
                    content_type: type
                },
                success: (response) => {
                    if (response.success) {
                        const idField = type === 'categories' ? 'term_id' : 'ID';
                        const existingIds = new Set(siteData[type].map(item => item[idField]));
                        const newItems = response.data.filter(item => !existingIds.has(item[idField]));
                        siteData[type].push(...newItems);
                    }
                },
                complete: () => {
                    searchLoading[type] = false;
                }
            });
        }, 300);
    };

    onMounted(() => {
      if (!window.yab_data) {
          console.error("YAB Data object not found.");
          appState.value = 'error';
          return;
      }
      
      siteData.posts = window.yab_data.posts || [];
      siteData.pages = window.yab_data.pages || [];
      siteData.categories = window.yab_data.categories || [];
      
      if (window.yab_data.existing_banner) {
          const existingData = JSON.parse(JSON.stringify(window.yab_data.existing_banner));
          deepMerge(banner, existingData);
          appState.value = 'editor';
      } else {
          appState.value = 'selection';
      }
    });

    return { 
        appState, isSaving, banner, siteData, allBannersUrl,CreateNewBanner, shortcode, searchTerms, searchLoading,
        bannerStyles, imageStyleObject, contentAlignment, buttonAlignment, 
        selectElementType, saveBanner, copyShortcode, openMediaUploader, removeImage, searchContent,
        sortedPosts, sortedPages, sortedCategories,
        modalComponent
    };
  },
  components: {
      'yab-modal': YabModal
  }
}).mount('#yab-app');