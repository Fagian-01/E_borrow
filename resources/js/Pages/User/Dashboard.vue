<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';

defineProps({
    stats: Object,
    recentBorrowings: Array,
    categories: Array,
});

const user = usePage().props.auth.user;
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-display font-bold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                Selamat Datang, {{ user.name }} 👋
            </h2>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                <!-- Stats Grid -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="card card-hover p-5 group">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-xl bg-sapphire-100 dark:bg-sapphire-900/40 flex items-center justify-center text-lg group-hover:scale-110 transition-transform">📋</div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Sedang Dipinjam</p>
                                <p class="text-xl font-bold text-sapphire-600 dark:text-sapphire-400">{{ stats.activeBorrowings }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="card card-hover p-5 group">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-xl bg-amber-100 dark:bg-amber-900/40 flex items-center justify-center text-lg group-hover:scale-110 transition-transform">⏳</div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Menunggu</p>
                                <p class="text-xl font-bold text-amber-600 dark:text-amber-400">{{ stats.pendingBorrowings }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="card card-hover p-5 group">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center text-lg group-hover:scale-110 transition-transform">📊</div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Total Transaksi</p>
                                <p class="text-xl font-bold text-emerald-600 dark:text-emerald-400">{{ stats.totalBorrowings }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="card card-hover p-5 group">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-xl bg-gold-100 dark:bg-gold-900/40 flex items-center justify-center text-lg group-hover:scale-110 transition-transform">🛒</div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Di Keranjang</p>
                                <p class="text-xl font-bold text-gold-600 dark:text-gold-400">{{ stats.cartItems }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Browse Categories -->
                <div>
                    <h3 class="font-display font-semibold text-gray-800 dark:text-gray-100 mb-3">Jelajahi Katalog</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                        <div
                            v-for="cat in categories"
                            :key="cat.id"
                            class="card card-hover p-4 text-center cursor-pointer group"
                        >
                            <div class="text-3xl mb-2 group-hover:scale-125 transition-transform duration-300">{{ cat.icon }}</div>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 truncate">{{ cat.name }}</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ cat.items_count }} barang</p>
                        </div>
                    </div>
                </div>

                <!-- Recent Borrowings -->
                <div class="table-container">
                    <div class="px-5 py-4 border-b border-gray-100 dark:border-cosmic-700/30">
                        <h3 class="font-display font-semibold text-gray-800 dark:text-gray-100">Riwayat Peminjaman Saya</h3>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Barang</th>
                                <th>Tanggal Pinjam</th>
                                <th>Tenggat</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="b in recentBorrowings" :key="b.id">
                                <td class="font-mono text-xs font-medium">{{ b.transaction_code }}</td>
                                <td>
                                    <div v-for="(item, i) in b.items" :key="i" class="text-sm">
                                        {{ item.name }} <span class="text-gray-400 dark:text-gray-500">×{{ item.quantity }}</span>
                                    </div>
                                </td>
                                <td>{{ b.borrow_date }}</td>
                                <td>
                                    <span :class="b.is_overdue ? 'text-red-600 dark:text-red-400 font-semibold' : ''">
                                        {{ b.expected_return_date }}
                                        <span v-if="b.is_overdue" class="badge badge-red ml-1 text-[10px]">Terlambat!</span>
                                    </span>
                                </td>
                                <td>
                                    <span :class="['badge', `badge-${b.status_color}`]">
                                        {{ b.status_label }}
                                    </span>
                                </td>
                            </tr>
                            <tr v-if="!recentBorrowings?.length">
                                <td colspan="5" class="text-center py-12 text-gray-400 dark:text-gray-500">
                                    <div class="text-4xl mb-2">📦</div>
                                    <p>Anda belum pernah meminjam barang</p>
                                    <p class="text-xs mt-1">Jelajahi katalog untuk mulai meminjam</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
