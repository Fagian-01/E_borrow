<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({ borrowings: Object, filters: Object });

const formatDate = (dateStr) => {
    if (!dateStr) return '-';
    try { return new Date(dateStr).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }); } catch { return dateStr; }
};

const statusFilter = ref(props.filters?.status ?? '');
watch(statusFilter, (val) => {
    router.get(route('user.borrowings.index'), val ? { status: val } : {}, { preserveState: true, replace: true });
});

const cancelTarget = ref(null);
const cancelBorrowing = (b) => {
    cancelTarget.value = b;
};
const doCancel = () => {
    if (cancelTarget.value) {
        router.post(route('user.borrowings.cancel', cancelTarget.value.id), {}, {
            preserveScroll: true,
            onFinish: () => { cancelTarget.value = null; },
        });
    }
};

const statusTabs = [
    { value: '', label: 'Semua' },
    { value: 'pending', label: '⏳ Pending' },
    { value: 'approved', label: '✅ Disetujui' },
    { value: 'active', label: '📋 Aktif' },
    { value: 'returned', label: '✔️ Selesai' },
    { value: 'rejected', label: '❌ Ditolak' },
    { value: 'cancelled', label: '🚫 Dibatalkan' },
];
</script>

<template>
    <Head title="Peminjaman Saya" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-display font-bold text-xl text-gray-800 dark:text-gray-100">Peminjaman Saya</h2>
        </template>

        <div class="py-6">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                <!-- Status Tabs -->
                <div class="flex flex-wrap gap-2">
                    <button
                        v-for="tab in statusTabs" :key="tab.value"
                        @click="statusFilter = tab.value"
                        :class="['px-4 py-2 text-sm rounded-xl font-medium transition-all', statusFilter === tab.value ? 'bg-sapphire-600 text-white shadow-glow-sapphire' : 'bg-white dark:bg-cosmic-800/60 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-cosmic-700/50 border border-gray-200 dark:border-cosmic-700/50']"
                    >
                        {{ tab.label }}
                    </button>
                </div>

                <!-- Borrowing Cards -->
                <div v-for="b in borrowings.data" :key="b.id" class="card p-5 space-y-3">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="font-mono text-sm font-semibold text-sapphire-600 dark:text-sapphire-400">{{ b.transaction_code }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ formatDate(b.borrow_date) }} &mdash; {{ formatDate(b.expected_return_date) }}</p>
                        </div>
                        <span :class="['badge', `badge-${b.status_color}`]">{{ b.status_label }}</span>
                    </div>

                    <!-- Items list -->
                    <div class="flex flex-wrap gap-2">
                        <div v-for="d in b.details" :key="d.id" class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-gray-50 dark:bg-cosmic-800/40 text-sm">
                            <span class="text-gray-500">{{ d.item?.name }}</span>
                            <span class="badge badge-gray text-[10px]">×{{ d.quantity }}</span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-2 pt-2 border-t border-gray-100 dark:border-cosmic-700/30">
                        <Link :href="route('user.borrowings.show', b.id)" class="btn-ghost btn-sm">Detail</Link>
                        <button v-if="b.status === 'pending'" @click="cancelBorrowing(b)" class="btn-ghost btn-sm text-red-500">Batalkan</button>
                    </div>
                </div>

                <!-- Empty -->
                <div v-if="!borrowings.data?.length" class="card p-12 text-center">
                    <div class="text-5xl mb-3">📋</div>
                    <p class="text-gray-500 dark:text-gray-400 text-lg">Belum ada peminjaman</p>
                    <Link :href="route('user.catalog.index')" class="btn-primary mt-4">Mulai Meminjam</Link>
                </div>

                <!-- Pagination -->
                <div v-if="borrowings.last_page > 1" class="flex justify-center gap-1">
                    <Link v-for="link in borrowings.links" :key="link.label" :href="link.url" v-html="link.label" :class="['px-3 py-2 text-sm rounded-lg transition-colors', link.active ? 'bg-sapphire-600 text-white' : link.url ? 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-cosmic-700/50' : 'text-gray-300 cursor-not-allowed']" :preserve-state="true" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>

    <!-- Cancel Confirm Modal -->
    <Teleport to="body">
        <Transition name="fade">
            <div v-if="cancelTarget" class="fixed inset-0 z-[999] flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="cancelTarget = null"></div>
                <div class="relative bg-white dark:bg-cosmic-800 rounded-2xl shadow-2xl p-6 max-w-md w-full border border-gray-200 dark:border-cosmic-700/50">
                    <h3 class="text-lg font-display font-bold text-gray-800 dark:text-gray-100 mb-2">Batalkan Peminjaman</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                        Batalkan peminjaman <span class="font-mono font-bold text-sapphire-600">{{ cancelTarget.transaction_code }}</span>? Stok akan dikembalikan.
                    </p>
                    <div class="flex justify-end gap-3">
                        <button @click="cancelTarget = null" class="btn-ghost">Batal</button>
                        <button @click="doCancel" class="btn-danger">Ya, Batalkan</button>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
