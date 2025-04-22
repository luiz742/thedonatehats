<script setup>
import { computed, ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';

const solanaWallet = ref('');
const selectedDonationId = ref(null); // Inicializa o valor com null
const showModal = ref(false);

// Função para requisitar o saque
const requestWithdrawal = async () => {
    if (!selectedDonationId.value || !solanaWallet.value) {
        alert("Please select a donation and provide a wallet address.");
        return;
    }

    try {
        const response = await axios.post('/withdrawal/request', {
            solana_wallet: solanaWallet.value,
            donation_id: selectedDonationId.value
        });

        alert('Withdrawal request submitted!');
        showModal.value = false; // Fecha o modal após o pedido de retirada
        solanaWallet.value = ''; // Limpa o campo da carteira
    } catch (err) {
        alert('Failed to request withdrawal');
    }
};

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
            !donation.withdrawal && // ainda não retirado
            donation.shisha_price &&
            donation.amount
        ) {
            total += donation.amount / donation.shisha_price;
        }
    });

    return total.toFixed(2);
});

const openWithdrawalModal = (donationId) => {
    selectedDonationId.value = donationId;
    showModal.value = true;
};

</script>

<template>
    <AppLayout title="Donation History">
        <template #header>
            <h2 class="font-semibold text-xl md:text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                Shisha Coin Balance: {{ totalShishaBalance }}
            </h2>

            <h4 style="color: yellow">SHISHA withdrawal request is subject to KYC approval.</h4>
        </template>

        <!-- Modal for Solana Wallet -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white dark:bg-gray-900 p-6 rounded-xl shadow-md max-w-sm w-full">
                <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-100">Enter your Solana Wallet</h3>
                <input v-model="solanaWallet" type="text" placeholder="Solana Wallet Address"
                    class="w-full p-2 border rounded mb-4 dark:bg-gray-800 dark:text-white" />
                <div class="flex justify-end gap-2">
                    <button @click="showModal = false"
                        class="px-4 py-2 bg-gray-300 dark:bg-gray-700 text-black dark:text-white rounded hover:bg-gray-400 dark:hover:bg-gray-600">
                        Cancel
                    </button>
                    <button @click="requestWithdrawal"
                        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                        Confirm
                    </button>
                </div>
            </div>
        </div>

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
                                    <th class="p-3 font-bold uppercase bg-gray-200 text-gray-600 border text-left">
                                        Action
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
                                    <td class="p-3 text-gray-800 border whitespace-nowrap">
                                        <div v-if="donation.status === 'completed' && !donation.withdrawal">
                                            <button @click="openWithdrawalModal(donation.id)">Request
                                                Withdrawal</button>

                                        </div>
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
