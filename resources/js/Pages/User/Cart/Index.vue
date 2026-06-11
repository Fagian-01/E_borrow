<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({ carts: Array });

const totalItems = computed(() => props.carts.reduce((sum, c) => sum + c.quantity, 0));

const updateQty = (cart, newQty) => {
    if (newQty < 1) return;
    router.patch(route('user.cart.update', cart.id), { quantity: newQty }, { preserveScroll: true });
};

const removeItem = (cart) => {
    router.delete(route('user.cart.destroy', cart.id), { preserveScroll: true });
};

const clearAll = () => {
    if (confirm('Kosongkan seluruh keranjang?')) {
        router.delete(route('user.cart.clear'));
    }
};

// Checkout form
const showCheckout = ref(false);
const form = useForm({
    borrow_date: new Date().toISOString().split('T')[0],
    expected_return_date: '',
    purpose: '',
    notes: '',
});

const submitCheckout = () => {
    form.post(route('user.borrowings.checkout'));
};

const formatCurrency = (val) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val);
</script>

<template>
    <Head title="Keranjang" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between w-full">
                <h2 class="font-display font-bold text-xl text-gray-800 dark:text-gray-100">
                    🛒 Keranjang <span class="text-gray-400 text-base font-normal">({{ totalItems }} item)</span>
                </h2>
                <button v-if="carts.length" @click="clearAll" class="btn-ghost btn-sm text-red-500">Kosongkan</button>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
                <!-- Cart Items -->
                <div v-for="cart in carts" :key="cart.id" class="card p-4">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-xl bg-gray-100 dark:bg-cosmic-700/50 flex items-center justify-center overflow-hidden flex-shrink-0">
                            <img v-if="cart.item?.image" :src="'/storage/' + cart.item.image" class="w-full h-full object-cover" />
                            <span v-else class="text-2xl">📦</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-gray-800 dark:text-gray-100 truncate">{{ cart.item?.name }}</h3>
                            <p class="text-xs text-gray-400">{{ cart.item?.category?.icon }} {{ cart.item?.category?.name }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">Denda: {{ formatCurrency(cart.item?.fine_per_day || 0) }}/hari · Maks {{ cart.item?.max_borrow_days }} hari</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button @click="updateQty(cart, cart.quantity - 1)" class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-cosmic-700/50 flex items-center justify-center text-lg hover:bg-gray-200 dark:hover:bg-cosmic-600/50 transition-colors" :disabled="cart.quantity <= 1">−</button>
                            <span class="w-10 text-center font-bold text-gray-800 dark:text-gray-100">{{ cart.quantity }}</span>
                            <button @click="updateQty(cart, cart.quantity + 1)" class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-cosmic-700/50 flex items-center justify-center text-lg hover:bg-gray-200 dark:hover:bg-cosmic-600/50 transition-colors" :disabled="cart.quantity >= (cart.item?.max_qty_per_user || 1)">+</button>
                        </div>
                        <button @click="removeItem(cart)" class="p-2 text-red-400 hover:text-red-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        </button>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="!carts.length" class="card p-12 text-center">
                    <div class="text-5xl mb-3">🛒</div>
                    <p class="text-gray-500 dark:text-gray-400 text-lg">Keranjang masih kosong</p>
                    <Link :href="route('user.catalog.index')" class="btn-primary mt-4">Jelajahi Katalog</Link>
                </div>

                <!-- Checkout Section -->
                <div v-if="carts.length" class="card p-6">
                    <div v-if="!showCheckout" class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Total {{ totalItems }} barang siap untuk diajukan</p>
                        </div>
                        <button @click="showCheckout = true" class="btn-gold">
                            📋 Ajukan Peminjaman
                        </button>
                    </div>

                    <!-- Checkout Form -->
                    <form v-else @submit.prevent="submitCheckout" class="space-y-4">
                        <h3 class="font-display font-semibold text-gray-800 dark:text-gray-100">Detail Peminjaman</h3>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tanggal Pinjam *</label>
                                <input type="date" v-model="form.borrow_date" class="input" :min="new Date().toISOString().split('T')[0]" required />
                                <p v-if="form.errors.borrow_date" class="mt-1 text-sm text-red-500">{{ form.errors.borrow_date }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tanggal Kembali *</label>
                                <input type="date" v-model="form.expected_return_date" class="input" :min="form.borrow_date" required />
                                <p v-if="form.errors.expected_return_date" class="mt-1 text-sm text-red-500">{{ form.errors.expected_return_date }}</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tujuan Peminjaman</label>
                            <textarea v-model="form.purpose" class="input h-20 resize-none" placeholder="Jelaskan tujuan peminjaman (opsional)..."></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Catatan</label>
                            <textarea v-model="form.notes" class="input h-16 resize-none" placeholder="Catatan tambahan (opsional)..."></textarea>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100 dark:border-cosmic-700/30">
                            <button type="button" @click="showCheckout = false" class="btn-ghost">Batal</button>
                            <button type="submit" class="btn-gold" :class="{ 'opacity-50': form.processing }" :disabled="form.processing">
                                <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                {{ form.processing ? 'Memproses...' : '✅ Kirim Pengajuan' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
