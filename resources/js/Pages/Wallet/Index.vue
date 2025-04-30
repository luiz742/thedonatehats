<!-- <script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { EyeIcon, EyeOffIcon } from 'lucide-vue-next'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    wallets: Array,
})
</script>

<template>
    <AppLayout>
        <div v-for="wallet in wallets" :key="wallet.id">
            <p class="text-white p-5">{{ wallet.private_key }}</p>
        </div>
    </AppLayout>
</template> -->

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { EyeIcon, EyeOffIcon } from 'lucide-vue-next'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    wallets: Array,
})

const wallets = ref(props.wallets.map(wallet => ({ ...wallet, balance: null })))
const loading = ref(true)
const showPrivateKeys = ref({})

onMounted(async () => {
    try {
        const response = await axios.get('/wallets/balances')
        const balances = response.data

        wallets.value = balances
    } finally {
        loading.value = false
    }
})

const togglePrivateKey = (walletId) => {
    showPrivateKeys.value[walletId] = !showPrivateKeys.value[walletId]
}
</script>

<template>
    <AppLayout title="Minhas Carteiras">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Carteiras e Saldos
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div v-if="loading" class="text-center text-gray-500 dark:text-gray-400 py-10">
                    Carregando saldos...
                </div>

                <div v-else-if="wallets.length === 0" class="text-center text-gray-500 dark:text-gray-400 py-10">
                    Nenhuma carteira com saldo.
                </div>

                <div v-else class="overflow-auto bg-white dark:bg-gray-900 shadow sm:rounded-lg">
                    <table class="min-w-full text-sm text-left text-gray-600 dark:text-gray-300">
                        <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase">
                            <tr>
                                <th class="px-6 py-4">Address</th>
                                <th class="px-6 py-4">Priv Key</th>
                                <th class="px-6 py-4 text-right">Amount (USDT)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="wallet in wallets" :key="wallet.id" class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="px-6 py-4 font-medium">{{ wallet.address }}</td>
                                <td class="px-6 py-4 flex items-center gap-2">
                                    <span class="break-all">
                                        <span v-if="showPrivateKeys[wallet.id]">{{ wallet.private_key }}</span>
                                        <span v-else>••••••••••••••••••</span>
                                    </span>
                                    <button @click="togglePrivateKey(wallet.id)" class="ml-2 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                                        <component :is="showPrivateKeys[wallet.id] ? EyeOffIcon : EyeIcon" class="w-5 h-5" />
                                    </button>
                                </td>
                                <td class="px-6 py-4 text-right text-green-600 dark:text-green-400 font-semibold">
                                    {{ wallet.balance?.toFixed(2) ?? '0.00' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
