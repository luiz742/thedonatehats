<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, onMounted } from 'vue';
import { usePage, router } from '@inertiajs/vue3';  // Importando o router do Inertia.js

const { params } = usePage();
const donation = ref(null);
const isLoading = ref(false);
const donationStatus = ref('pending');

// Buscando os dados da doação
onMounted(async () => {
    const response = await axios.get(`/admin/donations/${params.id}`);
    donation.value = response.data;
    donationStatus.value = donation.value.status;
});

// Atualizando o status da doação
const updateDonationStatus = async (status) => {
    if (!donation.value) return;
    isLoading.value = true;
    try {
        await axios.patch(`/admin/donations/${donation.value.id}/status`, { status });
        donationStatus.value = status;
    } finally {
        isLoading.value = false;
    }
};
</script>

<template>
    <AppLayout title="Donation Details">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Donation #{{ donation?.id }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Donation Information</h3>

                    <div v-if="donation">
                        <p class="text-gray-700 dark:text-gray-300"><strong>User:</strong> {{ donation.user?.name ?? 'N/A' }}</p>
                        <p class="text-gray-700 dark:text-gray-300"><strong>Amount:</strong> {{ donation.amount }}</p>
                        <p class="text-gray-700 dark:text-gray-300"><strong>Status:</strong>
                            <span :class="{
                                'text-yellow-500': donationStatus === 'pending',
                                'text-green-500': donationStatus === 'approved',
                                'text-red-500': donationStatus === 'rejected'
                            }">
                                {{ donationStatus }}
                            </span>
                        </p>
                        <p class="text-gray-700 dark:text-gray-300"><strong>Created At:</strong> {{ donation.created_at }}</p>
                        <p class="text-gray-700 dark:text-gray-300"><strong>Updated At:</strong> {{ donation.updated_at }}</p>

                        <div class="mt-6 flex gap-4">
                            <button @click="updateDonationStatus('approved')" :disabled="isLoading"
                                class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50">
                                Approve
                            </button>

                            <button @click="updateDonationStatus('rejected')" :disabled="isLoading"
                                class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50">
                                Reject
                            </button>
                        </div>
                    </div>
                    <div v-else class="text-red-500">No donation information found.</div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
