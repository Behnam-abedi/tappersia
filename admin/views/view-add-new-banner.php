<div id="yab-app" class="bg-[#232323] text-white min-h-screen font-sans" v-cloak>
    
    <div v-if="appState === 'selection'" class="flex items-center justify-center h-screen">
        <div class="p-8 text-center ">
            <h1 class="text-3xl font-bold mb-8 text-gray-200 ">Create a New Element</h1>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div @click="selectElementType('double-banner')" class="cursor-pointer bg-[#1A2B48] p-8 rounded-lg transform hover:-translate-y-1 transition-all duration-300 group flex justify-center items-center flex-col gap-2">
                    <span class="dashicons dashicons-columns text-5xl mb-4 text-[#00baa4] group-hover:text-white transition-colors flex justify-center"></span>
                    <h3 class="font-semibold text-lg text-gray-200 group-hover:text-white">Double Banner</h3>
                </div>
            </div>
        </div>
    </div>

    <div v-if="appState === 'editor'">
        <header class="sticky top-7 bg-[#434343]/60 backdrop-blur-md p-7 z-20  flex items-center justify-between shadow-lg">
            <div class="flex items-center gap-4">
                 <a :href="allBannersUrl" class="text-light-400 hover:text-white">&rarr; All Banners</a>
                 <span class="text-light-600">|</span>
                <input type="text" v-model="banner.name" placeholder="Enter Banner Name..." class="!bg-[#656565] !text-white border border-gray-600 rounded px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-[#00baa4] w-64 ltr">
                <span class="text-sm  ">: Title</span>
            </div>
            
            <div class="flex items-center gap-5">
                <div class="flex items-center gap-2">
<div class="flex rounded-lg bg-[#656565] overflow-hidden">
  <button
    @click="banner.displayMethod = 'Fixed'"
    :class="banner.displayMethod === 'Fixed'
      ? 'bg-[#00baa4] text-white'
      : 'text-gray-300'"
    class="px-3 py-1 text-sm transition-colors duration-300 flex-1"
  >
    Fixed
  </button>

  <button
    @click="banner.displayMethod = 'Embeddable'"
    :class="banner.displayMethod === 'Embeddable'
      ? 'bg-[#00baa4] text-white'
      : 'text-gray-300'"
    class="px-3 py-1 text-sm transition-colors duration-300 flex-1"
  >
    Embeddable
  </button>
</div>

                    <span class="text-sm  ">: Display Method</span>
                </div>

                <div class="flex items-center gap-2">
                    <input type="text" :value="shortcode" readonly @click="copyShortcode" class="w-52 !bg-[#656565] !text-white text-left rounded px-2 py-1 text-sm cursor-pointer" title="Click to copy">
                    <span class="text-sm ">: Shortcode</span>
                </div>

                <div class="flex items-center">
                    
                    <label class="relative inline-flex items-center cursor-pointer">
                      <input type="checkbox" v-model="banner.isActive" class="sr-only peer">
                      <div class="w-11 h-6 bg-gray-600 rounded-full peer peer-focus:ring-2 peer-focus:ring-red-500 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div>
                    </label>
                    <span class="text-sm mr-3">: Status</span>
                </div>

                <button @click="saveBanner" class="bg-[#00baa4] text-white font-bold px-8 py-1.5 rounded hover:bg-opacity-80 transition-all flex items-center gap-2">
                    <span v-if="isSaving" class="dashicons dashicons-update animate-spin"></span>
                    {{ isSaving ? 'Saving...' : 'Save' }}
                </button>
            </div>
        </header>
        
        <main class="grid grid-cols-12 gap-3 p-6">
<div class="col-span-4 overflow-y-auto ltr flex gap-3 flex-col" style="max-height: calc(100vh - 100px);">
    <div v-for="(b, key) in { left: banner.right ,right: banner.left}" :key="key" style="background:#434343;padding:20px;border-radius:10px;box-shadow:0 2px 6px rgba(0,0,0,0.3);">
        
<!-- Title + Alignment Switch -->
    
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-direction:col-reverse">
        <div class="flex flex-row justify-between items-center w-full bg-[#656565] p-2 rounded-[7px]  ">
    <h3 style="font-weight:bold;font-size:20px;color:#ffffff;text-transform:capitalize;letter-spacing:0.5px;">
        {{ key }} Banner Settings
    </h3>
    <div @click="b.alignment = b.alignment === 'left' ? 'right' : 'left'"
        style="width:130px;height:32px;background:#292929;border:1px solid #292929;border-radius:20px;cursor:pointer;display:flex;align-items:center;justify-content:space-between;padding:0 15px;position:relative;user-select:none;">
        
        <!-- دایره متحرک -->
        <div :style="{
            position: 'absolute',
            top: '2px',
            left: b.alignment === 'right' ? '3px' : 'calc(100% - 58px)',
            width: '55px',
            height: '26px',
            background: '#00baa4',
            borderRadius: '20px',
            transition: 'left 0.3s'
        }"></div>
        <!-- متن Right -->
                 <span :style="{
            position: 'relative',
            fontSize: '14px',
            color: '#fff',

            zIndex: 1
        }">Right</span>
        <span :style="{
            position: 'relative',
            fontSize: '14px',
            color: '#fff',
            zIndex: 1
        }">Left</span>
        <!-- متن Left -->

    </div>
        </div>

</div>


        <div style="display:flex;flex-direction:column;gap:20px;max-width:500px;direction: ltr;">

            <!-- Background -->
<div>
    <h4 style="color:#fff;font-size:15px;margin-bottom:8px;background: #656565;padding: 5px;border-radius: 7px;">Background</h4>
    
    <!-- دکمه های انتخاب -->
    <div style="display:flex;gap:8px;margin-bottom:8px;">
        <div 
            @click="b.backgroundType = 'solid'" 
            :style="{
                flex: 1,
                textAlign: 'center',
                padding: '6px 0',
                borderRadius: '5px',
                cursor: 'pointer',
                background: b.backgroundType === 'solid' ? '#292929' : '#494949ff',
                border: b.backgroundType === 'solid' ? '3px solid #00baa4' : '1px solid #292929',
                color: '#fff',
                fontWeight: b.backgroundType === 'solid' ? 'bold' : 'normal',
                transition: '0.2s',
                
            }"
        >Solid Color</div>

        <div 
            @click="b.backgroundType = 'gradient'" 
            :style="{
                flex: 1,
                textAlign: 'center',
                padding: '6px 0',
                borderRadius: '5px',
                cursor: 'pointer',
                background: b.backgroundType === 'gradient' ? '#292929' : '#494949ff',
                border: b.backgroundType === 'gradient' ? '3px solid #00baa4' : '1px solid #292929',
                color: '#fff',
                fontWeight: b.backgroundType === 'gradient' ? 'bold' : 'normal',
                transition: '0.2s'
            }"
        >Gradient</div>
    </div>

    <!-- Solid Color Picker -->
    <div v-if="b.backgroundType === 'solid'" style="display:flex;gap:8px;">
        <input type="color" v-model="b.bgColor" class="yab-color-picker " style="width: 100%;">
    </div>

    <!-- Gradient Pickers -->
    <div v-else style="display:flex;gap:8px;justify-content:space-between;">
                <input type="color" v-model="b.gradientColor1" class="yab-color-picker" style="width: 50%;">
        <input type="color" v-model="b.gradientColor2" class="yab-color-picker" style="width: 50%;">

    </div>
</div>

<hr class="h-px my-2 bg-[#656565] border-0 ">
            <!-- Title -->
            <div>
                <h4 style="color:#fff;font-size:15px;margin-bottom:8px;background: #656565;padding: 5px;border-radius: 7px;">Title</h4>
                <input type="text" v-model="b.titleText" style="width:100%;background:#292929;border:1px solid #292929;border-radius:5px;padding:6px;color:#fff;margin-bottom:8px;">
                <div style="display:grid;grid-template-columns:60px 1fr 1fr;gap:8px;">
                    <input type="color" v-model="b.titleColor" class="yab-color-picker" style="width: 100%;height: 100%;">
                    <input type="number" v-model.number="b.titleSize" style="background:#292929;border:1px solid #292929;border-radius:5px;padding:6px;color:#fff;" placeholder="Size (px)">
                    <select v-model="b.titleWeight" style="background:#292929;border:1px solid #292929;border-radius:5px;padding:6px;color:#fff;">
                        <option value="400">Normal</option><option value="500">Medium</option><option value="600">Semi-Bold</option><option value="700">Bold</option>
                    </select>
                </div>
            </div>
<hr class="h-px my-2 bg-[#656565] border-0 ">
            <!-- Description -->
            <div>
                <h4 style="color:#fff;font-size:15px;margin-bottom:8px;background: #656565;padding: 5px;border-radius: 7px;">Description</h4>
                <textarea v-model="b.descText" rows="3" style="width:100%;background:#292929;border:1px solid #292929;border-radius:5px;padding:6px;color:#fff;margin-bottom:8px;"></textarea>
                <div style="display:grid;grid-template-columns:60px 1fr 1fr;gap:8px;">
                    <input type="color" v-model="b.descColor" class="yab-color-picker" style="width: 100%;height: 100%;">
                    <input type="number" v-model.number="b.descSize" style="background:#292929;border:1px solid #292929;border-radius:5px;padding:6px;color:#fff;" placeholder="Size (px)">
                    <select v-model="b.descWeight" style="background:#292929;border:1px solid #292929;border-radius:5px;padding:6px;color:#fff;">
                        <option value="400">Normal</option><option value="500">Medium</option>
                    </select>
                </div>
            </div>
<hr class="h-px my-2 bg-[#656565] border-0 ">
            <!-- Button -->
            <div>
                <h4 style="color:#fff;font-size:15px;margin-bottom:8px;background: #656565;padding: 5px;border-radius: 7px;">Button</h4>
                <input type="text" v-model="b.buttonText" style="width:100%;background:#292929;border:1px solid #292929;border-radius:5px;padding:6px;color:#fff;margin-bottom:8px;" placeholder="Button Text">
                <input type="text" v-model="b.buttonLink" style="width:100%;background:#292929;border:1px solid #292929;border-radius:5px;padding:6px;color:#fff;margin-bottom:8px;" placeholder="Button Link (URL)">
                <div style="display:grid;grid-template-columns:60px 60px 1fr;gap:8px;">
                    <input type="color" v-model="b.buttonBgColor" class="yab-color-picker" title="Button BG" style="width: 100%;height: 100%;">
                    <input type="color" v-model="b.buttonTextColor" class="yab-color-picker" title="Button Text Color" style="width: 100%;height: 100%;">
                    <input type="number" v-model.number="b.buttonFontSize" style="background:#292929;border:1px solid #292929;border-radius:5px;padding:6px;color:#fff;" placeholder="Font Size (px)">
                </div>
            </div>
<hr class="h-px my-2 bg-[#656565] border-0 ">
            <!-- Image -->
<div>
    <h4 style="color:#fff;font-size:15px;margin-bottom:8px;background: #656565;padding: 5px;border-radius: 7px;background: #656565;padding: 5px;border-radius: 7px;">Image</h4>

    <!-- ردیف اول: دکمه انتخاب تصویر + نوع فیت -->
    <div style="display:flex;gap:8px;align-items:center;" >
        <!-- انتخاب تصویر -->
        <button @click="openMediaUploader(key)" 
            style="flex:1;background:#007BFF;color:#fff;padding:6px;border:none;border-radius:5px;cursor:pointer;white-space:nowrap;">
            {{ b.imageUrl ? 'Change' : 'Select' }}
        </button>

        <!-- نوع فیت -->
        <select v-if="b.imageUrl" v-model="b.imageFit" 
            style="flex:0.8;background:#292929;border:1px solid #292929;border-radius:5px;padding:6px;color:#fff;">
            <option value="cover">Cover</option>
            <option value="contain">Contain</option>
            <option value="fill">Fill</option>
        </select>
    </div>

    <!-- ردیف دوم: عنوان و سوییچ -->
    <div v-if="b.imageUrl" style="display:flex;align-items:center;justify-content:space-between;margin-top:8px;background:#656565;border-radius:5px;padding:6px;" >
        <span style="font-size:13px;color:#fff;">Enable Custom Position</span>
        <div style="position:relative;cursor:pointer;" @click="b.enableCustomPosition = !b.enableCustomPosition">
            <div :style="{
                width: '40px',
                height: '20px',
                background: b.enableCustomPosition ? '#00baa4' : '#666',
                borderRadius: '20px',
                position: 'relative',
                transition: 'background 0.3s'
            }">
                <div :style="{
                    width: '16px',
                    height: '16px',
                    background: '#fff',
                    borderRadius: '50%',
                    position: 'absolute',
                    top: '2px',
                    left: b.enableCustomPosition ? '22px' : '2px',
                    transition: 'left 0.3s'
                }"></div>
            </div>
        </div>
    </div>

    <!-- پوزیشن دستی -->
    <div v-if="b.imageUrl && b.enableCustomPosition" style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:8px;" >
        <input type="number" v-model.number="b.imagePosX" 
            style="background:#292929;border:1px solid #292929;border-radius:5px;padding:6px;color:#fff;" placeholder="X (px)">
        <input type="number" v-model.number="b.imagePosY" 
            style="background:#292929;border:1px solid #292929;border-radius:5px;padding:6px;color:#fff;" placeholder="Y (px)">
    </div>
    <div :style="{ marginBottom: key === 'left' ? '120px' : '0px' }"></div>
</div>


        </div>
    </div>
</div>


            <div class="col-span-8 sticky top-[80px] space-y-3">
                <div>
                    
                    <div class="bg-[#434343] p-4 rounded-lg ">
                        <h3 class="font-bold text-lg mb-4 text-white text-left bg-[#656565] p-[10px] rounded-[7px] "> Live Preview</h3>
                        <div class="flex flex-row gap-1 justify-between flex-wrap">
                            <div v-for="(b, key) in { left: banner.left, right: banner.right }" :key="`preview-${key}`" class="min-w-[432px] max-w-[432px] h-[177px] rounded-lg relative overflow-hidden flex " :style="{ background: bannerStyles(b), minHeight: b.imageSize + 'px' }">
                                <div v-if="b.alignment === 'right'" class="w-1/2 relative">
                                    <img v-if="b.imageUrl" :src="b.imageUrl" class="absolute w-full h-full" :style="imageStyleObject(b)"/>
                                </div>
                                <div class="w-full px-[30px] py-[37px] flex flex-col z-10" :class="b.alignment === 'left' ? 'items-start text-left' : 'items-end text-right'">
                                    <h4 class="font-bold " :style="{ color: b.titleColor, fontSize: b.titleSize + 'px', fontWeight: b.titleWeight }" :class="b.alignment === 'left' ? 'text-right rtl' : 'text-left ltr'">{{ b.titleText }}</h4>
                                    <p class="mt-[8px] leading-[12px]" :style="{ color: b.descColor, fontSize: b.descSize + 'px', fontWeight: b.descWeight }" :class="b.alignment === 'right' ? 'text-left ltr' : 'text-right rtl'">{{ b.descText }}</p>
                                    <a :href="b.buttonLink" target="_blank" class=" min-w-[88px] w-auto py-[7px] px-[13px] rounded-[4px] mt-[25px] text-center" :style="{ backgroundColor: b.buttonBgColor, color: b.buttonTextColor, fontSize: b.buttonFontSize + 'px' }">{{ b.buttonText }}</a>
                                </div>
                                <div v-if="b.alignment === 'left'" class="w-1/2 relative">
                                    <img v-if="b.imageUrl" :src="b.imageUrl" class="absolute w-full h-full" :style="imageStyleObject(b)"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="banner.displayMethod === 'Fixed'" class="bg-[#434343] p-4 rounded-lg">
                     <h3 class="font-bold text-lg mb-4 text-white text-left bg-[#656565] p-[10px] rounded-[7px]">Display Conditions</h3>
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
  <!-- Show on Posts -->
<div>
  <label class="block text-sm font-medium text-gray-300 mb-2 bg-[#292929] p-2 rounded-lg shadow-md text-left">
    Show on Posts
  </label>

  <div class="h-48 overflow-y-auto p-3 bg-[#292929] rounded-lg shadow-inner space-y-2">
    <label
      v-for="post in siteData.posts"
      :key="`post-${post.ID}`"
      class="flex items-center group hover:bg-[#333] transition-colors rounded-lg cursor-pointer gap-2 p-1"
    >
      <input
        type="checkbox"
        :value="post.ID"
        v-model="banner.displayOn.posts"
        class="!hidden peer"
      />

      <span
        class="w-5 h-5 flex items-center justify-center border-2 border-gray-500 rounded-md peer-checked:bg-[#00baa4] peer-checked:border-[#00baa4] transition-all duration-200"
      >
        <svg
          v-if="banner.displayOn.posts.includes(post.ID)"
          class="w-3 h-3 text-white"
          fill="none"
          stroke="currentColor"
          stroke-width="3"
          viewBox="0 0 24 24"
        >
          <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
        </svg>
      </span>

      <span class="ml-3 text-gray-300 truncate group-hover:text-white transition-colors">
        {{ post.post_title }}
      </span>
    </label>

    <hr class="h-px my-8 bg-gray-500 border-0">
  </div>
</div>


  <!-- Show on Categories -->
<div>
  <label class="block text-sm font-medium text-gray-300 mb-2 bg-[#292929] p-2 rounded-lg shadow-md text-left">
    Show on Categories
  </label>

  <div class="h-48 overflow-y-auto p-3 bg-[#292929] rounded-lg shadow-inner space-y-2">
    <label
      v-for="cat in siteData.categories"
      :key="`cat-${cat.term_id}`"
      class="flex items-center group hover:bg-[#333] transition-colors rounded-lg gap-2 p-1 cursor-pointer"
    >
      <input
        type="checkbox"
        :value="cat.term_id"
        v-model="banner.displayOn.categories"
        class="!hidden peer"
      />

      <span
        class="w-5 h-5 flex items-center justify-center border-2 border-gray-500 rounded-md peer-checked:bg-[#00baa4] peer-checked:border-[#00baa4] transition-all duration-200"
      >
        <svg
          v-if="banner.displayOn.categories.includes(cat.term_id)"
          class="w-3 h-3 text-white"
          fill="none"
          stroke="currentColor"
          stroke-width="3"
          viewBox="0 0 24 24"
        >
          <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
        </svg>
      </span>

      <span class="ml-3 text-gray-300 truncate group-hover:text-white transition-colors">
        {{ cat.name }}
      </span>
    </label>
    <hr class="h-px my-8 bg-gray-500 border-0">
  </div>
</div>


  <!-- Show on Pages -->
<div>
  <label class="block text-sm font-medium text-gray-300 mb-2 bg-[#292929] p-2 rounded-lg shadow-md text-left">
    Show on Pages
  </label>

  <div class="h-48 overflow-y-auto p-3 bg-[#292929] rounded-lg shadow-inner space-y-2">
    <label
      v-for="page in siteData.pages"
      :key="`page-${page.ID}`"
      class="flex items-center group hover:bg-[#333] transition-colors rounded-lg gap-2 p-1 cursor-pointer"
    >
      <input
        type="checkbox"
        :value="page.ID"
        v-model="banner.displayOn.pages"
        class="!hidden peer"
      />

      <span
        class="w-5 h-5 flex items-center justify-center border-2 border-gray-500 rounded-md peer-checked:bg-[#00baa4] peer-checked:border-[#00baa4] transition-all duration-200"
      >
        <svg
          v-if="banner.displayOn.pages.includes(page.ID)"
          class="w-3 h-3 text-white"
          fill="none"
          stroke="currentColor"
          stroke-width="3"
          viewBox="0 0 24 24"
        >
          <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
        </svg>
      </span>

      <span class="ml-3 text-gray-300 truncate group-hover:text-white transition-colors">
        {{ page.post_title }}
      </span>
    </label>
    <hr class="h-px my-8 bg-gray-500 border-0">
  </div>
</div>


            </div>
        </main>
    </div>

    <div v-if="appState === 'loading'" class="flex items-center justify-center h-screen">
        <span class="dashicons dashicons-update animate-spin text-5xl text-[#00baa4]"></span>
    </div>
</div>

<style>
    [v-cloak] { display: none; } #wpcontent { padding-left: 0; }
    .yab-color-picker { -webkit-appearance: none; -moz-appearance: none; appearance: none; width: 40px; height: 30px; background-color: transparent; border: none; cursor: pointer; }
    .yab-color-picker::-webkit-color-swatch { border-radius: 5px; border: 1px solid #4a5568; }
    .yab-color-picker::-moz-color-swatch { border-radius: 5px; border: 1px solid #4a5568; }
</style>