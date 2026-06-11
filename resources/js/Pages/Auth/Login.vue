<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Login" />

        <!-- Header -->
        <div class="text-center mb-6">
            <h1 class="text-2xl font-display font-bold text-gray-800 dark:text-white">Selamat Datang</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Masuk ke akun E-Borrow Anda</p>
        </div>

        <!-- Status Message -->
        <div v-if="status" class="mb-4 px-4 py-3 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 text-sm text-emerald-600 dark:text-emerald-400 font-medium">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-5">
            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Email
                </label>
                <input
                    id="email"
                    type="email"
                    class="input"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="nama@email.com"
                />
                <p v-if="form.errors.email" class="mt-1.5 text-sm text-red-500 dark:text-red-400">
                    {{ form.errors.email }}
                </p>
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Password
                </label>
                <input
                    id="password"
                    type="password"
                    class="input"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••"
                />
                <p v-if="form.errors.password" class="mt-1.5 text-sm text-red-500 dark:text-red-400">
                    {{ form.errors.password }}
                </p>
            </div>

            <!-- Remember + Forgot -->
            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input
                        type="checkbox"
                        v-model="form.remember"
                        class="w-4 h-4 rounded text-sapphire-600 border-gray-300 dark:border-cosmic-600
                               focus:ring-sapphire-500 dark:focus:ring-sapphire-400
                               dark:bg-cosmic-700 transition-colors"
                    />
                    <span class="text-sm text-gray-600 dark:text-gray-400">Ingat saya</span>
                </label>

                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-sm text-sapphire-600 dark:text-sapphire-400 hover:text-sapphire-700 dark:hover:text-sapphire-300 transition-colors"
                >
                    Lupa password?
                </Link>
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
                {{ form.processing ? 'Memproses...' : 'Masuk' }}
            </button>

            <!-- Register Link -->
            <p class="text-center text-sm text-gray-500 dark:text-gray-400">
                Belum punya akun?
                <Link
                    :href="route('register')"
                    class="text-sapphire-600 dark:text-sapphire-400 font-semibold hover:text-sapphire-700 dark:hover:text-sapphire-300 transition-colors"
                >
                    Daftar di sini
                </Link>
            </p>
        </form>
    </GuestLayout>
</template>
