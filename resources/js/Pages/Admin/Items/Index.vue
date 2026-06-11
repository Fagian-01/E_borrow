<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    items: Object,
    categories: Array,
    filters: Object,
});

const search = ref(props.filters?.search ?? '');
const selectedCategory = ref(props.filters?.category ?? '');
const selectedStatus = ref(props.filters?.status ?? '');
let searchTimeout = null;

const applyFilters = () => {
    const params = {};
    if (search.value) params.search = search.value;
    if (selectedCategory.value) params.category = selectedCategory.value;
    if (selectedStatus.value) params.status = selectedStatus.value;

    router.get(route('admin.items.index'), params, {
        preserveState: true,
        replace: true,
    });
};

watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(applyFilters, 300);
});

watch([selectedCategory, selectedStatus], applyFilters);

const deleteItem = (item) => {
    if (confirm(`Apakah Anda yakin ingin menghapus "${item.name}"?`)) {
        router.delete(route('admin.items.destroy', item.id));
    }
};

const formatCurrency = (val) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val);
};
</script>

<template>
    <Head title="Inventaris Barang" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between w-full">
                <h2 class="font-display font-bold text-xl text-gray-800 dark:text-gray-100">Inventaris Barang</h2>
                <Link :href="route('admin.items.create')" class="btn-primary btn-sm">
                    + Tambah Barang
                </Link>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                <!-- Filters -->
                <div class="card p-4 flex flex-wrap gap-3 items-center">
                    <input type="text" v-model="search" placeholder="🔍 Cari barang..." class="input max-w-xs" />
                    <select v-model="selectedCategory" class="input max-w-[200px]">
                        <option value="">Semua Kategori</option>
                        <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                            {{ cat.icon }} {{ cat.name }}
                        </option>
                    </select>
                    <select v-model="selectedStatus" class="input max-w-[160px]">
                        <option value="">Semua Status</option>
                        <option value="active">Aktif</option>
                        <option value="inactive">Nonaktif</option>
                    </select>
                </div>

                <!-- Table -->
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Barang</th>
                                <th>Kategori</th>
                                <th class="text-center">Stok</th>
                                <th class="text-center">Aset</th>
                                <th class="text-right">Denda/Hari</th>
                                <th class="text-center">Status</th>
                                <th class="text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in items.data" :key="item.id">
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-cosmic-700/50 flex items-center justify-center overflow-hidden flex-shrink-0">
                                            <img v-if="item.image" :src="'/storage/' + item.image" class="w-full h-full object-cover" />
                                            <span v-else class="text-lg">📦</span>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-semibold text-gray-800 dark:text-gray-100 truncate">{{ item.name }}</p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500 truncate">{{ item.description?.substring(0, 60) }}{{ item.description?.length > 60 ? '...' : '' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-blue">
                                        {{ item.category?.icon }} {{ item.category?.name }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span :class="item.available_stock > 0 ? 'text-emerald-600 dark:text-emerald-400 font-bold' : 'text-red-500 font-bold'">
                                        {{ item.available_stock }}
                                    </span>
                                    <span class="text-gray-400">/{{ item.total_stock }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-gray">{{ item.assets_count }} unit</span>
                                </td>
                                <td class="text-right text-sm font-mono">{{ formatCurrency(item.fine_per_day) }}</td>
                                <td class="text-center">
                                    <span :class="item.is_active ? 'badge badge-green' : 'badge badge-red'">
                                        {{ item.is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <Link :href="route('admin.items.show', item.id)" class="btn-ghost btn-sm">👁️</Link>
                                        <Link :href="route('admin.items.edit', item.id)" class="btn-ghost btn-sm">✏️</Link>
                                        <button @click="deleteItem(item)" class="btn-ghost btn-sm text-red-500 hover:text-red-700">🗑️</button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!items.data?.length">
                                <td colspan="7" class="text-center py-12 text-gray-400 dark:text-gray-500">
                                    <div class="text-4xl mb-2">📦</div>
                                    <p>Belum ada barang terdaftar</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div v-if="items.last_page > 1" class="px-5 py-3 border-t border-gray-100 dark:border-cosmic-700/30 flex items-center justify-between">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Menampilkan {{ items.from }}–{{ items.to }} dari {{ items.total }}
                        </p>
                        <div class="flex gap-1">
                            <Link
                                v-for="link in items.links"
                                :key="link.label"
                                :href="link.url"
                                v-html="link.label"
                                :class="[
                                    'px-3 py-1.5 text-sm rounded-lg transition-colors',
                                    link.active ? 'bg-sapphire-600 text-white' : link.url ? 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-cosmic-700/50' : 'text-gray-300 dark:text-gray-600 cursor-not-allowed',
                                ]"
                                :preserve-state="true"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
