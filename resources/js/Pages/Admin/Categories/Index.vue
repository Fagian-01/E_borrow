<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    categories: Object,
    filters: Object,
});

const search = ref(props.filters?.search ?? '');
let searchTimeout = null;

watch(search, (val) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('admin.categories.index'), { search: val || undefined }, {
            preserveState: true,
            replace: true,
        });
    }, 300);
});

const deleteCategory = (category) => {
    if (confirm(`Apakah Anda yakin ingin menghapus kategori "${category.name}"?`)) {
        router.delete(route('admin.categories.destroy', category.id));
    }
};
</script>

<template>
    <Head title="Kategori" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between w-full">
                <h2 class="font-display font-bold text-xl text-gray-800 dark:text-gray-100">Kelola Kategori</h2>
                <Link :href="route('admin.categories.create')" class="btn-primary btn-sm">
                    + Tambah Kategori
                </Link>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                <!-- Search -->
                <div class="card p-4">
                    <input
                        type="text"
                        v-model="search"
                        placeholder="🔍 Cari kategori..."
                        class="input max-w-sm"
                    />
                </div>

                <!-- Table -->
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th class="w-16">#</th>
                                <th>Kategori</th>
                                <th>Deskripsi</th>
                                <th class="text-center">Jumlah Barang</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Urutan</th>
                                <th class="text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(cat, index) in categories.data" :key="cat.id">
                                <td class="text-gray-400 text-sm">{{ (categories.current_page - 1) * categories.per_page + index + 1 }}</td>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <span class="text-2xl">{{ cat.icon || '📁' }}</span>
                                        <div>
                                            <p class="font-semibold text-gray-800 dark:text-gray-100">{{ cat.name }}</p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500">{{ cat.slug }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-gray-500 dark:text-gray-400 text-sm max-w-xs truncate">{{ cat.description || '-' }}</td>
                                <td class="text-center">
                                    <span class="badge badge-blue">{{ cat.items_count }} barang</span>
                                </td>
                                <td class="text-center">
                                    <span :class="cat.is_active ? 'badge badge-green' : 'badge badge-red'">
                                        {{ cat.is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="text-center text-gray-400">{{ cat.sort_order }}</td>
                                <td class="text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <Link :href="route('admin.categories.edit', cat.id)" class="btn-ghost btn-sm">
                                            ✏️
                                        </Link>
                                        <button @click="deleteCategory(cat)" class="btn-ghost btn-sm text-red-500 hover:text-red-700">
                                            🗑️
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!categories.data?.length">
                                <td colspan="7" class="text-center py-12 text-gray-400 dark:text-gray-500">
                                    <div class="text-4xl mb-2">📁</div>
                                    <p>Belum ada kategori</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div v-if="categories.last_page > 1" class="px-5 py-3 border-t border-gray-100 dark:border-cosmic-700/30 flex items-center justify-between">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Menampilkan {{ categories.from }}–{{ categories.to }} dari {{ categories.total }}
                        </p>
                        <div class="flex gap-1">
                            <Link
                                v-for="link in categories.links"
                                :key="link.label"
                                :href="link.url"
                                v-html="link.label"
                                :class="[
                                    'px-3 py-1.5 text-sm rounded-lg transition-colors',
                                    link.active
                                        ? 'bg-sapphire-600 text-white'
                                        : link.url
                                            ? 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-cosmic-700/50'
                                            : 'text-gray-300 dark:text-gray-600 cursor-not-allowed',
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
