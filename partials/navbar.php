<header class="header flex justify-between items-center px-4 bg-white shadow-md sticky top-0 z-40 h-16 md:h-20">
    <div class="flex items-center justify-between w-full md:hidden gap-2">
        <a :href="basePath + '/'" @click.prevent="setView('home')" class="logo flex-shrink-0">
            <?php if (!empty($site_logo_path) && file_exists($site_logo_path)): ?>
                <img src="<?= htmlspecialchars(BASE_PATH . '/' . $site_logo_path) ?>" alt="Submonth Logo" class="h-8">
            <?php else: ?>
                <img src="https://i.postimg.cc/gJRL0cdG/1758261543098.png" alt="Submonth Logo" class="h-8">
            <?php endif; ?>
        </a>
        <form @submit.prevent="performSearch" class="relative flex-1 min-w-0">
             <input type="text" v-model.lazy="searchQuery" placeholder="Search..." class="w-full py-2 pl-3 pr-10 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-gray-400 h-9 text-sm" aria-label="Search mobile">
            <div class="absolute top-2 bottom-2 right-8 w-px bg-gray-300"></div>
            <button type="submit" class="absolute right-2 top-1/2 transform -translate-y-1/2" aria-label="Submit search mobile">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"></path>
                </svg>
            </button>
        </form>
        <div class="flex items-center gap-3">
            <button @click="toggleCurrency()" class="icon text-gray-600 hover:text-[var(--primary-color)] cursor-pointer flex items-center gap-1 font-semibold">
                <i class="fas fa-dollar-sign text-xl"></i>
                <span class="text-sm">{{ currency }}</span>
            </button>
             <a :href="basePath + '/cart'" @click.prevent="setView('cart')" class="icon text-2xl text-gray-600 hover:text-[var(--primary-color)] cursor-pointer relative" aria-label="Shopping Cart">
                <i class="fas fa-shopping-bag relative -top-0.5"></i>
                <span v-show="cartCount > 0" class="notification-badge">{{ cartCount }}</span>
            </a>
            <button @click="isSideMenuOpen = !isSideMenuOpen" class="icon text-2xl text-gray-600 hover:text-[var(--primary-color)] cursor-pointer" aria-label="Open menu"><i class="fas fa-bars"></i></button>
        </div>
    </div>

    <div class="hidden md:flex items-center w-full gap-5">
        <a :href="basePath + '/'" @click.prevent="setView('home')" class="logo flex-shrink-0 flex items-center text-gray-800 no-underline">
             <?php if (!empty($site_logo_path) && file_exists($site_logo_path)): ?>
                <img src="<?= htmlspecialchars(BASE_PATH . '/' . $site_logo_path) ?>" alt="Submonth Logo" class="h-9">
            <?php else: ?>
                <img src="https://i.postimg.cc/gJRL0cdG/1758261543098.png" alt="Submonth Logo" class="h-9">
            <?php endif; ?>
        </a>
        <form @submit.prevent="performSearch" class="relative flex-1">
            <input type="text" v-model.lazy="searchQuery" placeholder="Search for premium subscriptions, courses, and more..." class="w-full py-2.5 px-4 pr-12 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-gray-500 focus:border-gray-500 transition-colors text-gray-900 placeholder-gray-400" aria-label="Search">
            <div class="absolute top-2.5 bottom-2.5 right-10 w-px bg-gray-300"></div>
            <button type="submit" class="absolute right-3 top-1/2 transform -translate-y-1/2" aria-label="Submit search">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 24 24" fill="currentColor">
                <path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
              </svg>
            </button>
        </form>
        <div class="flex-shrink-0 flex items-center gap-5">
            <button @click="toggleCurrency()" class="icon text-xl text-gray-600 hover:text-[var(--primary-color)] cursor-pointer flex items-center gap-2 font-semibold">
                <i class="fas fa-dollar-sign text-2xl pt-px"></i>
                <span class="pt-px">{{ currency }}</span>
            </button>
            <a :href="basePath + '/products'" @click.prevent="setView('products')" class="icon text-2xl text-gray-600 hover:text-[var(--primary-color)] cursor-pointer" aria-label="All Products"><i class="fas fa-box-open"></i></a>
            <a :href="basePath + '/cart'" @click.prevent="setView('cart')" class="icon text-2xl text-gray-600 hover:text-[var(--primary-color)] cursor-pointer relative" aria-label="Shopping Cart">
                <i class="fas fa-shopping-bag relative -top-0.5"></i>
                <span v-show="cartCount > 0" class="notification-badge">{{ cartCount }}</span>
            </a>
            <a :href="basePath + '/order-history'" @click.prevent="setView('orderHistory')" class="icon text-2xl text-gray-600 hover:text-[var(--primary-color)] cursor-pointer relative" aria-label="Order History">
                <i class="fas fa-receipt relative -top-0.5"></i>
                <span v-show="newNotificationCount > 0" class="notification-badge">{{ newNotificationCount }}</span>
            </a>
            <button @click="isSideMenuOpen = !isSideMenuOpen" class="icon text-2xl text-gray-600 hover:text-[var(--primary-color)] cursor-pointer" aria-label="Open menu"><i class="fas fa-bars"></i></button>
        </div>
    </div>
</header>