<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps<{
  kycs: Array<{
    id: number,
    user: {
      name: string | null
    } | null,
    status: 'pending' | 'approved' | 'rejected'
  }>
}>()
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
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-100 dark:bg-gray-700">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
              </thead>
              <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200">
                <tr v-for="kyc in props.kycs" :key="kyc.id">
                  <td class="px-6 py-4 text-gray-900 dark:text-gray-100 whitespace-nowrap">
                    {{ kyc.user?.name ?? '---' }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="{
                      'text-yellow-600': kyc.status === 'pending',
                      'text-green-600': kyc.status === 'approved',
                      'text-red-600': kyc.status === 'rejected'
                    }">
                      {{ kyc.status }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <Link
                      :href="`/admin/kyc/${kyc.id}`"
                      class="text-sm px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700"
                    >
                      View
                    </Link>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
