<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    phone: '',
    department: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Daftar Akun" />

        <!-- Header -->
        <div class="text-center mb-6">
            <h1 class="text-2xl font-display font-bold text-gray-800 dark:text-white">Buat Akun Baru</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Daftar untuk mulai meminjam peralatan</p>
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Nama Lengkap
                </label>
                <input id="name" type="text" class="input" v-model="form.name" required autofocus autocomplete="name" placeholder="John Doe" />
                <p v-if="form.errors.name" class="mt-1 text-sm text-red-500 dark:text-red-400">{{ form.errors.name }}</p>
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Email
                </label>
                <input id="email" type="email" class="input" v-model="form.email" required autocomplete="username" placeholder="nama@email.com" />
                <p v-if="form.errors.email" class="mt-1 text-sm text-red-500 dark:text-red-400">{{ form.errors.email }}</p>
            </div>

            <!-- Phone + Department -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        No. Telepon
                    </label>
                    <input id="phone" type="tel" class="input" v-model="form.phone" placeholder="08xx" />
                    <p v-if="form.errors.phone" class="mt-1 text-sm text-red-500 dark:text-red-400">{{ form.errors.phone }}</p>
                </div>
                <div>
                    <label for="department" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Departemen
                    </label>
                    <input id="department" type="text" class="input" v-model="form.department" placeholder="IT, HR, dll" />
                    <p v-if="form.errors.department" class="mt-1 text-sm text-red-500 dark:text-red-400">{{ form.errors.department }}</p>
                </div>
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Password
                </label>
                <input id="password" type="password" class="input" v-model="form.password" required autocomplete="new-password" placeholder="Min. 8 karakter" />
                <p v-if="form.errors.password" class="mt-1 text-sm text-red-500 dark:text-red-400">{{ form.errors.password }}</p>
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Konfirmasi Password
                </label>
                <input id="password_confirmation" type="password" class="input" v-model="form.password_confirmation" required autocomplete="new-password" placeholder="Ulangi password" />
                <p v-if="form.errors.password_confirmation" class="mt-1 text-sm text-red-500 dark:text-red-400">{{ form.errors.password_confirmation }}</p>
            </div>

            <!-- Submit -->
            <button
                type="submit"
                class="btn-primary w-full justify-center"
                :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                :disabled="form.processing"
            >
                <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                {{ form.processing ? 'Memproses...' : 'Daftar Sekarang' }}
            </button>

            <!-- Login Link -->
            <p class="text-center text-sm text-gray-500 dark:text-gray-400">
                Sudah punya akun?
                <Link
                    :href="route('login')"
                    class="text-sapphire-600 dark:text-sapphire-400 font-semibold hover:text-sapphire-700 dark:hover:text-sapphire-300 transition-colors"
                >
                    Login di sini
                </Link>
            </p>
        </form>
    </GuestLayout>
</template>
