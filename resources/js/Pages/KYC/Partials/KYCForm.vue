<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ActionMessage from '@/Components/ActionMessage.vue';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    kyc: Object,
});

const isSubmitted = ref(props.kyc?.status === 'pending' || props.kyc?.status === 'approved');

const form = useForm({
    full_name: props.kyc?.full_name || "",
    date_of_birth: props.kyc?.date_of_birth || "",
    document_type: props.kyc?.document_type || "",
    document_number: props.kyc?.document_number || "",
    document_image: null,
    selfie_image: null,
});

const kycStatus = computed(() => {
    const status = props.kyc?.status || 'unverified';
    const statusMap = {
        approved: { label: 'Approved', color: 'bg-green-500' },
        pending: { label: 'Pending', color: 'bg-yellow-500' },
        rejected: { label: 'Rejected', color: 'bg-red-500' },
        unverified: { label: 'Unverified', color: 'bg-gray-500' }
    };
    return statusMap[status] || statusMap.unverified;
});

const updateFile = (event, field) => {
    const file = event.target.files[0];
    if (file) {
        form[field] = file;
    }
};

const getKycLabel = (status) => {
    const statusMap = {
        pending: 'Under Review',
        approved: 'Verified',
        rejected: 'Rejected',
        unverified: 'Unverified'
    };

    return status ? (statusMap[status] || 'Unverified') : 'Unverified';
};

const submit = () => {
    form.post("/kyc", {
        onSuccess() {
            isSubmitted.value = true;
        },
    });
};

// ENUM('pending', 'approved', 'rejected', 'unverified')

const resubmitKYC = () => {
    form.post('/kyc/resubmit', {
        onSuccess: () => {
            isSubmitted.value = true;
        }
    });
};
</script>

<template>
    <FormSection @submitted="submit">
        <template #title>
            KYC Verification
        </template>

        <template #description>
            Complete your KYC verification by providing the necessary details.
        </template>

        <template #form>
            <div class="col-span-6 sm:col-span-4">
                <div class="flex items-center gap-2">
                    <span class="font-semibold dark:text-white">Status:</span>
                    <span class="px-3 py-1 text-white text-sm rounded-lg" :class="kycStatus.color">
                        <p class="text-gray-800 dark:text-gray-300">
                            <strong>{{ getKycLabel(props.kyc?.status) }}</strong>
                        </p>
                    </span>
                </div>
            </div>

            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="full_name" value="Full Name" />
                <TextInput id="full_name" v-model="form.full_name" type="text" class="mt-1 block w-full" required
                    :disabled="isSubmitted" />
                <InputError :message="form.errors.full_name" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="date_of_birth" value="Date of Birth" />
                <TextInput id="date_of_birth" v-model="form.date_of_birth" type="date" class="mt-1 block w-full"
                    required :disabled="isSubmitted" />
                <InputError :message="form.errors.date_of_birth" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="document_type" value="Document Type" />
                <select id="document_type" v-model="form.document_type"
                    class="mt-1 block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white dark:border-gray-700"
                    :disabled="isSubmitted" required>
                    <option value="" disabled>Select a document type</option>
                    <option value="passport">Passport</option>
                    <option value="national_id">National ID</option>
                </select>
                <InputError :message="form.errors.document_type" class="mt-2" />
            </div>


            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="document_number" value="Document Number" />
                <TextInput id="document_number" v-model="form.document_number" type="text" class="mt-1 block w-full"
                    required :disabled="isSubmitted" />
                <InputError :message="form.errors.document_number" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="document_image" value="Document Image" />
                <input type="file" id="document_image" @change="updateFile($event, 'document_image')"
                    class="mt-2 block w-full" :disabled="isSubmitted" />
                <InputError :message="form.errors.document_image" class="mt-2" />
                <div v-if="props.kyc?.document_image" class="mt-2">
                    <a :href="`/storage/${props.kyc.document_image}`" target="_blank" rel="noopener"
                        class="text-blue-600 underline hover:text-blue-800">
                        View Document
                    </a>
                </div>
            </div>

            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="selfie_image" value="Selfie" />
                <input type="file" id="selfie_image" @change="updateFile($event, 'selfie_image')"
                    class="mt-2 block w-full" :disabled="isSubmitted" />
                <InputError :message="form.errors.selfie_image" class="mt-2" />
                <div v-if="props.kyc?.selfie_image" class="mt-2">
                    <a :href="`/storage/${props.kyc.selfie_image}`" target="_blank" rel="noopener"
                        class="text-blue-600 underline hover:text-blue-800">
                        View Document
                    </a>
                </div>
            </div>
        </template>

        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="me-3">Saved.</ActionMessage>
            <PrimaryButton :class="{ 'opacity-25': form.processing || isSubmitted }"
                :disabled="form.processing || isSubmitted">
                {{ props.kyc ? 'Update' : 'Submit' }}
            </PrimaryButton>

            <template v-if="props.kyc?.status === 'rejected'">
                <PrimaryButton @click="resubmitKYC" class="ml-3 bg-blue-500 text-white px-4 py-2">
                    Resubmit KYC
                </PrimaryButton>
            </template>
        </template>
    </FormSection>
</template>
