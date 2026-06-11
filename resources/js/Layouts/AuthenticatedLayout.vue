<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import AppLogo from '@/Components/AppLogo.vue';
import DarkModeToggle from '@/Components/DarkModeToggle.vue';
import ToastNotification from '@/Components/ToastNotification.vue';
import SpaceBackground from '@/Components/SpaceBackground.vue';

const page = usePage();
const user = computed(() => page.props.auth?.user);
const isAdmin = computed(() => user.value?.is_admin);
const cartCount = computed(() => page.props.cartCount ?? 0);

const sidebarOpen = ref(true);
const mobileMenuOpen = ref(false);
const showUserDropdown = ref(false);
const screenWidth = ref(window.innerWidth);

// Responsive sidebar
const handleResize = () => {
    screenWidth.value = window.innerWidth;
    if (screenWidth.value < 1024) {
        sidebarOpen.value = false;
    } else {
        sidebarOpen.value = true;
        mobileMenuOpen.value = false;
    }
};

onMounted(() => {
    handleResize();
    window.addEventListener('resize', handleResize);
});

onUnmounted(() => {
    window.removeEventListener('resize', handleResize);
});

// Navigation items based on role
const adminNav = computed(() => {
    const items = [
        { name: 'Dashboard', route: 'admin.dashboard', icon: '📊' },
        { name: 'Kategori', route: 'admin.categories.index', icon: '📁' },
        { name: 'Inventaris', route: 'admin.items.index', icon: '📦' },
        { name: 'Peminjaman', route: 'admin.borrowings.index', icon: '📋' },
        { name: 'Laporan', route: 'admin.reports.index', icon: '📈' },
        { name: 'Log Aktivitas', route: 'admin.activity-logs.index', icon: '📜' },
    ];
    // Only superadmin can manage users
    if (page.props.auth.user?.is_super_admin) {
        items.push({ name: 'Pengguna', route: 'admin.users.index', icon: '👥' });
    }
    return items;
});

const userNav = [
    { name: 'Dashboard', route: 'user.dashboard', icon: '🏠' },
    { name: 'Katalog', route: 'user.catalog.index', icon: '📦' },
    { name: 'Keranjang', route: 'user.cart.index', icon: '🛒' },
    { name: 'Peminjaman', route: 'user.borrowings.index', icon: '📋' },
];

const navItems = computed(() => isAdmin.value ? adminNav.value : userNav);

const isActive = (routeName) => {
    try {
        // Support wildcard matching: admin.categories.index -> admin.categories.*
        const prefix = routeName.replace(/\.\w+$/, '.*');
        return route().current(routeName) || route().current(prefix);
    } catch {
        return false;
    }
};

const isLoggingOut = ref(false);

const logout = () => {
    isLoggingOut.value = true;
    router.post(route('logout'), {}, {
        onFinish: () => {
            // Unset in case of error, though normally it redirects
            isLoggingOut.value = false;
        }
    });
};
</script>

<template>
    <div class="min-h-screen bg-gray-50 dark:bg-cosmic-950 transition-colors duration-300">
        <!-- Space Background (dark mode only) -->
        <SpaceBackground />

        <!-- Mobile Overlay -->
        <Transition
            enter-active-class="transition-opacity duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-300"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="mobileMenuOpen"
                class="fixed inset-0 z-30 bg-black/50 backdrop-blur-sm lg:hidden"
                @click="mobileMenuOpen = false"
            ></div>
        </Transition>

        <!-- Sidebar -->
        <aside
            :class="[
                'sidebar w-64',
                mobileMenuOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
                !sidebarOpen && 'lg:w-20',
            ]"
        >
            <div class="flex flex-col h-full">
                <!-- Logo -->
                <div class="flex items-center justify-between px-4 h-16 border-b border-gray-200 dark:border-cosmic-700/50">
                    <Link :href="route('dashboard')" class="flex items-center">
                        <AppLogo :title="sidebarOpen ? 'E-Borrow' : ''" subtitle="Inventory System" />
                    </Link>
                    <button
                        v-if="screenWidth >= 1024"
                        @click="sidebarOpen = !sidebarOpen"
                        class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 dark:hover:bg-cosmic-700/50 transition-colors"
                    >
                        <svg class="w-5 h-5 transition-transform" :class="!sidebarOpen && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                        </svg>
                    </button>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                    <Link
                        v-for="item in navItems"
                        :key="item.name"
                        :href="route(item.route)"
                        :class="[
                            'sidebar-link',
                            isActive(item.route) && 'sidebar-link-active',
                        ]"
                    >
                        <span class="text-lg flex-shrink-0">{{ item.icon }}</span>
                        <span v-if="sidebarOpen" class="truncate">{{ item.name }}</span>
                    </Link>
                </nav>

                <!-- User section at bottom -->
                <div class="p-3 border-t border-gray-200 dark:border-cosmic-700/50">
                    <div v-if="sidebarOpen" class="flex items-center gap-3 px-3 py-2 rounded-xl bg-gray-50 dark:bg-cosmic-800/50">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-sapphire-500 to-sapphire-700 flex items-center justify-center text-white text-xs font-bold">
                            {{ user?.name?.charAt(0)?.toUpperCase() }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate">{{ user?.name }}</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 truncate">{{ user?.role }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div :class="['transition-all duration-300', sidebarOpen ? 'lg:ml-64' : 'lg:ml-20']">
            <!-- Top Navbar -->
            <header class="sticky top-0 z-20 bg-white/80 dark:bg-cosmic-900/80 backdrop-blur-xl border-b border-gray-200/50 dark:border-cosmic-700/30">
                <div class="flex items-center justify-between h-16 px-4 sm:px-6">
                    <!-- Left: Mobile menu + Page title -->
                    <div class="flex items-center gap-3">
                        <button
                            @click="mobileMenuOpen = !mobileMenuOpen"
                            class="lg:hidden p-2 rounded-xl text-gray-500 hover:bg-gray-100 dark:hover:bg-cosmic-700/50 transition-colors"
                        >
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        <slot name="header" />
                    </div>

                    <!-- Right: Actions -->
                    <div class="flex items-center gap-2">
                        <!-- Cart (user only) -->
                        <button
                            v-if="!isAdmin"
                            class="relative p-2.5 rounded-xl text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-cosmic-700/50 transition-colors"
                        >
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z" />
                            </svg>
                            <span
                                v-if="cartCount > 0"
                                class="absolute -top-0.5 -right-0.5 w-5 h-5 rounded-full bg-gold-500 text-white text-[10px] font-bold flex items-center justify-center animate-pulse-gold"
                            >
                                {{ cartCount }}
                            </span>
                        </button>

                        <!-- Notifications -->
                        <button class="relative p-2.5 rounded-xl text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-cosmic-700/50 transition-colors">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </button>

                        <!-- Dark Mode Toggle -->
                        <DarkModeToggle />

                        <!-- User Dropdown -->
                        <div class="relative">
                            <button
                                @click="showUserDropdown = !showUserDropdown"
                                class="flex items-center gap-2 p-1.5 rounded-xl hover:bg-gray-100 dark:hover:bg-cosmic-700/50 transition-colors"
                            >
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-sapphire-500 to-sapphire-700 dark:from-gold-500 dark:to-gold-700 flex items-center justify-center text-white text-xs font-bold">
                                    {{ user?.name?.charAt(0)?.toUpperCase() }}
                                </div>
                                <div class="hidden sm:block text-left">
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ user?.name }}</p>
                                </div>
                                <svg class="w-4 h-4 text-gray-400 hidden sm:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <Transition
                                enter-active-class="transition duration-200 ease-out"
                                enter-from-class="opacity-0 scale-95 -translate-y-1"
                                enter-to-class="opacity-100 scale-100 translate-y-0"
                                leave-active-class="transition duration-150 ease-in"
                                leave-from-class="opacity-100 scale-100"
                                leave-to-class="opacity-0 scale-95"
                            >
                                <div
                                    v-if="showUserDropdown"
                                    @click="showUserDropdown = false"
                                    class="absolute right-0 mt-2 w-56 rounded-xl bg-white dark:bg-cosmic-800 border border-gray-200 dark:border-cosmic-700/50 shadow-xl overflow-hidden z-50"
                                >
                                    <div class="px-4 py-3 border-b border-gray-100 dark:border-cosmic-700/30">
                                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ user?.name }}</p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500">{{ user?.email }}</p>
                                        <span class="badge badge-gold mt-1.5 text-[10px]">{{ user?.role }}</span>
                                    </div>
                                    <div class="py-1">
                                        <Link
                                            :href="route('profile.edit')"
                                            class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-cosmic-700/30 transition-colors"
                                        >
                                            <span>👤</span> Profil Saya
                                        </Link>
                                        <button
                                            @click="logout"
                                            class="w-full flex items-center gap-2 px-4 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
                                        >
                                            <span>🚪</span> Keluar
                                        </button>
                                    </div>
                                </div>
                            </Transition>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="relative z-10">
                <div class="page-enter">
                    <slot />
                </div>
            </main>
        </div>

            <!-- Click-away for dropdown -->
        <div
            v-if="showUserDropdown"
            class="fixed inset-0 z-40"
            @click="showUserDropdown = false"
        ></div>

        <!-- Logout Loading Overlay -->
        <Transition name="fade">
            <div v-if="isLoggingOut" class="fixed inset-0 z-[9999] flex flex-col items-center justify-center bg-white/80 dark:bg-cosmic-950/80 backdrop-blur-sm">
                <svg class="animate-spin w-12 h-12 text-sapphire-600 dark:text-sapphire-500 mb-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-lg font-display font-medium text-gray-800 dark:text-gray-100 animate-pulse">Sedang keluar...</p>
            </div>
        </Transition>

        <!-- Toast Notifications -->
        <ToastNotification />
    </div>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
