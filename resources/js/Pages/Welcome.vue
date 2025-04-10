<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});

function handleImageError() {
    document.getElementById('screenshot-container')?.classList.add('hidden');
    document.getElementById('docs-card')?.classList.add('row-span-1');
    document.getElementById('docs-card-content')?.classList.add('flex-row');
    document.getElementById('background')?.classList.add('hidden');
}
</script>

<template>
    <Head title="Welcome" />
    <div class="bg-gray-100 dark:bg-gray-900 text-black dark:text-white">
        <div class="relative min-h-screen flex flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white">
            <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">
                <header class="grid grid-cols-2 items-center gap-2 py-10 lg:grid-cols-3">
                    <div class="flex lg:justify-center lg:col-start-2">
                        <img src="https://theblackhats.org/wp-content/uploads/2025/03/logo-the-black-hats.png" alt="Logo The Black Hats" class="w-24 h-auto">
                    </div>
                    <nav v-if="canLogin" class="-mx-3 flex flex-1 justify-end">
                        <Link v-if="$page.props.auth.user" :href="route('dashboard')" class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-gray-300 dark:focus-visible:ring-black">
                            Dashboard
                        </Link>

                        <template v-else>
                            <Link :href="route('login')" class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-gray-300 dark:focus-visible:ring-black">
                                Log in
                            </Link>

                            <Link v-if="canRegister" :href="route('register')" class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-gray-300 dark:focus-visible:ring-black">
                                Register
                            </Link>
                        </template>
                    </nav>
                </header>

                <main class="mt-6">
                    <div class="grid gap-6 lg:grid-cols-2 lg:gap-8">
                    </div>
                </main>

                <footer class="py-16 text-center text-sm text-black dark:text-white/70">
                    © Copyright by The Black Hats – All rights reserved.
                </footer>
            </div>
        </div>
    </div>
</template>
