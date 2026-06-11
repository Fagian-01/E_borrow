<script setup>
import { ref, onMounted, watch } from 'vue';

const isDark = ref(false);

onMounted(() => {
    isDark.value = document.documentElement.classList.contains('dark');
});

const toggle = () => {
    isDark.value = !isDark.value;
    document.documentElement.classList.toggle('dark', isDark.value);
    localStorage.setItem('darkMode', isDark.value.toString());
};
</script>

<template>
    <button
        @click="toggle"
        class="relative inline-flex items-center justify-center w-10 h-10 rounded-xl
               text-gray-500 dark:text-gray-400
               hover:bg-gray-100 dark:hover:bg-cosmic-700/50
               transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-gold-400/30"
        :title="isDark ? 'Switch to Light Mode' : 'Switch to Dark Mode'"
    >
        <!-- Sun Icon (shown in dark mode) -->
        <svg
            v-if="isDark"
            class="w-5 h-5 text-gold-400 transition-transform duration-300 rotate-0 hover:rotate-45"
            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
        >
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>

        <!-- Moon Icon (shown in light mode) -->
        <svg
            v-else
            class="w-5 h-5 text-sapphire-600 transition-transform duration-300"
            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
        >
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
        </svg>
    </button>
</template>
