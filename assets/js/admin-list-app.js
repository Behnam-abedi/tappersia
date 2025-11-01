const { createApp, ref, reactive, computed, onMounted } = Vue;

createApp({
  setup() {
    const appState = ref('selection');
    const allBanners = reactive([]);
    const selectedType = ref('');
    const searchQuery = ref('');
    const currentPage = ref(1);
    const itemsPerPage = ref(20);
    const addNewURL = ref('');
    const nonce = ref('');
    const modalComponent = ref(null);

    const filteredBanners = computed(() => {
      // First, filter by selected type
      let bannersByType = allBanners;
      if (selectedType.value) {
          bannersByType = allBanners.filter(banner => banner.type === selectedType.value);
      }
      
      // Then, filter by search query
      if (!searchQuery.value) {
        return bannersByType;
      }
      return bannersByType.filter(banner =>
        banner.title.toLowerCase().includes(searchQuery.value.toLowerCase())
      );
    });

    const totalPages = computed(() => {
      return Math.ceil(filteredBanners.value.length / itemsPerPage.value);
    });

    const paginatedBanners = computed(() => {
      const start = (currentPage.value - 1) * itemsPerPage.value;
      const end = start + itemsPerPage.value;
      return filteredBanners.value.slice(start, end);
    });

    const selectType = (type) => {
      selectedType.value = type;
      appState.value = 'list';
    };

    const goBack = () => {
      appState.value = 'selection';
      selectedType.value = '';
      searchQuery.value = '';
      currentPage.value = 1;
    };

    const nextPage = () => {
      if (currentPage.value < totalPages.value) {
        currentPage.value++;
      }
    };

    const prevPage = () => {
      if (currentPage.value > 1) {
        currentPage.value--;
      }
    };

    const showModal = (title, message) => {
        return modalComponent.value?.show({ title, message });
    };

    const showConfirm = (title, message) => {
        return modalComponent.value?.show({ title, message, type: 'confirm' });
    };

    const copyShortcode = (event) => {
        navigator.clipboard.writeText(event.target.value).then(() => {
            showModal('Success', 'Shortcode copied to clipboard!');
        });
    };

    const confirmDelete = async (bannerId) => {
        const confirmed = await showConfirm(
            'Confirm Deletion',
            'Are you sure you want to permanently delete this banner? This action cannot be undone.'
        );
        
        if (confirmed) {
            deleteBanner(bannerId);
        }
    };

    const deleteBanner = (bannerId) => {
        jQuery.ajax({
            url: window.yab_list_data.ajax_url,
            type: 'POST',
            data: {
                action: 'yab_delete_banner',
                nonce: nonce.value,
                banner_id: bannerId,
            },
            success: function(response) {
                if (response.success) {
                    const index = allBanners.findIndex(b => b.id === bannerId);
                    if (index > -1) {
                        allBanners.splice(index, 1);
                    }
                    showModal('Success', 'The banner has been deleted.');
                } else {
                    showModal('Error', response.data.message || 'Could not delete the banner.');
                }
            },
            error: function() {
                showModal('Error', 'An unknown error occurred while deleting the banner.');
            }
        });
    };

    // (+) فانکشن جدید داپلیکیت
    const duplicateBanner = (banner) => {
        banner.isDuplicating = true;
        jQuery.ajax({
            url: window.yab_list_data.ajax_url,
            type: 'POST',
            data: {
                action: 'yab_duplicate_banner',
                nonce: nonce.value,
                banner_id: banner.id,
            },
            success: function(response) {
                if (response.success) {
                    // اضافه کردن بنر جدید به ابتدای لیست
                    const newBanner = {...response.data.newBanner, isDuplicating: false};
                    allBanners.unshift(newBanner);
                    showModal('Success', response.data.message);
                } else {
                    showModal('Error', response.data.message || 'Could not duplicate the banner.');
                }
            },
            error: function() {
                showModal('Error', 'An unknown error occurred while duplicating the banner.');
            },
            complete: function() {
                banner.isDuplicating = false;
            }
        });
    };

    onMounted(() => {
      if (window.yab_list_data) {
        // (+) اضافه کردن پراپرتی `isDuplicating` به آبجکت بنرها
        allBanners.push(...window.yab_list_data.banners.map(b => ({...b, isDuplicating: false})));
        addNewURL.value = window.yab_list_data.addNewURL;
        nonce.value = window.yab_list_data.nonce;
      } else {
        console.error('List data not found.');
      }
    });

    return {
      appState,
      searchQuery,
      currentPage,
      itemsPerPage,
      addNewURL,
      selectedType,
      filteredBanners,
      totalPages,
      paginatedBanners,
      selectType,
      goBack,
      nextPage,
      prevPage,
      copyShortcode,
      confirmDelete,
      duplicateBanner, // (+) اکسپوز کردن فانکشن
      modalComponent,
    };
  },
  components: {
      'yab-modal': YabModal
  }
}).mount('#yab-list-app');