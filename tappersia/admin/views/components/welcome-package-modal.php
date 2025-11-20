<?php
// tappersia/admin/views/components/welcome-package-modal.php
?>
<transition name="yab-modal-fade">
    <div v-if="isWelcomePackageModalOpen" dir="ltr" class="fixed inset-0 bg-black bg-opacity-80 z-[99999] flex items-center justify-center p-4" @keydown.esc="closeWelcomePackageModal" @click.self="closeWelcomePackageModal">
        <div class="bg-[#2d2d2d] w-full max-w-2xl rounded-xl shadow-2xl flex flex-col overflow-hidden max-h-[80vh]">
            <header class="bg-[#434343] p-4 flex items-center justify-between flex-shrink-0">
                <h2 class="text-xl font-bold text-white">Select Welcome Package</h2>
                <button @click="closeWelcomePackageModal" class="text-gray-400 hover:text-white text-3xl leading-none">&times;</button>
            </header>

            <main class="flex-grow relative overflow-y-auto p-6">
                <div v-if="isLoadingPackages" class="absolute inset-0 flex items-center justify-center bg-[#2d2d2d]/80 z-10">
                    <div class="yab-spinner w-12 h-12"></div>
                </div>

                <div v-else-if="availablePackages.length === 0" class="text-center text-gray-400 py-10">
                    <p class="text-lg">No packages found or failed to load.</p>
                </div>

                <div v-else class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div v-for="pkg in availablePackages" :key="pkg.key" @click="selectPackage(pkg)"
                         class="p-4 bg-[#434343] rounded-lg cursor-pointer border-2 transition-all duration-200 text-left"
                         :class="tempSelectedPackage && tempSelectedPackage.key === pkg.key ? 'border-[#00baa4] shadow-lg' : 'border-transparent hover:border-gray-600'">
                        <h4 class="font-bold text-lg text-white mb-2">{{ pkg.key }}</h4>
                        <div class="text-sm">
                            <span class="text-green-400 font-semibold">Price: €{{ pkg.moneyValue.toFixed(2) }}</span>
                            <span v-if="pkg.originalMoneyValue && pkg.originalMoneyValue !== pkg.moneyValue"
                                  class="ml-2 text-gray-400 line-through">
                                €{{ pkg.originalMoneyValue.toFixed(2) }}
                            </span>
                        </div>
                    </div>
                </div>
            </main>
             <footer class="bg-[#434343] p-4 flex-shrink-0 flex justify-end">
                 <button @click="closeWelcomePackageModal" class="px-6 py-2 rounded bg-gray-600 hover:bg-gray-500 transition-colors text-white font-semibold">Cancel</button>
                 </footer>
        </div>
    </div>
</transition>