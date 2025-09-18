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
            const selectedDoubleBanner = ref('left'); // To toggle between left/right settings

            // --- Composables ---
            const { banner, shortcode, mergeWithExisting, resetBannerState } = useBannerState();
            const ajax = useAjax(yabData.ajax_url, yabData.nonce);
            const apiBannerLogic = useApiBanner(banner, showModal, ajax);
            const displayConditionsLogic = useDisplayConditions(banner, ajax);

            // --- Watchers ---

            // Sync settings from Desktop to Mobile on first view change
            watch(currentView, (newView) => {
                // For Single Banner
                if (banner.type === 'single-banner' && newView === 'mobile' && !banner.isMobileConfigured) {
                    const desktop = banner.single;
                    const mobile = banner.single_mobile;
                    
                    // Copy non-style-specific values
                    mobile.gradientAngle = desktop.gradientAngle;
                    mobile.gradientStops = JSON.parse(JSON.stringify(desktop.gradientStops)); // Deep copy
                    mobile.enableCustomImageSize = desktop.enableCustomImageSize;
                    mobile.imageWidth = desktop.imageWidth;
                    mobile.imageWidthUnit = desktop.imageWidthUnit;
                    mobile.imageHeight = desktop.imageHeight;
                    mobile.imageHeightUnit = desktop.imageHeightUnit;
                    mobile.imagePosRight = desktop.imagePosRight;
                    mobile.imagePosBottom = desktop.imagePosBottom;
                    mobile.titleWeight = desktop.titleWeight;
                    mobile.titleLineHeight = desktop.titleLineHeight;
                    mobile.descWeight = desktop.descWeight;
                    mobile.descLineHeight = desktop.descLineHeight;
                    mobile.buttonFontWeight = desktop.buttonFontWeight;
                    mobile.buttonLineHeight = desktop.buttonLineHeight;
                    mobile.buttonBorderRadius = desktop.buttonBorderRadius;

                    banner.isMobileConfigured = true; // Mark as configured
                }

                // For Double Banner
                if (banner.type === 'double-banner' && newView === 'mobile' && !banner.double.isMobileConfigured) {
                    ['left', 'right'].forEach(key => {
                        const desktop = banner.double.desktop[key];
                        const mobile = banner.double.mobile[key];

                        // Inherit properties
                        mobile.borderColor = desktop.borderColor;
                        mobile.layerOrder = desktop.layerOrder;
                        mobile.backgroundType = desktop.backgroundType; // Sync background type
                        mobile.bgColor = desktop.bgColor;
                        mobile.gradientStops = JSON.parse(JSON.stringify(desktop.gradientStops));
                        mobile.imageUrl = desktop.imageUrl;
                        mobile.alignment = desktop.alignment;
                        mobile.titleText = desktop.titleText;
                        mobile.titleColor = desktop.titleColor;
                        mobile.titleWeight = desktop.titleWeight;
                        mobile.descText = desktop.descText;
                        mobile.descColor = desktop.descColor;
                        mobile.descWeight = desktop.descWeight;
                        mobile.buttonText = desktop.buttonText;
                        mobile.buttonLink = desktop.buttonLink;
                        mobile.buttonBgColor = desktop.buttonBgColor;
                        mobile.buttonBgHoverColor = desktop.buttonBgHoverColor;
                        mobile.buttonTextColor = desktop.buttonTextColor;
                        mobile.buttonFontWeight = desktop.buttonFontWeight;
                        mobile.buttonMinWidth = desktop.buttonMinWidth;
                        mobile.buttonMinWidthUnit = desktop.buttonMinWidthUnit;
                        mobile.buttonBorderRadius = desktop.buttonBorderRadius;
                        mobile.enableCustomImageSize = desktop.enableCustomImageSize;
                    });
                    banner.double.isMobileConfigured = true;
                }
            });

            // Watch for shared properties in Single Banner and sync them from desktop to mobile
            watch(() => ({
                borderColor: banner.single.borderColor,
                backgroundType: banner.single.backgroundType,
                bgColor: banner.single.bgColor,
                imageUrl: banner.single.imageUrl,
                alignment: banner.single.alignment,
                titleText: banner.single.titleText,
                titleColor: banner.single.titleColor,
                descText: banner.single.descText,
                descColor: banner.single.descColor,
                buttonText: banner.single.buttonText,
                buttonLink: banner.single.buttonLink,
                buttonBgColor: banner.single.buttonBgColor,
                buttonBgHoverColor: banner.single.buttonBgHoverColor,
                buttonTextColor: banner.single.buttonTextColor,
            }), (newDesktopSettings) => {
                 if (banner.type !== 'single-banner') return;
                Object.assign(banner.single_mobile, newDesktopSettings);
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
                    minHeight: settings.enableCustomDimensions ? `${settings.minHeight}${settings.minHeightUnit}` : (view === 'desktop' ? '190px' : '145px'),
                    border: settings.enableBorder ? `${settings.borderWidth}px solid ${settings.borderColor}` : 'none',
                    borderRadius: `${settings.borderRadius}px`,
                    fontFamily: 'Roboto, sans-serif'
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
                        lineHeight: settings.titleLineHeight,
                        margin: 0,
                    },
                    descriptionStyles: {
                        color: settings.descColor,
                        fontSize: `${settings.descSize}px`,
                        fontWeight: settings.descWeight,
                        whiteSpace: 'pre-wrap',
                        marginTop: `${settings.marginTopDescription}px`,
                        marginBottom: `0px`,
                        lineHeight: settings.descLineHeight,
                        width: `${settings.descWidth}${settings.descWidthUnit}`,
                        wordWrap: 'break-word'
                    },
                    buttonStyles: {
                        backgroundColor: settings.buttonBgColor,
                        color: settings.buttonTextColor,
                        fontSize: `${settings.buttonFontSize}px`,
                        fontWeight: settings.buttonFontWeight,
                        alignSelf: settings.alignment === 'center' ? 'center' : (settings.alignment === 'right' ? 'flex-end' : 'flex-start'),
                        borderRadius: `${settings.buttonBorderRadius}px`,
                        padding: `${settings.buttonPaddingTop}px ${settings.buttonPaddingRight}px ${settings.buttonPaddingBottom}px ${settings.buttonPaddingLeft}px`,
                        display: 'inline-flex',
                        alignItems: 'center',
                        justifyContent: 'center',
                        textDecoration: 'none',
                        lineHeight: settings.buttonLineHeight,
                        marginTop: `${settings.marginBottomDescription}px`
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
                    const keys = targetKey.split('_'); // e.g., ['double', 'desktop', 'left']
                    
                    keys.forEach(key => {
                        if (targetObject && typeof targetObject[key] !== 'undefined') {
                            targetObject = targetObject[key];
                        }
                    });
                    
                    if (targetObject && typeof targetObject === 'object') {
                        targetObject.imageUrl = attachment.url;

                        // Sync image to mobile for single and double banners
                        if (keys[0] === 'single') {
                            banner.single_mobile.imageUrl = attachment.url;
                        } else if (keys[0] === 'double' && keys[1] === 'desktop') {
                            const position = keys[2]; // 'left' or 'right'
                            banner.double.mobile[position].imageUrl = attachment.url;
                        }
                    }
                });
                uploader.open();
            };
            
            const removeImage = (targetKey) => { 
                let targetObject = banner;
                const keys = targetKey.split('_');
    
                keys.forEach(key => {
                    if (targetObject && typeof targetObject[key] !== 'undefined') {
                        targetObject = targetObject[key];
                    }
                });
    
                if (targetObject && typeof targetObject === 'object') {
                    targetObject.imageUrl = '';
                     if (keys[0] === 'single') {
                        banner.single_mobile.imageUrl = '';
                    } else if (keys[0] === 'double' && keys[1] === 'desktop') {
                        const position = keys[2]; // 'left' or 'right'
                        banner.double.mobile[position].imageUrl = '';
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
                     if (banner.type === 'api-banner' && banner.api.selectedTour?.id) {
                        apiBannerLogic.fetchFullTourDetails(banner.api.selectedTour.id);
                    }
                    appState.value = 'editor';
                } else {
                    appState.value = 'selection';
                }
            });

            const buttonAlignment = (align) => align === 'right' ? 'flex-end' : (align === 'center' ? 'center' : 'flex-start');

            return {
                appState, isSaving, banner, shortcode, modalComponent, allBannersUrl, bodyTextarea, currentView, selectedDoubleBanner,
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