// tappersia/assets/js/admin/app-logic/main.js
const { createApp, ref, computed, onMounted, reactive, watch } = Vue;

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

            // --- Watchers ---
            watch(currentView, (newView) => {
                if (newView === 'mobile' && !banner.isMobileConfigured) {
                    // One-time copy from desktop to mobile for fields that are NOT synced
                    const desktop = banner.single;
                    const mobile = banner.single_mobile;
                    
                    mobile.gradientAngle = desktop.gradientAngle;
                    mobile.gradientStops = JSON.parse(JSON.stringify(desktop.gradientStops));
                    mobile.enableCustomImageSize = desktop.enableCustomImageSize;
                    mobile.imageWidth = desktop.imageWidth;
                    mobile.imageWidthUnit = desktop.imageWidthUnit;
                    mobile.imageHeight = desktop.imageHeight;
                    mobile.imageHeightUnit = desktop.imageHeightUnit;
                    mobile.imagePosRight = desktop.imagePosRight;
                    mobile.imagePosBottom = desktop.imagePosBottom;
                    mobile.titleText = desktop.titleText;
                    mobile.titleWeight = desktop.titleWeight;
                    mobile.descWeight = desktop.descWeight;
                    mobile.buttonText = desktop.buttonText;
                    mobile.buttonLink = desktop.buttonLink;
                    mobile.buttonFontWeight = desktop.buttonFontWeight;
                    mobile.buttonWidth = desktop.buttonWidth;
                    mobile.buttonWidthUnit = desktop.buttonWidthUnit;
                    mobile.buttonHeight = desktop.buttonHeight;
                    mobile.buttonHeightUnit = desktop.buttonHeightUnit;
                    mobile.buttonBorderRadius = desktop.buttonBorderRadius;

                    banner.isMobileConfigured = true; // Mark as configured
                }
            });

            // Watch for changes in desktop settings and sync them to mobile
            watch(() => ({
                borderColor: banner.single.borderColor,
                backgroundType: banner.single.backgroundType,
                bgColor: banner.single.bgColor,
                imageUrl: banner.single.imageUrl,
                alignment: banner.single.alignment,
                titleColor: banner.single.titleColor,
                descText: banner.single.descText,
                descColor: banner.single.descColor,
                buttonBgColor: banner.single.buttonBgColor,
                buttonBgHoverColor: banner.single.buttonBgHoverColor,
                buttonTextColor: banner.single.buttonTextColor,
            }), (newDesktopSettings) => {
                banner.single_mobile.borderColor = newDesktopSettings.borderColor;
                banner.single_mobile.backgroundType = newDesktopSettings.backgroundType;
                banner.single_mobile.bgColor = newDesktopSettings.bgColor;
                banner.single_mobile.imageUrl = newDesktopSettings.imageUrl;
                banner.single_mobile.alignment = newDesktopSettings.alignment;
                banner.single_mobile.titleColor = newDesktopSettings.titleColor;
                banner.single_mobile.descText = newDesktopSettings.descText;
                banner.single_mobile.descColor = newDesktopSettings.descColor;
                banner.single_mobile.buttonBgColor = newDesktopSettings.buttonBgColor;
                banner.single_mobile.buttonBgHoverColor = newDesktopSettings.buttonBgHoverColor;
                banner.single_mobile.buttonTextColor = newDesktopSettings.buttonTextColor;
            }, { deep: true });


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

            const addGradientStop = (settings) => {
                if (!settings.gradientStops) {
                    settings.gradientStops = [];
                }
                settings.gradientStops.push({ color: 'rgba(255, 255, 255, 0.5)', stop: 100 });
            };

            const removeGradientStop = (settings, index) => {
                if (settings.gradientStops.length > 1) {
                    settings.gradientStops.splice(index, 1);
                } else {
                    showModal('Info', 'A gradient must have at least one color stop.');
                }
            };
            
            const getBannerContainerStyles = (view) => {
                const settings = view === 'desktop' ? banner.single : banner.single_mobile;
                if (!settings) return {};
                return {
                    width: settings.enableCustomDimensions ? `${settings.width}${settings.widthUnit}` : '100%',
                    height: 'auto',
                    minHeight: settings.enableCustomDimensions ? `${settings.minHeight}${settings.minHeightUnit}` : (view === 'desktop' ? '183px' : '110px'),
                    border: settings.enableBorder ? `${settings.borderWidth}px solid ${settings.borderColor}` : 'none',
                    borderRadius: `${settings.borderRadius}px`
                };
            };
            
            const getDynamicStyles = (view) => {
                const settings = view === 'desktop' ? banner.single : banner.single_mobile;
                if (!settings) return {};
    
                return {
                    contentStyles: {
                        alignItems: contentAlignment(settings.alignment),
                        textAlign: settings.alignment,
                        padding: `${settings.paddingTop}px ${settings.paddingRight}px ${settings.paddingBottom}px ${settings.paddingLeft}px`,
                        flexGrow: 1,
                    },
                    titleStyles: {
                        color: settings.titleColor,
                        fontSize: `${settings.titleSize}px`,
                        fontWeight: settings.titleWeight,
                        margin: 0,
                    },
                    descriptionStyles: {
                        color: settings.descColor,
                        fontSize: `${settings.descSize}px`,
                        fontWeight: settings.descWeight,
                        whiteSpace: 'pre-wrap',
                        marginTop: `${settings.marginTopDescription || 10}px`,
                        marginBottom: '10px',
                        lineHeight: settings.descLineHeight || 1.1,
                        width: `${settings.descWidth}${settings.descWidthUnit}`, // *** ADDED: Description width ***
                        wordWrap: 'break-word' // *** ADDED: Word wrap behavior ***
                    },
                    buttonStyles: {
                        backgroundColor: settings.buttonBgColor,
                        color: settings.buttonTextColor,
                        fontSize: `${settings.buttonFontSize}px`,
                        fontWeight: settings.buttonFontWeight,
                        alignSelf: settings.alignment === 'center' ? 'center' : (settings.alignment === 'right' ? 'flex-end' : 'flex-start'),
                        width: settings.buttonWidth ? `${settings.buttonWidth}${settings.buttonWidthUnit}` : 'auto',
                        height: settings.buttonHeight ? `${settings.buttonHeight}${settings.buttonHeightUnit}` : 'auto',
                        minWidth: settings.buttonMinWidth ? `${settings.buttonMinWidth}${settings.buttonMinWidthUnit}` : 'auto',
                        borderRadius: `${settings.buttonBorderRadius}px`,
                        padding: `${settings.buttonPaddingY}px ${settings.buttonPaddingX}px`,
                        display: 'inline-flex',
                        alignItems: 'center',
                        justifyContent: 'center',
                        textDecoration: 'none',
                        marginTop: 'auto',
                        lineHeight: '1.15',
                    }
                };
            };
    
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
                if (yabData.existing_banner) {
                    window.location.href = allBannersUrl.value;
                } else {
                    resetBannerState();
                    appState.value = 'selection';
                }
            };
    
            const goToListPage = () => {
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
                        if (targetKey === 'single') {
                            banner.single_mobile.imageUrl = attachment.url;
                        }
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
                     if (targetKey === 'single') {
                        banner.single_mobile.imageUrl = '';
                    }
                }
            };
            
            const copyShortcode = (event) => {
                if (!banner.id && banner.displayMethod === 'Embeddable') {
                    return showModal('Info', 'Please save the banner first to generate the shortcode.');
                }
            
                const textToCopy = event.target.value;
            
                if (navigator.clipboard && window.isSecureContext) {
                    // Modern async clipboard API
                    navigator.clipboard.writeText(textToCopy).then(() => {
                        showModal('Success', 'Shortcode copied to clipboard!');
                    }).catch(err => {
                        console.error('Failed to copy with navigator.clipboard: ', err);
                        showModal('Error', 'Could not copy the shortcode.');
                    });
                } else {
                    // Fallback for older browsers or insecure contexts
                    try {
                        const input = event.target;
                        input.select();
                        document.execCommand('copy');
                        input.setSelectionRange(0, 0); // Deselect
                        showModal('Success', 'Shortcode copied to clipboard!');
                    } catch (err) {
                        console.error('Fallback copy failed: ', err);
                        showModal('Error', 'Could not copy the shortcode. Please copy it manually.');
                    }
                }
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

            const buttonAlignment = (align) => align === 'right' ? 'flex-end' : (align === 'center' ? 'center' : 'flex-start');

            return {
                appState, isSaving, banner, shortcode, modalComponent, allBannersUrl, bodyTextarea, currentView,
                previewBodyText,
                selectElementType, saveBanner, openMediaUploader, removeImage, copyShortcode,
                goBackToSelection, goToListPage, makeSelectedTextPlaceholder,
                ...apiBannerLogic,
                ...displayConditionsLogic,
                bannerStyles, contentAlignment, imageStyleObject, buttonAlignment,
                ceil: Math.ceil,
                getContentStyles,
                getTitleStyles,
                getDescriptionStyles,
                getButtonStyles,
                addGradientStop,
                removeGradientStop,
                getBannerContainerStyles
            };
        },
        components: { 
            'yab-modal': YabModal,
            'image-loader': ImageLoader,
        }
    }).mount('#yab-app');
}