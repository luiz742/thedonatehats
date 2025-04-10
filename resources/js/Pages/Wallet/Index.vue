<template>
    <div class="max-w-2xl mx-auto mt-10">
      <h1 class="text-2xl font-bold mb-4">Minha Carteira TRX</h1>

      <div v-if="wallet">
        <p><strong>Endereço:</strong> {{ wallet.address }}</p>
        <p><strong>Chave Pública:</strong> {{ wallet.public_key }}</p>
      </div>
      <div v-else>
        <form @submit.prevent="generateWallet">
          <button
            type="submit"
            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
          >
            Gerar Carteira
          </button>
        </form>
      </div>
    </div>
  </template>

  <script setup>
  import { router } from '@inertiajs/vue3'
  defineProps({ wallet: Object })

  function generateWallet() {
    router.post('/wallet/generate')
  }
  </script>
