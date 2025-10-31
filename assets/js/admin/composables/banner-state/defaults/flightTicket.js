// tappersia/assets/js/admin/composables/banner-state/defaults/flightTicket.js

export const createDefaultFlightTicketPart = () => ({
    from: null, // Will store { countryName, city, iataCode }
    to: null,   // Will store { countryName, city, iataCode }
    cheapestPrice: null,
    cheapestFlightDetails: null, // To store the full flight object
    isLoadingFlights: false,
    lastSearchError: null,
});