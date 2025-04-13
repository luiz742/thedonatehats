<script setup lang="ts">
import Table from '@/Components/Table.vue'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps<{
  donations: Array<{
    id: number,
    user: {
      name: string | null
    } | null,
    amount: number,
    status: string
  }>
}>()

// Transformando os dados em formato achatado
const formattedDonations = props.donations.map(donation => ({
  user: donation.user?.name ?? '—',
  amount: donation.amount,
  status: donation.status
}))

const headers = ['User', 'Amount', 'Status']
</script>

<template>
  <AppLayout title="Donations">
    <template #header>
      <h2 class="font-semibold text-xl md:text-2xl text-gray-800 dark:text-gray-200 leading-tight">
        Donations list
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <Table
          :data="formattedDonations"
          :headers="headers"
          :filterByName="true"
        />
      </div>
    </div>
  </AppLayout>
</template>
