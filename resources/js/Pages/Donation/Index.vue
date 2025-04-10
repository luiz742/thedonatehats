<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { defineProps, ref, computed, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({
    wallet: Object,
    deposits: Array,
    donations: Array,
});

const selectedAmount = ref(null);
const amounts = [100, 500, 1000, 2000];
const showModal = ref(false);
const donationPending = ref(null);
const countdown = ref('15:00');
const donationExpired = ref(false);
let timerInterval = null;

const openModal = () => showModal.value = true;
const closeModal = () => {
    showModal.value = false;
    donationPending.value = null;
    clearInterval(timerInterval);
};

const donate = async () => {
    if (!selectedAmount.value) return;

    const response = await axios.post('/donations', { amount: selectedAmount.value });

    donationPending.value = response.data.donation;
    startTimer(new Date(donationPending.value.expires_at));
};

function startTimer(expiryTime) {
    clearInterval(timerInterval);
    updateCountdown(expiryTime);

    timerInterval = setInterval(() => {
        updateCountdown(expiryTime);
    }, 1000);
}

function updateCountdown(expiryTime) {
    const now = new Date();
    const distance = expiryTime - now;

    if (distance <= 0) {
        countdown.value = '00:00';
        donationExpired.value = true; // <- Aqui
        clearInterval(timerInterval);
        return;
    }

    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

    countdown.value = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
}


onUnmounted(() => {
    clearInterval(timerInterval);
});
</script>

<template>
    <AppLayout title="Doações">
        <div class="p-4">
            <button @click="openModal" class="bg-blue-600 text-white px-4 py-2 rounded">Criar Doação</button>

            <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
                <div class="bg-white rounded p-6 max-w-sm w-full relative">
                    <button class="absolute top-2 right-2 text-gray-500" @click="closeModal">✖</button>

                    <div v-if="!donationPending">
                        <h2 class="text-xl font-semibold mb-4">Escolha o valor da doação</h2>
                        <div class="space-y-2">
                            <button
                                v-for="amount in amounts"
                                :key="amount"
                                @click="selectedAmount = amount"
                                class="w-full px-4 py-2 border rounded hover:bg-gray-100"
                                :class="{
                                    'bg-blue-100 border-blue-500': selectedAmount === amount
                                }"
                            >
                                {{ amount }} USD
                            </button>
                        </div>
                        <button @click="donate" class="mt-4 w-full bg-green-600 text-white py-2 rounded">
                            Confirmar Doação
                        </button>
                    </div>

                    <div v-else>
                        <h2 class="text-xl font-semibold text-center mb-2">Aguardando pagamento</h2>
                        <p class="text-center text-gray-600 mb-4">Você tem <strong>{{ countdown }}</strong> para enviar {{ donationPending.amount }} USDT para:</p>
                        <div class="bg-gray-100 p-2 rounded text-center text-sm break-all">
                            {{ wallet?.address }}
                        </div>
                        <p class="text-xs text-gray-500 text-center mt-2">Após o pagamento, a doação será confirmada automaticamente.</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
