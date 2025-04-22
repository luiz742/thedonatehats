<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { defineProps, ref } from 'vue';
import { usePage, router } from '@inertiajs/vue3';

const props = defineProps({
    withdrawal: Object,  // Recebendo o objeto de 'withdrawal' (retirada)
});

const isLoading = ref(false);
const withdrawalStatus = ref(props.withdrawal.status || 'pending');

// Atualizando o status da retirada
const updateWithdrawalStatus = async (status) => {
    isLoading.value = true;
    try {
        // Definindo a URL de aprovação ou rejeição
        const url = status === 'approve'
            ? `/admin/withdrawals/${props.withdrawal.id}/approve`
            : `/admin/withdrawals/${props.withdrawal.id}/reject`;

        // Fazendo a requisição POST para a URL específica
        await router.post(url);
        withdrawalStatus.value = status === 'approve' ? 'approved' : 'rejected';
    } catch (error) {
        console.error('Error updating withdrawal status:', error);
    } finally {
        isLoading.value = false;
    }
};
</script>

<template>
    <AppLayout title="Withdrawal Details">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Withdrawal #{{ props.withdrawal.id }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Withdrawal Information</h3>

                    <div v-if="props.withdrawal">
                        <p class="text-gray-700 dark:text-gray-300"><strong>User:</strong> {{ props.withdrawal.user?.name ?? 'N/A' }}</p>
                        <p class="text-gray-700 dark:text-gray-300"><strong>Amount:</strong> {{ props.withdrawal.amount }}</p>
                        <p class="text-gray-700 dark:text-gray-300"><strong>Wallet:</strong> {{ props.withdrawal.solana_wallet }}</p>
                        <p class="text-gray-700 dark:text-gray-300"><strong>Status:</strong>
                            <span :class="{
                                'text-yellow-500': withdrawalStatus === 'pending',
                                'text-green-500': withdrawalStatus === 'approved',
                                'text-red-500': withdrawalStatus === 'rejected'
                            }">
                                {{ withdrawalStatus }}
                            </span>
                        </p>
                        <p class="text-gray-700 dark:text-gray-300"><strong>Created At:</strong> {{ props.withdrawal.created_at }}</p>
                        <p class="text-gray-700 dark:text-gray-300"><strong>Updated At:</strong> {{ props.withdrawal.updated_at }}</p>

                        <div class="mt-6 flex gap-4">
                            <button @click="updateWithdrawalStatus('approve')" :disabled="isLoading"
                                class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50">
                                Approve
                            </button>

                            <button @click="updateWithdrawalStatus('reject')" :disabled="isLoading"
                                class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50">
                                Reject
                            </button>
                        </div>
                    </div>
                    <div v-else class="text-red-500">No withdrawal information found.</div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
