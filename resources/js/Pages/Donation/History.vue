<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    donations: Array,
    deposits: Array
});
</script>

<template>
    <AppLayout title="Donation History">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Donation History
            </h2>
        </template>

        <div class="py-12 space-y-12">
            <!-- Donations Table -->
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <h3 class="text-lg font-semibold p-4 text-gray-800 dark:text-gray-200">Donations</h3>
                    <table class="border-collapse w-full">
                        <thead>
                            <tr>
                                <th class="p-3 font-bold uppercase bg-gray-200 text-gray-600 border">Amount</th>
                                <th class="p-3 font-bold uppercase bg-gray-200 text-gray-600 border">Wallet Address</th>
                                <th class="p-3 font-bold uppercase bg-gray-200 text-gray-600 border">Expired</th>
                                <th class="p-3 font-bold uppercase bg-gray-200 text-gray-600 border">Donation Date</th>
                                <th class="p-3 font-bold uppercase bg-gray-200 text-gray-600 border">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="donation in donations" :key="donation.id" class="bg-white hover:bg-gray-100">
                                <td class="p-3 text-gray-800 text-center border">${{ donation.amount }}</td>
                                <td class="p-3 text-gray-800 text-center border">{{ donation.wallet_address }}</td>
                                <td class="p-3 text-gray-800 text-center border">{{ donation.expires_at }}</td>
                                <td class="p-3 text-gray-800 text-center border">{{ new Date(donation.created_at).toLocaleString() }}</td>
                                <td class="p-3 text-gray-800 text-center border">
                                    <span :class="{
                                        'bg-green-400': donation.status === 'active',
                                        'bg-yellow-400': donation.status === 'pending',
                                        'bg-green-400': donation.status === 'completed'
                                    }" class="rounded py-1 px-3 text-xs text-dark font-bold">
                                        {{ donation.status.charAt(0).toUpperCase() + donation.status.slice(1) }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Deposits Table -->
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <h3 class="text-lg font-semibold p-4 text-gray-800 dark:text-gray-200">Deposits</h3>
                    <table class="border-collapse w-full">
                        <thead>
                            <tr>
                                <th class="p-3 font-bold uppercase bg-gray-200 text-gray-600 border">From</th>
                                <th class="p-3 font-bold uppercase bg-gray-200 text-gray-600 border">Amount</th>
                                <th class="p-3 font-bold uppercase bg-gray-200 text-gray-600 border">Timestamp</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="deposit in deposits" :key="deposit.transaction_id"
                                class="bg-white hover:bg-gray-100">
                                <td class="p-3 text-gray-800 text-center border">{{ deposit.to }}</td>
                                <td class="p-3 text-gray-800 text-center border">
                                    {{ (deposit.value / Math.pow(10, deposit.token_info.decimals)).toFixed(2) }} {{
                                    deposit.token_info.symbol }}
                                </td>
                                <td class="p-3 text-gray-800 text-center border">
                                    {{ new Date(deposit.block_timestamp).toLocaleString() }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </AppLayout>
</template>
