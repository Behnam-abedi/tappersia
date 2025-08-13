const { createApp, ref, reactive, computed, onMounted } = Vue;

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

    const createDefaultBannerState = () => ({
      alignment: 'left', backgroundType: 'solid', bgColor: '#232323',
      gradientColor1: '#232323', gradientColor2: '#1A2B48',
      titleText: 'Awesome Title', titleColor: '#ffffff', titleSize: 15, titleWeight: '800',
      descText: 'This is a short and engaging description for your new banner.',
      descColor: '#dddddd', descSize: 10, descWeight: '400',
      buttonText: 'Learn More', buttonLink: '#', buttonBgColor: '#00baa4',
      buttonTextColor: '#ffffff', buttonFontSize: 10,
      imageUrl: '', imageSize: 176, imageFit: 'cover',
      enableCustomPosition: false,
      imagePosX: 0,
      imagePosY: 0,
    });
    
    const banner = reactive({
      id: null, name: '', displayMethod: 'Fixed', isActive: true,
      displayOn: { posts: [], pages: [], categories: [] },
      left: createDefaultBannerState(), right: createDefaultBannerState(),
    });

    const siteData = reactive({ posts: [], pages: [], categories: [] });
    let mediaUploader;

    const allBannersUrl = computed(() => `admin.php?page=your-awesome-banner-list`);

    const shortcode = computed(() => {
        if (banner.displayMethod === 'Embeddable') {
            return banner.id ? `[doublebanner id="${banner.id}"]` : '[doublebanner id="..."]';
        }
        return '[doublebanner_fixed]';
    });
    
    const bannerStyles = (bannerData) => {
        return bannerData.backgroundType === 'solid' ? bannerData.bgColor : `linear-gradient(90deg, ${bannerData.gradientColor1}, ${bannerData.gradientColor2})`;
    };

    const imageStyleObject = (b) => {
      const style = {
        objectFit: b.imageFit,
      };
      if (b.enableCustomPosition) {
        style.objectPosition = `${b.imagePosX}px ${b.imagePosY}px`;
      }
      return style;
    };

    const selectElementType = (type) => {
      if (type === 'double-banner') appState.value = 'editor';
    };

    const saveBanner = () => {
        if (!banner.name) { alert('Please enter a name for the banner.'); return; }
        isSaving.value = true;
        jQuery.ajax({
            url: window.yab_data.ajax_url, type: 'POST',
            data: { action: 'yab_save_double_banner', nonce: window.yab_data.nonce, banner_data: JSON.stringify(banner) },
            success: function(response) {
                if(response.success) {
                    if (response.data.banner_id) banner.id = response.data.banner_id;
                    alert('Banner Saved!');
                } else { alert('Error: ' + response.data.message); }
            },
            error: function() { alert('An unknown AJAX error occurred.'); },
            complete: function() { isSaving.value = false; }
        });
    };
    
    const copyShortcode = (event) => {
        navigator.clipboard.writeText(event.target.value).then(() => alert('Shortcode copied!'));
    };

    const openMediaUploader = (targetBannerKey) => {
      if (mediaUploader) {
          mediaUploader.off('select');
      } else {
          mediaUploader = wp.media({ title: 'Select an Image', button: { text: 'Use this Image' }, multiple: false });
      }
      mediaUploader.on('select', () => {
          const attachment = mediaUploader.state().get('selection').first().toJSON();
          banner[targetBannerKey].imageUrl = attachment.url;
      });
      mediaUploader.open();
    };

    onMounted(() => {
      if (window.yab_data) {
          siteData.posts = window.yab_data.posts || [];
          siteData.pages = window.yab_data.pages || [];
          siteData.categories = window.yab_data.categories || [];
      } else {
          console.error("YAB Data object not found.");
          return;
      }
      
      if (window.yab_data.existing_banner) {
          const existingData = window.yab_data.existing_banner;
          deepMerge(banner, existingData);
          banner.displayOn.posts = existingData.displayOn?.posts || [];
          banner.displayOn.pages = existingData.displayOn?.pages || [];
          banner.displayOn.categories = existingData.displayOn?.categories || [];
          appState.value = 'editor';
      } else {
          appState.value = 'selection';
      }
    });

    return { appState, isSaving, banner, siteData, allBannersUrl, shortcode, bannerStyles, imageStyleObject, selectElementType, saveBanner, copyShortcode, openMediaUploader };
  }
}).mount('#yab-app');