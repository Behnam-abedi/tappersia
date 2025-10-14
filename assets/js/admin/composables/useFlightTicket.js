// tappersia/assets/js/admin/composables/useFlightTicket.js
const { ref, reactive, computed } = Vue;

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

    const openFlightModal = async () => {
        isFlightModalOpen.value = true;
        tempSelectedAirports.from = banner.flight_ticket.fromAirportCode ? airports.value.find(a => a.iataCode === banner.flight_ticket.fromAirportCode) || null : null;
        tempSelectedAirports.to = banner.flight_ticket.toAirportCode ? airports.value.find(a => a.iataCode === banner.flight_ticket.toAirportCode) || null : null;

        if (airports.value.length === 0) {
            isAirportsLoading.value = true;
            try {
                const data = await ajax.post('yab_fetch_airports_from_api');
                // *** FIX START: Removed the filter to show all airports ***
                airports.value = data;
                // *** FIX END ***
            } catch (error) {
                showModal('Error', `Could not fetch airports: ${error.message}`);
            } finally {
                isAirportsLoading.value = false;
            }
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
            banner.flight_ticket.fromAirportCode = tempSelectedAirports.from.iataCode;
            banner.flight_ticket.toAirportCode = tempSelectedAirports.to.iataCode;
            closeFlightModal();
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
    };
}