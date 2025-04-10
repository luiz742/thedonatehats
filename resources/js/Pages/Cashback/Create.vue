<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { defineProps, ref } from 'vue';
import { router } from '@inertiajs/vue3';

// Recebe a prop 'donation' que foi passada do controlador
const props = defineProps({
    donation: Object
});

const solanaWalletAddress = ref("");

// Função para enviar a solicitação de cashback
const requestCashback = () => {
    if (solanaWalletAddress.value) {
        router.post(`/cashback/store/${props.donation.id}`, {
            solana_wallet_address: solanaWalletAddress.value
        });
    }
};
</script>

<template>
    <AppLayout title="Request Cashback">
        <template #header>
            <h2 class="font-semibold text-2xl text-gray-900 dark:text-gray-100 leading-tight">
                🤑 Request Cashback
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-900 shadow-xl sm:rounded-lg p-8">
                    <!-- Texto informativo -->
                    <p class="mb-4 text-gray-700 dark:text-gray-300">
                        Your donation of ${{ props.donation.amount }} is now completed! To receive your cashback, enter your Solana wallet address below.
                    </p>

                    <!-- Formulário de entrada -->
                    <form @submit.prevent="requestCashback">
                        <div class="mb-6">
                            <label for="solana_wallet_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Solana Wallet Address
                            </label>
                            <input
                                type="text"
                                v-model="solanaWalletAddress"
                                class="mt-1 p-3 border border-gray-300 dark:border-gray-700 rounded-md w-full bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-green-500"
                                required
                            />
                        </div>

                        <button
                            type="submit"
                            class="w-full py-3 mt-6 rounded-lg text-lg font-semibold transition-all duration-300 bg-green-500 text-white hover:bg-green-600 focus:ring-4 focus:ring-green-300 dark:focus:ring-green-600"
                        >
                            Submit Cashback Request
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

