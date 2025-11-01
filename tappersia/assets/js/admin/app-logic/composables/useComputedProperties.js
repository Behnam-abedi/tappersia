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

    const welcomePackagePreviewHtml = computed(() => {
        if (banner.type !== 'welcome-package-banner' || !banner.welcome_package?.html) {
            return '';
        }
        const pkg = banner.welcome_package;
        let html = pkg.html;

        // Get saved values
        const price_val = pkg.selectedPrice ?? 0;
        const original_price_val = pkg.selectedOriginalPrice ?? 0;
        const key = pkg.selectedKey || 'Package';

        // ** START: Calculate Discount Percentage for Preview **
        let discountPercentage = 0;
        if (original_price_val > 0 && original_price_val > price_val) {
             discountPercentage = Math.round(((original_price_val - price_val) / original_price_val) * 100);
        }
        // ** END: Calculate Discount Percentage for Preview **

        // Format prices for display
        const priceFormatted = price_val.toFixed(2);
        const originalPriceFormatted = original_price_val.toFixed(2);

        // Replace placeholders
        html = html.replace(/\{\{price\}\}/g, priceFormatted);
        html = html.replace(/\{\{originalPrice\}\}/g, originalPriceFormatted);
        html = html.replace(/\{\{selectedKey\}\}/g, key);
        // ** Add replacement for discountPercentage **
        html = html.replace(/\{\{discountPercentage\}\}/g, discountPercentage);

        return html;
    });


    const apiItem = computed(() => {
        // ... (Keep existing API banner logic) ...
        return banner.type === 'api-banner' ? (banner.api.selectedHotel || banner.api.selectedTour) : null;
    });

    const isApiHotel = computed(() => {
         // ... (Keep existing API banner logic) ...
        return !!(banner.type === 'api-banner' && banner.api.selectedHotel);
    });

    // --- START FIX: Renamed to avoid conflict ---
    const computedSettings = computed(() => { 
    // --- END FIX ---
        if (!banner.type) return { header: {} };

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
            // --- START FIX: Added flight-ticket case ---
            case 'flight-ticket':
                // This template creates its own 'settings' var, but we add this for robustness
                // and to prevent the computed prop from returning a default object.
                return banner.flight_ticket.design; 
            // --- END FIX ---
            default:
                return { header: {} };
        }
    });

    return {
        previewBodyText,
        welcomePackagePreviewHtml, // Expose the new computed property
        apiItem,
        isApiHotel,
        // --- START FIX: Return renamed computed prop ---
        settings: computedSettings 
        // --- END FIX ---
    };
}