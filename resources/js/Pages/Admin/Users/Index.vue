<template>
    <AppLayout title="Gerenciar Usuários">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-900 dark:text-gray-100 leading-tight">
                Gerenciar Usuários
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <table class="w-full border-collapse border border-gray-300 dark:border-gray-600">
                        <thead>
                            <tr class="bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                                <th class="border border-gray-300 dark:border-gray-600 p-3">ID</th>
                                <th class="border border-gray-300 dark:border-gray-600 p-3">Nome</th>
                                <th class="border border-gray-300 dark:border-gray-600 p-3">E-mail</th>
                                <th class="border border-gray-300 dark:border-gray-600 p-3">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="user in users" :key="user.id"
                                class="border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                <td class="border border-gray-300 dark:border-gray-600 p-3 text-center text-gray-900 dark:text-gray-100">
                                    {{ user.id }}
                                </td>
                                <td class="border border-gray-300 dark:border-gray-600 p-3 text-center text-gray-900 dark:text-gray-100">
                                    {{ user.name }}
                                </td>
                                <td class="border border-gray-300 dark:border-gray-600 p-3 text-center text-gray-900 dark:text-gray-100">
                                    {{ user.email }}
                                </td>
                                <td class="border border-gray-300 dark:border-gray-600 p-3 text-center">
                                    <a :href="route('admin.users.edit', user.id)"
                                        class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow-md hover:bg-blue-700 transition">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { defineProps } from 'vue';

defineProps({
    users: Array
});
</script>
