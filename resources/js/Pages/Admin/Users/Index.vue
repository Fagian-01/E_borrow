<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({ users: Object, filters: Object, roleCounts: Object });

const search = ref(props.filters?.search ?? '');
const roleFilter = ref(props.filters?.role ?? '');
const statusFilter = ref(props.filters?.status ?? '');
let searchTimeout = null;

const applyFilters = () => {
    const params = {};
    if (search.value) params.search = search.value;
    if (roleFilter.value) params.role = roleFilter.value;
    if (statusFilter.value) params.status = statusFilter.value;
    router.get(route('admin.users.index'), params, { preserveState: true, replace: true });
};

watch(search, () => { clearTimeout(searchTimeout); searchTimeout = setTimeout(applyFilters, 300); });
watch([roleFilter, statusFilter], applyFilters);

const toggleTarget = ref(null);
const toggleActive = (user) => {
    toggleTarget.value = user;
};
const doToggle = () => {
    if (toggleTarget.value) {
        router.post(route('admin.users.toggle-active', toggleTarget.value.id), {}, {
            preserveScroll: true,
            onFinish: () => { toggleTarget.value = null; },
        });
    }
};

const roleColors = {
    superadmin: 'badge-gold',
    staff: 'badge-blue',
    user: 'badge-gray',
};

const roleLabels = {
    superadmin: 'Super Admin',
    staff: 'Staff',
    user: 'User',
};
</script>

<template>
    <Head title="Kelola Pengguna" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between w-full">
                <h2 class="font-display font-bold text-xl text-gray-800 dark:text-gray-100">Kelola Pengguna</h2>
                <Link :href="route('admin.users.create')" class="btn-primary btn-sm">+ Tambah User</Link>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                <!-- Role Tabs -->
                <div class="flex flex-wrap gap-2">
                    <button @click="roleFilter = ''" :class="['px-4 py-2 text-sm rounded-xl font-medium transition-all', !roleFilter ? 'bg-sapphire-600 text-white' : 'bg-white dark:bg-cosmic-800/60 text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-cosmic-700/50 hover:bg-gray-100 dark:hover:bg-cosmic-700/50']">
                        Semua ({{ roleCounts?.all }})
                    </button>
                    <button v-for="r in ['superadmin', 'staff', 'user']" :key="r" @click="roleFilter = r" :class="['px-4 py-2 text-sm rounded-xl font-medium transition-all', roleFilter === r ? 'bg-sapphire-600 text-white' : 'bg-white dark:bg-cosmic-800/60 text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-cosmic-700/50 hover:bg-gray-100 dark:hover:bg-cosmic-700/50']">
                        {{ roleLabels[r] }} ({{ roleCounts?.[r] }})
                    </button>
                </div>

                <!-- Filters -->
                <div class="card p-4 flex flex-wrap gap-3">
                    <input type="text" v-model="search" placeholder="🔍 Cari nama, email, atau ID karyawan..." class="input max-w-sm" />
                    <select v-model="statusFilter" class="input max-w-[160px]">
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
                                <th>Pengguna</th>
                                <th>Peran</th>
                                <th>Departemen</th>
                                <th class="text-center">Peminjaman</th>
                                <th class="text-center">Status</th>
                                <th class="text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="user in users.data" :key="user.id">
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold text-white" :class="user.role === 'superadmin' ? 'bg-gold-500' : user.role === 'staff' ? 'bg-sapphire-600' : 'bg-gray-400'">
                                            {{ user.name?.charAt(0)?.toUpperCase() }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-800 dark:text-gray-100">{{ user.name }}</p>
                                            <p class="text-xs text-gray-400">{{ user.email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td><span :class="['badge', roleColors[user.role]]">{{ roleLabels[user.role] }}</span></td>
                                <td class="text-sm text-gray-500 dark:text-gray-400">{{ user.department || '-' }}</td>
                                <td class="text-center">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ user.borrowings_count }}</span>
                                    <span v-if="user.active_borrowings_count" class="badge badge-blue text-[10px] ml-1">{{ user.active_borrowings_count }} aktif</span>
                                </td>
                                <td class="text-center">
                                    <button @click="toggleActive(user)" :class="user.is_active ? 'badge badge-green cursor-pointer hover:opacity-80' : 'badge badge-red cursor-pointer hover:opacity-80'">
                                        {{ user.is_active ? '✓ Aktif' : '✕ Nonaktif' }}
                                    </button>
                                </td>
                                <td class="text-right">
                                    <Link :href="route('admin.users.edit', user.id)" class="btn-ghost btn-sm">✏️</Link>
                                </td>
                            </tr>
                            <tr v-if="!users.data?.length">
                                <td colspan="6" class="text-center py-12 text-gray-400">
                                    <div class="text-4xl mb-2">👥</div>
                                    <p>Tidak ada pengguna ditemukan</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div v-if="users.last_page > 1" class="px-5 py-3 border-t border-gray-100 dark:border-cosmic-700/30 flex items-center justify-between">
                        <p class="text-sm text-gray-500">{{ users.from }}–{{ users.to }} dari {{ users.total }}</p>
                        <div class="flex gap-1">
                            <Link v-for="link in users.links" :key="link.label" :href="link.url" v-html="link.label" :class="['px-3 py-1.5 text-sm rounded-lg transition-colors', link.active ? 'bg-sapphire-600 text-white' : link.url ? 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-cosmic-700/50' : 'text-gray-300 cursor-not-allowed']" :preserve-state="true" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>

    <!-- Toggle Confirm Modal -->
    <Teleport to="body">
        <Transition name="fade">
            <div v-if="toggleTarget" class="fixed inset-0 z-[999] flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="toggleTarget = null"></div>
                <div class="relative bg-white dark:bg-cosmic-800 rounded-2xl shadow-2xl p-6 max-w-md w-full border border-gray-200 dark:border-cosmic-700/50">
                    <h3 class="text-lg font-display font-bold text-gray-800 dark:text-gray-100 mb-2">{{ toggleTarget.is_active ? 'Nonaktifkan' : 'Aktifkan' }} User</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                        Yakin ingin {{ toggleTarget.is_active ? 'menonaktifkan' : 'mengaktifkan' }} user <strong>{{ toggleTarget.name }}</strong>?
                    </p>
                    <div class="flex justify-end gap-3">
                        <button @click="toggleTarget = null" class="btn-ghost">Batal</button>
                        <button @click="doToggle" :class="toggleTarget.is_active ? 'btn-danger' : 'btn-primary'">Ya, Lanjutkan</button>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
