<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    item: Object,
    relatedItems: Array,
    inCart: Boolean,
    cartQuantity: Number,
});

const quantity = ref(1);

const addToCart = () => {
    router.post(route('user.cart.store'), { item_id: props.item.id, quantity: quantity.value }, { preserveScroll: true });
};

const formatCurrency = (val) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val);
</script>

<template>
    <Head :title="item.name" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <Link :href="route('user.catalog.index')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                </Link>
                <h2 class="font-display font-bold text-xl text-gray-800 dark:text-gray-100">Detail Barang</h2>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                <div class="card p-6">
                    <div class="flex flex-col md:flex-row gap-8">
                        <!-- Image -->
                        <div class="w-full md:w-80 h-64 rounded-2xl bg-gray-100 dark:bg-cosmic-700/50 flex items-center justify-center overflow-hidden flex-shrink-0">
                            <img v-if="item.image" :src="'/storage/' + item.image" class="w-full h-full object-cover" />
                            <span v-else class="text-7xl opacity-30">📦</span>
                        </div>

                        <!-- Details -->
                        <div class="flex-1 space-y-4">
                            <span class="badge badge-blue">{{ item.category?.icon }} {{ item.category?.name }}</span>
                            <h1 class="text-2xl font-display font-bold text-gray-800 dark:text-gray-100">{{ item.name }}</h1>
                            <p v-if="item.description" class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed">{{ item.description }}</p>

                            <div class="grid grid-cols-2 gap-3 pt-2">
                                <div class="p-3 rounded-xl bg-gray-50 dark:bg-cosmic-800/40">
                                    <p class="text-xs text-gray-400">Stok Tersedia</p>
                                    <p class="text-xl font-bold" :class="item.available_stock > 0 ? 'text-emerald-600' : 'text-red-500'">
                                        {{ item.available_stock }}/{{ item.total_stock }}
                                    </p>
                                </div>
                                <div class="p-3 rounded-xl bg-gray-50 dark:bg-cosmic-800/40">
                                    <p class="text-xs text-gray-400">Maks Pinjam</p>
                                    <p class="text-xl font-bold text-gray-800 dark:text-gray-100">{{ item.max_borrow_days }} hari</p>
                                </div>
                                <div class="p-3 rounded-xl bg-gray-50 dark:bg-cosmic-800/40">
                                    <p class="text-xs text-gray-400">Maks Qty/User</p>
                                    <p class="text-xl font-bold text-gray-800 dark:text-gray-100">{{ item.max_qty_per_user }} unit</p>
                                </div>
                                <div class="p-3 rounded-xl bg-gray-50 dark:bg-cosmic-800/40">
                                    <p class="text-xs text-gray-400">Denda/Hari</p>
                                    <p class="text-xl font-bold text-gold-600">{{ formatCurrency(item.fine_per_day) }}</p>
                                </div>
                            </div>

                            <!-- Cart Action -->
                            <div v-if="item.available_stock > 0" class="flex items-center gap-3 pt-3 border-t border-gray-100 dark:border-cosmic-700/30">
                                <div class="flex items-center gap-2">
                                    <label class="text-sm text-gray-500">Jumlah:</label>
                                    <input type="number" v-model="quantity" min="1" :max="item.max_qty_per_user" class="input w-20 text-center" />
                                </div>
                                <button @click="addToCart" class="btn-primary flex-1">
                                    🛒 {{ inCart ? 'Tambah Lagi' : 'Masukkan ke Keranjang' }}
                                </button>
                            </div>
                            <div v-else class="pt-3 border-t border-gray-100 dark:border-cosmic-700/30">
                                <p class="text-red-500 font-semibold">⚠️ Stok sedang habis</p>
                            </div>

                            <p v-if="inCart" class="text-sm text-gold-600 dark:text-gold-400">
                                ✓ Sudah di keranjang ({{ cartQuantity }} unit)
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Specifications -->
                <div v-if="item.specifications" class="card p-6">
                    <h3 class="font-display font-semibold text-gray-800 dark:text-gray-100 mb-3">Spesifikasi</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 whitespace-pre-line">{{ item.specifications }}</p>
                </div>

                <!-- Related Items -->
                <div v-if="relatedItems?.length">
                    <h3 class="font-display font-semibold text-gray-800 dark:text-gray-100 mb-3">Barang Serupa</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <Link
                            v-for="ri in relatedItems" :key="ri.id"
                            :href="route('user.catalog.show', ri.id)"
                            class="card card-hover p-4 text-center group"
                        >
                            <div class="w-full h-20 rounded-xl bg-gray-100 dark:bg-cosmic-700/50 flex items-center justify-center mb-2 overflow-hidden">
                                <img v-if="ri.image" :src="'/storage/' + ri.image" class="w-full h-full object-cover group-hover:scale-105 transition-transform" />
                                <span v-else class="text-2xl opacity-30">📦</span>
                            </div>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 truncate">{{ ri.name }}</p>
                            <p class="text-xs text-gray-400 mt-1">Stok: {{ ri.available_stock }}/{{ ri.total_stock }}</p>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
