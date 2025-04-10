<template>
    <AppLayout title="Cashback Status">
        <template #header>
            <h2 class="font-semibold text-2xl text-gray-900 dark:text-gray-100">
                Cashback Status
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-900 shadow-xl rounded-2xl p-8">

                    <p class="text-gray-900 dark:text-gray-100">Your cashback request has the following status:</p>
                    <p class="text-xl font-bold" :class="{
                        'text-green-500': cashback.status === 'completed',
                        'text-yellow-500': cashback.status === 'pending',
                        'text-red-500': cashback.status === 'failed',
                        'text-gray-900': cashback.status !== 'completed' && cashback.status !== 'pending' && cashback.status !== 'failed'
                    }">
                        {{ cashback.status }}
                    </p>

                    <p class="text-gray-900 dark:text-gray-100">Amount: {{ cashback.amount }} SHISHA</p>
                    <p class="text-gray-900 dark:text-gray-100">Solana Wallet Address: {{ cashback.solana_wallet_address }}</p>

                    <button v-if="cashback.status === 'pending'" @click="completeCashback" class="mt-6 w-full py-3 rounded-lg text-lg font-semibold transition-all duration-300 bg-blue-500 text-white hover:bg-blue-400 dark:bg-blue-600 dark:hover:bg-blue-500">
                        Mark as Completed (Admin)
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { defineProps, ref } from 'vue';
import { router } from '@inertiajs/vue3';

// Define a prop 'cashback' which is passed from the controller
const props = defineProps({
    cashback: Object
});

// Use the prop in the component
const cashback = ref(props.cashback);

// Function to complete the cashback (Admin action)
const completeCashback = () => {
    router.post(`/cashback/complete/${cashback.value.id}`);
};
</script>
