<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({ borrowing: Object });

const formatCurrency = (val) => {
    if (val === null || val === undefined) return 'Rp 0';
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val);
};
const formatDate = (dateStr) => {
    if (!dateStr) return '-';
    try { return new Date(dateStr).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }); } catch { return dateStr; }
};
</script>

<template>
    <Head :title="'Peminjaman ' + borrowing.transaction_code" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <Link :href="route('user.borrowings.index')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                </Link>
                <h2 class="font-display font-bold text-xl text-gray-800 dark:text-gray-100">Detail Peminjaman</h2>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                <!-- Header -->
                <div class="card p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <p class="font-mono text-lg font-bold text-sapphire-600 dark:text-sapphire-400">{{ borrowing.transaction_code }}</p>
                            <p class="text-sm text-gray-400 mt-1">Diajukan pada {{ borrowing.created_at }}</p>
                        </div>
                        <span :class="['badge text-sm', `badge-${borrowing.status_color}`]">{{ borrowing.status_label }}</span>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div><p class="text-xs text-gray-400">Tgl Pinjam</p><p class="font-semibold text-gray-800 dark:text-gray-100">{{ formatDate(borrowing.borrow_date) }}</p></div>
                        <div><p class="text-xs text-gray-400">Tgl Kembali</p><p class="font-semibold text-gray-800 dark:text-gray-100">{{ formatDate(borrowing.expected_return_date) }}</p></div>
                        <div v-if="borrowing.approver"><p class="text-xs text-gray-400">Disetujui Oleh</p><p class="font-semibold text-gray-800 dark:text-gray-100">{{ borrowing.approver.name }}</p></div>
                        <div v-if="borrowing.total_fine > 0"><p class="text-xs text-gray-400">Total Denda</p><p class="font-semibold text-red-600">{{ formatCurrency(borrowing.total_fine) }}</p></div>
                    </div>

                    <div v-if="borrowing.purpose" class="mt-4 pt-4 border-t border-gray-100 dark:border-cosmic-700/30">
                        <p class="text-xs text-gray-400 mb-1">Tujuan</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300">{{ borrowing.purpose }}</p>
                    </div>

                    <div v-if="borrowing.rejection_reason" class="mt-4 p-3 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/30">
                        <p class="text-xs text-red-500 mb-1">Alasan Ditolak</p>
                        <p class="text-sm text-red-700 dark:text-red-300">{{ borrowing.rejection_reason }}</p>
                    </div>
                </div>

                <!-- Items -->
                <div class="table-container">
                    <div class="px-5 py-4 border-b border-gray-100 dark:border-cosmic-700/30">
                        <h3 class="font-display font-semibold text-gray-800 dark:text-gray-100">Barang Dipinjam</h3>
                    </div>
                    <table>
                        <thead><tr><th>Barang</th><th class="text-center">Qty</th><th class="text-center">Dikembalikan</th><th class="text-right">Denda</th></tr></thead>
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
                                <td class="text-center">{{ d.quantity }}</td>
                                <td class="text-center">
                                    <span v-if="d.is_returned" class="badge badge-green">✓ Dikembalikan</span>
                                    <span v-else class="badge badge-yellow">Belum</span>
                                </td>
                                <td class="text-right font-mono text-sm">{{ formatCurrency(d.fine_amount || 0) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
