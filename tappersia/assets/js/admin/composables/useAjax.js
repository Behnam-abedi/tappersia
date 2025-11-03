/**
 * A reusable composable for handling WordPress AJAX requests securely.
 * @param {string} ajaxUrl - The admin-ajax.php URL.
 * @param {string} nonce - The WordPress nonce for verification.
 * @returns {object} - An object with a 'post' method.
 */
export function useAjax(ajaxUrl, nonce) {
    const post = (action, data = {}) => {
        return new Promise((resolve, reject) => {
            jQuery.ajax({
                url: ajaxUrl,
                type: 'POST',
                data: { action, nonce, ...data },
                success: (response) => {
                    if (response.success) {
                        resolve(response.data);
                    } else {
                        const errorMessage = response && response.data ? response.data.message : 'An unknown WordPress error occurred. The server response was not in the expected format.';
                        reject(new Error(errorMessage));
                    }
                },
                error: (jqXHR) => {
                    const message = jqXHR.responseJSON?.data?.message || 'An unknown AJAX error occurred.';
                    reject(new Error(message));
                }
            });
        });
    };
    return { post };
}