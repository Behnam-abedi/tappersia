// const { ref } = Vue; // This line is removed to prevent redeclaration errors.

const YabModal = {
  setup() {
    // We now access 'ref' directly from the global Vue object.
    const isVisible = Vue.ref(false);
    const title = Vue.ref('');
    const message = Vue.ref('');
    const type = Vue.ref('alert'); // 'alert' or 'confirm'
    const confirmText = Vue.ref('OK');
    let resolvePromise = null;

    const show = (config) => {
      title.value = config.title;
      message.value = config.message;
      type.value = config.type || 'alert';
      confirmText.value = config.confirmText || (type.value === 'confirm' ? 'Confirm' : 'OK');
      isVisible.value = true;
      
      return new Promise((resolve) => {
        resolvePromise = resolve;
      });
    };

    const handleConfirm = (result) => {
      if (resolvePromise) {
        resolvePromise(result);
      }
      isVisible.value = false;
    };

    return {
      isVisible,
      title,
      message,
      type,
      confirmText,
      show,
      handleConfirm,
    };
  },
  // *** FIX START: Wrapped template in a <transition> component ***
  template: `
    <transition name="yab-modal-fade">
      <div v-if="isVisible" class="fixed inset-0 bg-black bg-opacity-70 z-[10000] flex items-center justify-center p-4" @click.self="handleConfirm(false)">
          <div class="bg-[#434343] rounded-lg shadow-xl p-6 w-full max-w-md text-center transform transition-all" >
              <h3 class="text-xl font-bold mb-4 text-white">{{ title }}</h3>
              <p class="text-gray-300 mb-6" style="white-space: pre-wrap;">{{ message }}</p>
              <div class="flex justify-center gap-4">
                  <button v-if="type === 'confirm'" @click="handleConfirm(false)" class="px-6 py-2 rounded bg-gray-600 hover:bg-gray-500 transition-colors text-white font-semibold">Cancel</button>
                  <button @click="handleConfirm(true)" class="px-6 py-2 rounded bg-[#00baa4] hover:bg-opacity-80 transition-colors text-white font-bold">{{ confirmText }}</button>
              </div>
          </div>
      </div>
    </transition>
  `
  // *** FIX END ***
};