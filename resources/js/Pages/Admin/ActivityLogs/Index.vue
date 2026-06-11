<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({ logs: Object, filters: Object, actionTypes: Array });

const search = ref(props.filters?.search ?? '');
const actionFilter = ref(props.filters?.action ?? '');
let searchTimeout = null;

const applyFilters = () => {
    const params = {};
    if (search.value) params.search = search.value;
    if (actionFilter.value) params.action = actionFilter.value;
    router.get(route('admin.activity-logs.index'), params, { preserveState: true, replace: true });
};

watch(search, () => { clearTimeout(searchTimeout); searchTimeout = setTimeout(applyFilters, 300); });
watch(actionFilter, applyFilters);

const actionIcons = {
    'borrowing_submitted': '📝',
    'borrowing_approved': '✅',
    'borrowing_rejected': '❌',
    'borrowing_handed_over': '📦',
    'borrowing_returned': '🔄',
    'borrowing_cancelled': '🚫',
    'item_created': '➕',
    'item_updated': '✏️',
    'item_deleted': '🗑️',
    'category_created': '📁',
    'category_updated': '✏️',
    'user_created': '👤',
    'user_updated': '👤',
    'user_status_toggled': '🔄',
    'profile_updated': '👤',
    'account_deleted': '🗑️',
};

const actionLabels = {
    'borrowing_submitted': 'Pengajuan',
    'borrowing_approved': 'Persetujuan',
    'borrowing_rejected': 'Penolakan',
    'borrowing_handed_over': 'Serah Terima',
    'borrowing_returned': 'Pengembalian',
    'borrowing_cancelled': 'Pembatalan',
    'item_created': 'Item Dibuat',
    'item_updated': 'Item Diubah',
    'item_deleted': 'Item Dihapus',
    'category_created': 'Kategori Dibuat',
    'category_updated': 'Kategori Diubah',
    'user_created': 'User Dibuat',
    'user_updated': 'User Diubah',
    'user_status_toggled': 'Status User',
    'profile_updated': 'Profil Diubah',
    'account_deleted': 'Akun Dihapus',
};
</script>

<template>
    <Head title="Log Aktivitas" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-display font-bold text-xl text-gray-800 dark:text-gray-100">📋 Log Aktivitas</h2>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                <!-- Filters -->
                <div class="card p-4 flex flex-wrap gap-3">
                    <input type="text" v-model="search" placeholder="🔍 Cari aktivitas..." class="input max-w-sm" />
                    <select v-model="actionFilter" class="input max-w-[220px]">
                        <option value="">Semua Aksi</option>
                        <option v-for="a in actionTypes" :key="a" :value="a">{{ actionLabels[a] || a }}</option>
                    </select>
                </div>

                <!-- Timeline -->
                <div class="space-y-2">
                    <div v-for="log in logs.data" :key="log.id" class="card p-4 flex items-start gap-4 hover:border-sapphire-500/30 transition-colors">
                        <div class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-cosmic-700/50 flex items-center justify-center text-lg flex-shrink-0">
                            {{ actionIcons[log.action] || '📌' }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ log.description }}</p>
                            <div class="flex flex-wrap items-center gap-2 mt-1">
                                <span v-if="log.user" class="text-xs text-gray-400">oleh <strong class="text-gray-600 dark:text-gray-300">{{ log.user.name }}</strong></span>
                                <span class="badge badge-gray text-[10px]">{{ actionLabels[log.action] || log.action }}</span>
                                <span v-if="log.model_type" class="badge badge-blue text-[10px]">{{ log.model_type }} #{{ log.model_id }}</span>
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-xs text-gray-400 whitespace-nowrap">{{ log.created_at }}</p>
                            <p class="text-[10px] text-gray-300 dark:text-gray-600 font-mono">{{ log.ip_address }}</p>
                        </div>
                    </div>

                    <div v-if="!logs.data?.length" class="card p-12 text-center">
                        <div class="text-5xl mb-3">📋</div>
                        <p class="text-gray-500 dark:text-gray-400 text-lg">Belum ada aktivitas</p>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="logs.last_page > 1" class="flex justify-center gap-1">
                    <Link v-for="link in logs.links" :key="link.label" :href="link.url" v-html="link.label" :class="['px-3 py-2 text-sm rounded-lg transition-colors', link.active ? 'bg-sapphire-600 text-white' : link.url ? 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-cosmic-700/50' : 'text-gray-300 cursor-not-allowed']" :preserve-state="true" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
