<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { defineProps, ref, computed } from "vue";
import { router } from "@inertiajs/vue3";

const props = defineProps({
    user: Object,
    donations: Object
});

const isLoading = ref(false);
const kycStatus = ref(props.user.kyc?.status || 'pending');
const isAdmin = ref(props.user.is_admin);
const showKycModal = ref(false);

const getKycLabel = (status) => {
    const statusMap = {
        pending: 'Under Review',
        approved: 'Verified',
        rejected: 'Rejected',
        expired: 'Expired'
    };

    return status ? (statusMap[status] || 'Unverified') : 'Unverified';
};

const updateKycStatus = async (status) => {
    isLoading.value = true;
    await router.patch(route("admin.users.kyc.update", props.user.id), { status });
    kycStatus.value = status;
    isLoading.value = false;
};

const toggleAdmin = async () => {
    isLoading.value = true;
    try {
        await router.patch(route("admin.users.update", props.user.id), {
            name: props.user.name,
            email: props.user.email,
            is_admin: !isAdmin.value,
        }, {
            preserveScroll: true,
            onSuccess: () => {
                isAdmin.value = !isAdmin.value;
            },
            onError: (errors) => {
                console.error('Erro na atualização do admin:', errors);
            }
        });
    } catch (error) {
        console.error("Erro ao atualizar admin:", error);
    } finally {
        isLoading.value = false;
    }
};

const totalShisha = computed(() => {
    return (props.user.donations || [])
        .filter(d => d.status === 'completed' && d.shisha_price)
        .reduce((sum, d) => sum + d.amount / d.shisha_price, 0)
        .toFixed(2);
});

const formatImageUrl = (imagePath) => {
    if (!imagePath) return '';
    return imagePath.startsWith('/storage/') ? imagePath : `/storage/${imagePath}`;
};
</script>

<template>
    <AppLayout title="Edit User">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-900 dark:text-gray-100 leading-tight">
                Edit User
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-12">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        User Information
                    </h3>
                    <p class="text-gray-800 dark:text-gray-300"><strong>Name:</strong> {{ user.name }}</p>
                    <p class="text-gray-800 dark:text-gray-300"><strong>Email:</strong> {{ user.email }}</p>
                    <p class="text-gray-800 dark:text-gray-300"><strong>Admin:</strong>
                        <span :class="{ 'text-green-500': isAdmin, 'text-red-500': !isAdmin }">
                            {{ isAdmin ? '✅ Yes' : '❌ No' }}
                        </span>
                    </p>
                    <p class="text-gray-800 dark:text-gray-300">KYC Status: <strong>{{ getKycLabel(props.kyc?.status)
                            }}</strong></p>
                    <button
                        class="px-5 py-2.5 bg-indigo-600 text-white font-medium rounded-xl mt-2 hover:bg-indigo-700 transition-all duration-200"
                        :disabled="isLoading" @click="toggleAdmin">
                        {{ isAdmin ? 'Remove Admin' : 'Make Admin' }}
                    </button>

                    <!-- Botão para abrir modal de KYC -->
                    <div class="mt-6">
                        <button v-if="user.kyc"
                            class="px-5 py-2.5 bg-indigo-600 text-white font-medium rounded-xl hover:bg-indigo-700 transition-all duration-200"
                            @click="showKycModal = true">
                            View KYC Details
                        </button>
                        <p v-else class="text-red-500 dark:text-red-400 mt-2 italic">No KYC found for this user.</p>
                    </div>
                </div>

                <!-- Donations Table -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg md:text-xl font-semibold mb-4 text-gray-800 dark:text-gray-200">Donations</h3>
                    <p class="text-gray-800 dark:text-gray-200 text-base md:text-lg font-medium mb-2">
                        💰 <strong>Total SHISHA Balance:</strong> {{ totalShisha }} SHISHA
                    </p>
                    <div class="w-full overflow-x-auto">
                        <table class="min-w-full text-sm md:text-base border-collapse">
                            <thead>
                                <tr>
                                    <th class="p-3 font-bold uppercase bg-gray-200 text-gray-600 border text-left">
                                        Amount (USDT TRC20)</th>
                                    <th class="p-3 font-bold uppercase bg-gray-200 text-gray-600 border text-left">
                                        Wallet</th>
                                    <th class="p-3 font-bold uppercase bg-gray-200 text-gray-600 border text-left">
                                        SHISHA
                                    </th>
                                    <th class="p-3 font-bold uppercase bg-gray-200 text-gray-600 border text-left">Date
                                    </th>
                                    <th class="p-3 font-bold uppercase bg-gray-200 text-gray-600 border text-left">
                                        Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="donation in user.donations" :key="donation.id"
                                    class="bg-white hover:bg-gray-100">
                                    <td class="p-3 text-gray-800 border whitespace-nowrap">{{ donation.amount }}</td>
                                    <td class="p-3 text-gray-800 border break-all">{{ donation.wallet_address }}</td>
                                    <td class="p-3 text-gray-800 border whitespace-nowrap">
                                        <span v-if="donation.shisha_price & donation.status == 'completed'">
                                            {{ (donation.amount / donation.shisha_price).toFixed(2) }} SHISHA
                                        </span>
                                        <span v-else class="text-gray-400 italic">N/A</span>
                                    </td>
                                    <td class="p-3 text-gray-800 border whitespace-nowrap">
                                        {{ new Date(donation.created_at).toLocaleString() }}
                                    </td>
                                    <td class="p-3 text-gray-800 border">
                                        <span :class="{
                                            'bg-green-400': donation.status === 'active' || donation.status === 'completed',
                                            'bg-yellow-400': donation.status === 'pending'
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

        <!-- Modal de KYC -->
        <div v-if="showKycModal"
            class="fixed inset-0 z-50 bg-black bg-opacity-60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="relative bg-white dark:bg-gray-900 w-full max-w-2xl rounded-2xl shadow-2xl p-6 animate-fade-in">
                <!-- Close Button -->
                <button
                    class="absolute top-3 right-3 text-gray-500 hover:text-gray-800 dark:hover:text-gray-200 text-2xl"
                    @click="showKycModal = false">&times;</button>

                <!-- Modal Title -->
                <h3 class="text-2xl font-semibold mb-4 text-gray-800 dark:text-white">KYC Verification</h3>

                <!-- KYC Fields -->
                <div class="space-y-2 text-sm md:text-base">
                    <p class="text-gray-700 dark:text-gray-300"><strong>Full Name:</strong> {{ user.kyc.full_name }}</p>
                    <p class="text-gray-700 dark:text-gray-300"><strong>Date of Birth:</strong> {{
                        user.kyc.date_of_birth }}</p>
                    <p class="text-gray-700 dark:text-gray-300"><strong>Document Type:</strong> {{
                        user.kyc.document_type }}</p>
                    <p class="text-gray-700 dark:text-gray-300"><strong>Document Number:</strong> {{
                        user.kyc.document_number }}
                    </p>
                    <p class="text-gray-700 dark:text-gray-300"><strong>Status:</strong>
                        <strong>{{ getKycLabel(props.kyc?.status) }}</strong>
                    </p>
                </div>

                <!-- Images -->
                <div class="mt-5 flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <p class="text-gray-700 dark:text-gray-300 font-medium mb-1">Document Image:</p>
                        <a :href="formatImageUrl(user.kyc.document_image)" target="_blank"
                            class="inline-block px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm transition">
                            View Document
                        </a>
                    </div>
                    <div class="flex-1">
                        <p class="text-gray-700 dark:text-gray-300 font-medium mb-1">Selfie:</p>
                        <a :href="formatImageUrl(user.kyc.selfie_image)" target="_blank"
                            class="inline-block px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm transition">
                            View Selfie
                        </a>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-6 flex gap-4 justify-end">
                    <button
                        class="px-5 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-all disabled:opacity-50"
                        :disabled="isLoading" @click="updateKycStatus('approved')">
                        Approve
                    </button>
                    <button
                        class="px-5 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-all disabled:opacity-50"
                        :disabled="isLoading" @click="updateKycStatus('rejected')">
                        Reject
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
