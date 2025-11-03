<?php
// tappersia/admin/views/components/banner-list-header.php
?>
<header class="sticky top-7 bg-[#434343]/60 backdrop-blur-md p-4 z-20 flex items-center justify-between shadow-lg ltr">
    <div class="flex items-center gap-4">
        <button @click="goBack" class="text-gray-400 hover:text-white bg-transparent border-none p-0 cursor-pointer flex items-center">&larr; Back</button>
        <span class="text-gray-600">|</span>
        <h1 class="text-xl font-bold text-white capitalize">{{ selectedType.replace('-', ' ') }}s</h1>
    </div>
    
    <div class="flex items-center gap-4">
        <input type="search" v-model="searchQuery" placeholder="Search banners..." class="search-input !w-64 bg-[#656565] border-gray-600 rounded px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-[#00baa4]">
        <a :href="addNewURL" class="bg-[#00baa4] text-white font-bold px-8 py-1.5 rounded hover:bg-opacity-80 transition-all">Add New</a>
    </div>
</header>