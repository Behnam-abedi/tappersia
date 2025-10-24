<?php
// /admin/views/components/welcome-package-modal.php
?>
<transition name="yab-modal-fade">
    <div v-if="isWelcomePackageModalOpen" dir="ltr" class="fixed inset-0 bg-black bg-opacity-80 z-[99999] flex items-center justify-center p-4" @keydown.esc="closeWelcomePackageModal" @click.self="closeWelcomePackageModal">
        <div class="bg-[#2d2d2d] w-full max-w-4xl h-[80vh] rounded-xl shadow-2xl flex flex-col overflow-hidden">
            <header class="bg-[#434343] p-4 flex items-center justify-between flex-shrink-0">
                <h2 class="text-xl font-bold text-white">Select Welcome Package Price</h2>
                <button @click="closeWelcomePackageModal" class="text-gray-400 hover:text-white text-3xl leading-none">&times;</button>
            </header>

            <main class="flex-grow relative overflow-y-auto p-6">
                <div v-if="isWelcomePackageLoading" class="absolute inset-0 flex items-center justify-center bg-[#2d2d2d]/80 z-10">
                    <div class="yab-spinner w-12 h-12"></div>
                </div>
                <div v-else-if="welcomePackages.length === 0" class="text-center text-gray-400 py-16">
                    <p class="text-lg">No packages found or failed to load.</p>
                     <button @click="fetchWelcomePackages" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Retry</button>
                </div>
                <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div v-for="pkg in welcomePackages" :key="pkg.key" @click="selectWelcomePackage(pkg)"
                        class="p-4 bg-[#434343] rounded-lg cursor-pointer border-2 transition-all duration-200 min-h-[120px] flex flex-col justify-between"
                        :class="tempSelectedPackageKey === pkg.key ? 'border-[#00baa4] shadow-lg ring-2 ring-[#00baa4]' : 'border-transparent hover:border-gray-600'">
                        <div>
                            <h4 class="font-bold text-lg text-white mb-2">{{ pkg.key }}</h4>
                        </div>
                        <div class="text-sm mt-auto">
                            <p class="text-gray-400">
                                Original: <span class="line-through">€{{ formatPrice(pkg.originalMoneyValue) }}</span>
                            </p>
                             <p class="text-teal-400 font-semibold text-base">
                                Current: €{{ formatPrice(pkg.moneyValue) }}
                             </p>
                        </div>
                    </div>
                </div>
            </main>

            <footer class="bg-[#434343] p-4 flex-shrink-0 flex justify-end items-center border-t border-gray-700">
                <button @click="confirmWelcomePackageSelection" :disabled="!tempSelectedPackageKey" class="bg-[#00baa4] text-white font-bold px-6 py-2 rounded-lg hover:bg-opacity-80 transition-all disabled:bg-gray-500 disabled:cursor-not-allowed">
                    Confirm Selection
                </button>
            </footer>
        </div>
    </div>
</transition>
