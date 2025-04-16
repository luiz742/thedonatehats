<script setup>
import { computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    donations: {
        type: Array,
        required: true
    },
    kyc: Object

});

// Calcula o saldo total em SHISHA com base no valor salvo na donation
const totalShishaBalance = computed(() => {
    let total = 0;

    props.donations.forEach(donation => {
        if (
            donation.status === 'completed' &&
            donation.shisha_price &&
            donation.amount
        ) {
            total += donation.amount / donation.shisha_price;
        }
    });

    return total.toFixed(2);
});
</script>


<template>
    <AppLayout title="Donation History">
        <template #header>
            <h2 class="font-semibold text-xl md:text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                Shisha Coin Balance: {{ totalShishaBalance }}
            </h2>
            <div v-if="kyc && kyc.status != 'approved'" class="text-red-500">
                <h4 style="color: yellow">SHISHA withdrawal request is subject to KYC approval.</h4>
            </div>
            <div v-if="kyc && kyc.status === 'approved'">
                <button
                    class="bg-gradient-to-r mt-2 from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-semibold px-6 rounded-xl shadow-md hover:shadow-lg transition duration-300 ease-in-out">
                    Request Withdrawal
                </button>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg md:text-xl font-semibold mb-4 text-gray-800 dark:text-gray-200">Donation History
                    </h3>
                    <div class="w-full overflow-x-auto">
                        <table class="min-w-full text-sm md:text-base border-collapse">
                            <thead>
                                <tr>
                                    <th class="p-3 font-bold uppercase bg-gray-200 text-gray-600 border text-left">
                                        Amount (SHISHA)
                                    </th>
                                    <th class="p-3 font-bold uppercase bg-gray-200 text-gray-600 border text-left">
                                        Donation Amount (USD)
                                    </th>
                                    <th class="p-3 font-bold uppercase bg-gray-200 text-gray-600 border text-left">
                                        Date
                                    </th>
                                    <th class="p-3 font-bold uppercase bg-gray-200 text-gray-600 border text-left">
                                        Status
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="donation in donations" :key="donation.id" class="bg-white hover:bg-gray-100">
                                    <td class="p-3 text-gray-800 border whitespace-nowrap">
                                        {{ donation.shisha_price ? (donation.amount / donation.shisha_price).toFixed(2)
                                            : 'N/A'
                                        }} SHISHA
                                    </td>
                                    <td class="p-3 text-gray-800 border whitespace-nowrap">
                                        ${{ donation.amount }}
                                    </td>
                                    <td class="p-3 text-gray-800 border whitespace-nowrap">
                                        {{ new Date(donation.created_at).toLocaleString() }}
                                    </td>
                                    <td class="p-3 text-gray-800 border">
                                        <span :class="{
                                            'bg-green-400': donation.status === 'completed',
                                            'bg-yellow-400': donation.status === 'pending',
                                            'bg-red-400': donation.status === 'deleted'
                                        }"
                                            class="rounded py-1 px-3 text-xs md:text-sm text-dark font-bold inline-block">
                                            {{ donation.status.charAt(0).toUpperCase() + donation.status.slice(1) }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
