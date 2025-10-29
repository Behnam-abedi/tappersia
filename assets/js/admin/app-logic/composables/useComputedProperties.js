// tappersia/assets/js/admin/app-logic/composables/useComputedProperties.js
const { computed } = Vue;

export function useComputedProperties(banner, currentView, selectedDoubleBanner) {
    const previewBodyText = computed(() => {
        // ... (Keep existing promo banner logic) ...
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

    // --- START: NEW Computed Property for Welcome Package Admin Preview ---
    const welcomePackagePreviewHtml = computed(() => {
        if (banner.type !== 'welcome-package-banner' || !banner.welcome_package?.html) {
            return '';
        }
        const pkg = banner.welcome_package;
        let html = pkg.html;
        // Replace placeholders with SAVED values for admin preview
        const price = pkg.selectedPrice !== null ? pkg.selectedPrice.toFixed(2) : 'N/A';
        const originalPrice = pkg.selectedOriginalPrice !== null ? pkg.selectedOriginalPrice.toFixed(2) : 'N/A';
        const key = pkg.selectedKey || 'Package'; // Default text if key is somehow null

        html = html.replace(/\{\{price\}\}/g, price);
        html = html.replace(/\{\{originalPrice\}\}/g, originalPrice);
        html = html.replace(/\{\{selectedKey\}\}/g, key);

        return html;
    });
    // --- END: NEW Computed Property ---


    const apiItem = computed(() => {
        // ... (Keep existing API banner logic) ...
        return banner.type === 'api-banner' ? (banner.api.selectedHotel || banner.api.selectedTour) : null;
    });

    const isApiHotel = computed(() => {
         // ... (Keep existing API banner logic) ...
        return !!(banner.type === 'api-banner' && banner.api.selectedHotel);
    });

    const settings = computed(() => {
        // ... (Keep existing settings logic) ...
        if (!banner.type) return { header: {} }; // بازگشت یک آبجکت پایه برای جلوگیری از ارور

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
            case 'tour-carousel':
                return currentView.value === 'desktop' ? banner.tour_carousel.settings : banner.tour_carousel.settings_mobile;
            case 'hotel-carousel':
                return currentView.value === 'desktop' ? banner.hotel_carousel.settings : banner.hotel_carousel.settings_mobile;
            // No specific settings object needed for welcome package in this computed
            default:
                // Return an empty object with a nested header to prevent the 'text' error
                return { header: {} };
        }
    });

    return {
        previewBodyText,
        welcomePackagePreviewHtml, // Expose the new computed property
        apiItem,
        isApiHotel,
        settings
    };
}