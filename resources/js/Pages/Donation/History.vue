<script setup>
import { ref, onMounted } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import axios from 'axios';

defineProps({
    donations: Array,
});
</script>

<template>
    <AppLayout title="Donation History">
        <template #header>
            <h2 class="font-semibold text-xl md:text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                Donation History
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-12">
                <!-- Donations Table -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg md:text-xl font-semibold mb-4 text-gray-800 dark:text-gray-200">Donations</h3>
                    <div class="w-full overflow-x-auto">
                        <table class="min-w-full text-sm md:text-base border-collapse">
                            <thead>
                                <tr>
                                    <th class="p-3 font-bold uppercase bg-gray-200 text-gray-600 border text-left">Amount</th>
                                    <th class="p-3 font-bold uppercase bg-gray-200 text-gray-600 border text-left">Wallet</th>
                                    <th class="p-3 font-bold uppercase bg-gray-200 text-gray-600 border text-left">Expired</th>
                                    <th class="p-3 font-bold uppercase bg-gray-200 text-gray-600 border text-left">Date</th>
                                    <th class="p-3 font-bold uppercase bg-gray-200 text-gray-600 border text-left">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="donation in donations" :key="donation.id" class="bg-white hover:bg-gray-100">
                                    <td class="p-3 text-gray-800 border whitespace-nowrap">${{ donation.amount }}</td>
                                    <td class="p-3 text-gray-800 border break-all">{{ donation.wallet_address }}</td>
                                    <td class="p-3 text-gray-800 border whitespace-nowrap">{{ donation.expires_at }}</td>
                                    <td class="p-3 text-gray-800 border whitespace-nowrap">
                                        {{ new Date(donation.created_at).toLocaleString() }}
                                    </td>
                                    <td class="p-3 text-gray-800 border">
                                        <span
                                            :class="{
                                                'bg-green-400': donation.status === 'active' || donation.status === 'completed',
                                                'bg-yellow-400': donation.status === 'pending'
                                            }"
                                            class="rounded py-1 px-3 text-xs md:text-sm text-dark font-bold inline-block"
                                        >
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
