// tappersia/assets/js/admin/composables/banner-state/defaults/welcomePackageBanner.js

export const createDefaultWelcomePackagePart = () => ({
    selectedKey: null,
    selectedPrice: null,
    selectedOriginalPrice: null,
    html: `<div style="border: 2px solid #00baa4; border-radius: 8px; padding: 20px; text-align: center; background-color: #f0f8ff;">
  <h2 style="color: #333; margin-top: 0;">Get Your {{selectedKey}}!</h2>
  <p style="color: #555; font-size: 1.1em;">
    Special Price: <strong style="color: #008000;">€{{price}}</strong>
    <span style="text-decoration: line-through; color: #999; margin-left: 10px;">€{{originalPrice}}</span>
  </p>
  <a href="#" style="display: inline-block; margin-top: 15px; padding: 10px 20px; background-color: #00baa4; color: white; text-decoration: none; border-radius: 5px;">
    Learn More
  </a>
</div>`,
});