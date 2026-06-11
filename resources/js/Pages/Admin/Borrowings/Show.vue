<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({ borrowing: Object });

const showRejectForm = ref(false);
const showConfirmModal = ref(false);
const confirmAction = ref(null);
const confirmTitle = ref('');
const confirmMessage = ref('');
const processing = ref(false);
const rejectForm = useForm({ rejection_reason: '' });

const formatDate = (dateStr) => {
    if (!dateStr) return '-';
    try {
        return new Date(dateStr).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
    } catch { return dateStr; }
};

const formatCurrency = (val) => {
    if (val === null || val === undefined) return 'Rp 0';
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val);
};

// Generic confirm dialog
const openConfirm = (title, message, action) => {
    confirmTitle.value = title;
    confirmMessage.value = message;
    confirmAction.value = action;
    showConfirmModal.value = true;
};

const executeConfirm = () => {
    showConfirmModal.value = false;
    if (confirmAction.value) {
        confirmAction.value();
    }
};

const approve = () => {
    openConfirm('Setujui Peminjaman', 'Apakah Anda yakin ingin menyetujui peminjaman ini?', () => {
        processing.value = true;
        router.post(route('admin.borrowings.approve', props.borrowing.id), {}, {
            preserveScroll: true,
            onFinish: () => { processing.value = false; },
        });
    });
};

const reject = () => {
    rejectForm.post(route('admin.borrowings.reject', props.borrowing.id), {
        preserveScroll: true,
        onSuccess: () => { showRejectForm.value = false; },
    });
};

const handover = () => {
    openConfirm('Serahkan Barang', 'Serahkan barang ke peminjam? Status akan berubah menjadi AKTIF.', () => {
        processing.value = true;
        router.post(route('admin.borrowings.handover', props.borrowing.id), {}, {
            preserveScroll: true,
            onFinish: () => { processing.value = false; },
        });
    });
};

const returnItems = () => {
    openConfirm('Konfirmasi Pengembalian', 'Konfirmasi semua barang telah dikembalikan?', () => {
        processing.value = true;
        router.post(route('admin.borrowings.return', props.borrowing.id), {}, {
            preserveScroll: true,
            onFinish: () => { processing.value = false; },
        });
    });
};

const steps = computed(() => [
    { label: 'Diajukan', done: true },
    { label: 'Disetujui', done: ['approved', 'active', 'partially_returned', 'returned', 'overdue'].includes(props.borrowing.status) },
    { label: 'Diserahkan', done: ['active', 'partially_returned', 'returned', 'overdue'].includes(props.borrowing.status) },
    { label: 'Dikembalikan', done: ['returned'].includes(props.borrowing.status) },
]);
</script>

<template>
    <Head :title="'Peminjaman ' + borrowing.transaction_code" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <Link :href="route('admin.borrowings.index')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                </Link>
                <h2 class="font-display font-bold text-xl text-gray-800 dark:text-gray-100">Detail Peminjaman</h2>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                <!-- Progress Steps -->
                <div v-if="!['rejected', 'cancelled'].includes(borrowing.status)" class="card p-6">
                    <div class="flex items-center justify-between">
                        <div v-for="(step, i) in steps" :key="i" class="flex items-center gap-2" :class="i < steps.length - 1 ? 'flex-1' : ''">
                            <div :class="['w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold transition-colors', step.done ? 'bg-sapphire-600 text-white' : 'bg-gray-200 dark:bg-cosmic-700 text-gray-400']">
                                {{ step.done ? '✓' : i + 1 }}
                            </div>
                            <span :class="['text-sm font-medium', step.done ? 'text-sapphire-600 dark:text-sapphire-400' : 'text-gray-400']">{{ step.label }}</span>
                            <div v-if="i < steps.length - 1" :class="['flex-1 h-0.5 mx-3', step.done ? 'bg-sapphire-600' : 'bg-gray-200 dark:bg-cosmic-700']"></div>
                        </div>
                    </div>
                </div>

                <!-- Info Card -->
                <div class="card p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <p class="font-mono text-lg font-bold text-sapphire-600 dark:text-sapphire-400">{{ borrowing.transaction_code }}</p>
                            <p class="text-sm text-gray-400 mt-1">Diajukan oleh <strong class="text-gray-700 dark:text-gray-200">{{ borrowing.user?.name }}</strong></p>
                        </div>
                        <span :class="['badge text-sm', `badge-${borrowing.status_color}`]">{{ borrowing.status_label }}</span>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div><p class="text-xs text-gray-400">Email</p><p class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ borrowing.user?.email }}</p></div>
                        <div><p class="text-xs text-gray-400">Departemen</p><p class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ borrowing.user?.department || '-' }}</p></div>
                        <div><p class="text-xs text-gray-400">Tgl Pinjam</p><p class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ formatDate(borrowing.borrow_date) }}</p></div>
                        <div><p class="text-xs text-gray-400">Tgl Kembali</p><p class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ formatDate(borrowing.expected_return_date) }}</p></div>
                    </div>

                    <div v-if="borrowing.purpose" class="mt-4 pt-4 border-t border-gray-100 dark:border-cosmic-700/30">
                        <p class="text-xs text-gray-400 mb-1">Tujuan</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300">{{ borrowing.purpose }}</p>
                    </div>

                    <div v-if="borrowing.rejection_reason" class="mt-4 p-3 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/30">
                        <p class="text-xs text-red-500 mb-1">Alasan Ditolak</p>
                        <p class="text-sm text-red-700 dark:text-red-300">{{ borrowing.rejection_reason }}</p>
                    </div>

                    <div v-if="borrowing.total_fine > 0" class="mt-4 p-3 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/30">
                        <p class="text-xs text-amber-600 mb-1">Total Denda</p>
                        <p class="text-lg font-bold text-amber-700 dark:text-amber-300">{{ formatCurrency(borrowing.total_fine) }}</p>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="table-container">
                    <div class="px-5 py-4 border-b border-gray-100 dark:border-cosmic-700/30">
                        <h3 class="font-display font-semibold text-gray-800 dark:text-gray-100">Barang Dipinjam</h3>
                    </div>
                    <table>
                        <thead><tr><th>Barang</th><th>Aset</th><th class="text-center">Qty</th><th class="text-center">Status</th><th class="text-right">Denda</th></tr></thead>
                        <tbody>
                            <tr v-for="d in borrowing.details" :key="d.id">
                                <td>
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-cosmic-700/50 flex items-center justify-center overflow-hidden">
                                            <img v-if="d.item?.image" :src="'/storage/' + d.item.image" class="w-full h-full object-cover" />
                                            <span v-else class="text-sm">📦</span>
                                        </div>
                                        {{ d.item?.name }}
                                    </div>
                                </td>
                                <td class="font-mono text-xs text-gray-400">{{ d.asset?.asset_code || 'Belum ditentukan' }}</td>
                                <td class="text-center">{{ d.quantity }}</td>
                                <td class="text-center">
                                    <span v-if="d.is_returned" class="badge badge-green">✓</span>
                                    <span v-else class="badge badge-yellow">Belum</span>
                                </td>
                                <td class="text-right font-mono text-sm">{{ formatCurrency(d.fine_amount || 0) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Actions -->
                <div class="card p-6 flex flex-wrap gap-3">
                    <template v-if="borrowing.status === 'pending'">
                        <button @click="approve" class="btn-primary" :disabled="processing" :class="{ 'opacity-50': processing }">
                            {{ processing ? '⏳ Memproses...' : '✅ Setujui' }}
                        </button>
                        <button @click="showRejectForm = !showRejectForm" class="btn-danger" :disabled="processing">❌ Tolak</button>
                    </template>
                    <button v-if="borrowing.status === 'approved'" @click="handover" class="btn-gold" :disabled="processing" :class="{ 'opacity-50': processing }">
                        {{ processing ? '⏳ Memproses...' : '📦 Serahkan Barang' }}
                    </button>
                    <button v-if="['active', 'overdue'].includes(borrowing.status)" @click="returnItems" class="btn-primary" :disabled="processing" :class="{ 'opacity-50': processing }">
                        {{ processing ? '⏳ Memproses...' : '🔄 Konfirmasi Pengembalian' }}
                    </button>
                    <Link :href="route('admin.borrowings.index')" class="btn-ghost">← Kembali</Link>
                </div>

                <!-- Reject Form -->
                <div v-if="showRejectForm" class="card p-6">
                    <form @submit.prevent="reject" class="space-y-4">
                        <h3 class="font-display font-semibold text-red-600">Alasan Penolakan</h3>
                        <textarea v-model="rejectForm.rejection_reason" class="input h-24 resize-none" placeholder="Jelaskan alasan penolakan..." required></textarea>
                        <p v-if="rejectForm.errors.rejection_reason" class="text-sm text-red-500">{{ rejectForm.errors.rejection_reason }}</p>
                        <div class="flex gap-2">
                            <button type="submit" class="btn-danger" :disabled="rejectForm.processing">
                                {{ rejectForm.processing ? '⏳ Mengirim...' : 'Kirim Penolakan' }}
                            </button>
                            <button type="button" @click="showRejectForm = false" class="btn-ghost">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Vue Confirm Modal (replaces browser confirm()) -->
        <Teleport to="body">
            <Transition name="fade">
                <div v-if="showConfirmModal" class="fixed inset-0 z-[999] flex items-center justify-center p-4">
                    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="showConfirmModal = false"></div>
                    <div class="relative bg-white dark:bg-cosmic-800 rounded-2xl shadow-2xl p-6 max-w-md w-full border border-gray-200 dark:border-cosmic-700/50 transform transition-all">
                        <h3 class="text-lg font-display font-bold text-gray-800 dark:text-gray-100 mb-2">{{ confirmTitle }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">{{ confirmMessage }}</p>
                        <div class="flex justify-end gap-3">
                            <button @click="showConfirmModal = false" class="btn-ghost">Batal</button>
                            <button @click="executeConfirm" class="btn-primary">Ya, Lanjutkan</button>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </AuthenticatedLayout>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
