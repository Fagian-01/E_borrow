<script setup>
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;
    nextTick(() => passwordInput.value.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;
    form.reset();
    form.clearErrors();
};
</script>

<template>
    <section class="space-y-6">
        <header>
            <h2 class="text-lg font-display font-medium text-red-600 dark:text-red-400">
                Hapus Akun
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Setelah akun Anda dihapus, semua sumber daya dan data akan dihapus secara permanen. Sebelum menghapus akun Anda, harap unduh data atau informasi apa pun yang ingin Anda simpan.
            </p>
        </header>

        <button class="btn-danger" @click="confirmUserDeletion">
            Hapus Akun Saya
        </button>

        <Teleport to="body">
            <Transition name="fade">
                <div v-if="confirmingUserDeletion" class="fixed inset-0 z-[999] flex items-center justify-center p-4">
                    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="closeModal"></div>
                    <div class="relative bg-white dark:bg-cosmic-800 rounded-2xl shadow-2xl p-6 max-w-lg w-full border border-gray-200 dark:border-cosmic-700/50">
                        <h3 class="text-lg font-display font-bold text-gray-800 dark:text-gray-100 mb-2">
                            Apakah Anda yakin ingin menghapus akun Anda?
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                            Setelah akun Anda dihapus, semua sumber daya dan data akan dihapus secara permanen. Masukkan password Anda untuk mengonfirmasi bahwa Anda ingin menghapus akun Anda secara permanen.
                        </p>

                        <div class="mt-6">
                            <label for="password" class="sr-only">Password</label>
                            <input
                                id="password"
                                ref="passwordInput"
                                v-model="form.password"
                                type="password"
                                class="input w-full md:w-3/4"
                                placeholder="Password"
                                @keyup.enter="deleteUser"
                            />
                            <p v-if="form.errors.password" class="text-sm text-red-600 dark:text-red-400 mt-2">{{ form.errors.password }}</p>
                        </div>

                        <div class="mt-6 flex justify-end gap-3">
                            <button @click="closeModal" class="btn-ghost">Batal</button>
                            <button
                                class="btn-danger"
                                :class="{ 'opacity-50': form.processing }"
                                :disabled="form.processing"
                                @click="deleteUser"
                            >
                                Hapus Akun
                            </button>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </section>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
