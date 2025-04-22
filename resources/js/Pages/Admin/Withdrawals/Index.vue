<script setup lang="ts">
import Table from '@/Components/Table.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import { router } from '@inertiajs/vue3'

const props = defineProps<{
  withdrawals: Array<{
    id: number,
    user: {
      name: string | null
    } | null,
    solana_wallet: string,
    status: 'pending' | 'approved' | 'rejected'
    created_at: string
  }>
}>()

const statusLabelMap: Record<string, string> = {
  pending: 'Pending',
  approved: 'Approved',
  rejected: 'Rejected'
}

const formattedWithdrawals = props.withdrawals.map(w => ({
  id: w.id,
  user: w.user?.name ?? '—',
  wallet: w.solana_wallet,
  status: statusLabelMap[w.status] ?? 'Unknown',
  date: new Date(w.created_at).toLocaleString()
}))

const headers = ['User', 'Wallet', 'Status', 'Requested At', 'Actions']

const handleEdit = (withdrawal: { id: number }) => {
  router.visit(`/admin/withdrawals/${withdrawal.id}`)
}
</script>

<template>
  <AppLayout title="Withdrawals">
    <template #header>
      <h2 class="font-semibold text-xl md:text-2xl text-gray-800 dark:text-gray-200 leading-tight">
        Withdrawal Requests
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <Table
          :data="formattedWithdrawals"
          :headers="headers"
          :filterByName="true"
          :showEditAction="true"
          @edit="handleEdit"
        />
      </div>
    </div>
  </AppLayout>
</template>
