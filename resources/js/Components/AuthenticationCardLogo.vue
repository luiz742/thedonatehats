<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <Link :href="'/'">
    <img src="https://theblackhats.org/wp-content/uploads/2025/03/logo-the-black-hats.png" alt="Logo The Black Hats"
        class="w-24 h-auto dark:hidden" />
    <img src="https://theblackhats.org/wp-content/uploads/2025/03/white-logo.png" alt="Logo The Black Hats"
        class="w-24 h-auto hidden dark:block" />
    </Link>
</template>
