<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({ borrowings: Object, filters: Object, statusCounts: Object });

const search = ref(props.filters?.search ?? '');
const statusFilter = ref(props.filters?.status ?? '');
let searchTimeout = null;

const applyFilters = () => {
    const params = {};
    if (search.value) params.search = search.value;
    if (statusFilter.value) params.status = statusFilter.value;
    router.get(route('admin.borrowings.index'), params, { preserveState: true, replace: true });
};

watch(search, () => { clearTimeout(searchTimeout); searchTimeout = setTimeout(applyFilters, 300); });
watch(statusFilter, applyFilters);

const formatDate = (dateStr) => {
    if (!dateStr) return '-';
    try { return new Date(dateStr).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }); } catch { return dateStr; }
};

const statusTabs = [
    { value: '', label: 'Semua', count: null },
    { value: 'pending', label: '⏳ Pending', count: props.statusCounts?.pending },
    { value: 'approved', label: '✅ Disetujui', count: null },
    { value: 'active', label: '📋 Aktif', count: props.statusCounts?.active },
    { value: 'overdue', label: '🔴 Terlambat', count: props.statusCounts?.overdue },
    { value: 'returned', label: '✔️ Selesai', count: null },
];
</script>

<template>
    <Head title="Kelola Peminjaman" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-display font-bold text-xl text-gray-800 dark:text-gray-100">Kelola Peminjaman</h2>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                <!-- Status Tabs -->
                <div class="flex flex-wrap gap-2">
                    <button
                        v-for="tab in statusTabs" :key="tab.value"
                        @click="statusFilter = tab.value"
                        :class="['px-4 py-2 text-sm rounded-xl font-medium transition-all flex items-center gap-2', statusFilter === tab.value ? 'bg-sapphire-600 text-white shadow-glow-sapphire' : 'bg-white dark:bg-cosmic-800/60 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-cosmic-700/50 border border-gray-200 dark:border-cosmic-700/50']"
                    >
                        {{ tab.label }}
                        <span v-if="tab.count" class="px-1.5 py-0.5 rounded-full text-[10px]" :class="statusFilter === tab.value ? 'bg-white/20' : 'bg-red-100 text-red-600 dark:bg-red-900/40 dark:text-red-300'">{{ tab.count }}</span>
                    </button>
                </div>

                <!-- Search -->
                <div class="card p-4">
                    <input type="text" v-model="search" placeholder="🔍 Cari kode transaksi atau nama peminjam..." class="input max-w-md" />
                </div>

                <!-- Table -->
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Transaksi</th>
                                <th>Peminjam</th>
                                <th>Barang</th>
                                <th>Tanggal</th>
                                <th class="text-center">Status</th>
                                <th class="text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="b in borrowings.data" :key="b.id">
                                <td>
                                    <p class="font-mono text-xs font-semibold text-sapphire-600 dark:text-sapphire-400">{{ b.transaction_code }}</p>
                                </td>
                                <td>
                                    <p class="font-medium text-gray-800 dark:text-gray-100">{{ b.user?.name }}</p>
                                    <p class="text-xs text-gray-400">{{ b.user?.department || b.user?.email }}</p>
                                </td>
                                <td>
                                    <div class="flex flex-wrap gap-1">
                                        <span v-for="d in b.details?.slice(0, 2)" :key="d.id" class="badge badge-gray text-[10px]">
                                            {{ d.item?.name?.substring(0, 15) }}{{ d.item?.name?.length > 15 ? '...' : '' }} ×{{ d.quantity }}
                                        </span>
                                        <span v-if="b.details?.length > 2" class="badge badge-gray text-[10px]">+{{ b.details.length - 2 }} lainnya</span>
                                    </div>
                                </td>
                                <td class="text-sm text-gray-500 dark:text-gray-400">
                                    <p>{{ formatDate(b.borrow_date) }}</p>
                                    <p class="text-xs text-gray-400">→ {{ formatDate(b.expected_return_date) }}</p>
                                </td>
                                <td class="text-center">
                                    <span :class="['badge', `badge-${b.status_color}`]">{{ b.status_label }}</span>
                                </td>
                                <td class="text-right">
                                    <Link :href="route('admin.borrowings.show', b.id)" class="btn-ghost btn-sm">Detail</Link>
                                </td>
                            </tr>
                            <tr v-if="!borrowings.data?.length">
                                <td colspan="6" class="text-center py-12 text-gray-400 dark:text-gray-500">
                                    <div class="text-4xl mb-2">📋</div>
                                    <p>Tidak ada data peminjaman</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div v-if="borrowings.last_page > 1" class="px-5 py-3 border-t border-gray-100 dark:border-cosmic-700/30 flex items-center justify-between">
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ borrowings.from }}–{{ borrowings.to }} dari {{ borrowings.total }}</p>
                        <div class="flex gap-1">
                            <Link v-for="link in borrowings.links" :key="link.label" :href="link.url" v-html="link.label" :class="['px-3 py-1.5 text-sm rounded-lg transition-colors', link.active ? 'bg-sapphire-600 text-white' : link.url ? 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-cosmic-700/50' : 'text-gray-300 cursor-not-allowed']" :preserve-state="true" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
