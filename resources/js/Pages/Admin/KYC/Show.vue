<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { usePage } from '@inertiajs/vue3';

const { props } = usePage();
const kyc = props.kyc;
</script>

<template>
    <AppLayout title="KYC Details">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                KYC Detail
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 space-y-4">
                    <p><strong>Full Name:</strong> {{ kyc.full_name }}</p>
                    <p><strong>Date of Birth:</strong> {{ kyc.date_of_birth }}</p>
                    <p><strong>Document Type:</strong> {{ kyc.document_type }}</p>
                    <p><strong>Document Number:</strong> {{ kyc.document_number }}</p>

                    <div v-if="kyc.document_image_url">
                        <strong>Document:</strong>
                        <img :src="kyc.document_image_url" alt="Document" class="max-w-xs mt-2" />
                    </div>

                    <div v-if="kyc.selfie_image_url">
                        <strong>Selfie:</strong>
                        <img :src="kyc.selfie_image_url" alt="Selfie" class="max-w-xs mt-2" />
                    </div>

                    <div class="mt-6 flex gap-4">
                        <form :action="`/admin/kyc/${kyc.id}/approve`" method="POST">
                            <input type="hidden" name="_token" :value="props.csrf_token" />
                            <button
                                type="submit"
                                class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded"
                            >
                                Approve
                            </button>
                        </form>

                        <form :action="`/admin/kyc/${kyc.id}/reject`" method="POST">
                            <input type="hidden" name="_token" :value="props.csrf_token" />
                            <button
                                type="submit"
                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
                            >
                                Reject
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
