<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({ summary: Object, monthlyTrend: Array, topItems: Array, statusDistribution: Array, recentFines: Array });

const formatCurrency = (val) => {
    if (val === null || val === undefined) return 'Rp 0';
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val);
};

const summaryCards = computed(() => [
    { label: 'Total Peminjaman', value: props.summary.totalBorrowings, icon: '📊', color: 'from-sapphire-500 to-sapphire-700' },
    { label: 'Aktif', value: props.summary.activeBorrowings, icon: '📋', color: 'from-emerald-500 to-emerald-700' },
    { label: 'Selesai', value: props.summary.completedBorrowings, icon: '✅', color: 'from-blue-500 to-blue-700' },
    { label: 'Total Denda', value: formatCurrency(props.summary.totalFines), icon: '💰', color: 'from-amber-500 to-amber-700' },
]);

const statusColors = {
    pending: '#EAB308',
    approved: '#3B82F6',
    active: '#10B981',
    returned: '#6B7280',
    rejected: '#EF4444',
    cancelled: '#9CA3AF',
    overdue: '#DC2626',
};

const statusLabels = {
    pending: 'Pending',
    approved: 'Disetujui',
    active: 'Aktif',
    returned: 'Dikembalikan',
    rejected: 'Ditolak',
    cancelled: 'Dibatalkan',
    overdue: 'Terlambat',
};

// Calculate max value for bar chart scaling
const maxTrendValue = computed(() => {
    if (!props.monthlyTrend?.length) return 1;
    return Math.max(...props.monthlyTrend.map(t => t.total), 1);
});

const totalStatusCount = computed(() => {
    return props.statusDistribution?.reduce((sum, s) => sum + s.count, 0) || 1;
});
</script>

<template>
    <Head title="Laporan" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-display font-bold text-xl text-gray-800 dark:text-gray-100">📈 Laporan & Statistik</h2>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                <!-- Summary Cards -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div v-for="card in summaryCards" :key="card.label" class="rounded-2xl p-5 bg-gradient-to-br text-white shadow-lg" :class="card.color">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-white/80">{{ card.label }}</p>
                                <p class="text-3xl font-bold mt-1">{{ card.value }}</p>
                            </div>
                            <div class="text-3xl opacity-50">{{ card.icon }}</div>
                        </div>
                    </div>
                </div>

                <!-- Secondary Stats -->
                <div class="grid grid-cols-3 gap-4">
                    <div class="card p-5 text-center">
                        <p class="text-sm text-gray-400">Total User</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-1">{{ summary.totalUsers }}</p>
                    </div>
                    <div class="card p-5 text-center">
                        <p class="text-sm text-gray-400">Total Barang</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-1">{{ summary.totalItems }}</p>
                    </div>
                    <div class="card p-5 text-center">
                        <p class="text-sm text-gray-400">User Aktif Bulan Ini</p>
                        <p class="text-2xl font-bold text-sapphire-600 dark:text-sapphire-400 mt-1">{{ summary.activeUsersThisMonth }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Monthly Trend Chart (CSS bar chart) -->
                    <div class="card p-6">
                        <h3 class="font-display font-semibold text-gray-800 dark:text-gray-100 mb-4">📊 Tren Peminjaman (6 Bulan)</h3>
                        <div v-if="monthlyTrend.length" class="space-y-3">
                            <div v-for="t in monthlyTrend" :key="t.month" class="flex items-center gap-3">
                                <span class="text-xs text-gray-400 w-16 flex-shrink-0 text-right">{{ t.month }}</span>
                                <div class="flex-1 bg-gray-100 dark:bg-cosmic-700/50 rounded-full h-8 relative overflow-hidden">
                                    <div class="h-full rounded-full bg-gradient-to-r from-sapphire-500 to-sapphire-600 flex items-center justify-end px-2 text-xs font-bold text-white transition-all duration-700"
                                         :style="{ width: Math.max((t.total / maxTrendValue) * 100, 8) + '%' }">
                                        {{ t.total }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-8 text-gray-400">
                            <p>Belum ada data</p>
                        </div>
                    </div>

                    <!-- Status Distribution -->
                    <div class="card p-6">
                        <h3 class="font-display font-semibold text-gray-800 dark:text-gray-100 mb-4">🎯 Distribusi Status</h3>
                        <div v-if="statusDistribution.length" class="space-y-3">
                            <div v-for="s in statusDistribution" :key="s.status" class="flex items-center gap-3">
                                <span class="text-xs w-20 flex-shrink-0 text-right text-gray-400">{{ statusLabels[s.status] || s.status }}</span>
                                <div class="flex-1 bg-gray-100 dark:bg-cosmic-700/50 rounded-full h-6 relative overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-700 flex items-center px-2 text-[10px] font-bold text-white"
                                         :style="{ width: Math.max((s.count / totalStatusCount) * 100, 5) + '%', backgroundColor: statusColors[s.status] || '#6B7280' }">
                                        {{ s.count }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-8 text-gray-400">
                            <p>Belum ada data</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Top Borrowed Items -->
                    <div class="card p-6">
                        <h3 class="font-display font-semibold text-gray-800 dark:text-gray-100 mb-4">🏆 Barang Paling Sering Dipinjam</h3>
                        <div v-if="topItems.length" class="space-y-3">
                            <div v-for="(item, i) in topItems" :key="i" class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-sm font-bold" :class="i < 3 ? 'bg-gold-100 text-gold-700 dark:bg-gold-900/40 dark:text-gold-300' : 'bg-gray-100 text-gray-500 dark:bg-cosmic-700/50 dark:text-gray-400'">
                                    {{ i + 1 }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-800 dark:text-gray-100 truncate">{{ item.name }}</p>
                                </div>
                                <span class="badge badge-blue text-xs">{{ item.total_qty }}×</span>
                            </div>
                        </div>
                        <div v-else class="text-center py-8 text-gray-400">
                            <p>Belum ada data</p>
                        </div>
                    </div>

                    <!-- Recent Fines -->
                    <div class="card p-6">
                        <h3 class="font-display font-semibold text-gray-800 dark:text-gray-100 mb-4">💰 Denda Terbaru</h3>
                        <div v-if="recentFines.length" class="space-y-3">
                            <div v-for="f in recentFines" :key="f.transaction_code" class="flex items-center justify-between p-3 rounded-xl bg-amber-50/50 dark:bg-amber-900/10 border border-amber-100 dark:border-amber-800/20">
                                <div>
                                    <p class="font-mono text-xs text-sapphire-600 dark:text-sapphire-400">{{ f.transaction_code }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ f.user }} · {{ f.department || '-' }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-amber-600 dark:text-amber-400">{{ formatCurrency(f.fine) }}</p>
                                    <p class="text-[10px] text-gray-400">{{ f.date }}</p>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-8 text-gray-400">
                            <p class="text-xl mb-1">🎉</p>
                            <p>Tidak ada denda</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
