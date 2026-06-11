<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    stats: Object,
    recentBorrowings: Array,
});
</script>

<template>
    <Head title="Admin Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-display font-bold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                Admin Dashboard
            </h2>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                <!-- Primary Stats Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="card card-hover p-5 group">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-sapphire-100 dark:bg-sapphire-900/40 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                                👥
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Total Users</p>
                                <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ stats.totalUsers }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="card card-hover p-5 group">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-gold-100 dark:bg-gold-900/40 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                                📦
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Total Barang</p>
                                <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ stats.totalItems }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="card card-hover p-5 group">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                                ✅
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Asset Tersedia</p>
                                <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ stats.availableAssets }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="card card-hover p-5 group">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-amber-100 dark:bg-amber-900/40 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                                ⏳
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Pending Approval</p>
                                <p class="text-2xl font-bold text-amber-600 dark:text-amber-400">{{ stats.pendingBorrowings }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Secondary Stats -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="card card-hover p-5 group">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center text-lg group-hover:scale-110 transition-transform">📋</div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Sedang Dipinjam</p>
                                <p class="text-xl font-bold text-blue-600 dark:text-blue-400">{{ stats.activeBorrowings }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="card card-hover p-5 group">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-red-100 dark:bg-red-900/40 flex items-center justify-center text-lg group-hover:scale-110 transition-transform">🚨</div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Terlambat</p>
                                <p class="text-xl font-bold text-red-600 dark:text-red-400">{{ stats.overdueBorrowings }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="card card-hover p-5 group">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-yellow-100 dark:bg-yellow-900/40 flex items-center justify-center text-lg group-hover:scale-110 transition-transform">🔧</div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Maintenance</p>
                                <p class="text-xl font-bold text-yellow-600 dark:text-yellow-400">{{ stats.maintenanceAssets }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Borrowings Table -->
                <div class="table-container">
                    <div class="px-5 py-4 border-b border-gray-100 dark:border-cosmic-700/30 flex items-center justify-between">
                        <h3 class="font-display font-semibold text-gray-800 dark:text-gray-100">Peminjaman Terbaru</h3>
                        <span class="badge badge-blue">{{ recentBorrowings?.length ?? 0 }} transaksi</span>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Kode Transaksi</th>
                                <th>Peminjam</th>
                                <th>Departemen</th>
                                <th>Tanggal Pinjam</th>
                                <th>Tenggat</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="b in recentBorrowings" :key="b.id">
                                <td class="font-mono text-xs font-medium">{{ b.transaction_code }}</td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-gradient-to-br from-sapphire-400 to-sapphire-600 flex items-center justify-center text-white text-[10px] font-bold">
                                            {{ b.user?.name?.charAt(0)?.toUpperCase() }}
                                        </div>
                                        {{ b.user?.name }}
                                    </div>
                                </td>
                                <td class="text-gray-500 dark:text-gray-400">{{ b.user?.department ?? '-' }}</td>
                                <td>{{ b.borrow_date }}</td>
                                <td>{{ b.expected_return_date }}</td>
                                <td>
                                    <span :class="['badge', `badge-${b.status_color}`]">
                                        {{ b.status_label }}
                                    </span>
                                </td>
                            </tr>
                            <tr v-if="!recentBorrowings?.length">
                                <td colspan="6" class="text-center py-12 text-gray-400 dark:text-gray-500">
                                    <div class="text-4xl mb-2">📋</div>
                                    <p>Belum ada transaksi peminjaman</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
