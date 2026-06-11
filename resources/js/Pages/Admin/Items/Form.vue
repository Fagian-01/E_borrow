<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    item: Object,
    categories: Array,
});

const isEditing = !!props.item;
const imagePreview = ref(props.item?.image ? `/storage/${props.item.image}` : null);

const form = useForm({
    category_id: props.item?.category_id ?? '',
    name: props.item?.name ?? '',
    description: props.item?.description ?? '',
    specifications: props.item?.specifications ?? '',
    image: null,
    total_stock: props.item?.total_stock ?? 0,
    max_borrow_days: props.item?.max_borrow_days ?? 7,
    max_qty_per_user: props.item?.max_qty_per_user ?? 1,
    fine_per_day: props.item?.fine_per_day ?? 10000,
    is_active: props.item?.is_active ?? true,
    requires_approval: props.item?.requires_approval ?? true,
});

const handleImageChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.image = file;
        imagePreview.value = URL.createObjectURL(file);
    }
};

const removeImage = () => {
    form.image = null;
    imagePreview.value = null;
};

const submit = () => {
    if (isEditing) {
        // Use POST with _method=PUT for file upload support
        form.post(route('admin.items.update', props.item.id), {
            headers: { 'X-HTTP-Method-Override': 'PUT' },
            forceFormData: true,
            _method: 'put',
        });
    } else {
        form.post(route('admin.items.store'));
    }
};
</script>

<template>
    <Head :title="isEditing ? 'Edit Barang' : 'Tambah Barang'" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <Link :href="route('admin.items.index')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                </Link>
                <h2 class="font-display font-bold text-xl text-gray-800 dark:text-gray-100">
                    {{ isEditing ? 'Edit Barang' : 'Tambah Barang Baru' }}
                </h2>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Basic Info -->
                    <div class="card p-6 space-y-5">
                        <h3 class="font-display font-semibold text-gray-800 dark:text-gray-100">Informasi Dasar</h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nama Barang *</label>
                                <input type="text" v-model="form.name" class="input" placeholder="Contoh: Laptop Dell Latitude 5420" required />
                                <p v-if="form.errors.name" class="mt-1 text-sm text-red-500">{{ form.errors.name }}</p>
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Kategori *</label>
                                <select v-model="form.category_id" class="input" required>
                                    <option value="" disabled>Pilih Kategori</option>
                                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                                        {{ cat.icon }} {{ cat.name }}
                                    </option>
                                </select>
                                <p v-if="form.errors.category_id" class="mt-1 text-sm text-red-500">{{ form.errors.category_id }}</p>
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Deskripsi</label>
                                <textarea v-model="form.description" class="input h-24 resize-none" placeholder="Deskripsi singkat tentang barang..."></textarea>
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Spesifikasi</label>
                                <textarea v-model="form.specifications" class="input h-20 resize-none" placeholder="Spesifikasi teknis (opsional)..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Image -->
                    <div class="card p-6 space-y-4">
                        <h3 class="font-display font-semibold text-gray-800 dark:text-gray-100">Gambar</h3>
                        <div class="flex items-start gap-4">
                            <div class="w-24 h-24 rounded-xl bg-gray-100 dark:bg-cosmic-700/50 flex items-center justify-center overflow-hidden border-2 border-dashed border-gray-300 dark:border-cosmic-600">
                                <img v-if="imagePreview" :src="imagePreview" class="w-full h-full object-cover" />
                                <span v-else class="text-3xl">📦</span>
                            </div>
                            <div class="flex-1">
                                <input type="file" @change="handleImageChange" accept="image/jpeg,image/png,image/webp" class="input text-sm" />
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">JPG, PNG, atau WebP. Maks 2MB.</p>
                                <button v-if="imagePreview" type="button" @click="removeImage" class="text-red-500 text-xs mt-1 hover:underline">Hapus gambar</button>
                                <p v-if="form.errors.image" class="mt-1 text-sm text-red-500">{{ form.errors.image }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Stock & Rules -->
                    <div class="card p-6 space-y-5">
                        <h3 class="font-display font-semibold text-gray-800 dark:text-gray-100">Stok & Aturan</h3>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Total Stok *</label>
                                <input type="number" v-model="form.total_stock" class="input" min="0" required />
                                <p v-if="form.errors.total_stock" class="mt-1 text-sm text-red-500">{{ form.errors.total_stock }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Maks Hari *</label>
                                <input type="number" v-model="form.max_borrow_days" class="input" min="1" max="365" required />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Maks Qty/User *</label>
                                <input type="number" v-model="form.max_qty_per_user" class="input" min="1" required />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Denda/Hari (Rp) *</label>
                                <input type="number" v-model="form.fine_per_day" class="input" min="0" step="1000" required />
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-6 pt-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" v-model="form.is_active"
                                    class="w-5 h-5 rounded text-sapphire-600 border-gray-300 dark:border-cosmic-600 focus:ring-sapphire-500 dark:bg-cosmic-700" />
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Aktif (dapat dipinjam)</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" v-model="form.requires_approval"
                                    class="w-5 h-5 rounded text-sapphire-600 border-gray-300 dark:border-cosmic-600 focus:ring-sapphire-500 dark:bg-cosmic-700" />
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Perlu persetujuan admin</span>
                            </label>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-3">
                        <Link :href="route('admin.items.index')" class="btn-ghost">Batal</Link>
                        <button
                            type="submit"
                            class="btn-primary"
                            :class="{ 'opacity-50': form.processing }"
                            :disabled="form.processing"
                        >
                            <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            {{ isEditing ? 'Simpan Perubahan' : 'Tambah Barang' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
