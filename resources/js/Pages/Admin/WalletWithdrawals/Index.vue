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
const onConfirm = ref(() => {})

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

const withdraw = (walletId, amount) => {
    loading.value = walletId

    router.post(route('admin.wallet.withdraw', walletId), { amount }, {
        preserveScroll: true,
        onSuccess: (page) => {
            loading.value = null

            if (page.props.flash?.success) {
                openMessageModal('Withdrawal completed successfully!')

                wallets.value = wallets.value.filter(wallet => wallet.id !== walletId)
            } else {
                openMessageModal('An error occurred during withdrawal. Check the logs or try again.')
            }
        },
        onError: (errors) => {
            loading.value = null
            const errorMsg = errors.message || 'Unknown withdrawal error.'
            openMessageModal('Error: ' + errorMsg)
        }
    })
}

const fundWallet = (walletId) => {
    const amount = 10
    loading.value = `fund-${walletId}`

    axios.post(route('admin.wallet.fund', walletId), { amount })
        .then((response) => {
            loading.value = null
            if (response.data.success) {
                openMessageModal('TRX sent successfully! TX ID: ' + response.data.tx_id)
            } else {
                openMessageModal('Error sending TRX: ' + response.data.message)
            }
        })
        .catch(() => {
            loading.value = null
            openMessageModal('Error sending request.')
        })
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
                                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-600 dark:text-gray-300">User</th>
                                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-600 dark:text-gray-300">Address</th>
                                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-600 dark:text-gray-300">USDT Balance</th>
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
                                            class="bg-green-600 hover:bg-green-700 text-white text-sm px-3 py-1 rounded mr-2"
                                            :disabled="loading === `fund-${wallet.id}`"
                                            @click="openConfirmModal(
                                                'Confirmar envio de TRX',
                                                'Deseja realmente enviar 10 TRX para essa carteira?',
                                                () => fundWallet(wallet.id)
                                            )">
                                            <span v-if="loading === `fund-${wallet.id}`">Sending TRX...</span>
                                            <span v-else>Fund TRX</span>
                                        </button>

                                        <button
                                            class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-3 py-1 rounded"
                                            :disabled="loading === wallet.id"
                                            @click="openConfirmModal(
                                                'Confirmar saque',
                                                `Deseja sacar ${wallet.balance.toFixed(6)} USDT para a carteira fixa?`,
                                                () => withdraw(wallet.id, wallet.balance)
                                            )">
                                            <span v-if="loading === wallet.id">Sending...</span>
                                            <span v-else>Withdraw</span>
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

        <!-- Modal de Confirmação -->
        <Modal :show="confirmModal" @close="confirmModal = false">
            <div class="p-6">
                <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">{{ modalTitle }}</h2>
                <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">{{ modalMessage }}</p>
                <div class="flex justify-end space-x-2">
                    <button @click="confirmModal = false" class="px-4 py-2 bg-gray-500 text-white rounded">Cancelar</button>
                    <button @click="onConfirm" class="px-4 py-2 bg-blue-600 text-white rounded">Confirmar</button>
                </div>
            </div>
        </Modal>

        <!-- Modal de Mensagem -->
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


<!-- <script setup>
import { ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'
import AppLayout from '@/Layouts/AppLayout.vue'

const wallets = ref([])
const loading = ref(null)
const pageLoaded = ref(false)

const fetchWalletsWithBalances = async () => {
    try {
        const response = await axios.get('/wallets/balances')
        wallets.value = response.data
    } finally {
        pageLoaded.value = true
    }
}

onMounted(fetchWalletsWithBalances)

const withdraw = (walletId, amount) => {
    if (!confirm('Are you sure you want to withdraw this amount?')) return

    loading.value = walletId

    router.post(route('admin.wallet.withdraw', walletId), { amount }, {
        onFinish: () => loading.value = null
    })
}

const fundWallet = (walletId) => {
    const amount = 10;
    console.log('Enviando valor de TRX:', amount);



    loading.value = `fund-${walletId}`;


    axios
        .post(route('admin.wallet.fund', walletId), { amount })
        .then((response) => {

            if (response.data.success) {
                loading.value = null;
                console.log('Fundos enviados com sucesso! TX ID:', response.data.tx_id);
                alert('Saque realizado com sucesso! TX ID: ' + response.data.tx_id);
            } else {
                loading.value = null;
                console.error('Erro ao enviar fundos:', response.data.message);
                alert('Erro ao enviar fundos: ' + response.data.message);
            }
        })
        .catch((error) => {
            loading.value = null;
            console.error('Erro na requisição:', error);
            alert('Erro ao enviar a requisição.');
        });
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
                                    <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">{{ wallet.user?.name
                                        ??
                                        'Unnamed' }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">{{ wallet.address }}
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">{{
                                        wallet.balance.toFixed(6)
                                        }}</td>
                                    <td class="px-4 py-2">
                                        <button
                                            class="bg-green-600 hover:bg-green-700 text-white text-sm px-3 py-1 rounded mr-2"
                                            :disabled="loading === `fund-${wallet.id}`" @click="fundWallet(wallet.id)">
                                            <span v-if="loading === `fund-${wallet.id}`">Sending TRX...</span>
                                            <span v-else>Fund TRX</span>
                                        </button>

                                        <button
                                            class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-3 py-1 rounded"
                                            :disabled="loading === wallet.id"
                                            @click="withdraw(wallet.id, wallet.balance)">
                                            <span v-if="loading === wallet.id">Sending...</span>
                                            <span v-else>Withdraw</span>
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
    </AppLayout>
</template> -->
