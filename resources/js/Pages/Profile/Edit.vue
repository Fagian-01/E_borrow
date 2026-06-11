<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: Boolean,
    status: String,
});
</script>

<template>
    <Head title="Profil Saya" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-display font-bold text-xl text-gray-800 dark:text-gray-100">
                Profil Saya
            </h2>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Left Sidebar for Profile Overview (Optional, but looks nice) -->
                    <div class="md:col-span-1 space-y-6">
                        <div class="card p-6 text-center">
                            <div class="w-24 h-24 mx-auto rounded-full bg-gradient-to-br from-sapphire-500 to-sapphire-700 flex items-center justify-center text-white text-3xl font-bold shadow-lg mb-4">
                                {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                            </div>
                            <h3 class="font-display font-bold text-lg text-gray-800 dark:text-gray-100">{{ $page.props.auth.user.name }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $page.props.auth.user.email }}</p>
                            <span class="badge badge-gold mt-3">{{ $page.props.auth.user.role }}</span>
                        </div>
                    </div>

                    <!-- Right Forms -->
                    <div class="md:col-span-2 space-y-6">
                        <!-- Update Profile Info Card -->
                        <div class="card p-6 md:p-8">
                            <UpdateProfileInformationForm
                                :must-verify-email="mustVerifyEmail"
                                :status="status"
                                class="max-w-xl"
                            />
                        </div>

                        <!-- Update Password Card -->
                        <div class="card p-6 md:p-8">
                            <UpdatePasswordForm class="max-w-xl" />
                        </div>

                        <!-- Danger Zone -->
                        <div class="card p-6 md:p-8 border-red-200 dark:border-red-900/30">
                            <DeleteUserForm class="max-w-xl" />
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
