// tappersia/assets/js/admin/app-logic/composables/useBannerActions.js
export function useBannerActions(banner, isSaving, showModal, ajax) {
    const saveBanner = async () => {
        if (!banner.name) {
            return showModal('Input Required', 'Please enter a name for the banner.');
        }
        isSaving.value = true;
        try {
            const data = await ajax.post('yab_save_banner', {
                banner_data: JSON.stringify(banner),
                banner_type: banner.type,
            });
            if (data.banner_id) {
                banner.id = data.banner_id;
            }
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
            
            if (targetKey === 'promotion') {
                banner.promotion.iconUrl = attachment.url;
                return;
            }

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
        if (targetKey === 'promotion') {
            banner.promotion.iconUrl = '';
            return;
        }

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
            navigator.clipboard.writeText(textToCopy).then(() => {
                showModal('Success', 'Shortcode copied to clipboard!');
            }).catch(err => {
                console.error('Failed to copy with navigator.clipboard: ', err);
                showModal('Error', 'Could not copy the shortcode.');
            });
        } else {
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

    return { saveBanner, openMediaUploader, removeImage, copyShortcode };
}