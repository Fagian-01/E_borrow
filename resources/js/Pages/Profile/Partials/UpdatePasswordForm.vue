<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-display font-medium text-gray-900 dark:text-gray-100">
                Ubah Password
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Pastikan akun Anda menggunakan password yang panjang dan acak agar tetap aman.
            </p>
        </header>

        <form @submit.prevent="updatePassword" class="mt-6 space-y-6">
            <div>
                <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password Saat Ini</label>
                <input
                    id="current_password"
                    ref="currentPasswordInput"
                    v-model="form.current_password"
                    type="password"
                    class="input mt-1 w-full"
                    autocomplete="current-password"
                />
                <p v-if="form.errors.current_password" class="text-sm text-red-600 dark:text-red-400 mt-2">{{ form.errors.current_password }}</p>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password Baru</label>
                <input
                    id="password"
                    ref="passwordInput"
                    v-model="form.password"
                    type="password"
                    class="input mt-1 w-full"
                    autocomplete="new-password"
                />
                <p v-if="form.errors.password" class="text-sm text-red-600 dark:text-red-400 mt-2">{{ form.errors.password }}</p>
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Konfirmasi Password Baru</label>
                <input
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="input mt-1 w-full"
                    autocomplete="new-password"
                />
                <p v-if="form.errors.password_confirmation" class="text-sm text-red-600 dark:text-red-400 mt-2">{{ form.errors.password_confirmation }}</p>
            </div>

            <div class="flex items-center gap-4">
                <button type="submit" class="btn-primary" :disabled="form.processing">
                    Simpan Password
                </button>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p v-if="form.recentlySuccessful" class="text-sm text-green-600 dark:text-green-400">Tersimpan.</p>
                </Transition>
            </div>
        </form>
    </section>
</template>
