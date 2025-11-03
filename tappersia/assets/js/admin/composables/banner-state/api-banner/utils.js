// tappersia/assets/js/admin/composables/api-banner/utils.js
export const getRatingLabel = (score) => {
    if (score === null || score === undefined || score === 0) return 'New';
    if (score >= 4.6) return 'Excellent';
    if (score >= 4.1) return 'Very Good';
    if (score >= 3.6) return 'Good';
    if (score >= 3.0) return 'Average';
    return 'Poor';
};

export const formatRating = (score) => {
    if (score === null || score === undefined) return '';
    if (Math.floor(score) === score) {
        return parseInt(score, 10);
    }
    return Math.floor(score * 10) / 10;
};