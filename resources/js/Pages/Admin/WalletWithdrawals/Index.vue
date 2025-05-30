<script setup>
import { ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'
import AppLayout from '@/Layouts/AppLayout.vue'
import Modal from '@/Components/Modal.vue'

const wallets = ref([])
const loading = ref(null)
const pageLoaded = ref(false)

const confirmModal = ref(false)
const messageModal = ref(false)
const modalTitle = ref('')
const modalMessage = ref('')
const onConfirm = ref(() => { })

const openConfirmModal = (title, message, confirmAction) => {
    modalTitle.value = title
    modalMessage.value = message
    onConfirm.value = () => {
        confirmModal.value = false
        confirmAction()
    }
    confirmModal.value = true
}

const openMessageModal = (message) => {
    modalMessage.value = message
    messageModal.value = true
}

const fetchWalletsWithBalances = async () => {
    try {
        const response = await axios.get('/wallets/usdt/balances')
        wallets.value = response.data
    } finally {
        pageLoaded.value = true
    }
}

onMounted(fetchWalletsWithBalances)

const fundAndWithdraw = async (wallet) => {
    loading.value = wallet.id
    const amount = 10 // TRX necessário para fees

    try {
        // Passo 1: envia TRX
        const fundResponse = await axios.post(route('admin.wallet.fund', wallet.id), { amount })

        if (!fundResponse.data.success) {
            throw new Error('Erro no envio de TRX: ' + (fundResponse.data.message ?? 'Erro desconhecido'))
        }

        // Passo 2: saque de USDT
        const withdrawResponse = await axios.post(route('admin.wallet.withdraw', wallet.id), {
            amount: wallet.balance
        })

        if (withdrawResponse.data.success) {
            openMessageModal('TRX enviado e saque concluído com sucesso! TX: ' + withdrawResponse.data.tx_id)
            wallets.value = wallets.value.filter(w => w.id !== wallet.id)
        } else {
            throw new Error(withdrawResponse.data.message ?? 'Erro desconhecido no saque de USDT.')
        }

    } catch (e) {
        openMessageModal(e.message || 'Erro inesperado durante o processo.')
    } finally {
        loading.value = null
    }
}
</script>

<template>
    <AppLayout title="Withdraw USDT">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Withdraw USDT to Fixed Wallet
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div v-if="pageLoaded">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th
                                        class="px-4 py-2 text-left text-sm font-medium text-gray-600 dark:text-gray-300">
                                        User
                                    </th>
                                    <th
                                        class="px-4 py-2 text-left text-sm font-medium text-gray-600 dark:text-gray-300">
                                        Address
                                    </th>
                                    <th
                                        class="px-4 py-2 text-left text-sm font-medium text-gray-600 dark:text-gray-300">
                                        USDT
                                        Balance</th>
                                    <th class="px-4 py-2"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="wallet in wallets" :key="wallet.id" class="border-t dark:border-gray-700">
                                    <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">
                                        {{ wallet.user?.name ?? 'Unnamed' }}
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">
                                        {{ wallet.address }}
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">
                                        {{ wallet.balance.toFixed(6) }}
                                    </td>
                                    <td class="px-4 py-2">
                                        <button
                                            class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm px-3 py-1 rounded"
                                            :disabled="loading === wallet.id" @click="openConfirmModal(
                                                'Confirmar envio e saque',
                                                `Deseja enviar 10 TRX e sacar ${wallet.balance.toFixed(6)} USDT para a carteira fixa?`,
                                                () => fundAndWithdraw(wallet)
                                            )">
                                            <span v-if="loading === wallet.id">
                                                <svg class="animate-spin h-4 w-4 inline mr-1 text-white"
                                                    viewBox="0 0 24 24" fill="none">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                                        stroke="currentColor" stroke-width="4" />
                                                    <path class="opacity-75" fill="currentColor"
                                                        d="M4 12a8 8 0 018-8v2a6 6 0 100 12v2a8 8 0 01-8-8z" />
                                                </svg>
                                                Processando...
                                            </span>
                                            <span v-else>Fund + Withdraw</span>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-else class="text-center py-10 text-gray-500 dark:text-gray-400">
                        Loading wallets...
                    </div>
                </div>
            </div>
        </div>


        <Modal :show="confirmModal" @close="confirmModal = false">
            <div class="p-6">
                <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">{{ modalTitle }}</h2>
                <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">{{ modalMessage }}</p>
                <div class="flex justify-end space-x-2">
                    <button @click="confirmModal = false"
                        class="px-4 py-2 bg-gray-500 text-white rounded">Cancelar</button>
                    <button @click="onConfirm" class="px-4 py-2 bg-blue-600 text-white rounded">Confirmar</button>
                </div>
            </div>
        </Modal>


        <Modal :show="messageModal" @close="messageModal = false">
            <div class="p-6">
                <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">Aviso</h2>
                <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">{{ modalMessage }}</p>
                <div class="flex justify-end">
                    <button @click="messageModal = false" class="px-4 py-2 bg-blue-600 text-white rounded">OK</button>
                </div>
            </div>
        </Modal>
    </AppLayout>
</template>
