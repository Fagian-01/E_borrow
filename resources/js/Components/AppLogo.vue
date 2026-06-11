<script setup>
const props = defineProps({
    title: { type: String, default: 'E-Borrow' },
    subtitle: { type: String, default: '' },
});
</script>

<template>
    <div class="flex items-center gap-3">
        <!-- Logo Icon -->
        <div class="relative w-10 h-10 rounded-xl bg-gradient-to-br from-sapphire-500 to-sapphire-700
                    dark:from-gold-500 dark:to-gold-700
                    flex items-center justify-center shadow-lg
                    transition-all duration-300 hover:scale-105">
            <span class="text-white text-lg font-bold font-display">E</span>
            <div class="absolute -top-0.5 -right-0.5 w-2.5 h-2.5 bg-gold-400 rounded-full animate-pulse-gold"></div>
        </div>
        <!-- Text -->
        <div class="flex flex-col">
            <span class="text-lg font-display font-bold text-gray-800 dark:text-white leading-tight">
                {{ title }}
            </span>
            <span v-if="subtitle" class="text-[10px] text-gray-400 dark:text-gray-500 uppercase tracking-widest">
                {{ subtitle }}
            </span>
        </div>
    </div>
</template>
