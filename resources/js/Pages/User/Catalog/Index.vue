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
let searchTimeout = null;

const applyFilters = () => {
    const params = {};
    if (search.value) params.search = search.value;
    if (selectedCategory.value) params.category = selectedCategory.value;
    router.get(route('user.catalog.index'), params, { preserveState: true, replace: true });
};

watch(search, () => { clearTimeout(searchTimeout); searchTimeout = setTimeout(applyFilters, 300); });
watch(selectedCategory, applyFilters);

const addToCart = (itemId) => {
    router.post(route('user.cart.store'), { item_id: itemId, quantity: 1 }, { preserveScroll: true });
};
</script>

<template>
    <Head title="Katalog Barang" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-display font-bold text-xl text-gray-800 dark:text-gray-100">Katalog Barang</h2>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                <!-- Filters -->
                <div class="card p-4 flex flex-wrap gap-3 items-center">
                    <input type="text" v-model="search" placeholder="🔍 Cari barang..." class="input max-w-xs" />
                    <select v-model="selectedCategory" class="input max-w-[200px]">
                        <option value="">Semua Kategori</option>
                        <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                            {{ cat.icon }} {{ cat.name }} ({{ cat.items_count }})
                        </option>
                    </select>
                </div>

                <!-- Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    <div
                        v-for="item in items.data"
                        :key="item.id"
                        class="card card-hover overflow-hidden group"
                    >
                        <!-- Image -->
                        <div class="h-40 bg-gray-100 dark:bg-cosmic-700/50 flex items-center justify-center overflow-hidden">
                            <img v-if="item.image" :src="'/storage/' + item.image" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                            <span v-else class="text-5xl opacity-30">📦</span>
                        </div>

                        <div class="p-4 space-y-3">
                            <!-- Category Badge -->
                            <span class="badge badge-blue text-[10px]">{{ item.category?.icon }} {{ item.category?.name }}</span>

                            <!-- Title -->
                            <Link :href="route('user.catalog.show', item.id)" class="block">
                                <h3 class="font-semibold text-gray-800 dark:text-gray-100 group-hover:text-sapphire-600 dark:group-hover:text-sapphire-400 transition-colors truncate">
                                    {{ item.name }}
                                </h3>
                            </Link>

                            <!-- Stock -->
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500 dark:text-gray-400">Tersedia</span>
                                <span :class="item.available_stock > 0 ? 'text-emerald-600 dark:text-emerald-400 font-bold' : 'text-red-500 font-bold'">
                                    {{ item.available_stock }}/{{ item.total_stock }}
                                </span>
                            </div>

                            <!-- Max borrow -->
                            <div class="flex items-center justify-between text-sm text-gray-400 dark:text-gray-500">
                                <span>Maks {{ item.max_borrow_days }} hari</span>
                                <span>Maks {{ item.max_qty_per_user }} unit</span>
                            </div>

                            <!-- Add to Cart -->
                            <button
                                v-if="item.available_stock > 0"
                                @click.prevent="addToCart(item.id)"
                                class="btn-primary w-full text-xs py-2"
                            >
                                🛒 Tambah ke Keranjang
                            </button>
                            <button v-else disabled class="btn w-full text-xs py-2 bg-gray-200 dark:bg-cosmic-700 text-gray-400 cursor-not-allowed">
                                Stok Habis
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Empty -->
                <div v-if="!items.data?.length" class="card p-12 text-center">
                    <div class="text-5xl mb-3">📭</div>
                    <p class="text-gray-500 dark:text-gray-400 text-lg">Tidak ada barang ditemukan</p>
                    <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">Coba ubah filter pencarian Anda</p>
                </div>

                <!-- Pagination -->
                <div v-if="items.last_page > 1" class="flex justify-center gap-1">
                    <Link
                        v-for="link in items.links"
                        :key="link.label"
                        :href="link.url"
                        v-html="link.label"
                        :class="['px-3 py-2 text-sm rounded-lg transition-colors', link.active ? 'bg-sapphire-600 text-white' : link.url ? 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-cosmic-700/50' : 'text-gray-300 cursor-not-allowed']"
                        :preserve-state="true"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
