<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    category: Object,
});

const isEditing = !!props.category;

const form = useForm({
    name: props.category?.name ?? '',
    description: props.category?.description ?? '',
    icon: props.category?.icon ?? '',
    color: props.category?.color ?? 'blue',
    is_active: props.category?.is_active ?? true,
    sort_order: props.category?.sort_order ?? 0,
});

const emojiOptions = ['📁', '💻', '📽️', '📷', '🌐', '🖨️', '🎤', '🔧', '📱', '🖥️', '⌨️', '🎧', '🔌', '📡', '🧰', '🪛'];

const colorOptions = [
    { value: 'blue', label: 'Biru', class: 'bg-blue-500' },
    { value: 'green', label: 'Hijau', class: 'bg-emerald-500' },
    { value: 'red', label: 'Merah', class: 'bg-red-500' },
    { value: 'yellow', label: 'Kuning', class: 'bg-amber-500' },
    { value: 'purple', label: 'Ungu', class: 'bg-purple-500' },
    { value: 'pink', label: 'Pink', class: 'bg-pink-500' },
    { value: 'indigo', label: 'Indigo', class: 'bg-indigo-500' },
    { value: 'teal', label: 'Teal', class: 'bg-teal-500' },
];

const submit = () => {
    if (isEditing) {
        form.put(route('admin.categories.update', props.category.id));
    } else {
        form.post(route('admin.categories.store'));
    }
};
</script>

<template>
    <Head :title="isEditing ? 'Edit Kategori' : 'Tambah Kategori'" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <Link :href="route('admin.categories.index')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                </Link>
                <h2 class="font-display font-bold text-xl text-gray-800 dark:text-gray-100">
                    {{ isEditing ? 'Edit Kategori' : 'Tambah Kategori Baru' }}
                </h2>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
                <form @submit.prevent="submit" class="card p-6 space-y-6">
                    <!-- Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nama Kategori *</label>
                        <input type="text" v-model="form.name" class="input" placeholder="Contoh: Laptop & Komputer" required />
                        <p v-if="form.errors.name" class="mt-1 text-sm text-red-500">{{ form.errors.name }}</p>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Deskripsi</label>
                        <textarea v-model="form.description" class="input h-24 resize-none" placeholder="Deskripsi singkat tentang kategori ini..."></textarea>
                        <p v-if="form.errors.description" class="mt-1 text-sm text-red-500">{{ form.errors.description }}</p>
                    </div>

                    <!-- Icon Picker -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Ikon</label>
                        <div class="flex flex-wrap gap-2">
                            <button
                                type="button"
                                v-for="emoji in emojiOptions"
                                :key="emoji"
                                @click="form.icon = emoji"
                                :class="[
                                    'w-10 h-10 rounded-xl text-xl flex items-center justify-center transition-all border-2',
                                    form.icon === emoji
                                        ? 'border-sapphire-500 bg-sapphire-50 dark:bg-sapphire-900/30 scale-110'
                                        : 'border-transparent hover:bg-gray-100 dark:hover:bg-cosmic-700/30'
                                ]"
                            >
                                {{ emoji }}
                            </button>
                        </div>
                        <p v-if="form.errors.icon" class="mt-1 text-sm text-red-500">{{ form.errors.icon }}</p>
                    </div>

                    <!-- Color Picker -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Warna Badge</label>
                        <div class="flex flex-wrap gap-2">
                            <button
                                type="button"
                                v-for="c in colorOptions"
                                :key="c.value"
                                @click="form.color = c.value"
                                :class="[
                                    'w-8 h-8 rounded-full transition-all',
                                    c.class,
                                    form.color === c.value
                                        ? 'ring-2 ring-offset-2 ring-sapphire-500 dark:ring-offset-cosmic-800 scale-110'
                                        : 'opacity-60 hover:opacity-100'
                                ]"
                                :title="c.label"
                            ></button>
                        </div>
                    </div>

                    <!-- Sort Order + Active -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Urutan</label>
                            <input type="number" v-model="form.sort_order" class="input" min="0" />
                        </div>
                        <div class="flex items-end pb-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input
                                    type="checkbox"
                                    v-model="form.is_active"
                                    class="w-5 h-5 rounded text-sapphire-600 border-gray-300 dark:border-cosmic-600
                                           focus:ring-sapphire-500 dark:bg-cosmic-700 transition-colors"
                                />
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Aktif</span>
                            </label>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-cosmic-700/30">
                        <Link :href="route('admin.categories.index')" class="btn-ghost">
                            Batal
                        </Link>
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
                            {{ isEditing ? 'Simpan Perubahan' : 'Tambah Kategori' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
