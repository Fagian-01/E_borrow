<script setup>
import { ref, watch, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';

const toasts = ref([]);
let toastId = 0;

const addToast = (message, type = 'success', duration = 4000) => {
    const id = ++toastId;
    toasts.value.push({ id, message, type, visible: true });

    setTimeout(() => {
        removeToast(id);
    }, duration);
};

const removeToast = (id) => {
    const index = toasts.value.findIndex(t => t.id === id);
    if (index !== -1) {
        toasts.value[index].visible = false;
        setTimeout(() => {
            toasts.value = toasts.value.filter(t => t.id !== id);
        }, 300);
    }
};

const icons = {
    success: '✓',
    error: '✕',
    warning: '⚠',
    info: 'ℹ',
};

const bgClasses = {
    success: 'bg-emerald-500/95 dark:bg-emerald-600/95',
    error: 'bg-red-500/95 dark:bg-red-600/95',
    warning: 'bg-amber-500/95 dark:bg-amber-600/95',
    info: 'bg-sapphire-500/95 dark:bg-sapphire-600/95',
};

// Watch for flash messages from Inertia
const page = usePage();

onMounted(() => {
    checkFlash();
});

watch(() => [page.props.flash?.success, page.props.flash?.error, page.props.flash?.warning, page.props.flash?.info], () => {
    checkFlash();
});

const checkFlash = () => {
    if (page.props.flash?.success) addToast(page.props.flash.success, 'success');
    if (page.props.flash?.error) addToast(page.props.flash.error, 'error');
    if (page.props.flash?.warning) addToast(page.props.flash.warning, 'warning');
    if (page.props.flash?.info) addToast(page.props.flash.info, 'info');
};

// Expose for manual use
defineExpose({ addToast });
</script>

<template>
    <Teleport to="body">
        <div class="fixed bottom-6 right-6 z-[9999] space-y-3">
            <TransitionGroup
                enter-active-class="transition-all duration-300 ease-out"
                enter-from-class="translate-y-4 opacity-0 scale-95"
                enter-to-class="translate-y-0 opacity-100 scale-100"
                leave-active-class="transition-all duration-200 ease-in"
                leave-from-class="translate-y-0 opacity-100 scale-100"
                leave-to-class="translate-x-8 opacity-0 scale-95"
            >
                <div
                    v-for="toast in toasts"
                    :key="toast.id"
                    v-show="toast.visible"
                    :class="[
                        'flex items-center gap-3 px-5 py-3.5 rounded-xl shadow-lg backdrop-blur-xl text-white text-sm font-medium min-w-[300px] max-w-[420px] cursor-pointer',
                        bgClasses[toast.type],
                    ]"
                    @click="removeToast(toast.id)"
                >
                    <span class="flex-shrink-0 w-6 h-6 rounded-full bg-white/20 flex items-center justify-center text-xs font-bold">
                        {{ icons[toast.type] }}
                    </span>
                    <span class="flex-1">{{ toast.message }}</span>
                    <button
                        @click.stop="removeToast(toast.id)"
                        class="flex-shrink-0 w-5 h-5 rounded-full hover:bg-white/20 flex items-center justify-center text-xs transition-colors"
                    >
                        ✕
                    </button>
                </div>
            </TransitionGroup>
        </div>
    </Teleport>
</template>
