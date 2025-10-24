// assets/js/admin/composables/banner-state/defaults/welcomePackageBanner.js

export const createDefaultWelcomePackagePart = () => ({
    selectedPackageKey: null,
    htmlContent: `<div style="border: 1px solid #ccc; padding: 20px; text-align: center; border-radius: 8px;">
    <h2>Welcome Package Offer!</h2>
    <p>Get the "{{key}}" package now.</p>
    <p>
        Original Price: <del>€{{originalPrice}}</del><br>
        <strong>Special Price: €{{discountedPrice}}</strong>
    </p>
    <a href="#" style="display: inline-block; padding: 10px 20px; background-color: #00baa4; color: white; text-decoration: none; border-radius: 5px;">Learn More</a>
</div>`,
    // These will be filled when a package is selected in the modal
    originalPrice: null,
    discountedPrice: null,
});
