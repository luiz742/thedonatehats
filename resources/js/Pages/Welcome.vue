<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});
</script>

<template>

    <Head title="Welcome to The Black Hats" />
    <div class="min-h-screen flex flex-col items-center justify-center bg-black text-white px-6">
        <!-- Logo -->
        <div class="mb-12">
            <div>
                <img src="https://theblackhats.org/wp-content/uploads/2025/03/logo-the-black-hats.png"
                    alt="Logo The Black Hats" class="w-32 dark:hidden" />
                <img src="https://theblackhats.org/wp-content/uploads/2025/03/white-logo.png" alt="Logo The Black Hats"
                    class="w-32 hidden dark:block" />
            </div>
        </div>

        <!-- Menu -->
        <div class="flex space-x-6">
            <Link v-if="canLogin" :href="route($page.props.auth.user ? 'kyc.index' : 'login')"
                class="bg-white text-black font-semibold px-5 py-2 rounded hover:bg-gray-200 transition">
            {{ $page.props.auth.user ? 'Dashboard' : 'Login' }}
            </Link>

            <Link v-if="!$page.props.auth.user && canRegister" :href="route('register')"
                class="border border-white text-white font-semibold px-5 py-2 rounded hover:bg-white hover:text-black transition">
            Register
            </Link>
        </div>

        <!-- Footer -->
        <footer class="mt-16 text-sm text-gray-400">
            © The Black Hats – All rights reserved.
        </footer>
    </div>
</template>
