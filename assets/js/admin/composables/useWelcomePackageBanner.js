// assets/js/admin/composables/useWelcomePackageBanner.js
const { ref, reactive, computed } = Vue;

export function useWelcomePackageBanner(banner, showModal, ajax) {
    const isWelcomePackageModalOpen = ref(false);
    const isWelcomePackageLoading = ref(false);
    const welcomePackages = reactive([]);
    const tempSelectedPackageKey = ref(null);

    const welcomePackagePreviewHtml = computed(() => {
        if (!banner.welcome_package?.htmlContent || !banner.welcome_package?.selectedPackageKey) {
            return '<div style="color: #aaa; text-align: center; padding: 20px;">Select package and add HTML.</div>';
        }
        let html = banner.welcome_package.htmlContent;
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
        if (welcomePackages.length > 0) return;

        isWelcomePackageLoading.value = true;
        try {
            const data = await ajax.post('yab_fetch_welcome_packages');
            welcomePackages.splice(0, welcomePackages.length, ...data);
        } catch (error) {
            showModal('API Error', `Could not fetch welcome packages: ${error.message}`);
            welcomePackages.splice(0);
        } finally {
            isWelcomePackageLoading.value = false;
        }
    };

    const openWelcomePackageModal = async () => {
        isWelcomePackageModalOpen.value = true;
        tempSelectedPackageKey.value = banner.welcome_package.selectedPackageKey;
        await fetchWelcomePackages();
    };

    const closeWelcomePackageModal = () => {
        isWelcomePackageModalOpen.value = false;
        tempSelectedPackageKey.value = null; // Reset temp on close without confirm
    };

    const selectWelcomePackage = (pkg) => {
        tempSelectedPackageKey.value = pkg.key;
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
            banner.welcome_package.selectedPackageKey = tempSelectedPackageKey.value;
            banner.welcome_package.originalPrice = 'N/A';
            banner.welcome_package.discountedPrice = 'N/A';
            console.warn(`Selected package key "${tempSelectedPackageKey.value}" not found.`);
        }
        isWelcomePackageModalOpen.value = false;
    };

    // --- New Function: Copy Placeholder ---
    const copyPlaceholder = (placeholder) => {
        // Use navigator.clipboard if available (more modern)
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(placeholder).then(() => {
                showModal('Copied!', `Placeholder "${placeholder}" copied to clipboard.`);
            }).catch(err => {
                console.error('Failed to copy placeholder using navigator: ', err);
                // Fallback for older browsers or insecure contexts
                fallbackCopyTextToClipboard(placeholder);
            });
        } else {
            // Fallback for older browsers or insecure contexts
            fallbackCopyTextToClipboard(placeholder);
        }
    };

    // --- New Helper: Fallback Copy Function ---
    const fallbackCopyTextToClipboard = (text) => {
        const textArea = document.createElement("textarea");
        textArea.value = text;

        // Avoid scrolling to bottom
        textArea.style.top = "0";
        textArea.style.left = "0";
        textArea.style.position = "fixed";

        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();

        try {
            const successful = document.execCommand('copy');
            if (successful) {
                showModal('Copied!', `Placeholder "${text}" copied to clipboard.`);
            } else {
                showModal('Error', 'Could not copy placeholder.');
            }
        } catch (err) {
            console.error('Fallback copy failed: ', err);
            showModal('Error', 'Could not copy placeholder.');
        }

        document.body.removeChild(textArea);
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
        fetchWelcomePackages,
        formatPrice,
        welcomePackagePreviewHtml,
        copyPlaceholder // Expose the new copy function
    };
}

