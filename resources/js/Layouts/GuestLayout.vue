<script setup>
import AppLogo from '@/Components/AppLogo.vue';
import SpaceBackground from '@/Components/SpaceBackground.vue';
import DarkModeToggle from '@/Components/DarkModeToggle.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen flex flex-col items-center justify-center bg-gray-50 dark:bg-cosmic-950 px-4 py-8 relative transition-colors duration-300">
        <!-- Space Background (dark mode) -->
        <SpaceBackground />

        <!-- Dark Mode Toggle (top right) -->
        <div class="absolute top-4 right-4 z-20">
            <DarkModeToggle />
        </div>

        <!-- Logo -->
        <div class="relative z-10 mb-6">
            <Link href="/">
                <AppLogo title="E-Borrow" subtitle="Inventory System" />
            </Link>
        </div>

        <!-- Form Card -->
        <div class="relative z-10 w-full max-w-md">
            <div class="card-glass p-8 rounded-2xl shadow-card dark:shadow-card-dark">
                <slot />
            </div>
        </div>

        <!-- Footer -->
        <p class="relative z-10 mt-6 text-xs text-gray-400 dark:text-gray-600">
            &copy; {{ new Date().getFullYear() }} E-Borrow. Enterprise Inventory System.
        </p>
    </div>
</template>
