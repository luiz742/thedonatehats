<script setup lang="ts">
import Table from '@/Components/Table.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import { router } from '@inertiajs/vue3'

const props = defineProps<{
  users: Array<{
    id: number
    name: string
    email: string
  }>
}>()

// Definindo cabeçalhos para a tabela
const headers = ['ID', 'Name', 'Email', 'Actions']

// Função para redirecionar ao editar
const handleEdit = (user: { id: number }) => {
  router.visit(`/admin/users/${user.id}/edit`)
}
</script>

<template>
  <AppLayout title="Manage Users">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-900 dark:text-gray-100 leading-tight">
        Manage Users
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Exibindo a tabela de usuários -->
        <Table
          :data="props.users"
          :headers="headers"
          :filterByName="true"
          :showEditAction="true"
          @edit="handleEdit"
        />
      </div>
    </div>
  </AppLayout>
</template>
