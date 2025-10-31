// tappersia/assets/js/admin/app-logic/components.js
export const ImageLoader = {
  props: ['src', 'alt', 'imgClass'],
  data: () => ({ loading: true }),
  methods: {
    onImageLoad() { this.loading = false; },
    onImageError() { this.loading = false; }
  },
  template: `
    <div class="yab-image-loader-container">
      <div v-if="loading" class="yab-skeleton-loader"></div>
      <img :src="src" :alt="alt" :class="[imgClass, { 'opacity-0': loading }]" @load="onImageLoad" @error="onImageError"/>
    </div>
  `,
};