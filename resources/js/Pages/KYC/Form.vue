<script setup>
import { useForm } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import KYCForm from '@/Pages/KYC/Partials/KYCForm.vue';

const props = defineProps({
    kyc: Object,
});
</script>

<template>
    <AppLayout title="KYC Verification">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                KYC Verification
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-4">
                        Complete your KYC
                    </h3>
                    <!-- Importando o KYCForm e passando as props -->
                    <KYCForm :kyc="kyc" @submit="submit" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
