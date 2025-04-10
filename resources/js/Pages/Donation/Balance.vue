<script setup>
import { ref, onMounted, computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';

// Definindo as propriedades esperadas
const props = defineProps({
  donations: {
    type: Array,
    required: true
  }
});

const shishaPrice = ref(null); // Estado para armazenar o preço da moeda
const loading = ref(true); // Variável para controlar o estado de carregamento

// Função para buscar o preço da moeda SHISHA em USD
const fetchShishaPrice = async () => {
  try {
    const response = await fetch('https://api.dex-trade.com/v1/public/ticker?pair=SHISHAUSDT');
    if (!response.ok) {
      throw new Error('Falha ao buscar dados da API');
    }
    const data = await response.json();
    // Garantir que o preço seja um número válido
    shishaPrice.value = parseFloat(data?.data?.last) || null; // Convertendo para número
  } catch (error) {
    console.error(error);
    shishaPrice.value = null; // Garantir que o valor será null em caso de erro
  } finally {
    loading.value = false; // Definir como 'false' após tentar carregar
  }
}

// Calculando o total de SHISHA em saldo
const totalShishaBalance = computed(() => {
  if (loading.value || shishaPrice.value === null) return 0;
  // Acessando donations a partir de props
  const totalUSD = props.donations.reduce((sum, donation) => sum + donation.amount, 0);
  return (totalUSD / shishaPrice.value).toFixed(2);
});

// Carregar o preço da moeda ao montar o componente
onMounted(fetchShishaPrice);
</script>


<template>
    <AppLayout title="Donation History">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Shisha Coin Balance: {{ totalShishaBalance }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <table class="border-collapse w-full">
                        <thead>
                            <tr>
                                <th class="p-3 font-bold uppercase bg-gray-200 text-gray-600 border border-gray-300">
                                    Amount (SHISHA)
                                </th>
                                <th class="p-3 font-bold uppercase bg-gray-200 text-gray-600 border border-gray-300">
                                    Donation Amount (USD)
                                </th>
                                <th class="p-3 font-bold uppercase bg-gray-200 text-gray-600 border border-gray-300">
                                    Date
                                </th>
                                <th class="p-3 font-bold uppercase bg-gray-200 text-gray-600 border border-gray-300">
                                    Status
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="donation in donations" :key="donation.id" class="bg-white hover:bg-gray-100">
                                <td class="p-3 text-gray-800 text-center border">{{ loading.value ? 'Loading...' : (donation.amount / shishaPrice).toFixed(2) }} SHISHA</td>
                                <td class="p-3 text-gray-800 text-center border">
                                    <!-- Exibe "Loading..." enquanto o preço não for carregado -->
                                    {{ donation.amount }} USD
                                </td>
                                <td class="p-3 text-gray-800 text-center border">
                                    {{ new Date(donation.created_at).toLocaleString() }}
                                </td>
                                <td class="p-3 text-gray-800 text-center border">
                                    <span :class="{
                                        'bg-green-400': donation.status === 'active',
                                        'bg-yellow-400': donation.status === 'pending',
                                        'bg-red-400': donation.status === 'deleted'
                                    }" class="rounded py-1 px-3 text-xs font-bold">
                                        {{ donation.status.charAt(0).toUpperCase() + donation.status.slice(1) }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
