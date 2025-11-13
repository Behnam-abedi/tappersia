// tappersia/assets/js/admin/composables/useWelcomePackageBanner.js
const { ref, reactive } = Vue;

export function useWelcomePackageBanner(banner, showModal, ajax) {
    const isWelcomePackageModalOpen = ref(false);
    const isLoadingPackages = ref(false);
    const availablePackages = reactive([]);
    const tempSelectedPackage = ref(null); // Store the selection within the modal temporarily

    const fetchWelcomePackages = async () => {
        isLoadingPackages.value = true;
        availablePackages.splice(0); // Clear previous results
        try {
            const data = await ajax.post('yab_fetch_welcome_packages');
            if (data && Array.isArray(data)) {
                availablePackages.push(...data);
            } else {
                showModal('API Error', 'Received invalid data format for Welcome Packages.');
                console.error('Invalid data format:', data);
            }
        } catch (error) {
            showModal('API Error', `Could not fetch Welcome Packages: ${error.message}`);
        } finally {
            isLoadingPackages.value = false;
        }
    };

    const openWelcomePackageModal = async () => {
        isWelcomePackageModalOpen.value = true;
        tempSelectedPackage.value = availablePackages.find(p => p.key === banner.welcome_package.selectedKey) || null;

        // Fetch packages if the list is empty
        if (availablePackages.length === 0) {
            await fetchWelcomePackages();
             // Re-select after fetching if needed
             tempSelectedPackage.value = availablePackages.find(p => p.key === banner.welcome_package.selectedKey) || null;
        }
    };

    const closeWelcomePackageModal = () => {
        isWelcomePackageModalOpen.value = false;
    };

    const selectPackage = (pkg) => {
        // Update the main banner state directly when a package is clicked
        banner.welcome_package.selectedKey = pkg.key;
        banner.welcome_package.selectedPrice = pkg.moneyValue;
        banner.welcome_package.selectedOriginalPrice = pkg.originalMoneyValue;
        closeWelcomePackageModal(); // Close modal on selection
    };

    return {
        isWelcomePackageModalOpen,
        isLoadingPackages,
        availablePackages,
        tempSelectedPackage, // Expose for potential UI feedback in modal if needed
        openWelcomePackageModal,
        closeWelcomePackageModal,
        selectPackage,
        // No confirm needed as selection happens on click
    };
}