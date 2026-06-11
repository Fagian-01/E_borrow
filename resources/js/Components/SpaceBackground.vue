<script setup>
/**
 * SpaceBackground.vue
 * Animated space-themed background for dark mode.
 * Renders twinkling stars and occasional shooting stars.
 */
import { ref, onMounted } from 'vue';

const stars = ref([]);
const shootingStars = ref([]);

onMounted(() => {
    // Generate random stars
    for (let i = 0; i < 60; i++) {
        stars.value.push({
            id: i,
            left: `${Math.random() * 100}%`,
            top: `${Math.random() * 100}%`,
            size: `${Math.random() * 2 + 1}px`,
            delay: `${Math.random() * 5}s`,
            duration: `${Math.random() * 3 + 2}s`,
            opacity: Math.random() * 0.5 + 0.2,
        });
    }

    // Generate shooting stars
    for (let i = 0; i < 3; i++) {
        shootingStars.value.push({
            id: i,
            left: `${Math.random() * 70}%`,
            top: `${Math.random() * 40}%`,
            delay: `${i * 4 + Math.random() * 3}s`,
            duration: `${Math.random() * 1.5 + 1.5}s`,
        });
    }
});
</script>

<template>
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0 hidden dark:block">
        <!-- Gradient overlay -->
        <div class="absolute inset-0 bg-space-gradient opacity-60"></div>

        <!-- Stars -->
        <div
            v-for="star in stars"
            :key="'star-' + star.id"
            class="absolute rounded-full bg-white"
            :style="{
                left: star.left,
                top: star.top,
                width: star.size,
                height: star.size,
                opacity: star.opacity,
                animationDelay: star.delay,
                animationDuration: star.duration,
            }"
            style="animation: twinkle ease-in-out infinite"
        ></div>

        <!-- Shooting Stars -->
        <div
            v-for="ss in shootingStars"
            :key="'ss-' + ss.id"
            class="absolute w-1 h-1 bg-gold-300 rounded-full"
            :style="{
                left: ss.left,
                top: ss.top,
                animationDelay: ss.delay,
                animationDuration: ss.duration,
            }"
            style="animation: shootingStar ease-in-out infinite"
        >
            <div class="absolute inset-0 w-8 h-px bg-gradient-to-r from-gold-300 to-transparent -rotate-45 origin-left"></div>
        </div>
    </div>
</template>
