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
  <div class="overflow-x-auto bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6">
    <!-- Campo de busca -->
    <div v-if="filterByName" class="mb-4">
      <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Search</label>
      <input
        v-model="searchName"
        type="text"
        placeholder="Search..."
        class="mt-1 block w-full border border-gray-300 rounded-lg p-2 focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100"
      />
    </div>

    <!-- Tabela -->
    <table class="min-w-full divide-y divide-gray-200">
      <thead class="bg-gray-100 dark:bg-gray-700">
        <tr>
          <th
            v-for="header in headers"
            :key="header"
            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
          >
            {{ header }}
          </th>
        </tr>
      </thead>
      <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200">
        <tr v-for="item in filteredData" :key="item.id ?? JSON.stringify(item)">
          <td
            v-for="header in headers"
            :key="header"
            class="px-6 py-4 text-gray-900 dark:text-gray-100 whitespace-nowrap"
          >
            <template v-if="header === 'Actions'">
              <div class="flex gap-2">
                <button
                  v-if="props.showEditAction"
                  @click="$emit('edit', item)"
                  class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700"
                >
                  Edit
                </button>
                <button
                  v-if="props.showDeleteAction"
                  @click="$emit('delete', item)"
                  class="px-3 py-1 text-sm bg-red-600 text-white rounded hover:bg-red-700"
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
          <td :colspan="headers.length" class="px-6 py-4 text-sm text-center text-gray-500 dark:text-gray-400">
            No results found
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
