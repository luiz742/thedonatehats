<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { usePage, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const page = usePage();

const user = page.props.auth.user;
const kyc = page.props.kyc;
const csrf_token = page.props.csrf_token;

const isLoading = ref(false);
const kycStatus = ref(kyc?.status || 'pending');

const updateKycStatus = async (status) => {
    isLoading.value = true;
    try {
        await router.patch(route("admin.users.kyc.update", user.id), { status });
        kycStatus.value = status;
    } finally {
        isLoading.value = false;
    }
};

const formatImageUrl = (path) => {
    if (!path) return '';
    return path.startsWith('/storage/') ? path : `/storage/${path}`;
};
</script>

<template>
    <AppLayout title="KYC Details">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                KYC Details
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">KYC Information</h3>

                    <div v-if="kyc">
                        <p class="text-gray-700 dark:text-gray-300"><strong>Full Name:</strong> {{ kyc.full_name }}</p>
                        <p class="text-gray-700 dark:text-gray-300"><strong>Date of Birth:</strong> {{ kyc.date_of_birth
                            }}</p>
                        <p class="text-gray-700 dark:text-gray-300"><strong>Document Type:</strong> {{ kyc.document_type
                            }}</p>
                        <p class="text-gray-700 dark:text-gray-300"><strong>Document Number:</strong> {{
                            kyc.document_number }}
                        </p>
                        <p class="text-gray-700 dark:text-gray-300"><strong>Status:</strong>
                            <span :class="{
                                'text-yellow-500': kycStatus === 'pending',
                                'text-green-500': kycStatus === 'approved',
                                'text-red-500': kycStatus === 'rejected'
                            }">
                                {{ kycStatus }}
                            </span>
                        </p>

                        <div class="mt-4 space-y-4">
                            <div>
                                <strong class="text-gray-900 dark:text-gray-100">Document File:</strong><br>
                                <a :href="formatImageUrl(kyc.document_image)" target="_blank"
                                    class="text-blue-600 dark:text-blue-400 underline">
                                    View Document
                                </a>
                            </div>
                            <div class="mt-4">
                                <strong class="text-gray-900 dark:text-gray-100">Selfie File:</strong><br>
                                <a :href="formatImageUrl(kyc.selfie_image)" target="_blank"
                                    class="text-blue-600 dark:text-blue-400 underline">
                                    View Selfie
                                </a>
                            </div>
                        </div>

                        <div class="mt-6 flex gap-4">
                            <button @click="updateKycStatus('approved')" :disabled="isLoading"
                                class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50">
                                Approve
                            </button>

                            <button @click="updateKycStatus('rejected')" :disabled="isLoading"
                                class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50">
                                Reject
                            </button>
                        </div>
                    </div>
                    <div v-else class="text-red-500">No KYC information found.</div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
