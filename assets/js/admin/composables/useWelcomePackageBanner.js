// assets/js/admin/composables/useWelcomePackageBanner.js
const { ref, reactive, computed } = Vue;

export function useWelcomePackageBanner(banner, showModal, ajax) {
    const isWelcomePackageModalOpen = ref(false);
    const isWelcomePackageLoading = ref(false);
    const welcomePackages = reactive([]);
    const tempSelectedPackageKey = ref(null); // Store key temporarily during modal selection

    // Computed property for preview rendering
    const welcomePackagePreviewHtml = computed(() => {
        if (!banner.welcome_package?.htmlContent || !banner.welcome_package?.selectedPackageKey) {
            return '<div style="color: #aaa; text-align: center; padding: 20px;">Select package and add HTML.</div>';
        }
        let html = banner.welcome_package.htmlContent;
        // Replace placeholders with the *initially selected* prices for preview
        html = html.replace(/\{\{\s*originalPrice\s*\}\}/g, banner.welcome_package.originalPrice ?? '...');
        html = html.replace(/\{\{\s*discountedPrice\s*\}\}/g, banner.welcome_package.discountedPrice ?? '...');
        html = html.replace(/\{\{\s*key\s*\}\}/g, banner.welcome_package.selectedPackageKey ?? '...');
        return html;
    });

    const formatPrice = (value) => {
        if (value === null || typeof value === 'undefined') return 'N/A';
        return parseFloat(value).toFixed(2);
    };

    const fetchWelcomePackages = async () => {
        if (welcomePackages.length > 0) return; // Don't refetch if already loaded

        isWelcomePackageLoading.value = true;
        try {
            const data = await ajax.post('yab_fetch_welcome_packages');
            welcomePackages.splice(0, welcomePackages.length, ...data); // Clear and add new packages
        } catch (error) {
            showModal('API Error', `Could not fetch welcome packages: ${error.message}`);
            welcomePackages.splice(0); // Clear on error
        } finally {
            isWelcomePackageLoading.value = false;
        }
    };

    const openWelcomePackageModal = async () => {
        isWelcomePackageModalOpen.value = true;
        tempSelectedPackageKey.value = banner.welcome_package.selectedPackageKey; // Pre-select if already chosen
        await fetchWelcomePackages(); // Fetch packages if not already loaded
    };

    const closeWelcomePackageModal = () => {
        isWelcomePackageModalOpen.value = false;
        // Reset temporary selection if modal is closed without confirmation
        tempSelectedPackageKey.value = null;
    };

    const selectWelcomePackage = (pkg) => {
        tempSelectedPackageKey.value = pkg.key;
        // Optionally update temporary prices here if needed for immediate feedback in modal
    };

    const confirmWelcomePackageSelection = () => {
        if (!tempSelectedPackageKey.value) {
            showModal('Info', 'Please select a package.');
            return;
        }
        const selectedPkg = welcomePackages.find(p => p.key === tempSelectedPackageKey.value);
        if (selectedPkg) {
            banner.welcome_package.selectedPackageKey = selectedPkg.key;
            banner.welcome_package.originalPrice = formatPrice(selectedPkg.originalMoneyValue);
            banner.welcome_package.discountedPrice = formatPrice(selectedPkg.moneyValue);
        } else {
            // Handle case where selected package might not be found (e.g., API changed)
            banner.welcome_package.selectedPackageKey = tempSelectedPackageKey.value;
            banner.welcome_package.originalPrice = 'N/A';
            banner.welcome_package.discountedPrice = 'N/A';
            console.warn(`Selected package key "${tempSelectedPackageKey.value}" not found in fetched packages.`);
        }
        isWelcomePackageModalOpen.value = false; // Close modal on confirmation
    };

    return {
        isWelcomePackageModalOpen,
        isWelcomePackageLoading,
        welcomePackages,
        tempSelectedPackageKey,
        openWelcomePackageModal,
        closeWelcomePackageModal,
        selectWelcomePackage,
        confirmWelcomePackageSelection,
        fetchWelcomePackages, // Expose fetch if needed elsewhere
        formatPrice,
        welcomePackagePreviewHtml // Expose computed preview HTML
    };
}
