<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({ user: Object });
const isEditing = !!props.user;

const form = useForm({
    name: props.user?.name ?? '',
    email: props.user?.email ?? '',
    password: '',
    password_confirmation: '',
    role: props.user?.role ?? 'user',
    phone: props.user?.phone ?? '',
    department: props.user?.department ?? '',
    employee_id: props.user?.employee_id ?? '',
    is_active: props.user?.is_active ?? true,
});

const submit = () => {
    if (isEditing) {
        form.put(route('admin.users.update', props.user.id));
    } else {
        form.post(route('admin.users.store'));
    }
};

const roles = [
    { value: 'superadmin', label: 'Super Admin', desc: 'Akses penuh ke seluruh sistem' },
    { value: 'staff', label: 'Staff', desc: 'Kelola inventaris dan peminjaman' },
    { value: 'user', label: 'User', desc: 'Pinjam dan kembalikan barang' },
];
</script>

<template>
    <Head :title="isEditing ? 'Edit User' : 'Tambah User'" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <Link :href="route('admin.users.index')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                </Link>
                <h2 class="font-display font-bold text-xl text-gray-800 dark:text-gray-100">{{ isEditing ? 'Edit User' : 'Tambah User Baru' }}</h2>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Identity -->
                    <div class="card p-6 space-y-4">
                        <h3 class="font-display font-semibold text-gray-800 dark:text-gray-100">Identitas</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nama *</label>
                                <input type="text" v-model="form.name" class="input" required />
                                <p v-if="form.errors.name" class="mt-1 text-sm text-red-500">{{ form.errors.name }}</p>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Email *</label>
                                <input type="email" v-model="form.email" class="input" required />
                                <p v-if="form.errors.email" class="mt-1 text-sm text-red-500">{{ form.errors.email }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Telepon</label>
                                <input type="text" v-model="form.phone" class="input" placeholder="08xxxxxxxxxx" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">ID Karyawan</label>
                                <input type="text" v-model="form.employee_id" class="input" placeholder="EMP-001" />
                                <p v-if="form.errors.employee_id" class="mt-1 text-sm text-red-500">{{ form.errors.employee_id }}</p>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Departemen</label>
                                <input type="text" v-model="form.department" class="input" placeholder="IT, HR, Finance, dll." />
                            </div>
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="card p-6 space-y-4">
                        <h3 class="font-display font-semibold text-gray-800 dark:text-gray-100">
                            {{ isEditing ? 'Ubah Password (opsional)' : 'Password *' }}
                        </h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Password {{ isEditing ? '' : '*' }}</label>
                                <input type="password" v-model="form.password" class="input" :required="!isEditing" placeholder="Min 8 karakter" />
                                <p v-if="form.errors.password" class="mt-1 text-sm text-red-500">{{ form.errors.password }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Konfirmasi {{ isEditing ? '' : '*' }}</label>
                                <input type="password" v-model="form.password_confirmation" class="input" :required="!isEditing" />
                            </div>
                        </div>
                    </div>

                    <!-- Role & Status -->
                    <div class="card p-6 space-y-4">
                        <h3 class="font-display font-semibold text-gray-800 dark:text-gray-100">Peran & Status</h3>
                        <div class="space-y-2">
                            <label v-for="r in roles" :key="r.value" class="flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer transition-all" :class="form.role === r.value ? 'border-sapphire-500 bg-sapphire-50 dark:bg-sapphire-900/20' : 'border-transparent hover:bg-gray-50 dark:hover:bg-cosmic-800/30'">
                                <input type="radio" v-model="form.role" :value="r.value" class="text-sapphire-600 focus:ring-sapphire-500" />
                                <div>
                                    <p class="font-medium text-gray-800 dark:text-gray-100">{{ r.label }}</p>
                                    <p class="text-xs text-gray-400">{{ r.desc }}</p>
                                </div>
                            </label>
                        </div>
                        <p v-if="form.errors.role" class="text-sm text-red-500">{{ form.errors.role }}</p>

                        <div class="pt-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" v-model="form.is_active" class="w-5 h-5 rounded text-sapphire-600 border-gray-300 dark:border-cosmic-600 focus:ring-sapphire-500 dark:bg-cosmic-700" />
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Akun Aktif</span>
                            </label>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-3">
                        <Link :href="route('admin.users.index')" class="btn-ghost">Batal</Link>
                        <button type="submit" class="btn-primary" :class="{ 'opacity-50': form.processing }" :disabled="form.processing">
                            <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            {{ isEditing ? 'Simpan Perubahan' : 'Tambah User' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
