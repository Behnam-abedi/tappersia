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
        // --- START: ADDED TICKET LAYOUT ---
        ticketLayout: 'left', // 'left' or 'right'
        // --- END: ADDED TICKET LAYOUT ---

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
        // START: Added Content Width
        contentWidth: 100,
        contentWidthUnit: '%',
        // END: Added Content Width
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
            fromFontSize: 10, // <<< ADDED
        },

        // .ticket__button
        button: {
            bgColor: '#1EC2AF',
            BgHoverColor: '#169a8d',
            color: '#FFFFFF',
            fontSize: 13,
            fontWeight: '600',
            paddingX: 33, // <<< ADDED
            paddingY: 10, // <<< ADDED
            borderRadius: 8, // <<< ADDED
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

// +++ START: ADDED MOBILE DEFAULTS +++
export const createDefaultFlightTicketMobilePart = () => {
    const mobileDefaults = createDefaultFlightTicketPart(); // Start with desktop defaults

    // --- Apply Mobile Overrides ---
    
    // --- START: ADDED TICKET LAYOUT (Mobile inherits from desktop default) ---
    mobileDefaults.design.ticketLayout = 'left'; 
    // --- END: ADDED TICKET LAYOUT ---

    // 1. Banner Overrides
    mobileDefaults.design.minHeight = 70;
    mobileDefaults.design.borderRadius = 8;
    mobileDefaults.design.padding = 5;

    // 2. City Text Overrides
    mobileDefaults.design.fromCity.fontSize = 8;
    mobileDefaults.design.toCity.fontSize = 8;

    // 3. Button Overrides
    mobileDefaults.design.button.fontSize = 8;
    mobileDefaults.design.button.paddingX = 13;
    mobileDefaults.design.button.paddingY = 4;
    mobileDefaults.design.button.borderRadius = 4;

    // 4. Price Overrides
    mobileDefaults.design.price.fontSize = 8;
    mobileDefaults.design.price.fromFontSize = 5;
    
    // START: Added Content Width (Mobile default matches desktop)
    // (این بخش به‌طور خودکار از دسکتاپ به ارث می‌رسد، اما برای اطمینان اضافه شده)
    mobileDefaults.design.contentWidth = 100;
    mobileDefaults.design.contentWidthUnit = '%';
    // END: Added Content Width

    return mobileDefaults;
};
// +++ END: ADDED MOBILE DEFAULTS +++