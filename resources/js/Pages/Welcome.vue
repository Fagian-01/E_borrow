<script setup>
import SpaceBackground from '@/Components/SpaceBackground.vue';
import AppLogo from '@/Components/AppLogo.vue';
import DarkModeToggle from '@/Components/DarkModeToggle.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    appName: String,
});
</script>

<template>
    <Head title="Enterprise Inventory System" />

    <div class="min-h-screen bg-gray-50 dark:bg-cosmic-950 transition-colors duration-300 relative overflow-hidden">
        <!-- Space Background -->
        <SpaceBackground />

        <!-- Navbar -->
        <nav class="relative z-20 flex items-center justify-between px-6 lg:px-12 py-4">
            <AppLogo title="E-Borrow" subtitle="Inventory System" />

            <div class="flex items-center gap-3">
                <DarkModeToggle />
                <Link
                    v-if="canLogin"
                    :href="route('login')"
                    class="btn-outline btn-sm"
                >
                    Login
                </Link>
                <Link
                    v-if="canRegister"
                    :href="route('register')"
                    class="btn-primary btn-sm"
                >
                    Daftar
                </Link>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="relative z-10 flex flex-col items-center justify-center text-center px-6 pt-12 pb-20 lg:pt-20 lg:pb-32">
            <!-- Floating badge -->
            <div class="badge badge-gold mb-6 animate-float">
                ✨ Enterprise-Grade Inventory System
            </div>

            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-display font-bold text-gray-900 dark:text-white leading-tight max-w-4xl">
                Kelola Peminjaman
                <span class="text-gradient-sapphire dark:text-gradient-gold">Inventaris</span>
                dengan Mudah
            </h1>

            <p class="mt-6 text-lg text-gray-500 dark:text-gray-400 max-w-2xl leading-relaxed">
                Sistem peminjaman peralatan kantor yang aman, cepat, dan terintegrasi.
                Dilengkapi QR Scanner, approval workflow, dan tracking real-time.
            </p>

            <div class="flex flex-col sm:flex-row items-center gap-4 mt-10">
                <Link
                    v-if="canRegister"
                    :href="route('register')"
                    class="btn-gold btn-lg group"
                >
                    Mulai Sekarang
                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </Link>
                <Link
                    v-if="canLogin"
                    :href="route('login')"
                    class="btn-outline btn-lg"
                >
                    Sudah punya akun? Login
                </Link>
            </div>
        </section>

        <!-- Features Section -->
        <section class="relative z-10 px-6 lg:px-12 pb-20">
            <div class="max-w-6xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="card card-hover p-7 text-center">
                        <div class="w-14 h-14 rounded-2xl bg-sapphire-100 dark:bg-sapphire-900/40 flex items-center justify-center text-2xl mx-auto mb-4">
                            🔒
                        </div>
                        <h3 class="font-display font-bold text-lg text-gray-800 dark:text-gray-100 mb-2">
                            Keamanan Berlapis
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed">
                            Role-based access control, audit trail lengkap, dan proteksi race condition dengan pessimistic locking.
                        </p>
                    </div>

                    <div class="card card-hover p-7 text-center">
                        <div class="w-14 h-14 rounded-2xl bg-gold-100 dark:bg-gold-900/40 flex items-center justify-center text-2xl mx-auto mb-4">
                            📱
                        </div>
                        <h3 class="font-display font-bold text-lg text-gray-800 dark:text-gray-100 mb-2">
                            QR Code Scanner
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed">
                            Scan barcode atau QR code untuk checkout cepat. Setiap aset memiliki identitas unik yang terlacak.
                        </p>
                    </div>

                    <div class="card card-hover p-7 text-center">
                        <div class="w-14 h-14 rounded-2xl bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center text-2xl mx-auto mb-4">
                            📊
                        </div>
                        <h3 class="font-display font-bold text-lg text-gray-800 dark:text-gray-100 mb-2">
                            Laporan Otomatis
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed">
                            Dashboard real-time, perhitungan denda otomatis, dan notifikasi tenggat pengembalian.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="relative z-10 text-center py-8 border-t border-gray-200/50 dark:border-cosmic-700/30">
            <p class="text-sm text-gray-400 dark:text-gray-600">
                &copy; {{ new Date().getFullYear() }} E-Borrow — Rashqa Andrean Fitrah Sulaeman.
            </p>
        </footer>
    </div>
</template>
