<script setup lang="ts">
import Table from '@/Components/Table.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import { router } from '@inertiajs/vue3'

const props = defineProps<{
  kycs: Array<{
    id: number,
    user: {
      name: string | null
    } | null,
    status: 'pending' | 'approved' | 'rejected' | 'unverified'
  }>
}>()


const statusLabelMap: Record<string, string> = {
  pending: 'Pending',
  approved: 'Approved',
  rejected: 'Rejected',
  unverified: 'Unverified'
}

const formattedKycs = props.kycs.map(kyc => ({
  id: kyc.id,
  user: kyc.user?.name ?? '—',
  status: statusLabelMap[kyc.status] ?? 'Unknown'
}))


const headers = ['User', 'Status', 'Actions']

// Redirecionamento ao editar
const handleEdit = (kyc: { id: number }) => {
  router.visit(`/admin/kyc/${kyc.id}/`)
}
</script>

<template>
  <AppLayout title="KYC Management">
    <template #header>
      <h2 class="font-semibold text-xl md:text-2xl text-gray-800 dark:text-gray-200 leading-tight">
        KYC List
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <Table
          :data="formattedKycs"
          :headers="headers"
          :filterByName="true"
          :showEditAction="true"
          @edit="handleEdit"
        />
      </div>
    </div>
  </AppLayout>
</template>
