// tappersia/assets/js/admin/app-logic/main.js
const { createApp, ref, computed, onMounted, reactive } = Vue;

import { useAjax } from '../composables/useAjax.js';
import { useBannerState } from '../composables/useBannerState.js';
import { useApiBanner } from '../composables/useApiBanner.js';
import { useDisplayConditions } from '../composables/useDisplayConditions.js';
import { ImageLoader } from './components.js';
import { bannerStyles, contentAlignment, imageStyleObject } from './utils.js';

export function initializeApp(yabData) {
    createApp({
        setup() {
            // --- Core App State ---
            const appState = ref('loading');
            const isSaving = ref(false);
            const modalComponent = ref(null);
            const bodyTextarea = ref(null);
            const currentView = ref('desktop');

            // --- Composables ---
            const { banner, shortcode, mergeWithExisting, resetBannerState } = useBannerState();
            const ajax = useAjax(yabData.ajax_url, yabData.nonce);
            const apiBannerLogic = useApiBanner(banner, showModal, ajax);
            const displayConditionsLogic = useDisplayConditions(banner, ajax);

            // --- Computed Properties ---
            const allBannersUrl = computed(() => 'admin.php?page=tappersia-list');

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
            function showModal(title, message) {
                return modalComponent.value?.show({ title, message });
            }

            const copyDesktopToMobile = () => {
                banner.single_mobile = JSON.parse(JSON.stringify(banner.single));
                showModal('Success', 'Desktop settings copied to mobile.');
            };

            const getDynamicStyles = (view) => {
                const settings = view === 'desktop' ? banner.single : banner.single_mobile;
                if (!settings) return {};
    
                return {
                    bannerStyles: {
                        background: bannerStyles(settings),
                        width: settings.enableCustomDimensions ? `${settings.width}${settings.widthUnit}` : (view === 'desktop' ? 'auto' : '100%'),
                        height: settings.enableCustomDimensions ? `${settings.height}${settings.heightUnit}` : (view === 'desktop' ? '178px' : '250px'),
                        border: settings.enableBorder ? `${settings.borderWidth}px solid ${settings.borderColor}` : 'none',
                        borderRadius: `${settings.borderRadius}px`
                    },
                    contentStyles: {
                        alignItems: contentAlignment(settings.alignment),
                        textAlign: settings.alignment,
                        padding: `${settings.paddingTop}px ${settings.paddingRight}px ${settings.paddingBottom}px ${settings.paddingLeft}px`
                    },
                    titleStyles: {
                        color: settings.titleColor,
                        fontSize: `${settings.titleSize}px`,
                        fontWeight: settings.titleWeight,
                        margin: 0
                    },
                    descriptionStyles: {
                        color: settings.descColor,
                        fontSize: `${settings.descSize}px`,
                        fontWeight: settings.descWeight,
                        whiteSpace: 'pre-wrap',
                        marginTop: `${settings.marginTopDescription}px`,
                        marginBottom: 0
                    },
                    buttonStyles: {
                        backgroundColor: settings.buttonBgColor,
                        color: settings.buttonTextColor,
                        fontSize: `${settings.buttonFontSize}px`,
                        alignSelf: settings.alignment === 'center' ? 'center' : (settings.alignment === 'right' ? 'flex-end' : 'flex-start'),
                        width: settings.buttonWidth ? `${settings.buttonWidth}${settings.buttonWidthUnit}` : 'auto',
                        height: settings.buttonHeight ? `${settings.buttonHeight}${settings.buttonHeightUnit}` : 'auto',
                        minWidth: settings.buttonMinWidth ? `${settings.buttonMinWidth}${settings.buttonMinWidthUnit}` : 'auto',
                        borderRadius: `${settings.buttonBorderRadius}px`,
                        display: 'inline-flex',
                        alignItems: 'center',
                        justifyContent: 'center',
                        textDecoration: 'none',
                        padding: '8px 16px',
                        marginTop: (settings.marginBottomDescription !== null && settings.marginBottomDescription !== '') ? `${settings.marginBottomDescription}px` : 'auto'
                    }
                };
            };
    
            const getBannerStyles = (view) => getDynamicStyles(view).bannerStyles;
            const getContentStyles = (view) => getDynamicStyles(view).contentStyles;
            const getTitleStyles = (view) => getDynamicStyles(view).titleStyles;
            const getDescriptionStyles = (view) => getDynamicStyles(view).descriptionStyles;
            const getButtonStyles = (view) => getDynamicStyles(view).buttonStyles;

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
    
            const openMediaUploader = (targetKey) => {
                const uploader = wp.media({ title: 'Select Image', button: { text: 'Use this Image' }, multiple: false });
                uploader.on('select', () => {
                    const attachment = uploader.state().get('selection').first().toJSON();
                    let targetObject = banner;
                    const keys = targetKey.split('_');
                    
                    keys.forEach(key => {
                        if (targetObject[key]) {
                            targetObject = targetObject[key];
                        }
                    });
                    
                    if (targetObject && typeof targetObject === 'object') {
                        targetObject.imageUrl = attachment.url;
                    }
                });
                uploader.open();
            };
            
            const removeImage = (targetKey) => { 
                let targetObject = banner;
                const keys = targetKey.split('_');
    
                keys.forEach(key => {
                    if (targetObject[key]) {
                        targetObject = targetObject[key];
                    }
                });
    
                if (targetObject && typeof targetObject === 'object') {
                    targetObject.imageUrl = '';
                }
            };
            
            const copyShortcode = (event) => {
                if (!banner.id && banner.displayMethod === 'Embeddable') return showModal('Info', 'Please save the banner first.');
                navigator.clipboard.writeText(event.target.value).then(() => showModal('Success', 'Shortcode copied!'));
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
                displayConditionsLogic.siteData.posts = yabData.posts || [];
                displayConditionsLogic.siteData.pages = yabData.pages || [];
                displayConditionsLogic.siteData.categories = yabData.categories || [];
    
                if (yabData.existing_banner) {
                    mergeWithExisting(JSON.parse(JSON.stringify(yabData.existing_banner)));
                     if (banner.type === 'api-banner' && banner.api.selectedHotel?.id) {
                        apiBannerLogic.fetchFullHotelDetails(banner.api.selectedHotel.id);
                    }
                    appState.value = 'editor';
                } else {
                    appState.value = 'selection';
                }
            });

            return {
                appState, isSaving, banner, shortcode, modalComponent, allBannersUrl, bodyTextarea, currentView,
                previewBodyText,
                selectElementType, saveBanner, openMediaUploader, removeImage, copyShortcode,
                goBackToSelection, goToListPage, makeSelectedTextPlaceholder,
                ...apiBannerLogic,
                ...displayConditionsLogic,
                bannerStyles, contentAlignment, imageStyleObject,
                ceil: Math.ceil,
                copyDesktopToMobile,
                getBannerStyles,
                getContentStyles,
                getTitleStyles,
                getDescriptionStyles,
                getButtonStyles
            };
        },
        components: { 
            'yab-modal': YabModal,
            'image-loader': ImageLoader,
        }
    }).mount('#yab-app');
}