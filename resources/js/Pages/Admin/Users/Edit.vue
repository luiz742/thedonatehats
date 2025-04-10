<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { defineProps, ref } from "vue";
import { router } from "@inertiajs/vue3";

const props = defineProps({
    user: Object
});

const isLoading = ref(false);
const kycStatus = ref(props.user.kyc?.status || 'pending');
const isAdmin = ref(props.user.is_admin);

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
            is_admin: !isAdmin.value,
        }, {
            preserveScroll: true, // Evita que a página role para o topo após a atualização
            onSuccess: (page) => {
                isAdmin.value = page.props.user.is_admin; // Atualiza o estado com o novo valor
            },
        });
    } catch (error) {
        console.error("Erro ao atualizar admin:", error);
    } finally {
        isLoading.value = false;
    }
};

const formatImageUrl = (imagePath) => {
    if (!imagePath) return ''; // Prevent error if image does not exist
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
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
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
                    <button class="mt-3 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:opacity-50"
                        :disabled="isLoading" @click="toggleAdmin">
                        {{ isAdmin ? 'Remove Admin' : 'Make Admin' }}
                    </button>

                    <h3 class="mt-6 text-lg font-semibold text-gray-900 dark:text-gray-100">
                        KYC Information
                    </h3>
                    <div v-if="user.kyc">
                        <p class="text-gray-800 dark:text-gray-300"><strong>Full Name:</strong> {{ user.kyc.full_name }}
                        </p>
                        <p class="text-gray-800 dark:text-gray-300"><strong>Date of Birth:</strong> {{
                            user.kyc.date_of_birth }}
                        </p>
                        <p class="text-gray-800 dark:text-gray-300"><strong>Document Type:</strong> {{
                            user.kyc.document_type }}
                        </p>
                        <p class="text-gray-800 dark:text-gray-300"><strong>Document Number:</strong> {{
                            user.kyc.document_number }}</p>
                        <p class="text-gray-800 dark:text-gray-300"><strong>Status:</strong>
                            <span :class="{
                                'text-yellow-500': kycStatus === 'pending',
                                'text-green-500': kycStatus === 'approved',
                                'text-red-500': kycStatus === 'rejected'
                            }">
                                {{ kycStatus }}
                            </span>
                        </p>

                        <!-- Display selfie and document -->
                        <div class="mt-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Uploaded Photos</h3>
                            <div class="flex gap-4 mt-2">
                                <div>
                                    <p class="text-gray-800 dark:text-gray-300"><strong>Document:</strong></p>
                                    <a :href="formatImageUrl(user.kyc.document_image)" target="_blank"
                                        rel="noopener noreferrer"
                                        class="inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                        View Document
                                    </a>
                                </div>
                                <div>
                                    <p class="text-gray-800 dark:text-gray-300"><strong>Selfie:</strong></p>
                                    <a :href="formatImageUrl(user.kyc.selfie_image)" target="_blank"
                                        rel="noopener noreferrer"
                                        class="inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                        View Selfie
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 flex gap-3">
                            <button
                                class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 disabled:opacity-50"
                                :disabled="isLoading" @click="updateKycStatus('approved')">
                                Approve
                            </button>
                            <button class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 disabled:opacity-50"
                                :disabled="isLoading" @click="updateKycStatus('rejected')">
                                Reject
                            </button>
                        </div>
                    </div>
                    <div v-else class="text-red-500 dark:text-red-400">
                        No KYC found for this user.
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
