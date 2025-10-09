// tappersia/assets/js/admin/app-logic/composables/useComputedProperties.js
const { computed } = Vue;

export function useComputedProperties(banner, currentView, selectedDoubleBanner) {
    const previewBodyText = computed(() => {
        const promo = currentView.value === 'desktop' ? banner.promotion : banner.promotion_mobile;
        if (!promo || !promo.bodyText) return '';
        let text = promo.bodyText.replace(/</g, "&lt;").replace(/>/g, "&gt;");

        const links = banner.promotion.links;
        if (links && links.length > 0) {
            links.forEach(link => {
                if (link.placeholder && link.url) {
                    const placeholderRegex = new RegExp(`\\[\\[${link.placeholder.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&')}\\]\\]`, 'g');
                    const linkHtml = `<a href="${link.url}" target="_blank" style="color: ${link.color}; text-decoration: underline; padding: 0 5px;">${link.placeholder}</a>`;
                    text = text.replace(placeholderRegex, linkHtml);
                }
            });
        }
        return text;
    });

    const apiItem = computed(() => {
        return banner.type === 'api-banner' ? (banner.api.selectedHotel || banner.api.selectedTour) : null;
    });
    
    const isApiHotel = computed(() => {
        return !!(banner.type === 'api-banner' && banner.api.selectedHotel);
    });
    
    const settings = computed(() => {
        if (!banner.type) return {}; 
    
        switch (banner.type) {
            case 'api-banner':
                return currentView.value === 'desktop' ? banner.api.design : banner.api.design_mobile;
            case 'double-banner':
                return banner.double?.[currentView.value]?.[selectedDoubleBanner.value] || {};
            case 'single-banner':
                return currentView.value === 'desktop' ? banner.single : banner.single_mobile;
            case 'simple-banner':
                return currentView.value === 'desktop' ? banner.simple : banner.simple_mobile;
            case 'sticky-simple-banner':
                return currentView.value === 'desktop' ? banner.sticky_simple : banner.sticky_simple_mobile;
            case 'promotion-banner':
                return currentView.value === 'desktop' ? banner.promotion : banner.promotion_mobile;
            // --- START: FIX ---
            case 'tour-carousel':
                return currentView.value === 'desktop' ? banner.tour_carousel.settings : banner.tour_carousel.settings_mobile;
            // --- END: FIX ---
            default:
                // Return an empty object with a nested header to prevent the 'text' error
                return { header: {} };
        }
    });

    return {
        previewBodyText,
        apiItem,
        isApiHotel,
        settings
    };
}