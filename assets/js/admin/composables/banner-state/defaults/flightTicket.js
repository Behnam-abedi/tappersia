// tappersia/assets/js/admin/composables/banner-state/defaults/flightTicket.js

export const createDefaultFlightTicketPart = () => ({
    from: null, // Will store { countryName, city, iataCode }
    to: null,   // Will store { countryName, city, iataCode }
    cheapestPrice: null,
    cheapestFlightDetails: null, // To store the full flight object
    isLoadingFlights: false,
    lastSearchError: null,
    
    // --- START: Added Design Settings ---
    design: {
        // .promo-banner
        minHeight: 150,
        borderRadius: 16,
        padding: 12,
        
        // .promo-banner__background (Overlay)
        layerOrder: 'overlay-below-image', // 'overlay-below-image' or 'image-below-overlay'
        backgroundType: 'solid',
        bgColor: '#CEE8F6',
        gradientAngle: 90,
        gradientStops: [
            { color: '#CEE8F6', stop: 0 },
            { color: '#CEE8F6', stop: 100 }
        ],
        
        // .promo-banner__image-wrapper (Image) - Mirrored from singleBanner
        imageUrl: '', // <<<< FIX: Removed default image URL
        enableCustomImageSize: false,
        imageWidth: null,
        imageWidthUnit: 'px',
        imageHeight: null,
        imageHeightUnit: 'px',
        imagePosLeft: 0, // <<<< FIX: Changed to Left
        imagePosBottom: 0,
        
        // .promo-banner__content (Texts)
        content1: {
            text: 'Offering',
            color: '#555555',
            fontSize: 12,
            fontWeight: '400',
        },
        content2: {
            text: 'BEST DEALS',
            color: '#111111',
            fontSize: 18,
            fontWeight: '700', // bold
        },
        content3: {
            text: 'on Iran Domestic Flight Booking',
            color: '#333333',
            fontSize: 14,
            fontWeight: '400',
        },

        // .ticket__price-amount
        price: {
            color: '#00BAA4',
            fontSize: 17,
            fontWeight: '700',
        },

        // .ticket__button
        button: {
            bgColor: '#1EC2AF',
            color: '#FFFFFF',
            fontSize: 13,
            fontWeight: '600',
        },

        // .ticket-from-country
        fromCity: {
            color: '#000000',
            fontSize: 16,
            fontWeight: '700',
        },
        
        // .ticket-to-country
        toCity: {
            color: '#000000',
            fontSize: 16,
            fontWeight: '700',
        }
    }
    // --- END: Added Design Settings ---
});