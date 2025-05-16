<script setup lang="ts">
import { defineProps, ref, computed } from 'vue'

const props = defineProps<{
  headers: Array<string>
  data: Array<Record<string, any>>
  filterByName?: boolean
  showEditAction?: boolean
  showDeleteAction?: boolean
}>()

const searchName = ref('')

// Filtragem dinâmica
const filteredData = computed(() => {
  if (!props.filterByName || !searchName.value) return props.data

  return props.data.filter(item =>
    Object.values(item).some(value =>
      typeof value === 'string' &&
      value.toLowerCase().includes(searchName.value.toLowerCase())
    )
  )
})
</script>

<template>
    <div class="overflow-x-auto bg-white dark:bg-gray-900 shadow-xl rounded-2xl p-6 border border-gray-200 dark:border-gray-700">
      <!-- Campo de busca -->
      <div v-if="filterByName" class="mb-6">
        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Search</label>
        <input
          v-model="searchName"
          type="text"
          placeholder="Type a name..."
          class="block w-full px-4 py-2 text-sm border rounded-lg shadow-sm focus:ring focus:ring-blue-300 border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
        />
      </div>

      <!-- Tabela -->
      <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
        <table class="min-w-full divide-y divide-gray-200 text-sm bg-white">
          <thead class="bg-gray-50">
            <tr>
              <th
                v-for="header in headers"
                :key="header"
                class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"
              >
                {{ header }}
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="item in filteredData" :key="item.id ?? JSON.stringify(item)" class="hover:bg-gray-50 transition-colors">
              <td
                v-for="header in headers"
                :key="header"
                class="px-6 py-4 text-gray-800 whitespace-nowrap"
              >
                <template v-if="header === 'Actions'">
                  <div class="flex flex-wrap gap-2">
                    <button
                      v-if="props.showEditAction"
                      @click="$emit('edit', item)"
                      class="px-3 py-1 text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg"
                    >
                      Edit
                    </button>
                    <button
                      v-if="props.showDeleteAction"
                      @click="$emit('delete', item)"
                      class="px-3 py-1 text-xs font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg"
                    >
                      Delete
                    </button>
                  </div>
                </template>
                <template v-else>
                  {{ item[header.toLowerCase()] ?? '—' }}
                </template>
              </td>
            </tr>
            <tr v-if="filteredData.length === 0">
              <td :colspan="headers.length" class="px-6 py-4 text-center text-sm text-gray-500">
                No results found.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </template>
