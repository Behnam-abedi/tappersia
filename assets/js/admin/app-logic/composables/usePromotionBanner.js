// tappersia/assets/js/admin/app-logic/composables/usePromotionBanner.js
const { ref } = Vue;

export function usePromotionBanner(banner, showModal) {
    const bodyTextarea = ref(null);

    const addPromoLink = (placeholder) => {
        const placeholderExists = banner.promotion.links.some(link => link.placeholder === placeholder);
        if (!placeholderExists) {
             banner.promotion.links.push({
                placeholder: placeholder,
                url: '#',
                color: '#f07100'
            });
        }
    };
    
    const removePromoLink = (index) => {
        banner.promotion.links.splice(index, 1);
    };
    
    const makeSelectedTextPlaceholder = () => {
        const textarea = bodyTextarea.value;
        if (!textarea) return;

        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const selectedText = textarea.value.substring(start, end);

        if (!selectedText) {
            return showModal('Info', 'Please select a piece of text first.');
        }
        
        const placeholderExists = banner.promotion.links.some(link => link.placeholder === selectedText);
        if(placeholderExists) {
             return showModal('Info', `A link for "${selectedText}" already exists.`);
        }

        const newText = `${textarea.value.substring(0, start)}[[${selectedText}]]${textarea.value.substring(end)}`;
        banner.promotion.bodyText = newText;
        addPromoLink(selectedText);
    };

    return {
        bodyTextarea,
        makeSelectedTextPlaceholder,
        removePromoLink,
    };
}