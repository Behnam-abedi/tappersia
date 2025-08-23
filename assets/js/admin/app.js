// const { createApp, ref, computed, onMounted } = Vue; // This line was incorrect and was removed
const { createApp, ref, computed, onMounted } = Vue; // This correct line was substituted

import { useAjax } from './composables/useAjax.js';
import { useBannerState } from './composables/useBannerState.js';
import { useApiBanner } from './composables/useApiBanner.js';
import { useDisplayConditions } from './composables/useDisplayConditions.js';

const ImageLoader = {
  props: {
    src: String,
    alt: String,
    imgClass: String,
  },
  data() {
    return {
      loading: true,
    };
  },
  methods: {
    onImageLoad() {
      this.loading = false;
    },
    onImageError() {
      this.loading = false; 
    }
  },
  template: `
    <div class="yab-image-loader-container">
      <div v-if="loading" class="yab-skeleton-loader"></div>
      <img
        :src="src"
        :alt="alt"
        :class="[imgClass, { 'opacity-0': loading }]"
        @load="onImageLoad"
        @error="onImageError"
      />
    </div>
  `,
};


// --- Main Vue App Setup ---
createApp({
    setup() {
        // --- State Management ---
        const appState = ref('loading');
        const isSaving = ref(false);
        const modalComponent = ref(null);

        const { banner, shortcode, mergeWithExisting } = useBannerState();
        const ajax = useAjax(window.yab_data.ajax_url, window.yab_data.nonce);
        const showModal = (title, message) => modalComponent.value?.show({ title, message });

        // --- Initialize Composables ---
        const apiBannerLogic = useApiBanner(banner, showModal, ajax);
        const displayConditionsLogic = useDisplayConditions(banner, ajax);

        // --- Computed Properties ---
        const allBannersUrl = computed(() => 'admin.php?page=tappersia-list');

        const apiContentStyles = computed(() => {
            const design = banner.api.design;
            const styles = {
                textAlign: design.layout,
                justifyContent: 'flex-start',
            };

            if (design.enableCustomPadding) {
                styles.padding = `${design.paddingTop}px ${design.paddingRight}px ${design.paddingBottom}px ${design.paddingLeft}px`;
            } else {
                const isRightLayout = design.layout === 'right';
                styles.paddingTop = '23px';
                styles.paddingBottom = '23px';
                styles.paddingLeft = isRightLayout ? '30px' : '55px';
                styles.paddingRight = isRightLayout ? '55px' : '30px';
            }
            return styles;
        });

        // --- Methods ---
        const formatRating = (score) => {
            if (score == null) return '';
            // If it's a whole number, return as is.
            if (score % 1 === 0) {
                return score;
            }
            // Otherwise, truncate to one decimal place without rounding.
            return Math.trunc(score * 10) / 10;
        };
        
        const getRatingLabel = (score) => {
            if (!score || score == 0) {
                return 'New';
            }
            if (score >= 4.6) {
                return 'Excellent';
            } else if (score >= 4.1) {
                return 'Very Good';
            } else if (score >= 3.6) {
                return 'Good';
            } else if (score >= 3.0) {
                return 'Average';
            } else {
                return 'Poor';
            }
        };

        const selectElementType = (type) => { banner.type = type; appState.value = 'editor'; };

        const saveBanner = async () => {
            if (!banner.name) return showModal('Input Required', 'Please enter a name for the banner.');
            isSaving.value = true;
            try {
                const data = await ajax.post('yab_save_banner', {
                    banner_data: JSON.stringify(banner),
                    banner_type: banner.type,
                });
                if (data.banner_id) banner.id = data.banner_id;
                showModal('Success!', 'Banner saved successfully!');
            } catch (error) {
                showModal('Error', error.message);
            } finally {
                isSaving.value = false;
            }
        };

        const openMediaUploader = (targetBannerKey) => {
            const uploader = wp.media({ title: 'Select Image', button: { text: 'Use this Image' }, multiple: false });
            uploader.on('select', () => {
                const target = banner[targetBannerKey] || banner.single; // Fallback for single banner
                if (target) {
                    target.imageUrl = uploader.state().get('selection').first().toJSON().url;
                }
            });
            uploader.open();
        };
        
        const removeImage = (targetBannerKey) => { 
            const target = banner[targetBannerKey] || banner.single;
            if (target) {
                target.imageUrl = '';
            }
        };
        
        const copyShortcode = (event) => {
            if (!banner.id && banner.displayMethod === 'Embeddable') return showModal('Info', 'Please save the banner first.');
            navigator.clipboard.writeText(event.target.value).then(() => showModal('Success', 'Shortcode copied!'));
        };

        // --- Lifecycle Hooks ---
        onMounted(() => {
            if (!window.yab_data) {
                appState.value = 'error';
                return console.error("YAB Data object not found.");
            }
            displayConditionsLogic.siteData.posts = window.yab_data.posts || [];
            displayConditionsLogic.siteData.pages = window.yab_data.pages || [];
            displayConditionsLogic.siteData.categories = window.yab_data.categories || [];

            if (window.yab_data.existing_banner) {
                mergeWithExisting(JSON.parse(JSON.stringify(window.yab_data.existing_banner)));
                 if (banner.type === 'api-banner' && banner.api.selectedHotel?.id) {
                    apiBannerLogic.fetchFullHotelDetails(banner.api.selectedHotel.id);
                }
                appState.value = 'editor';
            } else {
                appState.value = 'selection';
            }
        });

        return {
            appState, isSaving, banner, shortcode, modalComponent, allBannersUrl,
            apiContentStyles,
            getRatingLabel,
            formatRating,
            selectElementType, saveBanner, openMediaUploader, removeImage, copyShortcode,
            ...apiBannerLogic,
            ...displayConditionsLogic,
            bannerStyles: (b) => {
                if (!b) return '';
                return b.backgroundType === 'gradient' ? `linear-gradient(${b.gradientAngle||90}deg, ${b.gradientColor1}, ${b.gradientColor2})` : b.bgColor
            },
            contentAlignment: (align) => align === 'right' ? 'flex-end' : (align === 'center' ? 'center' : 'flex-start'),
            buttonAlignment: (align) => align === 'right' ? 'flex-end' : (align === 'center' ? 'center' : 'flex-start'),
            imageStyleObject: (b) => {
                const style = { position: 'absolute', right: `${b.imagePosRight||0}px`, bottom: `${b.imagePosBottom||0}px` };
                if (b.enableCustomImageSize) {
                    style.width = b.imageWidth ? `${b.imageWidth}px` : 'auto';
                    style.height = b.imageHeight ? `${b.imageHeight}px` : 'auto';
                } else {
                    style.objectFit = b.imageFit;
                }
                return style;
            },
            // Helper for star rating display
            ceil: Math.ceil
        };
    },
    components: { 
        'yab-modal': YabModal,
        'image-loader': ImageLoader 
    }
}).mount('#yab-app');