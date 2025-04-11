<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { defineProps, ref, watch, onBeforeUnmount } from 'vue';
import axios from 'axios';

const props = defineProps({
    deposits: Array,
    donations: Array,
});

const selectedAmount = ref(null);
const amounts = [100, 500, 1000, 2000];
const showModal = ref(false);
const donationPending = ref(null);
const donationCompleted = ref(false);
const copied = ref(false);
let pollingInterval = null;

const openModal = () => showModal.value = true;

const closeModal = () => {
    showModal.value = false;
    donationPending.value = null;
    donationCompleted.value = false;
    selectedAmount.value = null;
    copied.value = false;
    clearInterval(pollingInterval);
};

const donate = async () => {
    if (!selectedAmount.value) return;

    const response = await axios.post('/donations', {
        amount: selectedAmount.value,
    });

    donationPending.value = response.data.donation;

    // Iniciar verificação periódica
    startPollingDonation();
};

const copyToClipboard = () => {
    navigator.clipboard.writeText(donationPending.value.wallet_address);
    copied.value = true;
    setTimeout(() => copied.value = false, 2000);
};

const checkDonationStatus = async () => {
    if (!donationPending.value) return;

    const response = await axios.get(`/api/donations/${donationPending.value.id}`);
    if (response.data.donation.status === 'completed') {
        donationCompleted.value = true;
        clearInterval(pollingInterval);
    }
};

const startPollingDonation = () => {
    clearInterval(pollingInterval);
    pollingInterval = setInterval(checkDonationStatus, 5000); // a cada 5 segundos
};

onBeforeUnmount(() => {
    clearInterval(pollingInterval);
});
</script>

<template>
<AppLayout title="Donations">
    <div class="min-h-screen flex items-center justify-center p-6">
        <div class=" max-w-3xl w-full text-center">

            <!-- Intro -->
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Support our project</h1>
                <p class="mt-2 text-gray-600 dark:text-gray-300">
                    Your donation helps us grow. All transactions are in USDT (TRC20).
                </p>
            </div>

            <!-- Create Donation Button -->
            <div>
                <button @click="openModal"
                    class="bg-green-600 hover:bg-green-500 text-white px-8 py-3 rounded-xl text-lg shadow-md transition">
                    💸 Make a Donation
                </button>
            </div>

            <!-- Modal Overlay -->
            <!-- Modal Overlay -->
            <transition name="fade">
                <div v-if="showModal"
                    class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center px-4">
                    <!-- Modal Content -->
                    <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-xl relative transition-all max-h-[90vh] overflow-y-auto">
                        <!-- Close Button -->
                        <button class="absolute top-3 right-3 text-gray-400 hover:text-black text-2xl" @click="closeModal">
                            &times;
                        </button>

                        <!-- Step 1: Select Amount -->
                        <div v-if="!donationPending">
                            <h2 class="text-xl font-semibold mb-5 text-gray-800 text-center">Choose a donation amount</h2>
                            <div class="grid grid-cols-2 gap-4 mb-6">
                                <button
                                    v-for="amount in amounts"
                                    :key="amount"
                                    @click="selectedAmount = amount"
                                    class="border border-gray-300 px-4 py-2 rounded-lg text-lg font-medium transition duration-150"
                                    :class="{
                                        'bg-blue-100 border-blue-500 text-blue-700': selectedAmount === amount,
                                        'hover:bg-gray-100': selectedAmount !== amount
                                    }"
                                >
                                    {{ amount }} USD
                                </button>
                            </div>
                            <button
                                @click="donate"
                                class="w-full bg-green-600 hover:bg-green-500 text-white py-2 rounded-lg text-lg font-semibold">
                                ✅ Confirm Donation
                            </button>
                        </div>

                        <!-- Step 2: Payment Instructions -->
                        <div v-else-if="!donationCompleted">
                            <h2 class="text-xl font-semibold text-center text-gray-800 mb-3">Awaiting Payment</h2>
                            <p class="text-gray-600 text-sm text-center mb-3">
                                Please send <strong>{{ donationPending.amount }} USDT</strong><br />
                                <span class="text-xs text-gray-500">(TRC20 - Tron Network)</span>
                            </p>

                            <div class="bg-gray-100 p-4 rounded-lg text-center font-mono text-sm break-all">
                                {{ donationPending.wallet_address }}
                            </div>

                            <!-- Copy Button -->
                            <div class="mt-3 flex justify-center">
                                <button @click="copyToClipboard"
                                    class="text-blue-600 hover:text-blue-800 text-sm flex items-center gap-1">
                                    📋 {{ copied ? 'Copied!' : 'Copy address' }}
                                </button>
                            </div>

                            <p class="text-xs text-gray-500 text-center mt-4">
                                The donation will be confirmed automatically after we detect the payment.
                            </p>
                        </div>

                        <!-- Step 3: Completed -->
                        <div v-else>
                            <h2 class="text-xl font-bold text-green-600 text-center mb-4">✅ Donation Completed</h2>
                            <p class="text-center text-gray-700">
                                Thank you for your support!<br>
                                We have received your <strong>{{ donationPending.amount }} USDT</strong> donation.
                            </p>
                            <div class="mt-6 flex justify-center">
                                <button @click="closeModal"
                                    class="bg-green-600 hover:bg-green-500 text-white px-6 py-2 rounded-xl shadow-md transition">
                                    Close
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </transition>

        </div>
    </div>
</AppLayout>
</template>
