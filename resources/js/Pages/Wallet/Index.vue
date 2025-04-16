<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
defineProps({
    wallets: Array
})
</script>

<template>
    <AppLayout title="Minhas Carteiras">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Carteiras e Saldos
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div v-for="wallet in wallets" :key="wallet.id"
                    class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6 transition-colors duration-300">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                        <div>
                            <p class="text-lg font-bold text-gray-800 dark:text-gray-100">
                                Carteira: {{ wallet.address }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Chave Pública: {{ wallet.public_key }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Saldo USDT</p>
                            <p class="text-2xl font-semibold text-green-600 dark:text-green-400">
                                {{ wallet.balance?.toFixed(2) }} USDT
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
