// tappersia/assets/js/admin/app-logic/composables/useAppSetup.js
const { ref } = Vue;

export function useAppSetup(yabData, resetBannerState) {
    const appState = ref('loading');
    const isSaving = ref(false);
    const modalComponent = ref(null);
    const allBannersUrl = 'admin.php?page=tappersia-list';

    const showModal = (title, message) => {
        return modalComponent.value?.show({ title, message });
    };

    const selectElementType = (type) => {
        resetBannerState();
        appState.value = 'editor';
        return type;
    };

    const goBackToSelection = () => {
        if (yabData.existing_banner) {
            window.location.href = allBannersUrl;
        } else {
            resetBannerState();
            appState.value = 'selection';
        }
    };

    const goToListPage = () => {
        window.location.href = allBannersUrl;
    };

    return {
        appState,
        isSaving,
        modalComponent,
        allBannersUrl,
        showModal,
        selectElementType,
        goBackToSelection,
        goToListPage,
    };
}