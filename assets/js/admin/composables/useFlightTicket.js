// tappersia/assets/js/admin/composables/useFlightTicket.js
const { ref, reactive, computed, watch } = Vue;

export function useFlightTicket(banner, showModal, ajax) {
    const isFlightModalOpen = ref(false);
    const isAirportsLoading = ref(false);
    const airports = ref([]);
    const airportSearchQuery = ref('');
    const tempSelectedAirports = reactive({
        from: null,
        to: null,
    });

    const filteredAirports = computed(() => {
        if (!airportSearchQuery.value) {
            return airports.value;
        }
        const query = airportSearchQuery.value.toLowerCase();
        return airports.value.filter(airport => 
            airport.name.toLowerCase().includes(query) ||
            airport.city.toLowerCase().includes(query) ||
            airport.iataCode.toLowerCase().includes(query)
        );
    });

    /**
     * Fetches the cheapest flight for the selected route for tomorrow.
     */
    const fetchCheapestFlight = async () => {
        if (!banner.flight_ticket.from || !banner.flight_ticket.to) {
            return; // Don't fetch if selection is incomplete
        }
        
        banner.flight_ticket.isLoadingFlights = true;
        banner.flight_ticket.cheapestPrice = null;
        banner.flight_ticket.cheapestFlightDetails = null;
        banner.flight_ticket.lastSearchError = null;

        try {
            const params = {
                fromAirportCode: banner.flight_ticket.from.iataCode,
                fromCountryName: banner.flight_ticket.from.countryName,
                toAirportCode: banner.flight_ticket.to.iataCode,
                toCountryName: banner.flight_ticket.to.countryName,
            };

            // This is the new AJAX action we will create in PHP
            const result = await ajax.post('yab_fetch_flight_search', params);
            
            // As requested, log the result
            console.log('Flight Search Result:', result);

            if (result && result.cheapestPrice !== null) {
                banner.flight_ticket.cheapestPrice = result.cheapestPrice;
                banner.flight_ticket.cheapestFlightDetails = result.cheapestFlight;
                console.log(`Found cheapest price: €${result.cheapestPrice}`);
            } else {
                 console.log('No flights found or no price available.');
                 banner.flight_ticket.lastSearchError = result.message || 'No flights found for tomorrow.';
            }

        } catch (error) {
            console.error('Error fetching flight search:', error);
            banner.flight_ticket.lastSearchError = error.message;
            showModal('Error', `Could not fetch flight data: ${error.message}`);
        } finally {
            banner.flight_ticket.isLoadingFlights = false;
        }
    };

    // START: Added bookingUrl computed property
    const bookingUrl = computed(() => {
        if (!banner.flight_ticket.from || !banner.flight_ticket.to) {
            return '#'; // Return placeholder if data is missing
        }

        // 1. Get tomorrow's date
        const tomorrow = new Date();
        console.log(tomorrow);
        
        tomorrow.setDate(tomorrow.getDate() + 1);
        const yyyy = tomorrow.getFullYear();
        const mm = String(tomorrow.getMonth() + 1).padStart(2, '0'); // Months are 0-indexed
        const dd = String(tomorrow.getDate()).padStart(2, '0');
        const departureDate = `${yyyy}-${mm}-${dd}`;
        console.log(departureDate);
        
        // 2. Get data from banner state
        const from = banner.flight_ticket.from;
        const to = banner.flight_ticket.to;

        // 3. Build URL parts
        const baseUrl = 'https://www.tappersia.com/iran-flights/';
        // Sanitize city names for URL path (e.g., "Tehran" -> "tehran", "Qeshm Island" -> "qeshm-island")
        const fromCityPath = from.city.toLowerCase().replace(/\s+/g, '-');
        const toCityPath = to.city.toLowerCase().replace(/\s+/g, '-');
        
        const path = `${fromCityPath}/${toCityPath}`;

        // 4. Build query parameters
        const params = new URLSearchParams({
            fromCountryName: from.countryName,
            fromCityName: from.city,
            fromAirportCode: from.iataCode,
            toCountryName: to.countryName,
            toCityName: to.city,
            toAirportCode: to.iataCode,
            departureDate: departureDate,
            pageNumber: 1,
            pageSize: 10,
            sort: 'earliest_time'
        });

        // 5. Combine and return
        return `${baseUrl}${path}?${params.toString()}`;
    });
    // END: Added bookingUrl computed property

    const openFlightModal = async () => {
        isFlightModalOpen.value = true;
        
        const findAirportByIata = (iataCode) => {
            if (!iataCode) return null;
            return airports.value.find(a => a.iataCode === iataCode) || null;
        };

        // Set temp selection based on current banner state
        const setTempFromBanner = () => {
            tempSelectedAirports.from = findAirportByIata(banner.flight_ticket.from?.iataCode);
            tempSelectedAirports.to = findAirportByIata(banner.flight_ticket.to?.iataCode);
        };

        if (airports.value.length === 0) {
            isAirportsLoading.value = true;
            try {
                const data = await ajax.post('yab_fetch_airports_from_api');
                airports.value = data;
                setTempFromBanner(); // Set selection *after* data is loaded
            } catch (error) {
                showModal('Error', `Could not fetch airports: ${error.message}`);
            } finally {
                isAirportsLoading.value = false;
            }
        } else {
            setTempFromBanner(); // Airports already loaded, just set selection
        }
    };

    const closeFlightModal = () => {
        isFlightModalOpen.value = false;
        airportSearchQuery.value = '';
    };

    const selectAirport = (airport) => {
        if (tempSelectedAirports.from && tempSelectedAirports.from.iataCode === airport.iataCode) {
            tempSelectedAirports.from = null;
            return;
        }
        if (tempSelectedAirports.to && tempSelectedAirports.to.iataCode === airport.iataCode) {
            tempSelectedAirports.to = null;
            return;
        }

        if (!tempSelectedAirports.from) {
            tempSelectedAirports.from = airport;
        } else if (!tempSelectedAirports.to) {
            tempSelectedAirports.to = airport;
        } else {
            // If both are selected, the next click resets 'from' and selects it.
            tempSelectedAirports.from = airport;
            tempSelectedAirports.to = null;
        }
    };
    
    const isAirportSelected = (airport) => {
        return (tempSelectedAirports.from && tempSelectedAirports.from.iataCode === airport.iataCode) || 
               (tempSelectedAirports.to && tempSelectedAirports.to.iataCode === airport.iataCode);
    };

    const confirmAirportSelection = () => {
        if (tempSelectedAirports.from && tempSelectedAirports.to) {
            // Store the required fields from the full airport object
            banner.flight_ticket.from = {
                countryName: tempSelectedAirports.from.countryName,
                city: tempSelectedAirports.from.city,
                iataCode: tempSelectedAirports.from.iataCode
            };
            banner.flight_ticket.to = {
                countryName: tempSelectedAirports.to.countryName,
                city: tempSelectedAirports.to.city,
                iataCode: tempSelectedAirports.to.iataCode
            };
            
            closeFlightModal();
            
            // Trigger the flight search
            fetchCheapestFlight(); 
        } else {
            showModal('Selection Incomplete', 'Please select both an origin and a destination airport.');
        }
    };

    return {
        isFlightModalOpen,
        isAirportsLoading,
        filteredAirports,
        airportSearchQuery,
        tempSelectedAirports,
        openFlightModal,
        closeFlightModal,
        selectAirport,
        isAirportSelected,
        confirmAirportSelection,
        fetchCheapestFlight, // Expose for use in main.js
        bookingUrl, // START: Expose the new computed property
    };
}