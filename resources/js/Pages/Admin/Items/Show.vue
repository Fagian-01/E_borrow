<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    item: Object,
});

const formatCurrency = (val) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val);
};

const statusColors = {
    available: 'badge-green',
    borrowed: 'badge-blue',
    maintenance: 'badge-yellow',
    damaged: 'badge-red',
    lost: 'badge-gray',
    retired: 'badge-gray',
};

const conditionColors = {
    baik: 'badge-green',
    cukup_baik: 'badge-blue',
    rusak_ringan: 'badge-yellow',
    rusak_berat: 'badge-red',
};
</script>

<template>
    <Head :title="item.name" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <Link :href="route('admin.items.index')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                </Link>
                <h2 class="font-display font-bold text-xl text-gray-800 dark:text-gray-100">
                    Detail Barang
                </h2>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                <!-- Item Header -->
                <div class="card p-6">
                    <div class="flex flex-col sm:flex-row gap-6">
                        <div class="w-32 h-32 rounded-2xl bg-gray-100 dark:bg-cosmic-700/50 flex items-center justify-center overflow-hidden flex-shrink-0">
                            <img v-if="item.image" :src="'/storage/' + item.image" class="w-full h-full object-cover" />
                            <span v-else class="text-5xl">📦</span>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h1 class="text-2xl font-display font-bold text-gray-800 dark:text-gray-100">{{ item.name }}</h1>
                                    <span class="badge badge-blue mt-1">{{ item.category?.icon }} {{ item.category?.name }}</span>
                                </div>
                                <Link :href="route('admin.items.edit', item.id)" class="btn-outline btn-sm">✏️ Edit</Link>
                            </div>
                            <p v-if="item.description" class="text-gray-500 dark:text-gray-400 mt-3 text-sm leading-relaxed">{{ item.description }}</p>

                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-4">
                                <div>
                                    <p class="text-xs text-gray-400 dark:text-gray-500">Stok Tersedia</p>
                                    <p class="text-lg font-bold" :class="item.available_stock > 0 ? 'text-emerald-600' : 'text-red-500'">
                                        {{ item.available_stock }}/{{ item.total_stock }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400 dark:text-gray-500">Maks Pinjam</p>
                                    <p class="text-lg font-bold text-gray-800 dark:text-gray-100">{{ item.max_borrow_days }} hari</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400 dark:text-gray-500">Denda/Hari</p>
                                    <p class="text-lg font-bold text-gold-600">{{ formatCurrency(item.fine_per_day) }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400 dark:text-gray-500">Status</p>
                                    <span :class="item.is_active ? 'badge badge-green' : 'badge badge-red'" class="mt-1">
                                        {{ item.is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Assets List -->
                <div class="table-container">
                    <div class="px-5 py-4 border-b border-gray-100 dark:border-cosmic-700/30 flex items-center justify-between">
                        <h3 class="font-display font-semibold text-gray-800 dark:text-gray-100">Daftar Aset Fisik</h3>
                        <span class="badge badge-gray">{{ item.assets?.length ?? 0 }} unit</span>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Kode Aset</th>
                                <th>Barcode</th>
                                <th>Serial Number</th>
                                <th>Lokasi</th>
                                <th class="text-center">Kondisi</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="asset in item.assets" :key="asset.id">
                                <td class="font-mono text-xs font-medium">{{ asset.asset_code }}</td>
                                <td class="font-mono text-xs text-gray-500">{{ asset.barcode || '-' }}</td>
                                <td class="text-sm">{{ asset.serial_number || '-' }}</td>
                                <td class="text-sm text-gray-500 dark:text-gray-400">{{ asset.location || '-' }}</td>
                                <td class="text-center">
                                    <span :class="['badge', conditionColors[asset.condition] || 'badge-gray']">
                                        {{ asset.condition?.replace('_', ' ') }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span :class="['badge', statusColors[asset.status] || 'badge-gray']">
                                        {{ asset.status }}
                                    </span>
                                </td>
                            </tr>
                            <tr v-if="!item.assets?.length">
                                <td colspan="6" class="text-center py-10 text-gray-400 dark:text-gray-500">
                                    <div class="text-3xl mb-2">🏷️</div>
                                    <p>Belum ada aset fisik terdaftar</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
