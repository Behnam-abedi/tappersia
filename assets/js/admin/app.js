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
        const bodyTextarea = ref(null); // Ref for the textarea element

        const { banner, shortcode, mergeWithExisting, resetBannerState } = useBannerState();
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

        const previewBodyText = computed(() => {
            const promo = banner.promotion;
            if (!promo.bodyText) return '';

            let text = promo.bodyText.replace(/</g, "&lt;").replace(/>/g, "&gt;");

            if (promo.links && promo.links.length > 0) {
                promo.links.forEach(link => {
                    if (link.placeholder && link.url) {
                        const placeholderRegex = new RegExp(`\\[\\[${link.placeholder.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&')}\\]\\]`, 'g');
                        const linkHtml = `<a href="${link.url}" target="_blank" style="color: ${link.color}; text-decoration: underline; padding: 0 5px;">${link.placeholder}</a>`;
                        text = text.replace(placeholderRegex, linkHtml);
                    }
                });
            }
            return text;
        });

        // --- Methods ---
        const formatRating = (score) => {
            if (score == null) return '';
            if (score % 1 === 0) return score;
            return Math.trunc(score * 10) / 10;
        };
        
        const getRatingLabel = (score) => {
            if (!score || score == 0) return 'New';
            if (score >= 4.6) return 'Excellent';
            if (score >= 4.1) return 'Very Good';
            if (score >= 3.6) return 'Good';
            if (score >= 3.0) return 'Average';
            return 'Poor';
        };

        const selectElementType = (type) => { 
            resetBannerState();
            banner.type = type; 
            appState.value = 'editor'; 
        };
        
        const goBackToSelection = () => {
            resetBannerState();
            appState.value = 'selection';
        };

        const goToListPage = () => {
            resetBannerState();
            window.location.href = allBannersUrl.value;
        };

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
                const attachment = uploader.state().get('selection').first().toJSON();
                if (targetBannerKey === 'promotion') {
                    banner.promotion.iconUrl = attachment.url;
                } else {
                    const target = banner[targetBannerKey] || banner.single;
                    if (target) {
                        target.imageUrl = attachment.url;
                    }
                }
            });
            uploader.open();
        };
        
        const removeImage = (targetBannerKey) => { 
            if (targetBannerKey === 'promotion') {
                banner.promotion.iconUrl = '';
            } else {
                const target = banner[targetBannerKey] || banner.single;
                if (target) {
                    target.imageUrl = '';
                }
            }
        };
        
        const copyShortcode = (event) => {
            if (!banner.id && banner.displayMethod === 'Embeddable') return showModal('Info', 'Please save the banner first.');
            navigator.clipboard.writeText(event.target.value).then(() => showModal('Success', 'Shortcode copied!'));
        };

        const addPromoLink = (placeholder = '') => {
            if (!Array.isArray(banner.promotion.links)) {
                banner.promotion.links = [];
            }
            banner.promotion.links.push({ placeholder: placeholder, url: '#', color: '#f07100' });
        };

        const removePromoLink = (index) => {
            banner.promotion.links.splice(index, 1);
        };
        
        const makeSelectedTextPlaceholder = () => {
            const textarea = bodyTextarea.value;
            if (!textarea) return;

            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            const selectedText = textarea.value.substring(start, end);

            if (!selectedText) {
                showModal('Info', 'Please select a piece of text first.');
                return;
            }
            
            const placeholderExists = banner.promotion.links.some(link => link.placeholder === selectedText);
            if(placeholderExists) {
                 showModal('Info', `A link for "${selectedText}" already exists.`);
                 return;
            }

            const newText = `${textarea.value.substring(0, start)}[[${selectedText}]]${textarea.value.substring(end)}`;
            banner.promotion.bodyText = newText;
            addPromoLink(selectedText);
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
            appState, isSaving, banner, shortcode, modalComponent, allBannersUrl, bodyTextarea,
            apiContentStyles,
            previewBodyText,
            getRatingLabel,
            formatRating,
            selectElementType, saveBanner, openMediaUploader, removeImage, copyShortcode,
            goBackToSelection,
            goToListPage,
            addPromoLink,
            removePromoLink,
            makeSelectedTextPlaceholder,
            ...apiBannerLogic,
            ...displayConditionsLogic,
            bannerStyles: (b, section = null) => {
                if (!b) return '';
                let typeKey = 'backgroundType';
                let gradAngleKey = 'gradientAngle';
                let gradColor1Key = 'gradientColor1';
                let gradColor2Key = 'gradientColor2';
                let bgColorKey = 'bgColor';

                if (section === 'header') {
                    typeKey = 'headerBackgroundType';
                    gradAngleKey = 'headerGradientAngle';
                    gradColor1Key = 'headerGradientColor1';
                    gradColor2Key = 'headerGradientColor2';
                    bgColorKey = 'headerBgColor';
                } else if (section === 'body') {
                    typeKey = 'bodyBackgroundType';
                    gradAngleKey = 'bodyGradientAngle';
                    gradColor1Key = 'bodyGradientColor1';
                    gradColor2Key = 'bodyGradientColor2';
                    bgColorKey = 'bodyBgColor';
                }

                return b[typeKey] === 'gradient' 
                    ? `linear-gradient(${b[gradAngleKey]||90}deg, ${b[gradColor1Key]}, ${b[gradColor2Key]})` 
                    : b[bgColorKey];
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
            ceil: Math.ceil
        };
    },
    components: { 
        'yab-modal': YabModal,
        'image-loader': ImageLoader,
    }
}).mount('#yab-app');